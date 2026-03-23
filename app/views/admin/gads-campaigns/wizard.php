<?php
$isEdit = $campaign !== null;
$cId    = $isEdit ? (int) $campaign['id'] : 0;
?>

<link rel="stylesheet" href="/assets/css/google-ads.css">

<div class="container">
    <a href="/admin/gads-campaigns" class="btn btn-small btn-ghost" style="margin-bottom: 1rem; display: inline-block;">
        &larr; Retour aux campagnes
    </a>

    <h1 style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 700; margin: 0 0 0.5rem;">
        <i class="fas fa-magic" style="color: var(--admin-accent);"></i>
        <?= $isEdit ? 'Modifier la campagne' : 'Nouvelle campagne Google Ads' ?>
    </h1>
    <p style="color: #666; margin-bottom: 2rem;">Configurez votre campagne étape par étape. L'IA peut générer les annonces automatiquement.</p>

    <!-- Progress Steps -->
    <div class="gads-wizard-steps">
        <button class="gads-wizard-step active" data-step="1">
            <span class="gads-step-num">1</span> Campagne
        </button>
        <button class="gads-wizard-step" data-step="2">
            <span class="gads-step-num">2</span> Groupes d'annonces
        </button>
        <button class="gads-wizard-step" data-step="3">
            <span class="gads-step-num">3</span> Mots-clés
        </button>
        <button class="gads-wizard-step" data-step="4">
            <span class="gads-step-num">4</span> Annonces
        </button>
        <button class="gads-wizard-step" data-step="5">
            <span class="gads-step-num">5</span> Récapitulatif
        </button>
    </div>

    <form id="gads-wizard-form" onsubmit="return false;">
        <input type="hidden" id="gads-campaign-id" value="<?= $cId ?>">

        <!-- STEP 1: Campaign Settings -->
        <div class="gads-wizard-panel" id="gads-step-1">
            <div class="card" style="padding: 1.5rem;">
                <h2 style="font-size: 1.15rem; margin: 0 0 1rem;">
                    <i class="fas fa-cog" style="color: var(--admin-primary);"></i> Paramètres de la campagne
                </h2>

                <div class="gads-form-grid">
                    <label class="gads-label">
                        Nom de la campagne <span class="gads-required">*</span>
                        <input type="text" id="gads-name" class="gads-input" required
                            placeholder="Ex: Estimation Bordeaux - Search"
                            value="<?= e($campaign['name'] ?? '') ?>">
                    </label>

                    <label class="gads-label">
                        Type de campagne
                        <select id="gads-type" class="gads-input">
                            <option value="search" <?= ($campaign['campaign_type'] ?? '') === 'search' ? 'selected' : '' ?>>Search (Réseau de recherche)</option>
                            <option value="display" <?= ($campaign['campaign_type'] ?? '') === 'display' ? 'selected' : '' ?>>Display</option>
                            <option value="performance_max" <?= ($campaign['campaign_type'] ?? '') === 'performance_max' ? 'selected' : '' ?>>Performance Max</option>
                        </select>
                    </label>

                    <label class="gads-label">
                        Budget quotidien (€)
                        <input type="number" id="gads-budget" class="gads-input" step="0.01" min="0"
                            placeholder="Ex: 15.00"
                            value="<?= e((string) ($campaign['daily_budget'] ?? '')) ?>">
                    </label>

                    <label class="gads-label">
                        Stratégie d'enchères
                        <select id="gads-bid-strategy" class="gads-input" onchange="GADS.toggleTargetCpa()">
                            <option value="maximize_clicks" <?= ($campaign['bid_strategy'] ?? '') === 'maximize_clicks' ? 'selected' : '' ?>>Maximiser les clics</option>
                            <option value="maximize_conversions" <?= ($campaign['bid_strategy'] ?? '') === 'maximize_conversions' ? 'selected' : '' ?>>Maximiser les conversions</option>
                            <option value="target_cpa" <?= ($campaign['bid_strategy'] ?? '') === 'target_cpa' ? 'selected' : '' ?>>CPA cible</option>
                            <option value="manual_cpc" <?= ($campaign['bid_strategy'] ?? '') === 'manual_cpc' ? 'selected' : '' ?>>CPC manuel</option>
                        </select>
                    </label>

                    <label class="gads-label" id="gads-target-cpa-wrap" style="<?= ($campaign['bid_strategy'] ?? '') !== 'target_cpa' ? 'display:none;' : '' ?>">
                        CPA cible (€)
                        <input type="number" id="gads-target-cpa" class="gads-input" step="0.01" min="0"
                            value="<?= e((string) ($campaign['target_cpa'] ?? '')) ?>">
                    </label>

                    <label class="gads-label">
                        Zone géographique
                        <input type="text" id="gads-location" class="gads-input"
                            placeholder="Ex: Bordeaux"
                            value="<?= e($campaign['target_location'] ?? CITY_NAME) ?>">
                    </label>

                    <label class="gads-label">
                        Rayon (km)
                        <input type="number" id="gads-radius" class="gads-input" min="1" max="500"
                            value="<?= e((string) ($campaign['target_radius_km'] ?? 30)) ?>">
                    </label>

                    <label class="gads-label">
                        Date de début
                        <input type="date" id="gads-start-date" class="gads-input"
                            value="<?= e($campaign['start_date'] ?? '') ?>">
                    </label>

                    <label class="gads-label">
                        Date de fin
                        <input type="date" id="gads-end-date" class="gads-input"
                            value="<?= e($campaign['end_date'] ?? '') ?>">
                    </label>
                </div>

                <label class="gads-label" style="margin-top: 1rem;">
                    Notes internes
                    <textarea id="gads-notes" class="gads-input" rows="2"
                        placeholder="Notes internes (non exportées)"><?= e($campaign['notes'] ?? '') ?></textarea>
                </label>
            </div>
        </div>

        <!-- STEP 2: Ad Groups -->
        <div class="gads-wizard-panel" id="gads-step-2" style="display:none;">
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.15rem; margin: 0;">
                        <i class="fas fa-layer-group" style="color: var(--admin-primary);"></i> Groupes d'annonces
                    </h2>
                    <button type="button" onclick="GADS.addAdGroup()" class="btn btn-small" style="background: var(--admin-accent); color: #1a1a1a;">
                        <i class="fas fa-plus"></i> Ajouter un groupe
                    </button>
                </div>
                <p style="color: #888; font-size: 0.85rem; margin-bottom: 1rem;">
                    Chaque groupe d'annonces cible un thème de mots-clés spécifique avec sa propre page de destination.
                </p>

                <div id="gads-ad-groups-container">
                    <!-- Ad groups injected by JS -->
                </div>
            </div>
        </div>

        <!-- STEP 3: Keywords -->
        <div class="gads-wizard-panel" id="gads-step-3" style="display:none;">
            <div class="card" style="padding: 1.5rem;">
                <h2 style="font-size: 1.15rem; margin: 0 0 1rem;">
                    <i class="fas fa-key" style="color: var(--admin-primary);"></i> Mots-clés
                </h2>
                <p style="color: #888; font-size: 0.85rem; margin-bottom: 1rem;">
                    Ajoutez les mots-clés pour chaque groupe d'annonces. L'IA peut aussi en suggérer.
                </p>
                <div id="gads-keywords-container">
                    <!-- Keywords UI injected by JS per ad group -->
                </div>
            </div>
        </div>

        <!-- STEP 4: Ads -->
        <div class="gads-wizard-panel" id="gads-step-4" style="display:none;">
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <h2 style="font-size: 1.15rem; margin: 0;">
                        <i class="fas fa-ad" style="color: var(--admin-primary);"></i> Annonces
                    </h2>
                </div>
                <p style="color: #888; font-size: 0.85rem; margin-bottom: 1rem;">
                    Créez des annonces Responsive Search Ads. Jusqu'à 15 titres (30 car.) et 4 descriptions (90 car.) par annonce.
                </p>
                <div id="gads-ads-container">
                    <!-- Ads UI injected by JS per ad group -->
                </div>
            </div>
        </div>

        <!-- STEP 5: Summary -->
        <div class="gads-wizard-panel" id="gads-step-5" style="display:none;">
            <div class="card" style="padding: 1.5rem;">
                <h2 style="font-size: 1.15rem; margin: 0 0 1rem;">
                    <i class="fas fa-clipboard-check" style="color: var(--admin-primary);"></i> Récapitulatif
                </h2>
                <div id="gads-summary">
                    <!-- Summary rendered by JS -->
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="gads-wizard-nav">
            <button type="button" id="gads-btn-prev" class="btn btn-ghost" onclick="GADS.prevStep()" style="display:none;">
                <i class="fas fa-arrow-left"></i> Précédent
            </button>
            <div style="margin-left: auto; display: flex; gap: 0.75rem;">
                <button type="button" id="gads-btn-save-draft" class="btn btn-ghost" onclick="GADS.saveDraft()">
                    <i class="fas fa-save"></i> Sauvegarder brouillon
                </button>
                <button type="button" id="gads-btn-next" class="btn" style="background: var(--admin-primary); color: #fff;" onclick="GADS.nextStep()">
                    Suivant <i class="fas fa-arrow-right"></i>
                </button>
                <button type="button" id="gads-btn-finalize" class="btn" style="background: #0d7a3e; color: #fff; display: none;" onclick="GADS.finalize()">
                    <i class="fas fa-check"></i> Finaliser la campagne
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Pass server data to JS -->
<script>
window.GADS_INIT = {
    campaignId: <?= $cId ?>,
    campaign: <?= $isEdit ? json_encode($campaign, JSON_UNESCAPED_UNICODE) : 'null' ?>,
    adGroups: <?= json_encode($ad_groups, JSON_UNESCAPED_UNICODE) ?>,
    keywords: <?= json_encode($keywords, JSON_UNESCAPED_UNICODE) ?>,
    ads: <?= json_encode($ads, JSON_UNESCAPED_UNICODE) ?>,
    landingPages: <?= json_encode($landing_pages, JSON_UNESCAPED_UNICODE) ?>,
    hasAnthropic: <?= $has_anthropic ? 'true' : 'false' ?>,
    cityName: <?= json_encode(CITY_NAME) ?>
};
</script>
<script src="/assets/js/google-ads.js"></script>
