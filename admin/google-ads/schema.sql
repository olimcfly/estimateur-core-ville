-- Migration: google_ads_campaigns
-- Engine: InnoDB | Charset: utf8mb4 | MySQL 8+
-- Safe to re-run (IF NOT EXISTS)

CREATE TABLE IF NOT EXISTS `google_ads_campaigns` (
  `id`                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
  `website_id`          INT UNSIGNED        NOT NULL,
  `created_at`          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `advisor_name`        VARCHAR(255)        NOT NULL DEFAULT '',
  `ville`               VARCHAR(255)        NOT NULL DEFAULT '',
  `domain`              VARCHAR(255)        NOT NULL DEFAULT '',
  `budget_daily`        DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
  `campaign_type`       ENUM('estimation','vendre','avis') NOT NULL DEFAULT 'estimation',
  `campaign_label`      VARCHAR(255)        NOT NULL DEFAULT '',
  `campaign_slug`       VARCHAR(255)        NOT NULL DEFAULT '',
  `ads_json`            JSON                NULL,
  `url_finale`          VARCHAR(2048)       NOT NULL DEFAULT '',
  `tracking_template`   VARCHAR(2048)       NOT NULL DEFAULT '',
  `keywords_json`       JSON                NULL,
  `landing_html`        LONGTEXT            NULL,
  `landing_path`        VARCHAR(512)        NOT NULL DEFAULT '',
  `landing_exported_at` DATETIME            NULL DEFAULT NULL,
  `status`              ENUM('draft','active','paused','archived') NOT NULL DEFAULT 'draft',
  `quality_score`       TINYINT UNSIGNED    NULL DEFAULT NULL COMMENT '1-10',

  PRIMARY KEY (`id`),
  INDEX `idx_website_id`    (`website_id`),
  INDEX `idx_campaign_type` (`campaign_type`),
  INDEX `idx_status`        (`status`),
  INDEX `idx_ville`         (`ville`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
