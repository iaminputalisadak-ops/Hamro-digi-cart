import API_BASE_URL from '../config/api';

/**
 * Derive the backend base URL (without trailing "/api") from API_BASE_URL.
 *
 * Examples:
 * - Dev:    "http://localhost:8000/api" -> "http://localhost:8000"
 * - Prod:   "/backend/api"              -> "https://yourdomain.com/backend"
 * - Prod:   "/api"                      -> "https://yourdomain.com"
 */
export function getBackendBaseUrl() {
  try {
    const apiUrl = new URL(API_BASE_URL, window.location.origin);
    const pathname = apiUrl.pathname || '';
    const backendPath = pathname.replace(/\/api\/?$/, '').replace(/\/$/, '');
    return `${apiUrl.origin}${backendPath}`;
  } catch (e) {
    return '';
  }
}

/**
 * Normalize image/file URLs that were stored as "/uploads/..." (works in local dev),
 * so they also work when backend is deployed under "/backend" (common on cPanel).
 *
 * - "/uploads/x.jpg" -> "<backendBase>/uploads/x.jpg"
 * - "https://domain.com/uploads/x.jpg" (same origin) -> "https://domain.com/backend/uploads/x.jpg" (if backendBase is /backend)
 */
export function normalizeUploadsUrl(value) {
  if (typeof value !== 'string') return value;
  const s = value.trim();
  if (!s) return s;
  if (/^(data:|blob:)/i.test(s)) return s;

  const backendBase = getBackendBaseUrl();
  if (!backendBase) return s;

  // Relative uploads path (common in DB/settings)
  if (s.startsWith('/uploads/')) {
    return `${backendBase}${s}`;
  }

  // Absolute URL pointing at /uploads/* on the same origin
  try {
    const u = new URL(s, window.location.origin);
    const backend = new URL(backendBase, window.location.origin);
    const backendPath = (backend.pathname || '').replace(/\/$/, '');
    if (u.origin === backend.origin && u.pathname.startsWith('/uploads/') && backendPath && backendPath !== '/') {
      u.pathname = `${backendPath}${u.pathname}`;
      return u.toString();
    }
  } catch (e) {
    // ignore parse errors
  }

  return s;
}


