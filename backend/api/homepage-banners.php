<?php
/**
 * Homepage Banners API
 *
 * Public:
 *   GET /api/homepage-banners.php           -> active banners ordered
 *
 * Admin (requires login):
 *   GET    /api/homepage-banners.php?all=1
 *   GET    /api/homepage-banners.php?id=#
 *   POST   /api/homepage-banners.php
 *   PUT    /api/homepage-banners.php
 *   DELETE /api/homepage-banners.php?id=#
 */

require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
try {
    $pdo = getDBConnection();
} catch (Throwable $e) {
    // Ensure API always returns JSON
    sendError('Database connection failed', 500);
}

function normalizeBannerStatus($status) {
    $status = strtolower(trim((string)$status));
    return in_array($status, ['active', 'inactive'], true) ? $status : 'inactive';
}

function normalizeDisplayOrder($value) {
    if ($value === null || $value === '') return 0;
    return (int)$value;
}

function normalizeBool($value) {
    if (is_bool($value)) return $value ? 1 : 0;
    $v = strtolower(trim((string)$value));
    return in_array($v, ['1', 'true', 'yes', 'on'], true) ? 1 : 0;
}

function normalizeDateTimeOrNull($value) {
    $value = trim((string)($value ?? ''));
    if ($value === '') return null;
    // Accept "YYYY-MM-DDTHH:MM" (datetime-local) or "YYYY-MM-DD HH:MM:SS"
    $value = str_replace('T', ' ', $value);
    // If seconds missing, add ":00"
    if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}$/', $value)) {
        $value .= ':00';
    }
    // Validate
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $value);
    if ($dt && $dt->format('Y-m-d H:i:s') === $value) return $value;
    return null;
}

function getHomepageBannerColumns($pdo) {
    static $cached = null;
    if (is_array($cached)) return $cached;
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM homepage_banners");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cols = [];
        foreach ($rows as $r) {
            if (!empty($r['Field'])) $cols[$r['Field']] = true;
        }
        $cached = $cols;
        return $cols;
    } catch (Throwable $e) {
        $cached = [];
        return [];
    }
}

function hasCol($cols, $name) {
    return isset($cols[$name]) && $cols[$name] === true;
}

function isLocalhostRequest() {
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    return $host === 'localhost' || str_starts_with($host, 'localhost:') || str_starts_with($host, '127.0.0.1');
}

function ensureHomepageBannersTableExists($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'homepage_banners'");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        if ($row && isset($row[0])) return true;
    } catch (Throwable $e) {
        // continue to attempt create
    }

    // Create a compatible table (safe to run if it already exists via IF NOT EXISTS)
    $sql = "CREATE TABLE IF NOT EXISTS homepage_banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        subtitle VARCHAR(500),
        image_path TEXT NOT NULL,
        button_text VARCHAR(100),
        button_link TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        display_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_display_order (display_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $pdo->exec($sql);
    return true;
}

switch ($method) {
    case 'GET':
        try {
        ensureHomepageBannersTableExists($pdo);
        $cols = getHomepageBannerColumns($pdo);
        // Admin-only single banner fetch
        if (isset($_GET['id'])) {
            requireAdminLogin();
            $id = (int)$_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM homepage_banners WHERE id = ?");
            $stmt->execute([$id]);
            $banner = $stmt->fetch();
            if (!$banner) {
                sendError('Banner not found', 404);
            }
            sendSuccess($banner);
        }

        // Admin list
        if (isset($_GET['all']) || isset($_GET['admin'])) {
            requireAdminLogin();
            $status = isset($_GET['status']) ? normalizeBannerStatus($_GET['status']) : null;
            if ($status) {
                $stmt = $pdo->prepare("SELECT * FROM homepage_banners WHERE status = ? ORDER BY display_order ASC, created_at DESC");
                $stmt->execute([$status]);
                sendSuccess($stmt->fetchAll());
            }
            $stmt = $pdo->query("SELECT * FROM homepage_banners ORDER BY display_order ASC, created_at DESC");
            sendSuccess($stmt->fetchAll());
        }

        // Public: only active banners (and scheduled if schedule columns exist), ordered
        $selectCols = ['id', 'title', 'subtitle', 'image_path', 'button_text', 'button_link', 'status', 'display_order'];
        $optional = ['image_path_desktop', 'image_path_mobile', 'use_device_images', 'link_url', 'start_at', 'end_at'];
        foreach ($optional as $c) {
            if (hasCol($cols, $c)) $selectCols[] = $c;
        }
        $where = "WHERE status = 'active'";
        if (hasCol($cols, 'start_at')) {
            $where .= " AND (start_at IS NULL OR start_at <= NOW())";
        }
        if (hasCol($cols, 'end_at')) {
            $where .= " AND (end_at IS NULL OR end_at >= NOW())";
        }
        $sql = "SELECT " . implode(', ', $selectCols) . " FROM homepage_banners $where ORDER BY display_order ASC, created_at DESC";
        $stmt = $pdo->query($sql);
        sendSuccess($stmt->fetchAll());
        } catch (Throwable $e) {
            // If table doesn't exist yet (or other DB error), return empty list for public callers.
            // For admin callers, still return JSON error to avoid breaking UI.
            if (isset($_GET['all']) || isset($_GET['admin']) || isset($_GET['id'])) {
                sendError('Homepage banners not available yet. Please run the database migration for homepage_banners.', 500);
            }
            sendSuccess([]);
        }
        break;

    case 'POST':
        requireAdminLogin();
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: [];
            ensureHomepageBannersTableExists($pdo);
            $cols = getHomepageBannerColumns($pdo);

            // Bulk actions (activate/deactivate/delete)
            if (isset($data['action']) && $data['action'] === 'bulk') {
                $op = strtolower(trim((string)($data['op'] ?? '')));
                $ids = $data['ids'] ?? [];
                if (!is_array($ids) || count($ids) === 0) {
                    sendError('ids array is required');
                }
                $ids = array_values(array_filter(array_map('intval', $ids), function($v){ return $v > 0; }));
                if (count($ids) === 0) {
                    sendError('No valid banner ids provided');
                }
                $pdo->beginTransaction();
                try {
                    if ($op === 'activate' || $op === 'deactivate') {
                        $status = $op === 'activate' ? 'active' : 'inactive';
                        $in = implode(',', array_fill(0, count($ids), '?'));
                        $stmt = $pdo->prepare("UPDATE homepage_banners SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id IN ($in)");
                        $stmt->execute(array_merge([$status], $ids));
                    } elseif ($op === 'delete') {
                        $in = implode(',', array_fill(0, count($ids), '?'));
                        $stmt = $pdo->prepare("DELETE FROM homepage_banners WHERE id IN ($in)");
                        $stmt->execute($ids);
                    } else {
                        throw new Exception('Unsupported bulk op');
                    }
                    $pdo->commit();
                    sendSuccess([], 'Bulk operation successful');
                } catch (Throwable $e) {
                    $pdo->rollBack();
                    sendError('Bulk operation failed', 500);
                }
            }

            // Bulk create (import)
            if (isset($data['action']) && $data['action'] === 'bulk_create') {
                $items = $data['items'] ?? [];
                if (!is_array($items) || count($items) === 0) {
                    sendError('items array is required');
                }
                $pdo->beginTransaction();
                try {
                    // Build insert columns dynamically based on actual DB schema
                    $baseCols = ['title','subtitle','image_path','button_text','button_link','status','display_order'];
                    $extraCols = ['image_path_desktop','image_path_mobile','use_device_images','link_url','start_at','end_at'];
                    $insCols = [];
                    foreach (array_merge($baseCols, $extraCols) as $c) {
                        if (hasCol($cols, $c)) $insCols[] = $c;
                    }
                    $placeholders = implode(', ', array_fill(0, count($insCols), '?'));
                    $stmt = $pdo->prepare("INSERT INTO homepage_banners (" . implode(', ', $insCols) . ") VALUES ($placeholders)");
                    foreach ($items as $it) {
                        $title = trim((string)($it['title'] ?? ''));
                        $imagePath = trim((string)($it['image_path'] ?? ''));
                        if ($title === '' || $imagePath === '') {
                            continue; // skip invalid rows
                        }
                        $row = [
                            'title' => $title,
                            'subtitle' => trim((string)($it['subtitle'] ?? '')),
                            'image_path' => $imagePath,
                            'image_path_desktop' => trim((string)($it['image_path_desktop'] ?? '')) ?: null,
                            'image_path_mobile' => trim((string)($it['image_path_mobile'] ?? '')) ?: null,
                            'use_device_images' => normalizeBool($it['use_device_images'] ?? 0),
                            'link_url' => trim((string)($it['link_url'] ?? '')) ?: null,
                            'button_text' => trim((string)($it['button_text'] ?? '')),
                            'button_link' => trim((string)($it['button_link'] ?? '')),
                            'status' => normalizeBannerStatus($it['status'] ?? 'inactive'),
                            'display_order' => normalizeDisplayOrder($it['display_order'] ?? 0),
                            'start_at' => normalizeDateTimeOrNull($it['start_at'] ?? null),
                            'end_at' => normalizeDateTimeOrNull($it['end_at'] ?? null),
                        ];
                        $values = [];
                        foreach ($insCols as $c) $values[] = $row[$c] ?? null;
                        $stmt->execute($values);
                    }
                    $pdo->commit();
                    sendSuccess([], 'Import completed');
                } catch (Throwable $e) {
                    $pdo->rollBack();
                    sendError('Import failed', 500);
                }
            }

            $title = trim((string)($data['title'] ?? ''));
            $subtitle = trim((string)($data['subtitle'] ?? ''));
            $imagePath = trim((string)($data['image_path'] ?? ''));
            $imagePathDesktop = trim((string)($data['image_path_desktop'] ?? ''));
            $imagePathMobile = trim((string)($data['image_path_mobile'] ?? ''));
            $useDeviceImages = normalizeBool($data['use_device_images'] ?? 0);
            $linkUrl = trim((string)($data['link_url'] ?? ''));
            $buttonText = trim((string)($data['button_text'] ?? ''));
            $buttonLink = trim((string)($data['button_link'] ?? ''));
            $status = normalizeBannerStatus($data['status'] ?? 'active');
            $displayOrder = normalizeDisplayOrder($data['display_order'] ?? 0);
            $startAt = normalizeDateTimeOrNull($data['start_at'] ?? null);
            $endAt = normalizeDateTimeOrNull($data['end_at'] ?? null);

            if ($title === '') {
                sendError('Title is required');
            }
            if ($imagePath === '') {
                sendError('Image is required');
            }

            // Insert dynamically based on actual columns present
            $row = [
                'title' => $title,
                'subtitle' => $subtitle,
                'image_path' => $imagePath,
                'image_path_desktop' => $imagePathDesktop !== '' ? $imagePathDesktop : null,
                'image_path_mobile' => $imagePathMobile !== '' ? $imagePathMobile : null,
                'use_device_images' => $useDeviceImages,
                'link_url' => $linkUrl !== '' ? $linkUrl : null,
                'button_text' => $buttonText,
                'button_link' => $buttonLink,
                'status' => $status,
                'display_order' => $displayOrder,
                'start_at' => $startAt,
                'end_at' => $endAt
            ];
            $baseCols = ['title','subtitle','image_path','button_text','button_link','status','display_order'];
            $extraCols = ['image_path_desktop','image_path_mobile','use_device_images','link_url','start_at','end_at'];
            $insCols = [];
            foreach (array_merge($baseCols, $extraCols) as $c) {
                if (hasCol($cols, $c)) $insCols[] = $c;
            }
            $placeholders = implode(', ', array_fill(0, count($insCols), '?'));
            $stmt = $pdo->prepare("INSERT INTO homepage_banners (" . implode(', ', $insCols) . ") VALUES ($placeholders)");
            $values = [];
            foreach ($insCols as $c) $values[] = $row[$c] ?? null;
            $stmt->execute($values);

            $id = (int)$pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM homepage_banners WHERE id = ?");
            $stmt->execute([$id]);
            sendSuccess($stmt->fetch(), 'Banner created successfully');
        } catch (Throwable $e) {
            $msg = 'Failed to create banner. Please ensure the homepage_banners table exists (and run the latest migration if you recently updated banner fields).';
            if (isLocalhostRequest()) {
                $msg .= ' DB: ' . $e->getMessage();
            }
            sendError($msg, 500);
        }
        break;

    case 'PUT':
        requireAdminLogin();
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: [];
            ensureHomepageBannersTableExists($pdo);
            $cols = getHomepageBannerColumns($pdo);

            $id = (int)($data['id'] ?? 0);
            if ($id <= 0) {
                sendError('Banner ID is required');
            }

            $title = trim((string)($data['title'] ?? ''));
            $subtitle = trim((string)($data['subtitle'] ?? ''));
            $imagePath = trim((string)($data['image_path'] ?? ''));
            $imagePathDesktop = trim((string)($data['image_path_desktop'] ?? ''));
            $imagePathMobile = trim((string)($data['image_path_mobile'] ?? ''));
            $useDeviceImages = normalizeBool($data['use_device_images'] ?? 0);
            $linkUrl = trim((string)($data['link_url'] ?? ''));
            $buttonText = trim((string)($data['button_text'] ?? ''));
            $buttonLink = trim((string)($data['button_link'] ?? ''));
            $status = normalizeBannerStatus($data['status'] ?? 'active');
            $displayOrder = normalizeDisplayOrder($data['display_order'] ?? 0);
            $startAt = normalizeDateTimeOrNull($data['start_at'] ?? null);
            $endAt = normalizeDateTimeOrNull($data['end_at'] ?? null);

            if ($title === '') {
                sendError('Title is required');
            }
            if ($imagePath === '') {
                sendError('Image is required');
            }

            // Update dynamically based on existing columns
            $row = [
                'title' => $title,
                'subtitle' => $subtitle,
                'image_path' => $imagePath,
                'image_path_desktop' => $imagePathDesktop !== '' ? $imagePathDesktop : null,
                'image_path_mobile' => $imagePathMobile !== '' ? $imagePathMobile : null,
                'use_device_images' => $useDeviceImages,
                'link_url' => $linkUrl !== '' ? $linkUrl : null,
                'button_text' => $buttonText,
                'button_link' => $buttonLink,
                'status' => $status,
                'display_order' => $displayOrder,
                'start_at' => $startAt,
                'end_at' => $endAt,
            ];
            $setParts = [];
            $values = [];
            $updateCols = ['title','subtitle','image_path','button_text','button_link','status','display_order','image_path_desktop','image_path_mobile','use_device_images','link_url','start_at','end_at'];
            foreach ($updateCols as $c) {
                if (hasCol($cols, $c)) {
                    $setParts[] = "$c = ?";
                    $values[] = $row[$c] ?? null;
                }
            }
            $setParts[] = "updated_at = CURRENT_TIMESTAMP";
            $values[] = $id;
            $stmt = $pdo->prepare("UPDATE homepage_banners SET " . implode(', ', $setParts) . " WHERE id = ?");
            $stmt->execute($values);

            $stmt = $pdo->prepare("SELECT * FROM homepage_banners WHERE id = ?");
            $stmt->execute([$id]);
            sendSuccess($stmt->fetch(), 'Banner updated successfully');
        } catch (Throwable $e) {
            $msg = 'Failed to update banner. Please ensure the homepage_banners table exists (and run the latest migration if you recently updated banner fields).';
            if (isLocalhostRequest()) {
                $msg .= ' DB: ' . $e->getMessage();
            }
            sendError($msg, 500);
        }
        break;

    case 'DELETE':
        requireAdminLogin();
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($id <= 0) {
                sendError('Banner ID is required');
            }

            $stmt = $pdo->prepare("DELETE FROM homepage_banners WHERE id = ?");
            $stmt->execute([$id]);
            sendSuccess([], 'Banner deleted successfully');
        } catch (Throwable $e) {
            sendError('Failed to delete banner. Ensure homepage_banners table exists.', 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}


