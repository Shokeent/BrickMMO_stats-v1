-- Update script for existing BrickMMO Stats database
-- Run this if you already have the database set up and want to add the demo sites

-- Add URL column to assets table if it doesn't exist
ALTER TABLE `assets` ADD COLUMN `url` varchar(2048) NULL AFTER `name`;

-- Clear existing demo data
DELETE FROM `stats` WHERE `asset_id` IN (1, 2, 3);
DELETE FROM `assets` WHERE `id` IN (1, 2, 3);

-- Insert the three demo sites
INSERT INTO assets (id, name, url, description, created_at) VALUES 
(1, 'TechFlow Solutions', 'http://localhost/BrickMMO_stats-v1/demo/site1.html', 'Tech company demo website showcasing innovative technology solutions', NOW()),
(2, 'Creative Studio', 'http://localhost/BrickMMO_stats-v1/demo/site2.html', 'Creative agency demo website with modern design and animation effects', NOW()),
(3, 'ShopNow Store', 'http://localhost/BrickMMO_stats-v1/demo/site3.html', 'E-commerce demo website featuring premium product showcase and shopping features', NOW());

-- Insert sample tracking data for demonstration
INSERT INTO `stats` (`asset_id`, `url`, `ip_address`, `browser`, `os`, `user_agent`, `referrer`, `viewed_at`) 
VALUES 
(1, 'http://localhost/BrickMMO_stats-v1/demo/site1.html', '127.0.0.1', 'Chrome', 'Windows 10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'https://google.com', NOW()),
(2, 'http://localhost/BrickMMO_stats-v1/demo/site2.html', '127.0.0.1', 'Firefox', 'macOS', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:91.0)', 'https://github.com', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(3, 'http://localhost/BrickMMO_stats-v1/demo/site3.html', '192.168.1.100', 'Safari', 'iOS', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X)', 'https://twitter.com', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'http://localhost/BrickMMO_stats-v1/demo/site1.html', '192.168.1.50', 'Chrome', 'Android', 'Mozilla/5.0 (Linux; Android 11)', 'https://facebook.com', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(2, 'http://localhost/BrickMMO_stats-v1/demo/site2.html', '10.0.0.15', 'Edge', 'Windows 11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'https://linkedin.com', DATE_SUB(NOW(), INTERVAL 4 HOUR));