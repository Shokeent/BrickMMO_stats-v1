CREATE TABLE IF NOT EXISTS `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(2048) NULL,
  `description` text NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `url` varchar(2048) NULL,
  `ip_address` varchar(45) NULL,
  `browser` varchar(100) NULL,
  `os` varchar(100) NULL,
  `user_agent` text NULL,
  `referrer` varchar(2048) NULL,
  `viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_asset_id` (`asset_id`),
  INDEX `idx_viewed_at` (`viewed_at`),
  INDEX `idx_ip_address` (`ip_address`),
  FOREIGN KEY (`asset_id`) REFERENCES `assets`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_username` (`username`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`username`, `password`, `role`, `created_at`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW())
ON DUPLICATE KEY UPDATE 
`password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
`role` = 'admin';

INSERT INTO assets (id, name, url, description, created_at) VALUES 
(1, 'TechFlow Solutions', 'http://localhost/BrickMMO_stats-v1/demo/site1.html', 'Tech company demo website showcasing innovative technology solutions', NOW()),
(2, 'Creative Studio', 'http://localhost/BrickMMO_stats-v1/demo/site2.html', 'Creative agency demo website with modern design and animation effects', NOW()),
(3, 'ShopNow Store', 'http://localhost/BrickMMO_stats-v1/demo/site3.html', 'E-commerce demo website featuring premium product showcase and shopping features', NOW());

INSERT INTO `stats` (`asset_id`, `url`, `ip_address`, `browser`, `os`, `user_agent`, `referrer`, `viewed_at`) 
VALUES 
(1, 'http://localhost/BrickMMO_stats-v1/demo/site1.html', '127.0.0.1', 'Chrome', 'Windows 10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'https://google.com', NOW()),
(2, 'http://localhost/BrickMMO_stats-v1/demo/site2.html', '127.0.0.1', 'Firefox', 'macOS', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:91.0)', 'https://github.com', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(3, 'http://localhost/BrickMMO_stats-v1/demo/site3.html', '192.168.1.100', 'Safari', 'iOS', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X)', 'https://twitter.com', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'http://localhost/mobile-page', '192.168.1.100', 'Safari', 'iOS', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0)', NULL, DATE_SUB(NOW(), INTERVAL 2 HOUR));