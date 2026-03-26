<?php
$trustConfig = function_exists('getSiteConfig') ? (array) getSiteConfig() : [];

$trustCity = isset($trustConfig['ville']) ? trim((string) $trustConfig['ville']) : '';
$trustAdvisorName = isset($trustConfig['advisor_name']) ? trim((string) $trustConfig['advisor_name']) : '';
$trustAdvisorPhoto = isset($trustConfig['advisor_photo']) ? trim((string) $trustConfig['advisor_photo']) : '';
$trustExperienceYears = isset($trustConfig['advisor_experience_years']) ? trim((string) $trustConfig['advisor_experience_years']) : '';
$trustAdvisorZone = isset($trustConfig['advisor_zone']) ? trim((string) $trustConfig['advisor_zone']) : '';
$trustAdvisorTagline = isset($trustConfig['advisor_tagline']) ? trim((string) $trustConfig['advisor_tagline']) : '';
$trustColorAccent = isset($trustConfig['color_accent']) ? trim((string) $trustConfig['color_accent']) : '';
$trustTestimonialsRaw = isset($trustConfig['testimonials']) && is_array($trustConfig['testimonials']) ? $trustConfig['testimonials'] : [];
$trustTestimonials = array_slice($trustTestimonialsRaw, 0, 3);

$trustEsc = static function (string $value): string {
    return function_exists('e') ? e($value) : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};

$trustButtonStyle = $trustColorAccent !== ''
    ? ' style="background:' . $trustEsc($trustColorAccent) . ';border-color:' . $trustEsc($trustColorAccent) . ';"'
    : '';

$trustFormatTestimonial = static function (string $text, int $limit = 120): string {
    $text = trim($text);
    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $limit - 1)) . '…';
    }

    if (strlen($text) <= $limit) {
        return $text;
    }

    return rtrim(substr($text, 0, $limit - 1)) . '…';
};

$trustDataLine = 'Données réelles' . ($trustCity !== '' ? ' ' . $trustCity : '');
?>

<section class="section trust-block" aria-label="Bloc confiance">
  <div class="container trust-block__container">
    <div class="trust-block__grid">
      <article class="trust-block__card trust-block__card--advisor">
        <div class="trust-block__advisor-head">
          <?php if ($trustAdvisorPhoto !== ''): ?>
            <img class="trust-block__advisor-photo" src="<?= $trustEsc($trustAdvisorPhoto) ?>" alt="<?= $trustEsc($trustAdvisorName !== '' ? $trustAdvisorName : 'Conseiller immobilier') ?>" loading="lazy">
          <?php else: ?>
            <div class="trust-block__advisor-photo trust-block__advisor-photo--placeholder" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5Z" fill="currentColor"/></svg>
            </div>
          <?php endif; ?>

          <div>
            <?php if ($trustAdvisorName !== '' || $trustCity !== ''): ?>
              <h3 class="trust-block__advisor-name">
                <?= $trustEsc(trim($trustAdvisorName . ' Conseiller immobilier à ' . $trustCity)) ?>
              </h3>
            <?php endif; ?>

            <?php if ($trustExperienceYears !== '' || $trustAdvisorZone !== ''): ?>
              <p class="trust-block__advisor-meta">
                <?php
                $trustMetaParts = [];
                if ($trustExperienceYears !== '') {
                    $trustMetaParts[] = $trustExperienceYears . " ans d'expérience";
                }
                if ($trustAdvisorZone !== '') {
                    $trustMetaParts[] = 'Zone : ' . $trustAdvisorZone;
                }
                echo $trustEsc(implode(' · ', $trustMetaParts));
                ?>
              </p>
            <?php endif; ?>

            <?php if ($trustAdvisorTagline !== ''): ?>
              <p class="trust-block__advisor-tagline"><?= $trustEsc($trustAdvisorTagline) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <a href="/contact" class="trust-block__cta"<?= $trustButtonStyle ?>>Me contacter directement</a>
      </article>

      <article class="trust-block__card trust-block__card--testimonials">
        <h3 class="trust-block__title">Avis clients</h3>

        <?php if (!empty($trustTestimonials)): ?>
          <div class="trust-block__testimonials-list">
            <?php foreach ($trustTestimonials as $trustItem): ?>
              <?php
              $trustName = isset($trustItem['name']) ? trim((string) $trustItem['name']) : '';
              $trustDistrict = isset($trustItem['quartier']) ? trim((string) $trustItem['quartier']) : '';
              $trustText = isset($trustItem['text']) ? (string) $trustItem['text'] : '';
              $trustStars = isset($trustItem['stars']) ? max(0, min(5, (int) $trustItem['stars'])) : 0;
              $trustDisplayText = $trustFormatTestimonial($trustText);
              ?>
              <article class="trust-block__testimonial-card">
                <div class="trust-block__stars" aria-label="<?= $trustEsc((string) $trustStars) ?> étoiles sur 5">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="trust-block__star <?= $i <= $trustStars ? 'trust-block__star--active' : '' ?>" viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 17.3 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                  <?php endfor; ?>
                </div>

                <?php if ($trustDisplayText !== ''): ?>
                  <p class="trust-block__testimonial-text"><?= $trustEsc($trustDisplayText) ?></p>
                <?php endif; ?>

                <?php if ($trustName !== '' || $trustDistrict !== ''): ?>
                  <p class="trust-block__testimonial-author"><?= $trustEsc(trim($trustName . ($trustDistrict !== '' ? ' · ' . $trustDistrict : ''))) ?></p>
                <?php endif; ?>
              </article>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="trust-block__testimonial-card">
            <div class="trust-block__stars" aria-label="5 étoiles sur 5">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <svg class="trust-block__star trust-block__star--active" viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 17.3 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
              <?php endfor; ?>
            </div>
            <p class="trust-block__testimonial-text">Des propriétaires de <?= $trustEsc($trustCity !== '' ? $trustCity : 'votre secteur') ?> recommandent notre accompagnement.</p>
          </div>
        <?php endif; ?>
      </article>

      <article class="trust-block__card trust-block__card--reassurance">
        <h3 class="trust-block__title">Réassurance</h3>

        <ul class="trust-block__reassurance-list">
          <li>
            <span class="trust-block__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2 3 6v6c0 5 3.8 9.7 9 11 5.2-1.3 9-6 9-11V6l-9-4Zm0 11h7a9.2 9.2 0 0 1-7 8 9.2 9.2 0 0 1-7-8V7.3l7-3.1 7 3.1V11h-7v2Z" fill="currentColor"/></svg>
            </span>
            <span><?= $trustEsc($trustDataLine) ?></span>
          </li>
          <li>
            <span class="trust-block__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 1a11 11 0 1 0 11 11A11 11 0 0 0 12 1Zm1 6v5.6l4.2 2.5-1 1.7L11 13.5V7Z" fill="currentColor"/></svg>
            </span>
            <span>Réponse sous 24h</span>
          </li>
          <li>
            <span class="trust-block__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 13H5v6h14v-6ZM5 11h14V5H5v6Zm-2 8V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z" fill="currentColor"/></svg>
            </span>
            <span>Sans obligation de mandat</span>
          </li>
        </ul>
      </article>
    </div>
  </div>
</section>
