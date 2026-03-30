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

<?php include __DIR__ . '/sticky-cta.php'; ?>
</body>
</html>
