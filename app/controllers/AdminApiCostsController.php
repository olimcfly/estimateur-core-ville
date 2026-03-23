<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;

/**
 * Dedicated dashboard for monitoring API costs and usage across all providers.
 */
final class AdminApiCostsController
{
    /**
     * Main costs dashboard page.
     */
    public function index(): void
    {
        AuthController::requireAuth();

        $period = $_GET['period'] ?? 'month';
        if (!in_array($period, ['7d', '30d', 'month', '3m', '6m', '12m'], true)) {
            $period = 'month';
        }

        $stats = $this->getStats($period);

        View::renderAdmin('admin/api-costs', [
            'page_title'       => 'Couts & Utilisation API',
            'admin_page_title' => 'Couts & Utilisation API',
            'admin_page'       => 'api-costs',
            'period'           => $period,
            'stats'            => $stats,
        ]);
    }

    /**
     * JSON endpoint for AJAX refresh.
     */
    public function apiStats(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        $period = $_GET['period'] ?? 'month';
        if (!in_array($period, ['7d', '30d', 'month', '3m', '6m', '12m'], true)) {
            $period = 'month';
        }

        echo json_encode($this->getStats($period), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Build all statistics for the given period.
     */
    private function getStats(string $period): array
    {
        $stats = [
            'total_cost'       => 0.0,
            'total_tokens'     => 0,
            'total_calls'      => 0,
            'total_input'      => 0,
            'total_output'     => 0,
            'providers'        => [],
            'daily'            => [],
            'by_feature'       => [],
            'by_model'         => [],
            'table_exists'     => false,
            'period_label'     => $this->periodLabel($period),
        ];

        try {
            $db = Database::getInstance();

            // Check if table exists
            try {
                $db->query("SELECT 1 FROM ai_usage_logs LIMIT 1");
                $stats['table_exists'] = true;
            } catch (\Throwable $e) {
                return $stats;
            }

            $dateCondition = $this->dateCondition($period);

            // ── Global totals ──
            $row = $db->fetchOne(
                "SELECT COUNT(*) as calls,
                        COALESCE(SUM(input_tokens), 0) as input_t,
                        COALESCE(SUM(output_tokens), 0) as output_t,
                        COALESCE(SUM(input_tokens + output_tokens), 0) as tokens,
                        COALESCE(SUM(estimated_cost), 0) as cost
                 FROM ai_usage_logs
                 WHERE {$dateCondition}"
            );

            $stats['total_calls']  = (int) ($row['calls'] ?? 0);
            $stats['total_input']  = (int) ($row['input_t'] ?? 0);
            $stats['total_output'] = (int) ($row['output_t'] ?? 0);
            $stats['total_tokens'] = (int) ($row['tokens'] ?? 0);
            $stats['total_cost']   = (float) ($row['cost'] ?? 0);

            // ── Per-provider breakdown ──
            $rows = $db->fetchAll(
                "SELECT provider,
                        COUNT(*) as calls,
                        SUM(input_tokens) as input_t,
                        SUM(output_tokens) as output_t,
                        SUM(input_tokens + output_tokens) as tokens,
                        SUM(estimated_cost) as cost,
                        MAX(created_at) as last_used
                 FROM ai_usage_logs
                 WHERE {$dateCondition}
                 GROUP BY provider
                 ORDER BY cost DESC"
            );

            foreach ($rows as $r) {
                $stats['providers'][$r['provider']] = [
                    'calls'     => (int) $r['calls'],
                    'input'     => (int) $r['input_t'],
                    'output'    => (int) $r['output_t'],
                    'tokens'    => (int) $r['tokens'],
                    'cost'      => (float) $r['cost'],
                    'last_used' => $r['last_used'],
                ];
            }

            // ── By model ──
            $models = $db->fetchAll(
                "SELECT provider, model,
                        COUNT(*) as calls,
                        SUM(input_tokens) as input_t,
                        SUM(output_tokens) as output_t,
                        SUM(estimated_cost) as cost,
                        MAX(created_at) as last_used
                 FROM ai_usage_logs
                 WHERE {$dateCondition}
                 GROUP BY provider, model
                 ORDER BY cost DESC"
            );
            $stats['by_model'] = $models;

            // ── Daily breakdown ──
            $daily = $db->fetchAll(
                "SELECT DATE(created_at) as day,
                        provider,
                        COUNT(*) as calls,
                        SUM(input_tokens + output_tokens) as tokens,
                        SUM(estimated_cost) as cost
                 FROM ai_usage_logs
                 WHERE {$dateCondition}
                 GROUP BY DATE(created_at), provider
                 ORDER BY day ASC"
            );
            $stats['daily'] = $daily;

            // ── By feature ──
            $features = $db->fetchAll(
                "SELECT feature,
                        COUNT(*) as calls,
                        SUM(input_tokens + output_tokens) as tokens,
                        SUM(estimated_cost) as cost
                 FROM ai_usage_logs
                 WHERE {$dateCondition}
                   AND feature != ''
                 GROUP BY feature
                 ORDER BY cost DESC
                 LIMIT 15"
            );
            $stats['by_feature'] = $features;

        } catch (\Throwable $e) {
            // Return partial stats on error
        }

        return $stats;
    }

    /**
     * SQL date condition for the given period.
     */
    private function dateCondition(string $period): string
    {
        return match ($period) {
            '7d'  => "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            '30d' => "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            'month' => "created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')",
            '3m'  => "created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)",
            '6m'  => "created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)",
            '12m' => "created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)",
            default => "created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')",
        };
    }

    /**
     * Human-readable label for the period.
     */
    private function periodLabel(string $period): string
    {
        return match ($period) {
            '7d'    => '7 derniers jours',
            '30d'   => '30 derniers jours',
            'month' => 'Mois en cours',
            '3m'    => '3 derniers mois',
            '6m'    => '6 derniers mois',
            '12m'   => '12 derniers mois',
            default => 'Mois en cours',
        };
    }
}
