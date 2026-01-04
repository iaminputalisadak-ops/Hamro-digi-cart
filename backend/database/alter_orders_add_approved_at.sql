-- Add approved_at column to orders table
-- Use this if your database was created before approved_at existed.

ALTER TABLE orders
  ADD COLUMN approved_at DATETIME NULL AFTER notes;

ALTER TABLE orders
  ADD INDEX idx_approved_at (approved_at);


