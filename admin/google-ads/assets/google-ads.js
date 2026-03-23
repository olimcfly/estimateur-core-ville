/* ============================================================
   Google Ads Campaign Manager — GADS IIFE Module
   ============================================================ */
var GADS = (function () {
  'use strict';

  // ── State ────────────────────────────────────────────────
  var state = {
    currentStep: 1,
    name: '',
    ville: '',
    domain: '',
    budget: '',
    phone: '',
    tagline: '',
    selectedType: '',
    campaignLabel: '',
    ads: null,
    urlFinale: '',
    trackingTemplate: '',
    keywords: [],
    landingHtml: '',
    landingPath: '',
    campaignId: null,
    websiteId: null
  };

  var totalSteps = 5;
  var API_BASE = '/admin/google-ads/api';

  // ── Helpers ──────────────────────────────────────────────
  function slugify(str) {
    return str
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  function $(sel, ctx) {
    return (ctx || document).querySelector(sel);
  }

  function $$(sel, ctx) {
    return Array.from((ctx || document).querySelectorAll(sel));
  }

  function apiPost(endpoint, body) {
    return fetch(API_BASE + '/' + endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    }).then(function (res) { return res.json(); });
  }

  // ── Step indicator ───────────────────────────────────────
  function updateSteps() {
    $$('.gads-step').forEach(function (el, i) {
      var stepNum = i + 1;
      el.classList.remove('is-active', 'is-done');
      if (stepNum < state.currentStep) {
        el.classList.add('is-done');
      } else if (stepNum === state.currentStep) {
        el.classList.add('is-active');
      }
    });
    $$('.gads-step-panel').forEach(function (el) {
      var panelStep = parseInt(el.dataset.step, 10);
      el.classList.toggle('is-active', panelStep === state.currentStep);
    });
  }

  function nextStep() {
    if (state.currentStep < totalSteps) {
      state.currentStep++;
      updateSteps();
    }
  }

  function prevStep() {
    if (state.currentStep > 1) {
      state.currentStep--;
      updateSteps();
    }
  }

  // ── Type selection ───────────────────────────────────────
  function selectType(type) {
    state.selectedType = type;
    $$('.gads-type-card').forEach(function (el) {
      el.classList.toggle('is-selected', el.dataset.type === type);
    });
  }

  // ── Read form values into state ──────────────────────────
  function readForm() {
    var fields = ['name', 'ville', 'domain', 'budget', 'phone', 'tagline', 'campaignLabel'];
    fields.forEach(function (f) {
      var el = $('#gads-' + f);
      if (el) state[f] = el.value.trim();
    });
    var ws = $('#gads-websiteId');
    if (ws) state.websiteId = ws.value;
  }

  // ── UTM builder ──────────────────────────────────────────
  function buildUtm() {
    var villeSlug = slugify(state.ville);
    var base = 'https://' + state.domain;
    state.urlFinale = base + '/estimation-immobiliere-' + villeSlug
      + '?utm_source=google&utm_medium=cpc&utm_campaign=' + slugify(state.campaignLabel);
    state.trackingTemplate = '{lpurl}&utm_term={keyword}&utm_content={creative}&gclid={gclid}';
    var urlEl = $('#gads-url-finale');
    if (urlEl) urlEl.textContent = state.urlFinale;
    var tmplEl = $('#gads-tracking-template');
    if (tmplEl) tmplEl.textContent = state.trackingTemplate;
  }

  // ── Generate ads via API ─────────────────────────────────
  function generateAds() {
    readForm();
    var btn = $('#gads-btn-generate-ads');
    if (btn) { btn.disabled = true; btn.textContent = 'Generation en cours...'; }

    return apiPost('generate.php', {
      action: 'generate_ads',
      name: state.name,
      ville: state.ville,
      domain: state.domain,
      campaign_type: state.selectedType,
      campaign_label: state.campaignLabel
    }).then(function (data) {
      if (btn) { btn.disabled = false; btn.textContent = 'Generer les annonces'; }
      if (data.ok) {
        state.ads = data.ads;
        renderAds();
        buildUtm();
      } else {
        alert('Erreur: ' + (data.error || 'Echec generation'));
      }
    }).catch(function (err) {
      if (btn) { btn.disabled = false; btn.textContent = 'Generer les annonces'; }
      alert('Erreur reseau: ' + err.message);
    });
  }

  // ── Render ads ───────────────────────────────────────────
  function renderAds() {
    var container = $('#gads-ads-container');
    if (!container || !state.ads) return;

    var html = '';
    ['varianteA', 'varianteB'].forEach(function (variant) {
      var ad = state.ads[variant];
      if (!ad) return;
      html += '<div class="gads-ad-card">';
      html += '<h4>' + variant + '</h4>';
      ['titre1', 'titre2', 'titre3'].forEach(function (field) {
        var val = ad[field] || '';
        var limit = 30;
        var cls = val.length > limit ? 'gads-char-over' : '';
        html += '<div class="gads-ad-field">';
        html += '<div class="gads-ad-field-label">' + field + '</div>';
        html += '<div class="gads-ad-field-value">' + escapeHtml(val);
        html += ' <span class="gads-char-count ' + cls + '">' + val.length + '/' + limit + '</span>';
        html += '</div></div>';
      });
      ['desc1', 'desc2'].forEach(function (field) {
        var val = ad[field] || '';
        var limit = 90;
        var cls = val.length > limit ? 'gads-char-over' : '';
        html += '<div class="gads-ad-field">';
        html += '<div class="gads-ad-field-label">' + field + '</div>';
        html += '<div class="gads-ad-field-value">' + escapeHtml(val);
        html += ' <span class="gads-char-count ' + cls + '">' + val.length + '/' + limit + '</span>';
        html += '</div></div>';
      });
      html += '<button class="gads-btn gads-btn--secondary gads-copy-ad" data-variant="' + variant + '">Copier</button>';
      html += '</div>';
    });

    container.innerHTML = html;
    container.classList.add('gads-ads-grid');

    $$('.gads-copy-ad', container).forEach(function (btn) {
      btn.addEventListener('click', function () { copyAdText(btn.dataset.variant); });
    });
  }

  function escapeHtml(str) {
    var d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  // ── Copy ad text ─────────────────────────────────────────
  function copyAdText(variant) {
    if (!state.ads || !state.ads[variant]) return;
    var ad = state.ads[variant];
    var text = [ad.titre1, ad.titre2, ad.titre3, ad.desc1, ad.desc2].join('\n');
    navigator.clipboard.writeText(text).then(function () {
      var btn = $('.gads-copy-ad[data-variant="' + variant + '"]');
      if (btn) {
        btn.textContent = 'Copie !';
        setTimeout(function () { btn.textContent = 'Copier'; }, 1500);
      }
    });
  }

  // ── Generate landing page ────────────────────────────────
  function generateLanding() {
    readForm();
    var btn = $('#gads-btn-generate-landing');
    if (btn) { btn.disabled = true; btn.textContent = 'Generation en cours...'; }

    return apiPost('generate.php', {
      action: 'generate_landing',
      name: state.name,
      ville: state.ville,
      domain: state.domain,
      phone: state.phone,
      tagline: state.tagline,
      campaign_type: state.selectedType,
      campaign_label: state.campaignLabel,
      primary_color: '#1a73e8',
      reviews_count: '150',
      rating: '4.9',
      estimations: '5000',
      years_exp: '15',
      testimonial1: '',
      testimonial2: '',
      ad_titre1: state.ads && state.ads.varianteA ? state.ads.varianteA.titre1 : ''
    }).then(function (data) {
      if (btn) { btn.disabled = false; btn.textContent = 'Generer la landing page'; }
      if (data.ok) {
        state.landingHtml = data.html;
        state.landingPath = data.path || '';
        var pathEl = $('#gads-landing-path');
        if (pathEl) pathEl.textContent = state.landingPath;
      } else {
        alert('Erreur: ' + (data.error || 'Echec generation'));
      }
    }).catch(function (err) {
      if (btn) { btn.disabled = false; btn.textContent = 'Generer la landing page'; }
      alert('Erreur reseau: ' + err.message);
    });
  }

  // ── Preview landing ──────────────────────────────────────
  function previewLanding() {
    if (!state.landingHtml) {
      alert('Generez d\'abord la landing page.');
      return;
    }
    var blob = new Blob([state.landingHtml], { type: 'text/html' });
    var url = URL.createObjectURL(blob);
    var iframe = $('#gads-preview-iframe');
    if (iframe) iframe.src = url;
    var overlay = $('.gads-modal-overlay');
    if (overlay) overlay.classList.add('is-open');
  }

  function closePreview() {
    var overlay = $('.gads-modal-overlay');
    if (overlay) overlay.classList.remove('is-open');
    var iframe = $('#gads-preview-iframe');
    if (iframe && iframe.src.startsWith('blob:')) {
      URL.revokeObjectURL(iframe.src);
      iframe.src = 'about:blank';
    }
  }

  // ── Copy / Export landing ────────────────────────────────
  function copyLanding() {
    if (!state.landingHtml) return;
    navigator.clipboard.writeText(state.landingHtml).then(function () {
      var btn = $('#gads-btn-copy-landing');
      if (btn) {
        btn.textContent = 'Copie !';
        setTimeout(function () { btn.textContent = 'Copier HTML'; }, 1500);
      }
    });
  }

  function exportLanding() {
    if (!state.landingHtml) return;
    var blob = new Blob([state.landingHtml], { type: 'text/html' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'landing-' + slugify(state.ville) + '.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(a.href);
  }

  // ── Save campaign ────────────────────────────────────────
  function saveCampaign() {
    readForm();
    buildUtm();
    var btn = $('#gads-btn-save');
    if (btn) { btn.disabled = true; btn.textContent = 'Sauvegarde...'; }

    return apiPost('generate.php', {
      action: 'save_campaign',
      website_id: state.websiteId,
      name: state.name,
      ville: state.ville,
      domain: state.domain,
      budget: state.budget,
      campaign_type: state.selectedType,
      campaign_label: state.campaignLabel,
      ads: state.ads,
      url_finale: state.urlFinale,
      tracking_template: state.trackingTemplate,
      keywords: state.keywords,
      landing_html: state.landingHtml,
      landing_path: state.landingPath,
      status: 'draft'
    }).then(function (data) {
      if (btn) { btn.disabled = false; btn.textContent = 'Sauvegarder'; }
      if (data.ok) {
        state.campaignId = data.campaign_id;
        alert('Campagne sauvegardee !');
      } else {
        alert('Erreur: ' + (data.error || 'Echec sauvegarde'));
      }
    }).catch(function (err) {
      if (btn) { btn.disabled = false; btn.textContent = 'Sauvegarder'; }
      alert('Erreur reseau: ' + err.message);
    });
  }

  // ── Pixel tabs ───────────────────────────────────────────
  function switchPixelTab(tabId) {
    $$('.gads-pixel-tab').forEach(function (el) {
      el.classList.toggle('is-active', el.dataset.tab === tabId);
    });
    $$('.gads-pixel-panel').forEach(function (el) {
      el.classList.toggle('is-active', el.id === tabId);
    });
  }

  // ── Generic copy ─────────────────────────────────────────
  function copyCode(elementId) {
    var el = document.getElementById(elementId);
    if (!el) return;
    navigator.clipboard.writeText(el.textContent).then(function () {
      // Brief visual feedback handled by caller if needed
    });
  }

  // ── Init ─────────────────────────────────────────────────
  function init() {
    // Step navigation
    $$('[data-action="next-step"]').forEach(function (btn) {
      btn.addEventListener('click', nextStep);
    });
    $$('[data-action="prev-step"]').forEach(function (btn) {
      btn.addEventListener('click', prevStep);
    });

    // Type selection
    $$('.gads-type-card').forEach(function (card) {
      card.addEventListener('click', function () { selectType(card.dataset.type); });
    });

    // Generate ads
    var genAdsBtn = $('#gads-btn-generate-ads');
    if (genAdsBtn) genAdsBtn.addEventListener('click', generateAds);

    // Generate landing
    var genLandingBtn = $('#gads-btn-generate-landing');
    if (genLandingBtn) genLandingBtn.addEventListener('click', generateLanding);

    // Preview / close
    var prevBtn = $('#gads-btn-preview-landing');
    if (prevBtn) prevBtn.addEventListener('click', previewLanding);
    var closeBtn = $('.gads-modal-close');
    if (closeBtn) closeBtn.addEventListener('click', closePreview);
    var overlay = $('.gads-modal-overlay');
    if (overlay) {
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closePreview();
      });
    }

    // Copy / export landing
    var copyBtn = $('#gads-btn-copy-landing');
    if (copyBtn) copyBtn.addEventListener('click', copyLanding);
    var exportBtn = $('#gads-btn-export-landing');
    if (exportBtn) exportBtn.addEventListener('click', exportLanding);

    // Save
    var saveBtn = $('#gads-btn-save');
    if (saveBtn) saveBtn.addEventListener('click', saveCampaign);

    // Pixel tabs
    $$('.gads-pixel-tab').forEach(function (tab) {
      tab.addEventListener('click', function () { switchPixelTab(tab.dataset.tab); });
    });

    // Copy code buttons
    $$('[data-copy-target]').forEach(function (btn) {
      btn.addEventListener('click', function () { copyCode(btn.dataset.copyTarget); });
    });

    // Keyboard: Escape closes modal
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closePreview();
    });

    // Set initial step
    updateSteps();
  }

  // ── Public API ───────────────────────────────────────────
  return {
    init: init,
    nextStep: nextStep,
    prevStep: prevStep,
    selectType: selectType,
    generateAds: generateAds,
    renderAds: renderAds,
    copyAdText: copyAdText,
    generateLanding: generateLanding,
    previewLanding: previewLanding,
    closePreview: closePreview,
    copyLanding: copyLanding,
    exportLanding: exportLanding,
    saveCampaign: saveCampaign,
    switchPixelTab: switchPixelTab,
    copyCode: copyCode
  };
})();

document.addEventListener('DOMContentLoaded', function () {
  GADS.init();
});
