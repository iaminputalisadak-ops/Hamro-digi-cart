-- Clear All Offers
-- This will delete all existing offers from the database
-- Use this to start fresh and add offers through the admin panel

USE digicart;

-- Delete all offers
DELETE FROM offers;

-- Reset auto increment (optional - only if you want to start IDs from 1)
ALTER TABLE offers AUTO_INCREMENT = 1;


