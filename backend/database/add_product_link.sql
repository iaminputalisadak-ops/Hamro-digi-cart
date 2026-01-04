-- Add product_link column to orders table
ALTER TABLE orders ADD COLUMN IF NOT EXISTS product_link TEXT AFTER payment_screenshot;






