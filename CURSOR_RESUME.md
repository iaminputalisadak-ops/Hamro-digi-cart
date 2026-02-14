# Resume Notes (Hamro Digi Cart)

This file is a quick “where we left off” note so you can continue later without re-explaining.

## What’s implemented
- **Homepage banner**: homepage shows **only banners created in Admin → Homepage Banners** (no fallback banners).
- **Product cards (mobile)**: stronger border + more rounded corners for card/image/button.
- **Admin offers**: removed **Delete All** button and disabled the API bulk delete path.
- **Dashboard**: avoids fatal error if `approved_at` is missing; auto-adds column when possible.
- **Social links**: Admin can change **icon + name + URL**, frontend footer updates from settings.
- **Orders**
  - Approve/Reject deep-links supported in admin.
  - **Hard delete** order removes DB row + deletes uploaded payment screenshot (only inside `backend/uploads/`).
- **Emails**
  - On purchase: email admin with order details + approve/reject links.
  - On approval: email customer with download link + receipt summary + support contact.
  - SMTP password is stored **encrypted** (not returned to browser).

## Important configuration (for production)
- Set **`HAMRODIGICART_APP_KEY`** (recommended) or edit `backend/config/secret.php` locally.
- Configure SMTP in **Admin → Settings → SMTP Email Settings**.
- Set receiver address in **Admin → Website Settings → Admin Order Notification Email**.

## Quick URLs
- Frontend: `http://localhost:3000`
- Admin: `http://localhost:8000/admin/login.php`
- Orders: `http://localhost:8000/admin/orders.php`
- Website Settings: `http://localhost:8000/admin/website-settings.php`
- SMTP Settings: `http://localhost:8000/admin/settings.php`



