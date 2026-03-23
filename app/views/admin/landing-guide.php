<div class="guide-wrapper">

  <!-- ═══════════════ HEADER ═══════════════ -->
  <div class="guide-header">
    <h2>Guide Google Ads & Pages de Destination</h2>
    <p>Bonnes pratiques, tracking UTM, et configuration de vos campagnes Google Ads.</p>
  </div>

  <!-- ═══════════════ NAVIGATION PILLS ═══════════════ -->
  <nav class="guide-nav">
    <a href="#section-pages"><i class="fas fa-file-alt"></i> Pages</a>
    <a href="#section-utm"><i class="fas fa-chart-pie"></i> UTM & Tracking</a>
    <a href="#section-config"><i class="fas fa-cog"></i> Configuration</a>
    <a href="#section-pixel"><i class="fas fa-code"></i> Pixel</a>
    <a href="#section-quality"><i class="fas fa-star"></i> Quality Score</a>
    <a href="#section-annonces"><i class="fas fa-ad"></i> Annonces</a>
    <a href="#section-keywords"><i class="fas fa-search"></i> Mots-cl&eacute;s</a>
    <a href="#section-budget"><i class="fas fa-euro-sign"></i> Budget</a>
    <a href="#section-erreurs"><i class="fas fa-ban"></i> Erreurs</a>
    <a href="#section-kpi"><i class="fas fa-tachometer-alt"></i> KPIs</a>
  </nav>

  <!-- ═══════════════ SECTION 1 : PAGES DISPONIBLES ═══════════════ -->
  <div class="guide-section" id="section-pages">
    <h3><i class="fas fa-file-alt"></i> Pages de destination disponibles</h3>
    <p>
      Chaque landing page est optimis&eacute;e pour un groupe de mots-cl&eacute;s sp&eacute;cifique. Utilisez l'URL correspondante dans vos annonces Google Ads.
    </p>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Page</th>
          <th>URL</th>
          <th>Mots-cl&eacute;s cibl&eacute;s</th>
          <th>Objectif</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Estimation Bordeaux</strong></td>
          <td><code>/lp/estimation-bordeaux</code></td>
          <td>estimation immobili&egrave;re bordeaux, estimer bien bordeaux, prix immobilier bordeaux</td>
          <td>Capture lead estimation</td>
        </tr>
        <tr>
          <td><strong>Vendre Maison</strong></td>
          <td><code>/lp/vendre-maison-bordeaux</code></td>
          <td>vendre maison bordeaux, vente maison bordeaux, mettre en vente maison bordeaux</td>
          <td>Capture lead vendeur maison</td>
        </tr>
        <tr>
          <td><strong>Avis de Valeur</strong></td>
          <td><code>/lp/avis-valeur-gratuit</code></td>
          <td>avis de valeur gratuit, avis de valeur bordeaux, estimation gratuite bordeaux</td>
          <td>Capture lead avis valeur</td>
        </tr>
      </tbody>
    </table>

    <div class="guide-tip guide-tip-info">
      <strong><i class="fas fa-info-circle"></i> Note :</strong>
      Ces pages n'ont pas de menu de navigation pour &eacute;viter les distractions. Elles sont en <code>noindex, nofollow</code>
      pour ne pas interf&eacute;rer avec votre SEO. Seule la conversion compte.
    </div>
  </div>

  <!-- ═══════════════ SECTION 2 : UTM TRACKING ═══════════════ -->
  <div class="guide-section" id="section-utm">
    <h3><i class="fas fa-chart-pie"></i> Param&egrave;tres UTM & Tracking</h3>
    <p>
      Les param&egrave;tres UTM permettent de tracer l'origine exacte de chaque lead dans votre CRM.
      Ils sont automatiquement captur&eacute;s et sauvegard&eacute;s dans les notes du lead.
    </p>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Param&egrave;tre</th>
          <th>Description</th>
          <th>Exemple</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><code>utm_source</code></td>
          <td>D'o&ugrave; vient le trafic (plateforme publicitaire)</td>
          <td><code>google</code></td>
        </tr>
        <tr>
          <td><code>utm_medium</code></td>
          <td>Type de canal / m&eacute;dia</td>
          <td><code>cpc</code> (co&ucirc;t par clic)</td>
        </tr>
        <tr>
          <td><code>utm_campaign</code></td>
          <td>Nom de la campagne Google Ads</td>
          <td><code>estimation-bordeaux-2024</code></td>
        </tr>
        <tr>
          <td><code>utm_term</code></td>
          <td>Mot-cl&eacute; qui a d&eacute;clench&eacute; l'annonce</td>
          <td><code>estimation+immobiliere+bordeaux</code></td>
        </tr>
        <tr>
          <td><code>utm_content</code></td>
          <td>Variante de l'annonce (pour tests A/B)</td>
          <td><code>annonce-variante-a</code></td>
        </tr>
        <tr>
          <td><code>gclid</code></td>
          <td>Google Click ID (auto-tagging Google Ads)</td>
          <td><em>Automatique si activ&eacute;</em></td>
        </tr>
      </tbody>
    </table>

    <h4>Exemple d'URL compl&egrave;te avec UTM :</h4>
    <div class="guide-code">
<span class="string">https://estimation-immobilier-bordeaux.fr/lp/estimation-bordeaux</span>
  <span class="tag">?utm_source=</span><span class="string">google</span>
  <span class="tag">&amp;utm_medium=</span><span class="string">cpc</span>
  <span class="tag">&amp;utm_campaign=</span><span class="string">estimation-bordeaux-2024</span>
  <span class="tag">&amp;utm_term=</span><span class="string">estimation+immobiliere+bordeaux</span>
  <span class="tag">&amp;utm_content=</span><span class="string">annonce-variante-a</span>
    </div>

    <div class="guide-tip guide-tip-success">
      <strong><i class="fas fa-check-circle"></i> Auto-capture :</strong>
      Les UTM sont automatiquement captur&eacute;s &agrave; l'arriv&eacute;e sur la page et sauvegard&eacute;s en session.
      Ils sont inclus dans les notes de chaque lead cr&eacute;&eacute; depuis une landing page Google Ads.
      Vous pouvez les voir dans le d&eacute;tail de chaque lead dans le CRM (onglet Leads).
    </div>
  </div>

  <!-- ═══════════════ SECTION 3 : CONFIG GOOGLE ADS ═══════════════ -->
  <div class="guide-section" id="section-config">
    <h3><i class="fas fa-cog"></i> Configuration dans Google Ads</h3>

    <h4>1. Activer l'auto-tagging (gclid)</h4>
    <p style="font-size: 0.86rem; color: #5f6368; margin-bottom: 1rem;">
      Dans Google Ads &rarr; Param&egrave;tres du compte &rarr; Cochez <strong>"Marquage automatique"</strong>.
      Cela ajoute automatiquement le param&egrave;tre <code>gclid</code> &agrave; chaque clic, ce qui permet le suivi des conversions.
    </p>

    <h4>2. Configurer les mod&egrave;les de suivi (Tracking Template)</h4>
    <p style="font-size: 0.86rem; color: #5f6368; margin-bottom: 0.5rem;">
      Au niveau de la campagne ou du groupe d'annonces, configurez le mod&egrave;le de suivi :
    </p>
    <div class="guide-code">
<span class="comment">-- Mod&egrave;le de suivi (Tracking Template) --</span>
<span class="string">{lpurl}?utm_source=google&amp;utm_medium=cpc&amp;utm_campaign={campaignid}&amp;utm_term={keyword}&amp;utm_content={creative}</span>
    </div>

    <h4>3. URL finale dans l'annonce</h4>
    <p style="font-size: 0.86rem; color: #5f6368; margin-bottom: 0.5rem;">
      Dans le champ "URL finale" de chaque annonce, utilisez l'URL de la landing page correspondante :
    </p>
    <div class="guide-code">
<span class="comment">-- Campagne "Estimation Bordeaux" --</span>
<span class="string">https://estimation-immobilier-bordeaux.fr/lp/estimation-bordeaux</span>

<span class="comment">-- Campagne "Vendre Maison" --</span>
<span class="string">https://estimation-immobilier-bordeaux.fr/lp/vendre-maison-bordeaux</span>

<span class="comment">-- Campagne "Avis de Valeur" --</span>
<span class="string">https://estimation-immobilier-bordeaux.fr/lp/avis-valeur-gratuit</span>
    </div>

    <h4>4. Configurer le suivi de conversion</h4>
    <p style="font-size: 0.86rem; color: #5f6368; margin-bottom: 1rem;">
      Cr&eacute;ez une action de conversion dans Google Ads &rarr; Outils &rarr; Conversions :
    </p>
    <ul class="guide-checklist">
      <li><i class="fas fa-check-circle"></i> <strong>Cat&eacute;gorie :</strong> Demande de formulaire (Lead)</li>
      <li><i class="fas fa-check-circle"></i> <strong>Source :</strong> Site Web</li>
      <li><i class="fas fa-check-circle"></i> <strong>M&eacute;thode :</strong> Balise Google (gtag.js) ou Google Tag Manager</li>
      <li><i class="fas fa-check-circle"></i> <strong>Page de conversion :</strong> La page "Merci" (apr&egrave;s soumission du formulaire)</li>
      <li><i class="fas fa-check-circle"></i> <strong>Mod&egrave;le d'attribution :</strong> Bas&eacute; sur les donn&eacute;es (recommand&eacute; par Google)</li>
    </ul>

    <div class="guide-tip guide-tip-warning">
      <strong><i class="fas fa-exclamation-triangle"></i> Important :</strong>
      Dans le fichier <code>app/views/landing/layout.php</code>, d&eacute;commentez les balises Google Ads en haut du &lt;head&gt;
      et remplacez <code>AW-XXXXXXXXX</code> par votre ID de conversion Google Ads.
      Sur la page Merci (<code>app/views/landing/pages/merci.php</code>), activez l'&eacute;v&eacute;nement de conversion.
    </div>
  </div>

  <!-- ═══════════════ SECTION 4 : PIXEL GOOGLE ADS ═══════════════ -->
  <div class="guide-section" id="section-pixel">
    <h3><i class="fas fa-code"></i> Installation du pixel Google Ads</h3>

    <h4>Option A : Google Tag (gtag.js) direct</h4>
    <div class="guide-code">
<span class="comment">&lt;!-- Dans le &lt;head&gt; de layout.php --&gt;</span>
<span class="tag">&lt;script</span> async src="https://www.googletagmanager.com/gtag/js?id=<span class="string">AW-VOTRE-ID</span>"<span class="tag">&gt;&lt;/script&gt;</span>
<span class="tag">&lt;script&gt;</span>
  window.dataLayer = window.dataLayer || [];
  <span class="keyword">function</span> gtag(){dataLayer.push(arguments);}
  gtag(<span class="string">'js'</span>, <span class="keyword">new</span> Date());
  gtag(<span class="string">'config'</span>, <span class="string">'AW-VOTRE-ID'</span>);
<span class="tag">&lt;/script&gt;</span>

<span class="comment">&lt;!-- Sur la page Merci (&eacute;v&eacute;nement de conversion) --&gt;</span>
<span class="tag">&lt;script&gt;</span>
  gtag(<span class="string">'event'</span>, <span class="string">'conversion'</span>, {
    <span class="string">'send_to'</span>: <span class="string">'AW-VOTRE-ID/VOTRE-LABEL'</span>,
    <span class="string">'value'</span>: 1.0,
    <span class="string">'currency'</span>: <span class="string">'EUR'</span>
  });
<span class="tag">&lt;/script&gt;</span>
    </div>

    <h4>Option B : Google Tag Manager (recommand&eacute;)</h4>
    <div class="guide-code">
<span class="comment">&lt;!-- Dans le &lt;head&gt; --&gt;</span>
<span class="tag">&lt;script&gt;</span>
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({<span class="string">'gtm.start'</span>:
  <span class="keyword">new</span> Date().getTime(),event:<span class="string">'gtm.js'</span>});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!=<span class="string">'dataLayer'</span>?<span class="string">'&amp;l='</span>+l:<span class="string">''</span>;j.async=true;
  j.src=<span class="string">'https://www.googletagmanager.com/gtm.js?id='</span>+i+dl;
  f.parentNode.insertBefore(j,f);})(window,document,<span class="string">'script'</span>,<span class="string">'dataLayer'</span>,<span class="string">'GTM-VOTRE-ID'</span>);
<span class="tag">&lt;/script&gt;</span>

<span class="comment">&lt;!-- L'&eacute;v&eacute;nement dataLayer.push est d&eacute;j&agrave; en place sur la page Merci --&gt;</span>
<span class="comment">&lt;!-- Configurez un d&eacute;clencheur GTM sur l'&eacute;v&eacute;nement 'lead_form_submit' --&gt;</span>
    </div>
  </div>

  <!-- ═══════════════ SECTION 5 : BONNES PRATIQUES ═══════════════ -->
  <div class="guide-section" id="section-quality">
    <h3><i class="fas fa-star"></i> Bonnes pratiques Quality Score</h3>
    <p>
      Le <strong>Quality Score</strong> (note de 1 &agrave; 10) d&eacute;termine le co&ucirc;t et la position de vos annonces.
      Un score de <strong>7+</strong> r&eacute;duit votre CPC de 20-30%. Voici les 3 composantes et comment les optimiser :
    </p>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Composante</th>
          <th>Poids</th>
          <th>Comment optimiser</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Taux de clic attendu (CTR)</strong></td>
          <td>~40%</td>
          <td>Titre d'annonce accrocheur, extensions d'annonce, correspondance mot-cl&eacute;</td>
          <td>Viser "Au-dessus de la moyenne"</td>
        </tr>
        <tr>
          <td><strong>Pertinence de l'annonce</strong></td>
          <td>~25%</td>
          <td>Le mot-cl&eacute; doit appara&icirc;tre dans le titre ET la description de l'annonce</td>
          <td>Viser "Au-dessus de la moyenne"</td>
        </tr>
        <tr>
          <td><strong>Exp&eacute;rience page de destination</strong></td>
          <td>~35%</td>
          <td>Vitesse, mobile-first, coh&eacute;rence avec l'annonce, contenu pertinent</td>
          <td>Viser "Au-dessus de la moyenne"</td>
        </tr>
      </tbody>
    </table>

    <h4>Checklist d'optimisation :</h4>

    <ul class="guide-checklist">
      <li><i class="fas fa-check-circle"></i> <strong>Coh&eacute;rence mot-cl&eacute; &rarr; annonce &rarr; page :</strong> Le titre H1 de la landing page reprend le mot-cl&eacute; de l'annonce</li>
      <li><i class="fas fa-check-circle"></i> <strong>Vitesse de chargement &lt; 3 secondes :</strong> Images compress&eacute;es, CSS/JS minifi&eacute;s, pas de scripts lourds</li>
      <li><i class="fas fa-check-circle"></i> <strong>Mobile-first :</strong> 63% du trafic est mobile. Les pages sont responsive par d&eacute;faut</li>
      <li><i class="fas fa-check-circle"></i> <strong>Pas de menu de navigation :</strong> Pas de distractions, un seul objectif = le formulaire</li>
      <li><i class="fas fa-check-circle"></i> <strong>CTA visible au-dessus de la ligne de flottaison :</strong> Le formulaire est visible sans scroller</li>
      <li><i class="fas fa-check-circle"></i> <strong>Preuve sociale :</strong> T&eacute;moignages, chiffres, &eacute;toiles pour rassurer le visiteur</li>
      <li><i class="fas fa-check-circle"></i> <strong>FAQ :</strong> R&eacute;pond aux objections courantes et r&eacute;duit l'anxi&eacute;t&eacute;</li>
      <li><i class="fas fa-check-circle"></i> <strong>Transparence :</strong> Mentions l&eacute;gales, politique de confidentialit&eacute;, RGPD accessibles</li>
      <li><i class="fas fa-check-circle"></i> <strong>Formulaire court :</strong> Nom, email, t&eacute;l&eacute;phone uniquement (3 champs obligatoires)</li>
      <li><i class="fas fa-check-circle"></i> <strong>Tracking activ&eacute; :</strong> UTM + gclid + pixel de conversion sur la page Merci</li>
    </ul>
  </div>

  <!-- ═══════════════ SECTION 6 : STRUCTURE ANNONCE ═══════════════ -->
  <div class="guide-section" id="section-annonces">
    <h3><i class="fas fa-ad"></i> Structure recommand&eacute;e des annonces</h3>
    <p>
      Pour chaque page de destination, voici des exemples d'annonces Google Ads optimis&eacute;es :
    </p>

    <h4>Campagne 1 : Estimation Immobili&egrave;re Bordeaux</h4>
    <div class="guide-code">
<span class="keyword">Titre 1 :</span> <span class="string">Estimation Immobili&egrave;re Bordeaux</span>
<span class="keyword">Titre 2 :</span> <span class="string">Gratuite en 60 Secondes</span>
<span class="keyword">Titre 3 :</span> <span class="string">R&eacute;sultat Imm&eacute;diat</span>

<span class="keyword">Description 1 :</span> <span class="string">Obtenez une estimation gratuite de votre bien &agrave; Bordeaux. Donn&eacute;es du march&eacute; r&eacute;el, r&eacute;sultat en 60 secondes. Sans engagement.</span>
<span class="keyword">Description 2 :</span> <span class="string">Plus de 2 400 estimations r&eacute;alis&eacute;es &agrave; Bordeaux. Note 4.8/5. Un expert vous rappelle sous 24h.</span>

<span class="keyword">URL finale :</span> <span class="string">https://estimation-immobilier-bordeaux.fr/lp/estimation-bordeaux</span>
    </div>

    <h4>Campagne 2 : Vendre Maison Bordeaux</h4>
    <div class="guide-code">
<span class="keyword">Titre 1 :</span> <span class="string">Vendez Votre Maison &agrave; Bordeaux</span>
<span class="keyword">Titre 2 :</span> <span class="string">Estimation Gratuite du Prix</span>
<span class="keyword">Titre 3 :</span> <span class="string">Accompagnement Expert</span>

<span class="keyword">Description 1 :</span> <span class="string">Vendez votre maison au meilleur prix. Estimation gratuite bas&eacute;e sur le march&eacute; bordelais actuel. Expert local.</span>
<span class="keyword">Description 2 :</span> <span class="string">Fixez le bon prix d&egrave;s le d&eacute;part. Rappel expert sous 24h. Service 100% gratuit, sans engagement.</span>

<span class="keyword">URL finale :</span> <span class="string">https://estimation-immobilier-bordeaux.fr/lp/vendre-maison-bordeaux</span>
    </div>

    <h4>Campagne 3 : Avis de Valeur Gratuit</h4>
    <div class="guide-code">
<span class="keyword">Titre 1 :</span> <span class="string">Avis de Valeur Gratuit Bordeaux</span>
<span class="keyword">Titre 2 :</span> <span class="string">Sans Engagement</span>
<span class="keyword">Titre 3 :</span> <span class="string">Expert Immobilier Local</span>

<span class="keyword">Description 1 :</span> <span class="string">Recevez un avis de valeur gratuit pour votre bien &agrave; Bordeaux. Analyse experte bas&eacute;e sur le march&eacute; actuel.</span>
<span class="keyword">Description 2 :</span> <span class="string">Id&eacute;al pour vente, succession ou divorce. Avis professionnel affin&eacute; par un expert. R&eacute;sultat sous 24h.</span>

<span class="keyword">URL finale :</span> <span class="string">https://estimation-immobilier-bordeaux.fr/lp/avis-valeur-gratuit</span>
    </div>
  </div>

  <!-- ═══════════════ SECTION 7 : MOTS-CLÉS ═══════════════ -->
  <div class="guide-section" id="section-keywords">
    <h3><i class="fas fa-search"></i> Suggestions de mots-cl&eacute;s par campagne</h3>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Campagne</th>
          <th>Mots-cl&eacute;s (Exact / Expression)</th>
          <th>Mots-cl&eacute;s n&eacute;gatifs</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Estimation Bordeaux</strong></td>
          <td>
            [estimation immobili&egrave;re bordeaux]<br>
            [estimer mon bien bordeaux]<br>
            "estimation maison bordeaux"<br>
            "prix immobilier bordeaux"<br>
            [estimation appartement bordeaux]<br>
            "combien vaut ma maison bordeaux"
          </td>
          <td>location, louer, agent, recrutement, formation, emploi</td>
        </tr>
        <tr>
          <td><strong>Vendre Maison</strong></td>
          <td>
            [vendre maison bordeaux]<br>
            "vendre sa maison bordeaux"<br>
            [vente maison bordeaux]<br>
            "mettre en vente maison bordeaux"<br>
            "prix vente maison bordeaux"
          </td>
          <td>acheter, location, louer, construire, terrain, neuf</td>
        </tr>
        <tr>
          <td><strong>Avis de Valeur</strong></td>
          <td>
            [avis de valeur bordeaux]<br>
            [avis de valeur gratuit]<br>
            "avis de valeur immobilier bordeaux"<br>
            "estimation gratuite bordeaux"<br>
            [estimation bien immobilier gratuit bordeaux]
          </td>
          <td>location, louer, acheter, notaire (si non pertinent)</td>
        </tr>
      </tbody>
    </table>

    <div class="guide-tip guide-tip-info">
      <strong><i class="fas fa-lightbulb"></i> Conseil :</strong>
      Commencez avec des mots-cl&eacute;s en correspondance exacte <code>[mot-cl&eacute;]</code> et expression <code>"mot-cl&eacute;"</code>
      pour ma&icirc;triser vos co&ucirc;ts. &Eacute;largissez progressivement une fois que vous avez des donn&eacute;es de conversion.
    </div>
  </div>

  <!-- ═══════════════ SECTION 8 : BUDGET & ENCHÈRES ═══════════════ -->
  <div class="guide-section" id="section-budget">
    <h3><i class="fas fa-euro-sign"></i> Budget & Strat&eacute;gie d'ench&egrave;res</h3>

    <table class="guide-table">
      <thead>
        <tr>
          <th>Phase</th>
          <th>Budget / jour</th>
          <th>Strat&eacute;gie ench&egrave;res</th>
          <th>Dur&eacute;e</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Phase 1 : Test</strong></td>
          <td>10-20 &euro; / jour</td>
          <td>Maximiser les clics (pour collecter des donn&eacute;es)</td>
          <td>2-4 semaines</td>
        </tr>
        <tr>
          <td><strong>Phase 2 : Optimisation</strong></td>
          <td>20-50 &euro; / jour</td>
          <td>Maximiser les conversions (une fois 15+ conversions atteintes)</td>
          <td>4-8 semaines</td>
        </tr>
        <tr>
          <td><strong>Phase 3 : Scale</strong></td>
          <td>50+ &euro; / jour</td>
          <td>CPA cible (bas&eacute; sur votre co&ucirc;t par lead id&eacute;al)</td>
          <td>Continu</td>
        </tr>
      </tbody>
    </table>

    <div class="guide-tip guide-tip-warning">
      <strong><i class="fas fa-exclamation-triangle"></i> Attention :</strong>
      Ne passez en strat&eacute;gie "Maximiser les conversions" qu'apr&egrave;s avoir collect&eacute; au moins 15 conversions
      en 30 jours. Sinon l'algorithme Google n'a pas assez de donn&eacute;es pour optimiser efficacement.
    </div>
  </div>

  <!-- ═══════════════ SECTION 9 : À NE PAS FAIRE ═══════════════ -->
  <div class="guide-section" id="section-erreurs">
    <h3><i class="fas fa-ban"></i> Erreurs &agrave; &eacute;viter</h3>

    <ul class="guide-checklist">
      <li><i class="fas fa-times-circle"></i> <strong>Envoyer vers la page d'accueil :</strong> Toujours utiliser une landing page d&eacute;di&eacute;e, jamais la homepage</li>
      <li><i class="fas fa-times-circle"></i> <strong>Mots-cl&eacute;s en requ&ecirc;te large :</strong> En phase de test, &eacute;vitez la correspondance large qui gaspille le budget</li>
      <li><i class="fas fa-times-circle"></i> <strong>Pas de suivi de conversion :</strong> Sans tracking, impossible d'optimiser. Configurez le pixel AVANT de lancer</li>
      <li><i class="fas fa-times-circle"></i> <strong>Page lente :</strong> Si la page met plus de 3 secondes &agrave; charger, le Quality Score chute</li>
      <li><i class="fas fa-times-circle"></i> <strong>Titre d'annonce &ne; titre de page :</strong> L'incoh&eacute;rence fait baisser la pertinence et augmente le taux de rebond</li>
      <li><i class="fas fa-times-circle"></i> <strong>Formulaire trop long :</strong> Plus de 4-5 champs = chute des conversions</li>
      <li><i class="fas fa-times-circle"></i> <strong>Pas de test A/B :</strong> Cr&eacute;ez au moins 2-3 variantes d'annonce par groupe pour laisser Google optimiser</li>
      <li><i class="fas fa-times-circle"></i> <strong>Ignorer les mots-cl&eacute;s n&eacute;gatifs :</strong> Ajoutez-les r&eacute;guli&egrave;rement pour bloquer le trafic non pertinent</li>
    </ul>
  </div>

  <!-- ═══════════════ SECTION 10 : MESURER LES RÉSULTATS ═══════════════ -->
  <div class="guide-section" id="section-kpi">
    <h3><i class="fas fa-tachometer-alt"></i> KPIs &agrave; suivre</h3>

    <table class="guide-table">
      <thead>
        <tr>
          <th>KPI</th>
          <th>Objectif</th>
          <th>O&ugrave; le voir</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Quality Score</strong></td>
          <td>7+ / 10</td>
          <td>Google Ads &rarr; Mots-cl&eacute;s &rarr; Colonnes &rarr; Niveau de qualit&eacute;</td>
        </tr>
        <tr>
          <td><strong>CTR (taux de clic)</strong></td>
          <td>> 3-5%</td>
          <td>Google Ads &rarr; Campagnes</td>
        </tr>
        <tr>
          <td><strong>Taux de conversion</strong></td>
          <td>> 5-10%</td>
          <td>Google Ads &rarr; Conversions (n&eacute;cessite le pixel)</td>
        </tr>
        <tr>
          <td><strong>Co&ucirc;t par lead (CPA)</strong></td>
          <td>&lt; 20-40 &euro;</td>
          <td>Google Ads &rarr; Campagnes &rarr; Co&ucirc;t/conversion</td>
        </tr>
        <tr>
          <td><strong>ROAS</strong></td>
          <td>Positif</td>
          <td>Leads CRM &rarr; Valeur des mandats sign&eacute;s vs d&eacute;pense Ads</td>
        </tr>
      </tbody>
    </table>

    <div class="guide-tip guide-tip-success">
      <strong><i class="fas fa-check-circle"></i> Rappel :</strong>
      Tous les leads issus des landing pages Google Ads apparaissent dans votre CRM
      (<a href="/admin/leads" style="color: inherit; text-decoration: underline;">Admin &rarr; Leads</a>) avec les d&eacute;tails UTM dans les notes.
      Filtrez par source pour mesurer le ROI de chaque campagne.
    </div>
  </div>

</div>
