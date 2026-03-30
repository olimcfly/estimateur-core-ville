</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
(function () {
  const toggle = document.querySelector('[data-nav-toggle]');
  const menu = document.querySelector('[data-nav-menu]');
  if (!toggle || !menu) return;

  toggle.addEventListener('click', function () {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!expanded));
    menu.classList.toggle('is-open');
  });
})();
</script>

<?php
  $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
  $canonicalPath = (string) parse_url($requestUri, PHP_URL_PATH);
  $canonicalPath = $canonicalPath !== '' ? $canonicalPath : '/';
?>
<?php include __DIR__ . '/sticky-cta.php'; ?>
</body>
</html>
