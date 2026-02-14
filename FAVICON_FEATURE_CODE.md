# Favicon Upload Feature - Code Summary

## Files Modified

### 1. Backend Admin Page: `backend/admin/website-settings.php`

#### Added Favicon Field to Settings Array (Line ~13)
```php
$settings = [
    'website_logo' => $_POST['website_logo'] ?? '',
    'website_favicon' => $_POST['website_favicon'] ?? '',  // NEW
    'logo_text_line1' => $_POST['logo_text_line1'] ?? 'Hamro Digi',
    // ... rest of settings
];
```

#### Updated SQL Query (Line ~54)
```php
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings 
    WHERE setting_key LIKE 'website_%' 
    OR setting_key LIKE 'logo_%' 
    OR setting_key LIKE '%_url' 
    OR setting_key LIKE 'contact_%' 
    OR setting_key LIKE 'footer_%' 
    OR setting_key LIKE 'banner%' 
    OR setting_key LIKE 'popup%' 
    OR setting_key = 'website_favicon'");  // NEW
```

#### Added Favicon Form Field (Lines ~142-154)
```php
<div class="form-group">
    <label>Favicon</label>
    <div style="display: flex; gap: 10px; align-items: flex-start;">
        <div style="flex: 1;">
            <input type="file" id="faviconFile" accept="image/x-icon,image/png,image/jpeg,image/svg+xml" style="margin-bottom: 10px;">
            <input type="url" id="website_favicon" name="website_favicon" placeholder="Or enter favicon image URL" value="<?php echo htmlspecialchars($settings['website_favicon'] ?? ''); ?>">
        </div>
        <div id="faviconPreview" style="width: 64px; height: 64px; border: 2px dashed #ddd; border-radius: 5px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9; flex-shrink: 0;">
            <img id="faviconPreviewImg" src="" alt="Favicon Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        </div>
    </div>
    <small style="color: #666;">Upload a favicon (ICO, PNG, JPEG, or SVG) or enter an image URL. Recommended size: 32x32px or 64x64px</small>
</div>
```

#### Added JavaScript Handling for Favicon (Lines ~384-436)
```javascript
// Favicon image handling
const faviconFile = document.getElementById('faviconFile');
const faviconURL = document.getElementById('website_favicon');
const faviconPreview = document.getElementById('faviconPreview');
const faviconPreviewImg = document.getElementById('faviconPreviewImg');

// Load existing favicon preview
if (faviconURL.value) {
    faviconPreviewImg.src = faviconURL.value;
    faviconPreview.style.display = 'flex';
}

faviconFile.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        
        // Check if it's a valid image type
        const validTypes = ['image/x-icon', 'image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid favicon file (ICO, PNG, JPEG, or SVG)');
            this.value = '';
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            alert('Favicon size should be less than 5MB');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = (e) => {
            faviconPreviewImg.src = e.target.result;
            faviconPreview.style.display = 'flex';
            faviconURL.value = '';
        };
        reader.onerror = () => {
            alert('Error reading file');
            this.value = '';
        };
        reader.readAsDataURL(file);
    }
});

faviconURL.addEventListener('input', function() {
    if (this.value) {
        faviconPreviewImg.src = this.value;
        faviconPreview.style.display = 'flex';
        faviconFile.value = '';
    } else {
        faviconPreview.style.display = 'none';
        faviconPreviewImg.src = '';
    }
});
```

#### Added Favicon Upload in Form Submission (Lines ~754-782)
```javascript
// Upload favicon file if selected
let faviconUrl = faviconURL.value.trim();
if (faviconFile.files && faviconFile.files.length > 0) {
    const file = faviconFile.files[0];
    const formData = new FormData();
    formData.append('file', file);
    
    try {
        const uploadResponse = await fetch('../api/upload.php', {
            method: 'POST',
            body: formData
        });
        
        if (!uploadResponse.ok) {
            throw new Error('Upload request failed');
        }
        
        const uploadData = await uploadResponse.json();
        
        if (uploadData.success) {
            if (uploadData.data && uploadData.data.url) {
                faviconUrl = uploadData.data.url;
            } else if (uploadData.url) {
                faviconUrl = uploadData.url;
            }
            faviconURL.value = faviconUrl;
        } else {
            throw new Error(uploadData.error || 'Upload failed');
        }
    } catch (error) {
        alert('Favicon upload failed: ' + error.message);
        submitButton.disabled = false;
        submitButton.textContent = originalText;
        return;
    }
}
```

---

### 2. Backend API: `backend/api/website-settings.php`

#### Updated SQL Query (Line ~22)
```php
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings 
    WHERE setting_key LIKE 'website_%' 
    OR setting_key LIKE 'logo_%' 
    OR setting_key LIKE '%_url' 
    OR setting_key LIKE 'contact_%' 
    OR setting_key LIKE 'footer_%'
    OR setting_key LIKE 'product_card_%'
    OR setting_key LIKE 'product_details_%'
    OR setting_key LIKE 'banner%'
    OR setting_key LIKE 'popup%'
    OR setting_key = 'website_favicon'");  // NEW
```

#### Added to Default Settings (Line ~41)
```php
$defaults = [
    'website_logo' => '',
    'website_favicon' => '',  // NEW
    'logo_text_line1' => 'Hamro Digi',
    // ... rest of defaults
];
```

---

### 3. Frontend Component: `src/components/Layout.js`

#### Added Favicon Update Logic
```javascript
import React, { useEffect } from 'react';
import { Outlet } from 'react-router-dom';
import Header from './Header';
import Footer from './Footer';
import { useWebsiteSettings } from '../hooks/useWebsiteSettings';

const Layout = () => {
  const { settings } = useWebsiteSettings();

  useEffect(() => {
    // Update favicon if set in website settings
    if (settings?.website_favicon) {
      // Remove existing favicon links
      const existingFavicons = document.querySelectorAll('link[rel*="icon"]');
      existingFavicons.forEach(link => link.remove());
      
      // Add new favicon
      const link = document.createElement('link');
      link.rel = 'icon';
      link.href = settings.website_favicon;
      document.head.appendChild(link);
      
      // Also update apple-touch-icon if it's an image
      const appleTouchIcon = document.querySelector('link[rel="apple-touch-icon"]');
      if (appleTouchIcon && settings.website_favicon) {
        appleTouchIcon.href = settings.website_favicon;
      }
    }
  }, [settings?.website_favicon]);

  return (
    <>
      <Header />
      <Outlet />
      <Footer />
    </>
  );
};

export default Layout;
```

---

## How to Run the Application

### Quick Start (Windows)
Double-click `START_APPLICATION.bat` in the project root.

### Manual Start

**Terminal 1 - Backend Server:**
```bash
cd backend
php -S localhost:8000 -c php.ini -t . router.php
```

**Terminal 2 - Frontend Server:**
```bash
npm start
```

### Access Points
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api
- **Admin Panel**: http://localhost:8000/admin/login.php
  - Username: `hamrodigicart1`
  - Password: `admin123`
- **Website Settings (Favicon)**: http://localhost:8000/admin/website-settings.php

---

## Testing the Favicon Feature

1. Login to admin panel: http://localhost:8000/admin/login.php
2. Navigate to: Website Settings
3. Scroll to "Website Information" section
4. Find the "Favicon" field
5. Either upload a file or enter a URL
6. Click "Save All Settings"
7. Refresh the frontend to see the new favicon

---

## Supported Formats
- ICO (`.ico`)
- PNG (`.png`)
- JPEG/JPG (`.jpeg`, `.jpg`)
- SVG (`.svg`)

## File Size Limit
Maximum 5MB per file










