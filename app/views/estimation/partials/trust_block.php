<?php
$siteConfig = function_exists('getSiteConfig') ? (array) getSiteConfig() : [];

$trustVille = isset($siteConfig['ville']) ? trim((string) $siteConfig['ville']) : '';
$advisorName = isset($siteConfig['advisor_name']) ? trim((string) $siteConfig['advisor_name']) : '';
$advisorPhoto = isset($siteConfig['advisor_photo']) ? trim((string) $siteConfig['advisor_photo']) : '';
$advisorExperienceYears = isset($siteConfig['advisor_experience_years']) ? trim((string) $siteConfig['advisor_experience_years']) : '';
$advisorZone = isset($siteConfig['advisor_zone']) ? trim((string) $siteConfig['advisor_zone']) : '';
$advisorTagline = isset($siteConfig['advisor_tagline']) ? trim((string) $siteConfig['advisor_tagline']) : '';
$accentColor = isset($siteConfig['color_accent']) ? trim((string) $siteConfig['color_accent']) : '';
$testimonials = isset($siteConfig['testimonials']) && is_array($siteConfig['testimonials']) ? $siteConfig['testimonials'] : [];

$advisorTitleParts = [];
if ($advisorName !== '') {
    $advisorTitleParts[] = $advisorName;
}
if ($trustVille !== '') {
    $advisorTitleParts[] = 'Conseiller immobilier à ' . $trustVille;
}
$advisorTitle = trim(implode(' · ', $advisorTitleParts));

$experienceParts = [];
if ($advisorExperienceYears !== '') {
    $experienceParts[] = $advisorExperienceYears . " ans d'expérience";
}
if ($advisorZone !== '') {
    $experienceParts[] = 'Zone : ' . $advisorZone;
}
$experienceLine = trim(implode(' · ', $experienceParts));

$visibleTestimonials = array_slice($testimonials, 0, 3);
$buttonStyle = $accentColor !== '' ? ' style="background:' . e($accentColor) . ';border-color:' . e($accentColor) . ';"' : '';
?>

<section class="section trust-block" aria-label="Bloc de confiance">
  <div class="container trust-block__container">
    <div class="trust-block__grid">
      <article class="trust-block__card trust-block__card--advisor">
        <?php if ($advisorPhoto !== ''): ?>
          <div class="trust-block__avatar-wrap">
            <img src="<?= e($advisorPhoto) ?>" alt="<?= $advisorName !== '' ? e($advisorName) : 'Conseiller immobilier' ?>" class="trust-block__avatar">
          </div>
        <?php endif; ?>

        <?php if ($advisorTitle !== ''): ?>
          <h3 class="trust-block__title"><?= e($advisorTitle) ?></h3>
        <?php endif; ?>

        <?php if ($experienceLine !== ''): ?>
          <p class="trust-block__meta"><?= e($experienceLine) ?></p>
        <?php endif; ?>

        <?php if ($advisorTagline !== ''): ?>
          <p class="trust-block__tagline"><?= e($advisorTagline) ?></p>
        <?php endif; ?>

        <a href="/contact" class="btn trust-block__btn"<?= $buttonStyle ?>>Me contacter directement</a>
      </article>

      <article class="trust-block__card trust-block__card--testimonials">
        <h3 class="trust-block__title">Ils nous ont confié leur projet</h3>

        <?php if (!empty($visibleTestimonials)): ?>
          <div class="trust-block__testimonials-list">
            <?php foreach ($visibleTestimonials as $testimonial): ?>
              <?php
                $testimonialName = isset($testimonial['name']) ? trim((string) $testimonial['name']) : '';
                $testimonialDistrict = isset($testimonial['quartier']) ? trim((string) $testimonial['quartier']) : '';
                $testimonialText = isset($testimonial['text']) ? trim((string) $testimonial['text']) : '';
                $testimonialStars = isset($testimonial['stars']) ? (int) $testimonial['stars'] : 0;
                $testimonialStars = max(0, min(5, $testimonialStars));

                if ($testimonialText !== '') {
                    $maxLength = 120;
                    if ((function_exists('mb_strlen') && mb_strlen($testimonialText, 'UTF-8') > $maxLength) || (!function_exists('mb_strlen') && strlen($testimonialText) > $maxLength)) {
                        $testimonialText = function_exists('mb_substr')
                            ? rtrim(mb_substr($testimonialText, 0, $maxLength, 'UTF-8')) . '…'
                            : rtrim(substr($testimonialText, 0, $maxLength)) . '…';
                    }
                }

                $identityParts = [];
                if ($testimonialName !== '') {
                    $identityParts[] = $testimonialName;
                }
                if ($testimonialDistrict !== '') {
                    $identityParts[] = $testimonialDistrict;
                }
                $testimonialIdentity = implode(' · ', $identityParts);
              ?>

              <div class="trust-block__testimonial-item">
                <div class="trust-block__stars" aria-label="<?= e((string) $testimonialStars) ?> étoiles sur 5">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="trust-block__star<?= $i <= $testimonialStars ? ' is-filled' : '' ?>" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path d="M12 2.75l2.83 5.74 6.34.92-4.59 4.47 1.08 6.31L12 17.23l-5.66 2.96 1.08-6.31L2.83 9.41l6.34-.92L12 2.75z"/>
                    </svg>
                  <?php endfor; ?>
                </div>

                <?php if ($testimonialText !== ''): ?>
                  <p class="trust-block__testimonial-text">“<?= e($testimonialText) ?>”</p>
                <?php endif; ?>

                <?php if ($testimonialIdentity !== ''): ?>
                  <p class="trust-block__testimonial-meta"><?= e($testimonialIdentity) ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="trust-block__empty">
            <?php if ($trustVille !== ''): ?>
              Les vendeurs de <?= e($trustVille) ?> apprécient l'accompagnement local et personnalisé.
            <?php else: ?>
              Nos clients apprécient l'accompagnement local et personnalisé.
            <?php endif; ?>
          </p>
        <?php endif; ?>
      </article>

      <article class="trust-block__card trust-block__card--reassurance">
        <h3 class="trust-block__title">Pourquoi avancer avec nous ?</h3>
        <ul class="trust-block__reassurance-list">
          <li class="trust-block__reassurance-item">
            <span class="trust-block__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/></svg>
            </span>
            <span>
              Données réelles
              <?php if ($trustVille !== ''): ?>
                <?= e($trustVille) ?>
              <?php endif; ?>
            </span>
          </li>
          <li class="trust-block__reassurance-item">
            <span class="trust-block__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M12 1.75A10.25 10.25 0 112.75 12 10.26 10.26 0 0112 1.75zm1 5.5h-2V13l4.75 2.85 1-1.64L13 11.98z"/></svg>
            </span>
            <span>Réponse sous 24h</span>
          </li>
          <li class="trust-block__reassurance-item">
            <span class="trust-block__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M12 2l8 4v6c0 5.25-3.44 10.74-8 12-4.56-1.26-8-6.75-8-12V6l8-4zm0 5a3 3 0 00-3 3v1H8v7h8v-7h-1v-1a3 3 0 00-3-3zm1 4h-2v-1a1 1 0 112 0v1z"/></svg>
            </span>
            <span>Sans obligation de mandat</span>
          </li>
        </ul>
      </article>
    </div>
  </div>
</section>
