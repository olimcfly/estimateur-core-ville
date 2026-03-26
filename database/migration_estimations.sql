CREATE TABLE IF NOT EXISTS estimations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    ville VARCHAR(120) NOT NULL,
    type_bien VARCHAR(80) NOT NULL,
    surface_m2 DECIMAL(8,2) NOT NULL,
    pieces INT UNSIGNED NOT NULL DEFAULT 3,
    per_sqm_low DECIMAL(10,2) NOT NULL,
    per_sqm_mid DECIMAL(10,2) NOT NULL,
    per_sqm_high DECIMAL(10,2) NOT NULL,
    estimated_low DECIMAL(12,2) NOT NULL,
    estimated_mid DECIMAL(12,2) NOT NULL,
    estimated_high DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_estimations_website_id (website_id),
    INDEX idx_estimations_ville (ville),
    INDEX idx_estimations_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
