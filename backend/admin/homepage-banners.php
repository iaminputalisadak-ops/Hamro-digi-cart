<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$apiUrl = '../api/homepage-banners.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Banner Settings - Admin Panel</title>
    <?php include 'includes/favicon.php'; ?>
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        /* Module-scoped polish for Homepage Banner Settings */
        .hb-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .hb-page-header h1 { margin: 0; }
        .hb-page-subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-top: 6px;
            line-height: 1.35;
        }
        .hb-grid {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 18px;
            align-items: start;
        }
        .hb-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .hb-card-header {
            padding: 18px 18px 10px;
            border-bottom: 1px solid #eef2f7;
        }
        .hb-card-header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hb-card-body {
            padding: 16px 18px 18px;
        }
        .hb-help {
            color: #6b7280;
            font-size: 13px;
            margin-top: 8px;
            line-height: 1.4;
        }
        .hb-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .hb-form-grid .form-group { margin: 0; }
        .hb-kv {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid #eef2f7;
            border-radius: 10px;
            background: #f9fafb;
        }
        .hb-preview-shell {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            background: #0b1220;
        }
        .hb-preview-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .hb-toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }
        .hb-toolbar-left {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .hb-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            background: #fff;
            font-weight: 700;
            font-size: 12px;
            color: #374151;
        }
        .hb-table-wrap { overflow-x: auto; }
        .hb-table { width: 100%; border-collapse: collapse; }
        .hb-table th, .hb-table td { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        .hb-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
        }
        .hb-row-title { font-weight: 800; color: #111827; }
        .hb-subtle { color: #6b7280; font-size: 12px; }
        .hb-status-toggle {
            border: 1px solid #e5e7eb;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 800;
            cursor: pointer;
            background: #6b7280;
            color: #fff;
        }
        .hb-status-toggle.is-active { background: #16a34a; }
        .hb-order-controls { display: flex; align-items: center; gap: 8px; }
        .hb-order-controls input {
            width: 80px;
            padding: 7px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }
        .hb-icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            cursor: pointer;
            font-weight: 900;
            color: #111827;
        }
        .hb-icon-btn:hover { background: #f3f4f6; }

        @media (max-width: 1100px) {
            .hb-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="hb-page-header">
                    <div>
                        <h1>Homepage Banner Settings</h1>
                        <div class="hb-page-subtitle">Manage homepage banners and how they display on your website.</div>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="openBannerModal()">+ Add New Banner</button>
                    </div>
                </div>

                <div class="hb-grid">
                    <!-- Left column: settings + preview -->
                    <div style="display:flex; flex-direction:column; gap:18px;">
                        <div class="hb-card">
                            <div class="hb-card-header">
                                <h2>Display Settings</h2>
                                <div class="hb-help">Controls how banners behave on the homepage.</div>
                            </div>
                            <div class="hb-card-body">
                                <div id="displaySettingsMsg" style="display:none; padding: 10px 12px; border-radius: 10px; margin-bottom: 12px;"></div>
                                <form id="displaySettingsForm">
                                    <div class="hb-form-grid">
                                        <div class="hb-kv">
                                            <label style="display:flex; align-items:center; gap:10px; font-weight:800;">
                                                <input type="checkbox" id="bannerAutoplay" name="homepage_banner_autoplay">
                                                Autoplay
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>Slide Duration (sec)</label>
                                            <input type="number" id="bannerDuration" name="homepage_banner_duration" min="1" step="1" value="5">
                                        </div>
                                        <div class="form-group">
                                            <label>Banner Height (px)</label>
                                            <input type="number" id="bannerHeight" name="homepage_banner_height" min="120" step="10" value="260">
                                        </div>
                                        <div class="form-group">
                                            <label>Animation</label>
                                            <select id="bannerAnimation" name="homepage_banner_animation">
                                                <option value="slide">Slide</option>
                                                <option value="fade">Fade</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="action-buttons" style="margin-top: 14px;">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="hb-card">
                            <div class="hb-card-header">
                                <h2>Live Preview</h2>
                                <div class="hb-help">Preview banners for desktop and mobile.</div>
                            </div>
                            <div class="hb-card-body hb-preview-grid">
                                <div>
                                    <div class="hb-subtle" style="font-weight:800; margin-bottom:8px;">Desktop</div>
                                    <div id="previewDesktop" class="hb-preview-shell"></div>
                                </div>
                                <div>
                                    <div class="hb-subtle" style="font-weight:800; margin-bottom:8px;">Mobile</div>
                                    <div id="previewMobile" class="hb-preview-shell" style="max-width: 375px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right column: manage banners -->
                    <div class="hb-card">
                        <div class="hb-card-header">
                            <h2>Manage Banners</h2>
                            <div class="hb-toolbar">
                                <div class="hb-toolbar-left">
                                    <label class="hb-chip">
                                        <input type="checkbox" id="selectAllBanners" name="select_all_banners">
                                        Select All
                                    </label>
                                    <select id="bulkAction" name="bulk_action" style="min-width: 220px;">
                                        <option value="">Bulk action…</option>
                                        <option value="activate">Activate</option>
                                        <option value="deactivate">Deactivate</option>
                                        <option value="delete">Delete</option>
                                    </select>
                                    <button class="btn btn-primary" type="button" onclick="applyBulkAction()">Apply</button>
                                </div>
                                <span id="bulkCount" class="hb-subtle" style="font-weight:800;"></span>
                            </div>
                        </div>
                        <div class="hb-table-wrap">
                            <table class="hb-table">
                                <thead>
                                    <tr>
                                        <th style="width: 46px;">
                                            <input type="checkbox" id="tableSelectAll" name="table_select_all" aria-label="Select all banners">
                                        </th>
                                        <th style="width: 96px;">Image</th>
                                        <th>Title</th>
                                        <th>Subtitle</th>
                                        <th style="width: 150px;">Status</th>
                                        <th style="width: 180px;">Order</th>
                                        <th style="min-width: 160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="banners-table">
                                    <tr><td colspan="7">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Banner Modal -->
    <div id="bannerModal" class="modal-overlay" style="display: none;">
        <div class="modal" style="max-width: 720px;">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Banner</h2>
                <button class="modal-close" onclick="closeBannerModal()">&times;</button>
            </div>
            <form id="bannerForm">
                <input type="hidden" id="bannerId" name="id">
                
                <div class="form-group">
                    <label>Banner Title *</label>
                    <input type="text" id="bannerTitle" name="title" placeholder="Enter banner title" required>
                </div>
                
                <div class="form-group">
                    <label>Banner Subtitle</label>
                    <input type="text" id="bannerSubtitle" name="subtitle" placeholder="Enter banner subtitle">
                </div>

                <div class="form-group">
                    <label>Banner Link/URL (Optional)</label>
                    <input type="text" id="bannerLinkUrl" name="link_url" placeholder="e.g., /reels-bundle or https://example.com">
                    <small style="color:#666;">If set, clicking the banner can open this link (frontend behavior).</small>
                </div>
                
                <div class="form-group">
                    <label>Banner Image *</label>
                    <div style="display: flex; gap: 10px; align-items: flex-start;">
                        <div style="flex: 1;">
                            <input type="file" id="bannerImageFile" name="image_file" accept="image/*" style="margin-bottom: 10px;">
                            <input type="url" id="bannerImageUrl" name="image_path" placeholder="Or enter image URL">
                            <small style="color: #666;">Image required (upload OR URL). PNG/JPG/WebP recommended.</small>
                        </div>
                        <div id="bannerImagePreview" style="width: 120px; height: 120px; border: 2px dashed #ddd; border-radius: 5px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
                            <img id="bannerImagePreviewImg" src="" alt="Banner Preview" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" id="useDeviceImages" name="use_device_images">
                        Mobile/Desktop specific images (optional)
                    </label>
                    <small style="color:#666;">Enable to provide separate images for Desktop and Mobile. If disabled, the main image is used everywhere.</small>
                </div>

                <div id="deviceImagesSection" style="display:none; margin-top: 6px;">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label>Desktop Image (Optional)</label>
                            <input type="file" id="bannerImageFileDesktop" name="image_file_desktop" accept="image/*" style="margin-bottom: 10px;">
                            <input type="url" id="bannerImageUrlDesktop" name="image_path_desktop" placeholder="Or enter desktop image URL">
                            <div id="bannerImagePreviewDesktop" style="width: 100%; height: 140px; border: 2px dashed #ddd; border-radius: 8px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9; margin-top:10px;">
                                <img id="bannerImagePreviewImgDesktop" src="" alt="Desktop Banner Preview" style="width:100%; height:100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mobile Image (Optional)</label>
                            <input type="file" id="bannerImageFileMobile" name="image_file_mobile" accept="image/*" style="margin-bottom: 10px;">
                            <input type="url" id="bannerImageUrlMobile" name="image_path_mobile" placeholder="Or enter mobile image URL">
                            <div id="bannerImagePreviewMobile" style="width: 100%; height: 140px; border: 2px dashed #ddd; border-radius: 8px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9; margin-top:10px;">
                                <img id="bannerImagePreviewImgMobile" src="" alt="Mobile Banner Preview" style="width:100%; height:100%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label>Button Text (Optional)</label>
                        <input type="text" id="bannerButtonText" name="button_text" placeholder="e.g., Shop Now">
                    </div>
                    <div class="form-group">
                        <label>Button Link (Optional)</label>
                        <input type="text" id="bannerButtonLink" name="button_link" placeholder="e.g., /reels-bundle">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label>Status</label>
                        <select id="bannerStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" id="bannerOrder" name="display_order" value="0" min="0" step="1">
                        <small style="color: #666;">Lower number shows first.</small>
                    </div>
                </div>


                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-danger" onclick="closeBannerModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const apiUrl = '<?php echo $apiUrl; ?>';
        const uploadUrl = '../api/upload.php';
        const settingsApiUrl = '../api/settings.php';
        let bannersCache = [];
        let bannerDisplaySettings = {
            autoplay: false,
            duration: 5,
            height: 260,
            animation: 'slide'
        };
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function statusBadge(status) {
            const s = (status || '').toLowerCase();
            const isActive = s === 'active';
            const bg = isActive ? '#d4edda' : '#f8d7da';
            const fg = isActive ? '#155724' : '#721c24';
            const label = isActive ? 'Active' : 'Inactive';
            return `<span style="display:inline-block; padding:4px 10px; border-radius:999px; background:${bg}; color:${fg}; font-weight:600; font-size:12px;">${label}</span>`;
        }
        
        function loadBanners() {
            fetch(apiUrl + '?all=1')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayBanners(data.data || []);
                        updateBulkCount();
                        renderPreviews();
                    } else {
                        document.getElementById('banners-table').innerHTML = `<tr><td colspan="7">Error: ${escapeHtml(data.error || 'Unknown error')}</td></tr>`;
                    }
                })
                .catch(err => {
                    document.getElementById('banners-table').innerHTML = `<tr><td colspan="7">Error: ${escapeHtml(err.message || 'Failed to load')}</td></tr>`;
                });
        }
        
        function displayBanners(banners) {
            bannersCache = Array.isArray(banners) ? banners : [];
            const tbody = document.getElementById('banners-table');
            if (!banners.length) {
                tbody.innerHTML = '<tr><td colspan="7">No banners found. Click "+ Add New Banner" to create one.</td></tr>';
                return;
            }
            
            tbody.innerHTML = banners.map(b => {
                const img = b.image_path ? `<img src="${escapeHtml(b.image_path)}" alt="Banner" style="width:72px;height:48px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;">` : '-';
                const safeId = Number(b.id);
                const isActive = String(b.status || '').toLowerCase() === 'active';
                const orderVal = Number.isFinite(Number(b.display_order)) ? Number(b.display_order) : 0;
                const linkPreview = b.link_url ? `<a href="${escapeHtml(b.link_url)}" target="_blank" rel="noreferrer" style="color:#2563eb; text-decoration:underline; font-weight:800;">Open</a>` : '';
                return `
                    <tr>
                        <td>
                          <input type="checkbox" class="banner-select" data-id="${safeId}" aria-label="Select banner ${safeId}">
                        </td>
                        <td>${img}</td>
                        <td>
                          <div class="hb-row-title">${escapeHtml(b.title || '')}</div>
                          ${linkPreview ? `<div class="hb-subtle" style="margin-top:6px;">${linkPreview}</div>` : ``}
                        </td>
                        <td>${escapeHtml(b.subtitle || '')}</td>
                        <td>
                          <button
                            class="hb-status-toggle ${isActive ? 'is-active' : ''}"
                            onclick="toggleBannerStatus(${safeId})"
                            title="Toggle Active/Inactive"
                            type="button"
                          >
                            ${isActive ? 'Active' : 'Inactive'}
                          </button>
                        </td>
                        <td>
                          <div class="hb-order-controls">
                            <button class="hb-icon-btn" type="button" onclick="nudgeBannerOrder(${safeId}, -1)" title="Move up">▲</button>
                            <input
                              type="number"
                              min="0"
                              step="1"
                              value="${escapeHtml(String(orderVal))}"
                              onchange="saveBannerOrder(${safeId}, this.value)"
                              aria-label="Display order"
                            />
                            <button class="hb-icon-btn" type="button" onclick="nudgeBannerOrder(${safeId}, 1)" title="Move down">▼</button>
                          </div>
                          <div class="hb-subtle" style="margin-top:6px; font-weight:800;">Lower shows first</div>
                        </td>
                        <td>
                            <button class="btn btn-edit btn-small" onclick="editBanner(${b.id})">Edit</button>
                            <button class="btn btn-danger btn-small" onclick="deleteBanner(${b.id})">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');

            // Hook selection change
            tbody.querySelectorAll('.banner-select').forEach(cb => {
                cb.addEventListener('change', updateBulkCount);
            });
        }

        function getBannerFromCache(id) {
            const bannerId = Number(id);
            return bannersCache.find(b => Number(b.id) === bannerId) || null;
        }

        async function updateBannerPartial(id, fields) {
            const current = getBannerFromCache(id);
            if (!current) {
                // Fallback: fetch latest banner if cache missing
                const res = await fetch(`${apiUrl}?id=${id}`);
                const data = await res.json();
                if (!data.success) throw new Error(data.error || 'Failed to load banner');
                bannersCache.push(data.data);
                return updateBannerPartial(id, fields);
            }

            const payload = {
                id: Number(current.id),
                title: String(current.title || '').trim(),
                subtitle: String(current.subtitle || '').trim(),
                image_path: String(current.image_path || '').trim(),
                image_path_desktop: String(current.image_path_desktop || '').trim(),
                image_path_mobile: String(current.image_path_mobile || '').trim(),
                use_device_images: Number(current.use_device_images || 0) ? 1 : 0,
                link_url: String(current.link_url || '').trim(),
                button_text: String(current.button_text || '').trim(),
                button_link: String(current.button_link || '').trim(),
                status: String(current.status || 'inactive').toLowerCase() === 'active' ? 'active' : 'inactive',
                display_order: Number.isFinite(Number(current.display_order)) ? Number(current.display_order) : 0,
                start_at: current.start_at || null,
                end_at: current.end_at || null,
                ...fields
            };

            if (!payload.title) throw new Error('Banner title missing');
            if (!payload.image_path) throw new Error('Banner image missing');

            const res = await fetch(apiUrl, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.error || 'Update failed');
            return data.data;
        }

        async function toggleBannerStatus(id) {
            try {
                const b = getBannerFromCache(id);
                const currentStatus = String(b?.status || 'inactive').toLowerCase();
                const nextStatus = currentStatus === 'active' ? 'inactive' : 'active';
                await updateBannerPartial(id, { status: nextStatus });
                loadBanners();
                showNotification('Banner status updated!', 'success');
            } catch (err) {
                alert('Error: ' + (err.message || 'Failed to update status'));
            }
        }

        async function saveBannerOrder(id, value) {
            const newOrder = parseInt(value || '0', 10);
            try {
                await updateBannerPartial(id, { display_order: isNaN(newOrder) ? 0 : newOrder });
                loadBanners();
                showNotification('Banner order saved!', 'success');
            } catch (err) {
                alert('Error: ' + (err.message || 'Failed to save order'));
            }
        }

        async function nudgeBannerOrder(id, delta) {
            const b = getBannerFromCache(id);
            const current = Number.isFinite(Number(b?.display_order)) ? Number(b.display_order) : 0;
            const next = Math.max(0, current + Number(delta || 0));
            return saveBannerOrder(id, String(next));
        }
        
        function openBannerModal(banner = null) {
            document.getElementById('bannerModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = banner ? 'Edit Banner' : 'Add New Banner';
            
            document.getElementById('bannerForm').reset();
            document.getElementById('bannerId').value = '';
            
            const preview = document.getElementById('bannerImagePreview');
            const previewImg = document.getElementById('bannerImagePreviewImg');
            preview.style.display = 'none';
            previewImg.src = '';

            // Reset device images UI
            document.getElementById('deviceImagesSection').style.display = 'none';
            document.getElementById('useDeviceImages').checked = false;
            document.getElementById('bannerImagePreviewDesktop').style.display = 'none';
            document.getElementById('bannerImagePreviewImgDesktop').src = '';
            document.getElementById('bannerImagePreviewMobile').style.display = 'none';
            document.getElementById('bannerImagePreviewImgMobile').src = '';
            
            if (banner) {
                document.getElementById('bannerId').value = banner.id;
                document.getElementById('bannerTitle').value = banner.title || '';
                document.getElementById('bannerSubtitle').value = banner.subtitle || '';
                document.getElementById('bannerLinkUrl').value = banner.link_url || '';
                document.getElementById('bannerImageUrl').value = banner.image_path || '';
                document.getElementById('bannerButtonText').value = banner.button_text || '';
                document.getElementById('bannerButtonLink').value = banner.button_link || '';
                document.getElementById('bannerStatus').value = (banner.status || 'active').toLowerCase() === 'inactive' ? 'inactive' : 'active';
                document.getElementById('bannerOrder').value = banner.display_order ?? 0;


                
                if (banner.image_path) {
                    previewImg.src = banner.image_path;
                    preview.style.display = 'flex';
                }

                // Device images
                const useDevice = Number(banner.use_device_images || 0) === 1;
                document.getElementById('useDeviceImages').checked = useDevice;
                document.getElementById('deviceImagesSection').style.display = useDevice ? 'block' : 'none';
                document.getElementById('bannerImageUrlDesktop').value = banner.image_path_desktop || '';
                document.getElementById('bannerImageUrlMobile').value = banner.image_path_mobile || '';

                if (banner.image_path_desktop) {
                    document.getElementById('bannerImagePreviewImgDesktop').src = banner.image_path_desktop;
                    document.getElementById('bannerImagePreviewDesktop').style.display = 'flex';
                }
                if (banner.image_path_mobile) {
                    document.getElementById('bannerImagePreviewImgMobile').src = banner.image_path_mobile;
                    document.getElementById('bannerImagePreviewMobile').style.display = 'flex';
                }
            }
        }
        
        function closeBannerModal() {
            document.getElementById('bannerModal').style.display = 'none';
        }
        
        function editBanner(id) {
            fetch(`${apiUrl}?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        openBannerModal(data.data);
                    } else {
                        alert('Error: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(err => alert('Error: ' + err.message));
        }
        
        function deleteBanner(id) {
            if (!confirm('Are you sure you want to delete this banner? This action cannot be undone.')) return;
            fetch(`${apiUrl}?id=${id}`, { method: 'DELETE' })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadBanners();
                        showNotification('Banner deleted successfully!', 'success');
                    } else {
                        alert('Error: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(err => alert('Error: ' + err.message));
        }
        
        // Preview for URL input
        const bannerImageUrl = document.getElementById('bannerImageUrl');
        const bannerImageFile = document.getElementById('bannerImageFile');
        const bannerImagePreview = document.getElementById('bannerImagePreview');
        const bannerImagePreviewImg = document.getElementById('bannerImagePreviewImg');
        const useDeviceImagesCheckbox = document.getElementById('useDeviceImages');
        const deviceImagesSection = document.getElementById('deviceImagesSection');
        const bannerImageUrlDesktop = document.getElementById('bannerImageUrlDesktop');
        const bannerImageFileDesktop = document.getElementById('bannerImageFileDesktop');
        const bannerImagePreviewDesktop = document.getElementById('bannerImagePreviewDesktop');
        const bannerImagePreviewImgDesktop = document.getElementById('bannerImagePreviewImgDesktop');
        const bannerImageUrlMobile = document.getElementById('bannerImageUrlMobile');
        const bannerImageFileMobile = document.getElementById('bannerImageFileMobile');
        const bannerImagePreviewMobile = document.getElementById('bannerImagePreviewMobile');
        const bannerImagePreviewImgMobile = document.getElementById('bannerImagePreviewImgMobile');
        
        bannerImageUrl.addEventListener('input', function() {
            const val = this.value.trim();
            if (val) {
                bannerImagePreviewImg.src = val;
                bannerImagePreview.style.display = 'flex';
                bannerImageFile.value = '';
            } else {
                bannerImagePreview.style.display = 'none';
                bannerImagePreviewImg.src = '';
            }
        });
        
        bannerImageFile.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    this.value = '';
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    bannerImagePreviewImg.src = e.target.result;
                    bannerImagePreview.style.display = 'flex';
                    bannerImageUrl.value = '';
                };
                reader.readAsDataURL(file);
            }
        });

        // Toggle device images section
        useDeviceImagesCheckbox.addEventListener('change', function() {
            deviceImagesSection.style.display = this.checked ? 'block' : 'none';
        });

        function wirePreviewUrl(urlInput, fileInput, previewWrap, previewImg) {
            urlInput.addEventListener('input', function() {
                const val = this.value.trim();
                if (val) {
                    previewImg.src = val;
                    previewWrap.style.display = 'flex';
                    if (fileInput) fileInput.value = '';
                } else {
                    previewWrap.style.display = 'none';
                    previewImg.src = '';
                }
            });
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        if (!file.type.startsWith('image/')) {
                            alert('Please select an image file');
                            this.value = '';
                            return;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            alert('Image size should be less than 5MB');
                            this.value = '';
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            previewImg.src = e.target.result;
                            previewWrap.style.display = 'flex';
                            urlInput.value = '';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        wirePreviewUrl(bannerImageUrlDesktop, bannerImageFileDesktop, bannerImagePreviewDesktop, bannerImagePreviewImgDesktop);
        wirePreviewUrl(bannerImageUrlMobile, bannerImageFileMobile, bannerImagePreviewMobile, bannerImagePreviewImgMobile);
        
        document.getElementById('bannerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
            
            try {
                const id = document.getElementById('bannerId').value.trim();
                const title = document.getElementById('bannerTitle').value.trim();
                const subtitle = document.getElementById('bannerSubtitle').value.trim();
                const linkUrl = document.getElementById('bannerLinkUrl').value.trim();
                const buttonText = document.getElementById('bannerButtonText').value.trim();
                const buttonLink = document.getElementById('bannerButtonLink').value.trim();
                const status = document.getElementById('bannerStatus').value;
                const displayOrder = parseInt(document.getElementById('bannerOrder').value || '0', 10);
                const useDeviceImages = useDeviceImagesCheckbox.checked ? 1 : 0;
                const startAt = null;
                const endAt = null;
                
                if (!title) {
                    alert('Title is required');
                    return;
                }
                
                let imagePath = bannerImageUrl.value.trim();
                let imagePathDesktop = bannerImageUrlDesktop.value.trim();
                let imagePathMobile = bannerImageUrlMobile.value.trim();
                
                // Upload file if selected
                if (bannerImageFile.files && bannerImageFile.files.length > 0) {
                    const file = bannerImageFile.files[0];
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    const uploadRes = await fetch(uploadUrl, { method: 'POST', body: formData });
                    const uploadData = await uploadRes.json();
                    if (!uploadData.success) {
                        throw new Error(uploadData.error || 'Image upload failed');
                    }
                    imagePath = (uploadData.data && uploadData.data.url) ? uploadData.data.url : (uploadData.url || '');
                }

                // Upload device-specific files (optional)
                if (useDeviceImages && bannerImageFileDesktop.files && bannerImageFileDesktop.files.length > 0) {
                    const file = bannerImageFileDesktop.files[0];
                    const formData = new FormData();
                    formData.append('file', file);
                    const uploadRes = await fetch(uploadUrl, { method: 'POST', body: formData });
                    const uploadData = await uploadRes.json();
                    if (!uploadData.success) throw new Error(uploadData.error || 'Desktop image upload failed');
                    imagePathDesktop = (uploadData.data && uploadData.data.url) ? uploadData.data.url : (uploadData.url || '');
                }
                if (useDeviceImages && bannerImageFileMobile.files && bannerImageFileMobile.files.length > 0) {
                    const file = bannerImageFileMobile.files[0];
                    const formData = new FormData();
                    formData.append('file', file);
                    const uploadRes = await fetch(uploadUrl, { method: 'POST', body: formData });
                    const uploadData = await uploadRes.json();
                    if (!uploadData.success) throw new Error(uploadData.error || 'Mobile image upload failed');
                    imagePathMobile = (uploadData.data && uploadData.data.url) ? uploadData.data.url : (uploadData.url || '');
                }
                
                if (!imagePath) {
                    alert('Image is required (upload or URL)');
                    return;
                }
                
                const payload = {
                    title,
                    subtitle,
                    image_path: imagePath,
                    image_path_desktop: imagePathDesktop,
                    image_path_mobile: imagePathMobile,
                    use_device_images: useDeviceImages,
                    link_url: linkUrl,
                    button_text: buttonText,
                    button_link: buttonLink,
                    status,
                    display_order: isNaN(displayOrder) ? 0 : displayOrder,
                    start_at: startAt,
                    end_at: endAt
                };
                
                const method = id ? 'PUT' : 'POST';
                if (id) payload.id = parseInt(id, 10);
                
                const res = await fetch(apiUrl, {
                    method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!data.success) {
                    throw new Error(data.error || 'Save failed');
                }
                
                closeBannerModal();
                loadBanners();
                showNotification(id ? 'Banner updated successfully!' : 'Banner created successfully!', 'success');
            } catch (err) {
                console.error(err);
                alert('Error: ' + (err.message || 'Unknown error'));
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });

        // Bulk selection helpers
        function getSelectedBannerIds() {
            return Array.from(document.querySelectorAll('.banner-select:checked')).map(cb => Number(cb.getAttribute('data-id'))).filter(n => Number.isFinite(n) && n > 0);
        }

        function updateBulkCount() {
            const count = getSelectedBannerIds().length;
            const el = document.getElementById('bulkCount');
            if (el) el.textContent = count ? `${count} selected` : '';
        }

        document.getElementById('selectAllBanners').addEventListener('change', function() {
            const checked = this.checked;
            document.querySelectorAll('.banner-select').forEach(cb => { cb.checked = checked; });
            document.getElementById('tableSelectAll').checked = checked;
            updateBulkCount();
        });

        document.getElementById('tableSelectAll').addEventListener('change', function() {
            const checked = this.checked;
            document.querySelectorAll('.banner-select').forEach(cb => { cb.checked = checked; });
            document.getElementById('selectAllBanners').checked = checked;
            updateBulkCount();
        });

        async function applyBulkAction() {
            const action = document.getElementById('bulkAction').value;
            const ids = getSelectedBannerIds();
            if (!action) return alert('Choose a bulk action first');
            if (!ids.length) return alert('Select at least one banner');
            if (action === 'delete' && !confirm('Delete selected banners? This cannot be undone.')) return;
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'bulk', op: action, ids })
            });
            const data = await res.json();
            if (!data.success) return alert('Error: ' + (data.error || 'Bulk action failed'));
            showNotification('Bulk action completed!', 'success');
            loadBanners();
        }

        // (Export/Import removed per request)

        // Display settings (saved in settings table)
        function showDisplaySettingsMsg(text, type) {
            const el = document.getElementById('displaySettingsMsg');
            if (!el) return;
            el.style.display = 'block';
            el.style.background = type === 'success' ? '#d4edda' : '#f8d7da';
            el.style.color = type === 'success' ? '#155724' : '#721c24';
            el.textContent = text;
        }

        async function saveSetting(key, value) {
            const res = await fetch(settingsApiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ key, value })
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.error || data.message || 'Failed saving setting');
        }

        async function loadDisplaySettings() {
            try {
                const res = await fetch(settingsApiUrl);
                const data = await res.json();
                if (!data.success) return;
                bannerDisplaySettings.autoplay = String(data.data.homepage_banner_autoplay || '0') === '1';
                bannerDisplaySettings.duration = parseInt(data.data.homepage_banner_duration || '5', 10) || 5;
                bannerDisplaySettings.height = parseInt(data.data.homepage_banner_height || '260', 10) || 260;
                bannerDisplaySettings.animation = (data.data.homepage_banner_animation || 'slide') === 'fade' ? 'fade' : 'slide';

                document.getElementById('bannerAutoplay').checked = bannerDisplaySettings.autoplay;
                document.getElementById('bannerDuration').value = bannerDisplaySettings.duration;
                document.getElementById('bannerHeight').value = bannerDisplaySettings.height;
                document.getElementById('bannerAnimation').value = bannerDisplaySettings.animation;

                renderPreviews();
            } catch (e) {}
        }

        document.getElementById('displaySettingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                bannerDisplaySettings.autoplay = !!document.getElementById('bannerAutoplay').checked;
                bannerDisplaySettings.duration = parseInt(document.getElementById('bannerDuration').value || '5', 10) || 5;
                bannerDisplaySettings.height = parseInt(document.getElementById('bannerHeight').value || '260', 10) || 260;
                bannerDisplaySettings.animation = document.getElementById('bannerAnimation').value === 'fade' ? 'fade' : 'slide';

                await saveSetting('homepage_banner_autoplay', bannerDisplaySettings.autoplay ? '1' : '0');
                await saveSetting('homepage_banner_duration', String(bannerDisplaySettings.duration));
                await saveSetting('homepage_banner_height', String(bannerDisplaySettings.height));
                await saveSetting('homepage_banner_animation', bannerDisplaySettings.animation);

                showDisplaySettingsMsg('Display settings saved!', 'success');
                renderPreviews();
            } catch (err) {
                showDisplaySettingsMsg('Error: ' + (err.message || err), 'error');
            }
        });

        // Preview renderer (simple 1-column slider)
        let previewIndex = 0;
        let previewTimer = null;

        function getBestImageForPreview(b, isMobile) {
            const useDevice = Number(b.use_device_images || 0) === 1;
            if (useDevice) {
                const candidate = isMobile ? (b.image_path_mobile || '') : (b.image_path_desktop || '');
                if (candidate) return candidate;
            }
            return b.image_path || '';
        }

        function renderPreviewInto(el, isMobile) {
            if (!el) return;
            const list = bannersCache || [];
            const b = list.length ? list[Math.min(previewIndex, list.length - 1)] : null;
            const height = Math.max(120, Number(bannerDisplaySettings.height || 260));
            if (!b) {
                el.innerHTML = `<div style="padding:20px; color:#fff;">No banners to preview.</div>`;
                return;
            }
            const img = escapeHtml(getBestImageForPreview(b, isMobile));
            const title = escapeHtml(b.title || '');
            const subtitle = escapeHtml(b.subtitle || '');
            el.innerHTML = `
              <div style="position:relative; height:${height}px; overflow:hidden;">
                <img src="${img}" alt="${title}" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,0.05) 35%, rgba(0,0,0,0.78) 100%);"></div>
                <div style="position:absolute; left:14px; right:14px; bottom:12px; color:#fff;">
                  <div style="font-weight:900; font-size:${isMobile ? '18px' : '22px'}; line-height:1.15; text-shadow:0 2px 10px rgba(0,0,0,0.45);">${title}</div>
                  ${subtitle ? `<div style="margin-top:6px; font-weight:700; opacity:0.95;">${subtitle}</div>` : ''}
                </div>
              </div>
            `;
        }

        function renderPreviews() {
            const desktop = document.getElementById('previewDesktop');
            const mobile = document.getElementById('previewMobile');
            // Clamp index
            if (!bannersCache || bannersCache.length === 0) previewIndex = 0;
            else previewIndex = Math.max(0, Math.min(previewIndex, bannersCache.length - 1));
            renderPreviewInto(desktop, false);
            renderPreviewInto(mobile, true);

            if (previewTimer) {
                clearInterval(previewTimer);
                previewTimer = null;
            }
            if (bannerDisplaySettings.autoplay && bannersCache && bannersCache.length > 1) {
                previewTimer = setInterval(() => nextPreview(true), Math.max(1000, (bannerDisplaySettings.duration || 5) * 1000));
            }
        }

        function nextPreview(fromTimer = false) {
            if (!bannersCache || bannersCache.length === 0) return;
            previewIndex = (previewIndex + 1) % bannersCache.length;
            renderPreviews();
        }
        
        document.getElementById('bannerModal').addEventListener('click', function(e) {
            if (e.target === this) closeBannerModal();
        });

        // Initial loads
        loadDisplaySettings();
        loadBanners();
    </script>
</body>
</html>


