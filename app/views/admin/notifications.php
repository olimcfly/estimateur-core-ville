<div class="container">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
    <div>
      <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;margin:0 0 0.25rem;">Notifications Internes</h1>
      <p style="color:#6b6459;font-size:0.9rem;margin:0;">
        Notifications visibles uniquement dans le panneau d'administration.
        <?php if (($unread_count ?? 0) > 0): ?>
          <span style="background:#8B1538;color:#fff;padding:0.15rem 0.5rem;border-radius:10px;font-size:0.78rem;font-weight:600;margin-left:0.5rem;">
            <?= (int) $unread_count ?> non lue(s)
          </span>
        <?php endif; ?>
      </p>
    </div>
    <div style="display:flex;gap:0.5rem;">
      <button onclick="markAllRead()" style="padding:0.5rem 1rem;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:0.85rem;">
        <i class="fas fa-check-double"></i> Tout marquer comme lu
      </button>
    </div>
  </div>

  <?php if (empty($notifications)): ?>
    <div class="card" style="padding:3rem;text-align:center;">
      <i class="fas fa-bell-slash" style="font-size:2.5rem;color:#e8dfd7;margin-bottom:1rem;"></i>
      <h3 style="color:#6b6459;margin:0 0 0.5rem;">Aucune notification</h3>
      <p style="color:#999;font-size:0.9rem;">Les notifications internes apparaitront ici lorsqu'elles seront generees.</p>
    </div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:0.5rem;">
      <?php foreach ($notifications as $n): ?>
        <?php
          $nId = (int) $n['id'];
          $isRead = (bool) $n['is_read'];
          $type = $n['type'];
          $title = htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8');
          $message = htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8');
          $link = $n['link'] ? htmlspecialchars($n['link'], ENT_QUOTES, 'UTF-8') : '';
          $createdAt = date('d/m/Y H:i', strtotime($n['created_at']));

          $typeIcon = match ($type) {
              'lead' => 'fa-user-plus',
              'success' => 'fa-check-circle',
              'warning' => 'fa-exclamation-triangle',
              'error' => 'fa-times-circle',
              'system' => 'fa-cog',
              default => 'fa-info-circle',
          };
          $typeColor = match ($type) {
              'lead' => '#8B1538',
              'success' => '#22c55e',
              'warning' => '#f97316',
              'error' => '#e24b4a',
              'system' => '#6b6459',
              default => '#3b82f6',
          };
        ?>
        <div class="card" style="padding:1rem 1.25rem;display:flex;align-items:flex-start;gap:1rem;<?= $isRead ? 'opacity:0.7;' : 'border-left:3px solid ' . $typeColor . ';' ?>" id="notif-<?= $nId ?>">
          <div style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:<?= $typeColor ?>15;border-radius:8px;flex-shrink:0;">
            <i class="fas <?= $typeIcon ?>" style="color:<?= $typeColor ?>;font-size:0.95rem;"></i>
          </div>
          <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.2rem;">
              <strong style="font-size:0.9rem;color:#1a1410;"><?= $title ?></strong>
              <?php if (!$isRead): ?>
                <span style="width:8px;height:8px;background:<?= $typeColor ?>;border-radius:50;display:inline-block;"></span>
              <?php endif; ?>
            </div>
            <?php if ($message): ?>
              <p style="color:#6b6459;font-size:0.82rem;margin:0 0 0.35rem;line-height:1.4;"><?= $message ?></p>
            <?php endif; ?>
            <div style="display:flex;align-items:center;gap:1rem;font-size:0.78rem;color:#999;">
              <span><i class="far fa-clock"></i> <?= $createdAt ?></span>
              <?php if ($link): ?>
                <a href="<?= $link ?>" style="color:#8B1538;text-decoration:none;font-weight:600;" onclick="markRead(<?= $nId ?>)">
                  <i class="fas fa-external-link-alt"></i> Voir
                </a>
              <?php endif; ?>
              <?php if (!$isRead): ?>
                <button onclick="markRead(<?= $nId ?>)" style="background:none;border:none;color:#3b82f6;cursor:pointer;font-size:0.78rem;padding:0;">
                  <i class="fas fa-check"></i> Marquer comme lu
                </button>
              <?php endif; ?>
              <button onclick="deleteNotif(<?= $nId ?>)" style="background:none;border:none;color:#e24b4a;cursor:pointer;font-size:0.78rem;padding:0;">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<script>
function markRead(id) {
  var fd = new FormData();
  fd.append('id', id);
  fetch('/admin/notifications/read', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        var el = document.getElementById('notif-' + id);
        if (el) el.style.opacity = '0.7';
      }
    });
}

function markAllRead() {
  fetch('/admin/notifications/read-all', { method: 'POST', credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) location.reload(); });
}

function deleteNotif(id) {
  var fd = new FormData();
  fd.append('id', id);
  fetch('/admin/notifications/delete', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        var el = document.getElementById('notif-' + id);
        if (el) el.remove();
      }
    });
}
</script>
