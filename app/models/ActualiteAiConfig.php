<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;

final class ActualiteAiConfig
{
    private const DEFAULTS = [
        'zone_priority' => 'local_first',
        'exclude_agencies' => '1',
        'exclude_keywords' => 'annonce,vente appartement,location meublée,agence immobilière,programme neuf promoteur',
        'require_keywords' => '',
        'max_article_age_days' => '7',
        'min_relevance_score' => '6',
        'article_tone' => 'journalistique',
        'article_length' => '800-1200',
        'seo_focus' => '',
        'local_angle' => '',
        'cta_style' => 'soft',
        'source_citation' => '1',
        'auto_publish' => '0',
        'generation_model' => 'anthropic',
    ];

    public function getAll(): array
    {
        try {
            $sql = 'SELECT config_key, config_value FROM actualite_ai_config WHERE website_id = :wid';
            $stmt = Database::connection()->prepare($sql);
            $stmt->execute([':wid' => $this->websiteId()]);
            $rows = $stmt->fetchAll();

            $config = self::DEFAULTS;
            foreach ($rows as $row) {
                $config[$row['config_key']] = $row['config_value'];
            }

            return $config;
        } catch (\Throwable) {
            return self::DEFAULTS;
        }
    }

    public function get(string $key, ?string $default = null): string
    {
        $all = $this->getAll();
        return $all[$key] ?? $default ?? (self::DEFAULTS[$key] ?? '');
    }

    public function set(string $key, string $value): void
    {
        $sql = 'INSERT INTO actualite_ai_config (website_id, config_key, config_value, updated_at)
                VALUES (:wid, :key, :value, NOW())
                ON DUPLICATE KEY UPDATE config_value = :value2, updated_at = NOW()';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':wid' => $this->websiteId(),
            ':key' => $key,
            ':value' => $value,
            ':value2' => $value,
        ]);
    }

    public function saveAll(array $config): void
    {
        foreach ($config as $key => $value) {
            if (array_key_exists($key, self::DEFAULTS)) {
                $this->set($key, (string) $value);
            }
        }
    }

    public function getExcludeKeywords(): array
    {
        $raw = $this->get('exclude_keywords');
        return array_filter(array_map('trim', explode(',', $raw)));
    }

    public function getRequireKeywords(): array
    {
        $raw = $this->get('require_keywords');
        return array_filter(array_map('trim', explode(',', $raw)));
    }

    public function getSeoFocusKeywords(): array
    {
        $raw = $this->get('seo_focus');
        return array_filter(array_map('trim', explode(',', $raw)));
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
