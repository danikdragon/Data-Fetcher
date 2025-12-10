CREATE DATABASE IF NOT EXISTS `data_fetcher` 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `data_fetcher`;

CREATE TABLE IF NOT EXISTS `api_cache` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `source` VARCHAR(50) NOT NULL,
    `cache_key` VARCHAR(255) NOT NULL,
    `data` TEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_cache_key` (`source`, `cache_key`),
    KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `request_logs` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `source` VARCHAR(50) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `request_params` TEXT DEFAULT NULL,
    `response_status` VARCHAR(20) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_source` (`source`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
