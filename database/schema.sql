-- Import into an already-created, empty database.
-- No CREATE DATABASE / USE: works with provider-prefixed database names too.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS dishes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(160) NOT NULL,
    category VARCHAR(32) NOT NULL,
    price DECIMAL(12, 0) UNSIGNED NOT NULL,
    description VARCHAR(1000) NOT NULL DEFAULT '',
    is_available TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_dishes_category (category),
    CONSTRAINT chk_dishes_availability CHECK (is_available IN (0, 1)),
    CONSTRAINT chk_dishes_price CHECK (price <= 999999999)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
