-- Alter homepage_banners table to add scheduling, device-specific images, and link_url
-- Run this if you already created the homepage_banners table before these columns existed.

-- NOTE: MySQL does not support "ADD COLUMN IF NOT EXISTS" in many versions.
-- If any ALTER fails because the column already exists, remove that line and re-run.

ALTER TABLE homepage_banners
  ADD COLUMN image_path_desktop TEXT NULL AFTER image_path;

ALTER TABLE homepage_banners
  ADD COLUMN image_path_mobile TEXT NULL AFTER image_path_desktop;

ALTER TABLE homepage_banners
  ADD COLUMN use_device_images TINYINT(1) NOT NULL DEFAULT 0 AFTER image_path_mobile;

ALTER TABLE homepage_banners
  ADD COLUMN link_url TEXT NULL AFTER use_device_images;

ALTER TABLE homepage_banners
  ADD COLUMN start_at DATETIME NULL AFTER display_order;

ALTER TABLE homepage_banners
  ADD COLUMN end_at DATETIME NULL AFTER start_at;

ALTER TABLE homepage_banners
  ADD INDEX idx_schedule (start_at, end_at);


