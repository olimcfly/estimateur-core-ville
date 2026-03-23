<div class="container">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <div>
      <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;margin:0 0 0.25rem;">Gestion des Modules</h1>
      <p style="color:#6b6459;font-size:0.9rem;margin:0;">Activez ou desactivez les fonctionnalites de la plateforme. Seul le super-utilisateur peut modifier ces parametres.</p>
    </div>
    <button onclick="seedModules()" style="padding:0.5rem 1rem;background:#8B1538;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:0.85rem;">
      <i class="fas fa-sync-alt"></i> Reinitialiser
    </button>
  </div>

  <?php
  $categoryLabels = $categories ?? [];
  $groupedModules = $grouped ?? [];
  $categoryOrder = ['principal', 'contenu', 'communication', 'notifications', 'marketing', 'outils', 'systeme', 'general'];
  ?>

  <?php foreach ($categoryOrder as $catKey): ?>
    <?php if (empty($groupedModules[$catKey])) continue; ?>
    <div style="margin-bottom:2rem;">
      <h2 style="font-size:1rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b6459;margin:0 0 1rem;padding-bottom:0.5rem;border-bottom:1px solid #e8dfd7;">
        <?= htmlspecialchars($categoryLabels[$catKey] ?? ucfirst($catKey), ENT_QUOTES, 'UTF-8') ?>
      </h2>

      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1rem;">
        <?php foreach ($groupedModules[$catKey] as $mod): ?>
          <?php
            $isActive = (bool) $mod['is_active'];
            $isSuperOnly = (bool) $mod['superuser_only'];
            $slug = htmlspecialchars($mod['slug'], ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($mod['name'], ENT_QUOTES, 'UTF-8');
            $desc = htmlspecialchars($mod['description'], ENT_QUOTES, 'UTF-8');
            $icon = htmlspecialchars($mod['icon'], ENT_QUOTES, 'UTF-8');
          ?>
          <div class="card" id="module-<?= $slug ?>" style="padding:1.25rem;display:flex;align-items:flex-start;gap:1rem;<?= $isActive ? '' : 'opacity:0.6;' ?>">
            <div style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;background:<?= $isActive ? 'rgba(139,21,56,0.08)' : 'rgba(0,0,0,0.05)' ?>;border-radius:10px;flex-shrink:0;">
              <i class="fas <?= $icon ?>" style="color:<?= $isActive ? '#8B1538' : '#999' ?>;font-size:1.1rem;"></i>
            </div>
            <div style="flex:1;min-width:0;">
              <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;">
                <strong style="font-size:0.95rem;color:#1a1410;"><?= $name ?></strong>
                <?php if ($isSuperOnly): ?>
                  <span style="background:#f97316;color:#fff;font-size:0.65rem;padding:0.1rem 0.4rem;border-radius:4px;font-weight:600;">SUPER</span>
                <?php endif; ?>
              </div>
              <p style="color:#6b6459;font-size:0.82rem;margin:0 0 0.75rem;line-height:1.4;"><?= $desc ?></p>
              <div style="display:flex;align-items:center;gap:0.75rem;">
                <label style="position:relative;display:inline-block;width:44px;height:24px;cursor:pointer;">
                  <input type="checkbox" <?= $isActive ? 'checked' : '' ?> onchange="toggleModule('<?= $slug ?>', this.checked)"
                    style="opacity:0;width:0;height:0;position:absolute;">
                  <span style="position:absolute;top:0;left:0;right:0;bottom:0;background:<?= $isActive ? '#22c55e' : '#ccc' ?>;border-radius:12px;transition:0.3s;"></span>
                  <span style="position:absolute;top:2px;left:<?= $isActive ? '22px' : '2px' ?>;width:20px;height:20px;background:#fff;border-radius:50%;transition:0.3s;box-shadow:0 1px 3px rgba(0,0,0,0.15);"></span>
                </label>
                <span style="font-size:0.8rem;color:<?= $isActive ? '#22c55e' : '#999' ?>;font-weight:600;">
                  <?= $isActive ? 'Actif' : 'Inactif' ?>
                </span>

                <?php if (!$isSuperOnly): ?>
                  <label style="font-size:0.78rem;color:#6b6459;margin-left:auto;cursor:pointer;display:flex;align-items:center;gap:0.3rem;">
                    <input type="checkbox" <?= $isSuperOnly ? 'checked' : '' ?> onchange="toggleSuperOnly('<?= $slug ?>', this.checked)"
                      style="width:14px;height:14px;">
                    Super-user seul
                  </label>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

</div>

<script>
function toggleModule(slug, active) {
  var fd = new FormData();
  fd.append('slug', slug);
  fd.append('active', active ? '1' : '0');
  fetch('/admin/modules/toggle', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        location.reload();
      } else {
        alert(data.error || 'Erreur');
      }
    })
    .catch(function() { alert('Erreur reseau'); });
}

function toggleSuperOnly(slug, superOnly) {
  var fd = new FormData();
  fd.append('slug', slug);
  fd.append('superuser_only', superOnly ? '1' : '0');
  fetch('/admin/modules/update', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) alert(data.error || 'Erreur');
    })
    .catch(function() { alert('Erreur reseau'); });
}

function seedModules() {
  if (!confirm('Reinitialiser les modules par defaut ? Les modules existants ne seront pas modifies.')) return;
  fetch('/admin/modules/seed', { method: 'POST', credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) location.reload();
      else alert(data.error || 'Erreur');
    })
    .catch(function() { alert('Erreur reseau'); });
}
</script>
