-- Google Ads Campaign Manager — schema migration
-- Module: gads_campaigns

CREATE TABLE IF NOT EXISTS gads_campaigns (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    site_id         INT UNSIGNED NOT NULL DEFAULT 1,
    name            VARCHAR(255) NOT NULL,
    campaign_type   ENUM('search','display','performance_max') NOT NULL DEFAULT 'search',
    status          ENUM('draft','ready','exported','active','paused','archived') NOT NULL DEFAULT 'draft',
    daily_budget    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    target_location VARCHAR(255) NOT NULL DEFAULT 'Bordeaux',
    target_radius_km INT UNSIGNED NOT NULL DEFAULT 30,
    language        VARCHAR(10) NOT NULL DEFAULT 'fr',
    bid_strategy    ENUM('manual_cpc','maximize_clicks','maximize_conversions','target_cpa') NOT NULL DEFAULT 'maximize_clicks',
    target_cpa      DECIMAL(10,2) DEFAULT NULL,
    start_date      DATE DEFAULT NULL,
    end_date        DATE DEFAULT NULL,
    notes           TEXT DEFAULT NULL,
    created_by      INT UNSIGNED DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_gads_status (status),
    INDEX idx_gads_site (site_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gads_ad_groups (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id     INT UNSIGNED NOT NULL,
    name            VARCHAR(255) NOT NULL,
    landing_url     VARCHAR(500) NOT NULL DEFAULT '',
    cpc_bid         DECIMAL(10,2) DEFAULT NULL,
    sort_order      INT UNSIGNED NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_adgroup_campaign (campaign_id),
    CONSTRAINT fk_adgroup_campaign FOREIGN KEY (campaign_id) REFERENCES gads_campaigns(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gads_keywords (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ad_group_id     INT UNSIGNED NOT NULL,
    keyword         VARCHAR(255) NOT NULL,
    match_type      ENUM('broad','phrase','exact') NOT NULL DEFAULT 'phrase',
    is_negative     TINYINT(1) NOT NULL DEFAULT 0,
    cpc_bid         DECIMAL(10,2) DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_kw_adgroup (ad_group_id),
    CONSTRAINT fk_kw_adgroup FOREIGN KEY (ad_group_id) REFERENCES gads_ad_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gads_ads (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ad_group_id     INT UNSIGNED NOT NULL,
    ad_type         ENUM('responsive_search','responsive_display') NOT NULL DEFAULT 'responsive_search',
    headlines       JSON NOT NULL COMMENT 'Array of up to 15 headlines (max 30 chars each)',
    descriptions    JSON NOT NULL COMMENT 'Array of up to 4 descriptions (max 90 chars each)',
    final_url       VARCHAR(500) NOT NULL DEFAULT '',
    path1           VARCHAR(15) NOT NULL DEFAULT '',
    path2           VARCHAR(15) NOT NULL DEFAULT '',
    sitelinks       JSON DEFAULT NULL,
    callouts        JSON DEFAULT NULL,
    ai_generated    TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ad_adgroup (ad_group_id),
    CONSTRAINT fk_ad_adgroup FOREIGN KEY (ad_group_id) REFERENCES gads_ad_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
