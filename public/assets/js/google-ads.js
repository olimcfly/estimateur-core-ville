/**
 * Google Ads Campaign Manager — GADS module JS
 * IIFE pattern matching project conventions.
 */
const GADS = (() => {
    'use strict';

    let currentStep = 1;
    const TOTAL_STEPS = 5;

    // In-memory campaign state
    let state = {
        adGroups: [],   // [{ name, landing_url, cpc_bid, keywords: [], ads: [] }]
    };

    // ─── Init ──────────────────────────────────────────────

    function init() {
        if (typeof window.GADS_INIT === 'undefined') return;

        const d = window.GADS_INIT;

        // Hydrate from server data (edit mode)
        if (d.adGroups && d.adGroups.length > 0) {
            d.adGroups.forEach(ag => {
                const agId = parseInt(ag.id, 10);
                const kws = (d.keywords || []).filter(k => parseInt(k.ad_group_id, 10) === agId).map(k => ({
                    keyword: k.keyword,
                    match_type: k.match_type,
                    is_negative: !!parseInt(k.is_negative, 10),
                    cpc_bid: k.cpc_bid || '',
                }));
                const ads = (d.ads || []).filter(a => parseInt(a.ad_group_id, 10) === agId).map(a => ({
                    headlines: JSON.parse(a.headlines || '[]'),
                    descriptions: JSON.parse(a.descriptions || '[]'),
                    final_url: a.final_url || '',
                    path1: a.path1 || '',
                    path2: a.path2 || '',
                    sitelinks: JSON.parse(a.sitelinks || 'null') || [],
                    callouts: JSON.parse(a.callouts || 'null') || [],
                    ai_generated: !!parseInt(a.ai_generated, 10),
                }));
                state.adGroups.push({
                    name: ag.name,
                    landing_url: ag.landing_url || '',
                    cpc_bid: ag.cpc_bid || '',
                    keywords: kws.length ? kws : [{ keyword: '', match_type: 'phrase', is_negative: false, cpc_bid: '' }],
                    ads: ads.length ? ads : [],
                });
            });
        }

        if (state.adGroups.length === 0) {
            addAdGroup();
        }

        renderAdGroups();
        renderKeywords();
        renderAds();
    }

    // ─── Step Navigation ───────────────────────────────────

    function goToStep(step) {
        if (step < 1 || step > TOTAL_STEPS) return;

        // Hide all panels
        for (let i = 1; i <= TOTAL_STEPS; i++) {
            const panel = document.getElementById('gads-step-' + i);
            if (panel) panel.style.display = i === step ? '' : 'none';
        }

        // Update step indicators
        document.querySelectorAll('.gads-wizard-step').forEach(btn => {
            const s = parseInt(btn.dataset.step, 10);
            btn.classList.toggle('active', s === step);
            btn.classList.toggle('completed', s < step);
        });

        currentStep = step;

        // Show/hide nav buttons
        const prevBtn = document.getElementById('gads-btn-prev');
        const nextBtn = document.getElementById('gads-btn-next');
        const finalBtn = document.getElementById('gads-btn-finalize');

        if (prevBtn) prevBtn.style.display = step > 1 ? '' : 'none';
        if (nextBtn) nextBtn.style.display = step < TOTAL_STEPS ? '' : 'none';
        if (finalBtn) finalBtn.style.display = step === TOTAL_STEPS ? '' : 'none';

        // Render step-specific content
        if (step === 2) renderAdGroups();
        if (step === 3) renderKeywords();
        if (step === 4) renderAds();
        if (step === 5) renderSummary();
    }

    function nextStep() {
        if (currentStep === 1 && !validateStep1()) return;
        syncFromDOM();
        goToStep(currentStep + 1);
    }

    function prevStep() {
        syncFromDOM();
        goToStep(currentStep - 1);
    }

    function validateStep1() {
        const name = document.getElementById('gads-name');
        if (!name || name.value.trim() === '') {
            name.focus();
            toast('Le nom de la campagne est requis.', 'error');
            return false;
        }
        return true;
    }

    // ─── Ad Groups ─────────────────────────────────────────

    function addAdGroup() {
        state.adGroups.push({
            name: 'Groupe ' + (state.adGroups.length + 1),
            landing_url: '',
            cpc_bid: '',
            keywords: [{ keyword: '', match_type: 'phrase', is_negative: false, cpc_bid: '' }],
            ads: [],
        });
        renderAdGroups();
        renderKeywords();
        renderAds();
    }

    function removeAdGroup(idx) {
        if (state.adGroups.length <= 1) {
            toast('Au moins un groupe d\'annonces requis.', 'error');
            return;
        }
        state.adGroups.splice(idx, 1);
        renderAdGroups();
        renderKeywords();
        renderAds();
    }

    function renderAdGroups() {
        const container = document.getElementById('gads-ad-groups-container');
        if (!container) return;

        const d = window.GADS_INIT || {};
        const lpOptions = (d.landingPages || []).map(
            lp => '<option value="' + esc(lp.url) + '">' + esc(lp.label) + '</option>'
        ).join('');

        let html = '';
        state.adGroups.forEach((ag, i) => {
            html += '<div class="gads-adgroup-card" data-ag-idx="' + i + '">'
                + '<div class="gads-adgroup-header">'
                + '<div class="gads-adgroup-title"><i class="fas fa-layer-group"></i> Groupe ' + (i + 1) + '</div>'
                + '<button type="button" class="gads-remove-btn" onclick="GADS.removeAdGroup(' + i + ')" title="Supprimer">'
                + '<i class="fas fa-trash"></i></button>'
                + '</div>'
                + '<div class="gads-form-grid">'
                + '<label class="gads-label">Nom du groupe'
                + '<input type="text" class="gads-input gads-ag-name" value="' + esc(ag.name) + '" data-idx="' + i + '"></label>'
                + '<label class="gads-label">Page de destination'
                + '<select class="gads-input gads-ag-landing" data-idx="' + i + '">'
                + '<option value="">-- URL personnalisée --</option>'
                + lpOptions
                + '</select>'
                + '<input type="text" class="gads-input gads-ag-landing-custom" placeholder="https://..." data-idx="' + i + '" '
                + 'value="' + esc(ag.landing_url) + '" style="margin-top:0.3rem;"></label>'
                + '<label class="gads-label">CPC max (€)'
                + '<input type="number" class="gads-input gads-ag-cpc" step="0.01" min="0" '
                + 'value="' + esc(ag.cpc_bid) + '" data-idx="' + i + '"></label>'
                + '</div></div>';
        });

        container.innerHTML = html;

        // Set selected landing page options
        state.adGroups.forEach((ag, i) => {
            const sel = container.querySelector('.gads-ag-landing[data-idx="' + i + '"]');
            if (sel) {
                for (const opt of sel.options) {
                    if (opt.value === ag.landing_url) {
                        sel.value = ag.landing_url;
                        break;
                    }
                }
            }
        });
    }

    // ─── Keywords ──────────────────────────────────────────

    function addKeyword(agIdx) {
        state.adGroups[agIdx].keywords.push({ keyword: '', match_type: 'phrase', is_negative: false, cpc_bid: '' });
        renderKeywords();
    }

    function removeKeyword(agIdx, kwIdx) {
        state.adGroups[agIdx].keywords.splice(kwIdx, 1);
        if (state.adGroups[agIdx].keywords.length === 0) {
            state.adGroups[agIdx].keywords.push({ keyword: '', match_type: 'phrase', is_negative: false, cpc_bid: '' });
        }
        renderKeywords();
    }

    function renderKeywords() {
        const container = document.getElementById('gads-keywords-container');
        if (!container) return;

        let html = '';
        state.adGroups.forEach((ag, agIdx) => {
            html += '<div class="gads-adgroup-card">'
                + '<div class="gads-adgroup-header">'
                + '<div class="gads-adgroup-title"><i class="fas fa-layer-group"></i> ' + esc(ag.name) + '</div>'
                + '<button type="button" class="gads-btn-sm gads-btn-success" onclick="GADS.addKeyword(' + agIdx + ')">'
                + '<i class="fas fa-plus"></i> Mot-clé</button></div>';

            ag.keywords.forEach((kw, kwIdx) => {
                html += '<div class="gads-kw-row">'
                    + '<input type="text" class="gads-input gads-kw-text" placeholder="mot-clé" '
                    + 'value="' + esc(kw.keyword) + '" data-ag="' + agIdx + '" data-kw="' + kwIdx + '">'
                    + '<select class="gads-input gads-kw-match" data-ag="' + agIdx + '" data-kw="' + kwIdx + '" style="width:120px;">'
                    + '<option value="broad"' + (kw.match_type === 'broad' ? ' selected' : '') + '>Large</option>'
                    + '<option value="phrase"' + (kw.match_type === 'phrase' ? ' selected' : '') + '>Expression</option>'
                    + '<option value="exact"' + (kw.match_type === 'exact' ? ' selected' : '') + '>Exact</option>'
                    + '</select>'
                    + '<label style="font-size:0.8rem;display:flex;align-items:center;gap:0.3rem;white-space:nowrap;">'
                    + '<input type="checkbox" class="gads-kw-neg" data-ag="' + agIdx + '" data-kw="' + kwIdx + '"'
                    + (kw.is_negative ? ' checked' : '') + '> Négatif</label>'
                    + '<button type="button" class="gads-remove-btn" onclick="GADS.removeKeyword(' + agIdx + ',' + kwIdx + ')">'
                    + '<i class="fas fa-times"></i></button></div>';
            });

            html += '</div>';
        });

        container.innerHTML = html;
    }

    // ─── Ads ───────────────────────────────────────────────

    function addAd(agIdx) {
        state.adGroups[agIdx].ads.push({
            headlines: ['', '', ''],
            descriptions: ['', ''],
            final_url: state.adGroups[agIdx].landing_url || '',
            path1: '',
            path2: '',
            sitelinks: [],
            callouts: [],
            ai_generated: false,
        });
        renderAds();
    }

    function removeAd(agIdx, adIdx) {
        state.adGroups[agIdx].ads.splice(adIdx, 1);
        renderAds();
    }

    function addHeadline(agIdx, adIdx) {
        const ad = state.adGroups[agIdx].ads[adIdx];
        if (ad.headlines.length >= 15) {
            toast('Maximum 15 titres.', 'error');
            return;
        }
        ad.headlines.push('');
        renderAds();
    }

    function addDescription(agIdx, adIdx) {
        const ad = state.adGroups[agIdx].ads[adIdx];
        if (ad.descriptions.length >= 4) {
            toast('Maximum 4 descriptions.', 'error');
            return;
        }
        ad.descriptions.push('');
        renderAds();
    }

    function renderAds() {
        const container = document.getElementById('gads-ads-container');
        if (!container) return;

        const d = window.GADS_INIT || {};
        const hasAI = d.hasAnthropic === true;

        let html = '';
        state.adGroups.forEach((ag, agIdx) => {
            html += '<div class="gads-adgroup-card">'
                + '<div class="gads-adgroup-header">'
                + '<div class="gads-adgroup-title"><i class="fas fa-layer-group"></i> ' + esc(ag.name) + '</div>'
                + '<div style="display:flex;gap:0.5rem;">';

            if (hasAI) {
                html += '<button type="button" class="gads-ai-btn" onclick="GADS.generateAI(' + agIdx + ')" id="gads-ai-btn-' + agIdx + '">'
                    + '<i class="fas fa-robot"></i> Générer avec IA</button>';
            }

            html += '<button type="button" class="gads-btn-sm gads-btn-success" onclick="GADS.addAd(' + agIdx + ')">'
                + '<i class="fas fa-plus"></i> Annonce</button>'
                + '</div></div>';

            if (ag.ads.length === 0) {
                html += '<p style="color:#999;font-size:0.85rem;text-align:center;padding:1rem;">Aucune annonce. '
                    + (hasAI ? 'Cliquez sur "Générer avec IA" ou ' : '')
                    + 'ajoutez une annonce manuellement.</p>';
            }

            ag.ads.forEach((ad, adIdx) => {
                html += '<div style="background:#fff;border:1px solid #e8dfd7;border-radius:6px;padding:0.75rem;margin-bottom:0.75rem;">'
                    + '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">'
                    + '<span style="font-weight:600;font-size:0.85rem;">Annonce ' + (adIdx + 1)
                    + (ad.ai_generated ? ' <span class="gads-badge" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-robot"></i> IA</span>' : '')
                    + '</span>'
                    + '<button type="button" class="gads-remove-btn" onclick="GADS.removeAd(' + agIdx + ',' + adIdx + ')">'
                    + '<i class="fas fa-trash"></i></button></div>';

                // Headlines
                html += '<div style="margin-bottom:0.75rem;"><label style="font-size:0.8rem;font-weight:600;color:#666;">Titres (' + ad.headlines.length + '/15)</label>';
                ad.headlines.forEach((h, hIdx) => {
                    const len = h.length;
                    const cls = len > 30 ? 'gads-over' : (len > 0 ? 'gads-ok' : '');
                    html += '<div class="gads-headline-row">'
                        + '<input type="text" class="gads-input gads-ad-headline" maxlength="30" '
                        + 'value="' + esc(h) + '" data-ag="' + agIdx + '" data-ad="' + adIdx + '" data-h="' + hIdx + '" '
                        + 'oninput="GADS.updateCharCount(this, 30)" placeholder="Titre ' + (hIdx + 1) + ' (max 30 car.)">'
                        + '<span class="gads-char-count ' + cls + '">' + len + '/30</span>'
                        + '<button type="button" class="gads-remove-btn" onclick="GADS.removeHeadline(' + agIdx + ',' + adIdx + ',' + hIdx + ')">'
                        + '<i class="fas fa-times"></i></button></div>';
                });
                html += '<button type="button" class="gads-btn-sm" style="background:#e8dfd7;color:#333;" '
                    + 'onclick="GADS.addHeadline(' + agIdx + ',' + adIdx + ')">'
                    + '<i class="fas fa-plus"></i> Titre</button></div>';

                // Descriptions
                html += '<div style="margin-bottom:0.75rem;"><label style="font-size:0.8rem;font-weight:600;color:#666;">Descriptions (' + ad.descriptions.length + '/4)</label>';
                ad.descriptions.forEach((desc, dIdx) => {
                    const len = desc.length;
                    const cls = len > 90 ? 'gads-over' : (len > 0 ? 'gads-ok' : '');
                    html += '<div class="gads-desc-row">'
                        + '<textarea class="gads-input gads-ad-desc" maxlength="90" rows="2" '
                        + 'data-ag="' + agIdx + '" data-ad="' + adIdx + '" data-d="' + dIdx + '" '
                        + 'oninput="GADS.updateCharCount(this, 90)" placeholder="Description ' + (dIdx + 1) + ' (max 90 car.)">'
                        + esc(desc) + '</textarea>'
                        + '<span class="gads-char-count ' + cls + '">' + len + '/90</span>'
                        + '<button type="button" class="gads-remove-btn" onclick="GADS.removeDescription(' + agIdx + ',' + adIdx + ',' + dIdx + ')">'
                        + '<i class="fas fa-times"></i></button></div>';
                });
                html += '<button type="button" class="gads-btn-sm" style="background:#e8dfd7;color:#333;" '
                    + 'onclick="GADS.addDescription(' + agIdx + ',' + adIdx + ')">'
                    + '<i class="fas fa-plus"></i> Description</button></div>';

                // Paths
                html += '<div class="gads-form-grid" style="gap:0.5rem;">'
                    + '<label class="gads-label">Path 1 <input type="text" class="gads-input gads-ad-path1" maxlength="15" '
                    + 'value="' + esc(ad.path1) + '" data-ag="' + agIdx + '" data-ad="' + adIdx + '" placeholder="estimation"></label>'
                    + '<label class="gads-label">Path 2 <input type="text" class="gads-input gads-ad-path2" maxlength="15" '
                    + 'value="' + esc(ad.path2) + '" data-ag="' + agIdx + '" data-ad="' + adIdx + '" placeholder="bordeaux"></label>'
                    + '</div>';

                html += '</div>';
            });

            html += '</div>';
        });

        container.innerHTML = html;
    }

    function removeHeadline(agIdx, adIdx, hIdx) {
        const ad = state.adGroups[agIdx].ads[adIdx];
        if (ad.headlines.length <= 1) return;
        ad.headlines.splice(hIdx, 1);
        renderAds();
    }

    function removeDescription(agIdx, adIdx, dIdx) {
        const ad = state.adGroups[agIdx].ads[adIdx];
        if (ad.descriptions.length <= 1) return;
        ad.descriptions.splice(dIdx, 1);
        renderAds();
    }

    function updateCharCount(el, max) {
        const len = el.value.length;
        const span = el.parentElement.querySelector('.gads-char-count');
        if (!span) return;
        span.textContent = len + '/' + max;
        span.className = 'gads-char-count ' + (len > max ? 'gads-over' : (len > 0 ? 'gads-ok' : ''));

        // Sync value to state
        const agIdx = parseInt(el.dataset.ag, 10);
        const adIdx = parseInt(el.dataset.ad, 10);
        if (el.classList.contains('gads-ad-headline')) {
            state.adGroups[agIdx].ads[adIdx].headlines[parseInt(el.dataset.h, 10)] = el.value;
        } else if (el.classList.contains('gads-ad-desc')) {
            state.adGroups[agIdx].ads[adIdx].descriptions[parseInt(el.dataset.d, 10)] = el.value;
        }
    }

    // ─── AI Generation ─────────────────────────────────────

    function generateAI(agIdx) {
        syncFromDOM();

        const ag = state.adGroups[agIdx];
        const btn = document.getElementById('gads-ai-btn-' + agIdx);
        if (!btn) return;

        const mainKeyword = ag.keywords.find(k => k.keyword.trim() !== '' && !k.is_negative);
        const keyword = mainKeyword ? mainKeyword.keyword : ag.name;

        const landingUrl = ag.landing_url || '/lp/estimation-bordeaux';
        const d = window.GADS_INIT || {};

        btn.disabled = true;
        btn.innerHTML = '<span class="gads-spinner"></span> Génération...';

        fetch('/admin/gads-campaigns/api/generate', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                keyword: keyword,
                landing_url: landingUrl,
                city: d.cityName || 'Bordeaux',
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.error || 'Erreur');

            const gen = data.data;

            // Create new ad from AI response
            const newAd = {
                headlines: (gen.headlines || []).slice(0, 15),
                descriptions: (gen.descriptions || []).slice(0, 4),
                final_url: landingUrl,
                path1: gen.path1 || '',
                path2: gen.path2 || '',
                sitelinks: gen.sitelinks || [],
                callouts: gen.callouts || [],
                ai_generated: true,
            };

            state.adGroups[agIdx].ads.push(newAd);

            // Add suggested negative keywords
            if (gen.negative_keywords && gen.negative_keywords.length) {
                gen.negative_keywords.forEach(nk => {
                    state.adGroups[agIdx].keywords.push({
                        keyword: nk,
                        match_type: 'exact',
                        is_negative: true,
                        cpc_bid: '',
                    });
                });
            }

            // Add additional keywords
            if (gen.additional_keywords && gen.additional_keywords.length) {
                gen.additional_keywords.forEach(ak => {
                    state.adGroups[agIdx].keywords.push({
                        keyword: ak.keyword || ak,
                        match_type: ak.match_type || 'phrase',
                        is_negative: false,
                        cpc_bid: '',
                    });
                });
            }

            renderAds();
            renderKeywords();
            toast('Annonce IA générée avec succès !', 'success');
        })
        .catch(err => {
            toast(err.message || 'Erreur lors de la génération.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-robot"></i> Générer avec IA';
        });
    }

    // ─── Summary ───────────────────────────────────────────

    function renderSummary() {
        syncFromDOM();

        const container = document.getElementById('gads-summary');
        if (!container) return;

        const name = val('gads-name');
        const type = val('gads-type');
        const budget = val('gads-budget');
        const strategy = val('gads-bid-strategy');
        const location = val('gads-location');

        let html = '<div class="gads-summary-section">'
            + '<h3><i class="fas fa-cog" style="color:var(--admin-primary);"></i> Campagne</h3>'
            + '<table class="gads-summary-table"><tbody>'
            + row('Nom', name) + row('Type', type) + row('Budget/jour', budget ? budget + ' €' : 'Non défini')
            + row('Stratégie', strategy) + row('Zone', location)
            + '</tbody></table></div>';

        state.adGroups.forEach((ag, i) => {
            const kwCount = ag.keywords.filter(k => k.keyword.trim() !== '').length;
            const adCount = ag.ads.length;

            html += '<div class="gads-summary-section">'
                + '<h3><i class="fas fa-layer-group" style="color:var(--admin-accent);"></i> ' + esc(ag.name) + '</h3>'
                + '<table class="gads-summary-table"><tbody>'
                + row('Landing page', ag.landing_url || 'Non définie')
                + row('Mots-clés', kwCount + ' mot(s)-clé(s)')
                + row('Annonces', adCount + ' annonce(s)')
                + '</tbody></table></div>';
        });

        container.innerHTML = html;
    }

    function row(label, value) {
        return '<tr><th>' + esc(label) + '</th><td>' + esc(value) + '</td></tr>';
    }

    // ─── Sync DOM → State ──────────────────────────────────

    function syncFromDOM() {
        // Sync ad groups from step 2
        document.querySelectorAll('.gads-ag-name').forEach(el => {
            const idx = parseInt(el.dataset.idx, 10);
            if (state.adGroups[idx]) state.adGroups[idx].name = el.value.trim();
        });
        document.querySelectorAll('.gads-ag-landing-custom').forEach(el => {
            const idx = parseInt(el.dataset.idx, 10);
            const sel = document.querySelector('.gads-ag-landing[data-idx="' + idx + '"]');
            if (state.adGroups[idx]) {
                state.adGroups[idx].landing_url = (sel && sel.value) ? sel.value : el.value.trim();
            }
        });
        document.querySelectorAll('.gads-ag-cpc').forEach(el => {
            const idx = parseInt(el.dataset.idx, 10);
            if (state.adGroups[idx]) state.adGroups[idx].cpc_bid = el.value;
        });

        // Sync keywords from step 3
        document.querySelectorAll('.gads-kw-text').forEach(el => {
            const ag = parseInt(el.dataset.ag, 10);
            const kw = parseInt(el.dataset.kw, 10);
            if (state.adGroups[ag] && state.adGroups[ag].keywords[kw]) {
                state.adGroups[ag].keywords[kw].keyword = el.value.trim();
            }
        });
        document.querySelectorAll('.gads-kw-match').forEach(el => {
            const ag = parseInt(el.dataset.ag, 10);
            const kw = parseInt(el.dataset.kw, 10);
            if (state.adGroups[ag] && state.adGroups[ag].keywords[kw]) {
                state.adGroups[ag].keywords[kw].match_type = el.value;
            }
        });
        document.querySelectorAll('.gads-kw-neg').forEach(el => {
            const ag = parseInt(el.dataset.ag, 10);
            const kw = parseInt(el.dataset.kw, 10);
            if (state.adGroups[ag] && state.adGroups[ag].keywords[kw]) {
                state.adGroups[ag].keywords[kw].is_negative = el.checked;
            }
        });

        // Sync ad paths from step 4
        document.querySelectorAll('.gads-ad-path1').forEach(el => {
            const ag = parseInt(el.dataset.ag, 10);
            const ad = parseInt(el.dataset.ad, 10);
            if (state.adGroups[ag] && state.adGroups[ag].ads[ad]) {
                state.adGroups[ag].ads[ad].path1 = el.value.trim();
            }
        });
        document.querySelectorAll('.gads-ad-path2').forEach(el => {
            const ag = parseInt(el.dataset.ag, 10);
            const ad = parseInt(el.dataset.ad, 10);
            if (state.adGroups[ag] && state.adGroups[ag].ads[ad]) {
                state.adGroups[ag].ads[ad].path2 = el.value.trim();
            }
        });
    }

    // ─── Save ──────────────────────────────────────────────

    function buildPayload(status) {
        syncFromDOM();

        return {
            campaign_id: parseInt(val('gads-campaign-id') || '0', 10) || null,
            name: val('gads-name'),
            campaign_type: val('gads-type'),
            status: status || 'draft',
            daily_budget: parseFloat(val('gads-budget')) || 0,
            target_location: val('gads-location'),
            target_radius_km: parseInt(val('gads-radius'), 10) || 30,
            bid_strategy: val('gads-bid-strategy'),
            target_cpa: parseFloat(val('gads-target-cpa')) || null,
            start_date: val('gads-start-date'),
            end_date: val('gads-end-date'),
            notes: val('gads-notes'),
            ad_groups: state.adGroups.map(ag => ({
                name: ag.name,
                landing_url: ag.landing_url,
                cpc_bid: ag.cpc_bid,
                keywords: ag.keywords.filter(k => k.keyword.trim() !== ''),
                ads: ag.ads.map(ad => ({
                    headlines: ad.headlines.filter(h => h.trim() !== ''),
                    descriptions: ad.descriptions.filter(d => d.trim() !== ''),
                    final_url: ad.final_url || ag.landing_url,
                    path1: ad.path1,
                    path2: ad.path2,
                    sitelinks: ad.sitelinks,
                    callouts: ad.callouts,
                    ai_generated: ad.ai_generated,
                })),
            })),
        };
    }

    function saveDraft() {
        save('draft');
    }

    function finalize() {
        save('ready');
    }

    function save(status) {
        const payload = buildPayload(status);

        if (!payload.name) {
            toast('Le nom de la campagne est requis.', 'error');
            return;
        }

        fetch('/admin/gads-campaigns/api/save', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.error || 'Erreur');

            // Update campaign ID for subsequent saves
            const idField = document.getElementById('gads-campaign-id');
            if (idField && data.campaign_id) idField.value = data.campaign_id;

            toast(data.message || 'Campagne sauvegardée.', 'success');

            if (status === 'ready') {
                setTimeout(() => {
                    window.location.href = '/admin/gads-campaigns?message=' + encodeURIComponent('Campagne prête à exporter.');
                }, 1200);
            }
        })
        .catch(err => {
            toast(err.message || 'Erreur lors de la sauvegarde.', 'error');
        });
    }

    // ─── Campaign List Actions ─────────────────────────────

    function deleteCampaign(id) {
        if (!confirm('Supprimer cette campagne et tout son contenu ?')) return;

        const fd = new FormData();
        fd.append('id', id);

        fetch('/admin/gads-campaigns/api/delete', {
            method: 'POST',
            credentials: 'same-origin',
            body: fd,
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.error);
            const card = document.querySelector('.gads-campaign-card[data-id="' + id + '"]');
            if (card) card.remove();
            toast('Campagne supprimée.', 'success');
        })
        .catch(err => toast(err.message || 'Erreur', 'error'));
    }

    function setStatus(id, status) {
        const fd = new FormData();
        fd.append('id', id);
        fd.append('status', status);

        fetch('/admin/gads-campaigns/api/status', {
            method: 'POST',
            credentials: 'same-origin',
            body: fd,
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.error);
            window.location.reload();
        })
        .catch(err => toast(err.message || 'Erreur', 'error'));
    }

    // ─── UI: Toggle CPA ────────────────────────────────────

    function toggleTargetCpa() {
        const strategy = val('gads-bid-strategy');
        const wrap = document.getElementById('gads-target-cpa-wrap');
        if (wrap) wrap.style.display = strategy === 'target_cpa' ? '' : 'none';
    }

    // ─── Utilities ─────────────────────────────────────────

    function val(id) {
        const el = document.getElementById(id);
        return el ? el.value.trim() : '';
    }

    function esc(str) {
        if (typeof str !== 'string') return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function toast(msg, type) {
        const el = document.createElement('div');
        el.className = 'gads-toast ' + (type || 'success');
        el.innerHTML = '<i class="fas fa-' + (type === 'error' ? 'exclamation-circle' : 'check-circle') + '"></i> ' + esc(msg);
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 4000);
    }

    // ─── Boot ──────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', init);

    // Allow step clicking
    document.addEventListener('click', function(e) {
        const stepBtn = e.target.closest('.gads-wizard-step');
        if (stepBtn) {
            const step = parseInt(stepBtn.dataset.step, 10);
            if (step) {
                syncFromDOM();
                goToStep(step);
            }
        }
    });

    return {
        nextStep,
        prevStep,
        addAdGroup,
        removeAdGroup,
        addKeyword,
        removeKeyword,
        addAd,
        removeAd,
        addHeadline,
        addDescription,
        removeHeadline,
        removeDescription,
        updateCharCount,
        generateAI,
        saveDraft,
        finalize,
        deleteCampaign,
        setStatus,
        toggleTargetCpa,
    };
})();
