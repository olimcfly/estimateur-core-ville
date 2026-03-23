<div class="container">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
    <div>
      <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;margin:0 0 0.25rem;">Gestion des Utilisateurs</h1>
      <p style="color:#6b6459;font-size:0.9rem;margin:0;">Gerez les comptes administrateurs et leurs roles d'acces.</p>
    </div>
    <button onclick="document.getElementById('add-user-modal').style.display='flex'" style="padding:0.6rem 1.2rem;background:#8B1538;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:0.85rem;font-weight:600;">
      <i class="fas fa-user-plus"></i> Ajouter un utilisateur
    </button>
  </div>

  <!-- Role legend -->
  <div style="display:flex;gap:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;">
      <span style="display:inline-block;width:12px;height:12px;background:#D4AF37;border-radius:50%;"></span>
      <strong>Super-utilisateur</strong> — Acces complet, gestion des modules et utilisateurs
    </div>
    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;">
      <span style="display:inline-block;width:12px;height:12px;background:#3b82f6;border-radius:50%;"></span>
      <strong>Administrateur</strong> — Acces aux modules autorises uniquement
    </div>
  </div>

  <!-- Users table -->
  <div class="card" style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:0.9rem;">
      <thead>
        <tr style="background:#faf9f7;border-bottom:2px solid #e8dfd7;">
          <th style="text-align:left;padding:0.75rem 1rem;font-weight:600;color:#6b6459;">Utilisateur</th>
          <th style="text-align:left;padding:0.75rem 1rem;font-weight:600;color:#6b6459;">Email</th>
          <th style="text-align:center;padding:0.75rem 1rem;font-weight:600;color:#6b6459;">Role</th>
          <th style="text-align:center;padding:0.75rem 1rem;font-weight:600;color:#6b6459;">Statut</th>
          <th style="text-align:center;padding:0.75rem 1rem;font-weight:600;color:#6b6459;">Cree le</th>
          <th style="text-align:center;padding:0.75rem 1rem;font-weight:600;color:#6b6459;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <?php
            $uid = (int) $u['id'];
            $uEmail = htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8');
            $uName = htmlspecialchars($u['name'] ?: $u['email'], ENT_QUOTES, 'UTF-8');
            $uRole = $u['role'] ?? 'admin';
            $uActive = (bool) ($u['is_active'] ?? true);
            $isSelf = strtolower($u['email']) === strtolower((string) ($_SESSION['admin_user_email'] ?? ''));
          ?>
          <tr style="border-bottom:1px solid #e8dfd7;" id="user-row-<?= $uid ?>">
            <td style="padding:0.75rem 1rem;">
              <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:36px;height:36px;border-radius:50%;background:<?= $uRole === 'superuser' ? 'linear-gradient(135deg,#D4AF37,#B8941F)' : 'linear-gradient(135deg,#3b82f6,#2563eb)' ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                  <?= strtoupper(mb_substr($uName, 0, 1)) ?>
                </div>
                <div>
                  <div style="font-weight:600;color:#1a1410;"><?= $uName ?></div>
                  <?php if ($isSelf): ?>
                    <span style="font-size:0.7rem;color:#22c55e;font-weight:600;">(vous)</span>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td style="padding:0.75rem 1rem;color:#6b6459;"><?= $uEmail ?></td>
            <td style="padding:0.75rem 1rem;text-align:center;">
              <select onchange="updateUser(<?= $uid ?>, 'role', this.value)" <?= $isSelf ? 'disabled' : '' ?>
                style="padding:0.3rem 0.5rem;border:1px solid #e8dfd7;border-radius:4px;font-size:0.82rem;background:#fff;<?= $isSelf ? 'opacity:0.5;' : '' ?>">
                <option value="superuser" <?= $uRole === 'superuser' ? 'selected' : '' ?>>Super-utilisateur</option>
                <option value="admin" <?= $uRole === 'admin' ? 'selected' : '' ?>>Administrateur</option>
              </select>
            </td>
            <td style="padding:0.75rem 1rem;text-align:center;">
              <?php if ($uActive): ?>
                <span style="background:#dcfce7;color:#166534;padding:0.2rem 0.6rem;border-radius:10px;font-size:0.78rem;font-weight:600;">Actif</span>
              <?php else: ?>
                <span style="background:#fef2f2;color:#991b1b;padding:0.2rem 0.6rem;border-radius:10px;font-size:0.78rem;font-weight:600;">Inactif</span>
              <?php endif; ?>
            </td>
            <td style="padding:0.75rem 1rem;text-align:center;color:#6b6459;font-size:0.82rem;">
              <?= date('d/m/Y', strtotime($u['created_at'])) ?>
            </td>
            <td style="padding:0.75rem 1rem;text-align:center;">
              <?php if (!$isSelf): ?>
                <div style="display:flex;gap:0.5rem;justify-content:center;">
                  <?php if ($uRole !== 'superuser'): ?>
                  <button onclick="openUserModules(<?= $uid ?>)" title="Gerer les modules"
                    style="padding:0.35rem 0.6rem;background:#eff6ff;color:#1d4ed8;border:1px solid #93c5fd;border-radius:4px;cursor:pointer;font-size:0.8rem;">
                    <i class="fas fa-puzzle-piece"></i>
                  </button>
                  <?php endif; ?>
                  <?php if ($uActive): ?>
                    <button onclick="updateUser(<?= $uid ?>, 'is_active', '0')" title="Desactiver"
                      style="padding:0.35rem 0.6rem;background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;border-radius:4px;cursor:pointer;font-size:0.8rem;">
                      <i class="fas fa-ban"></i>
                    </button>
                  <?php else: ?>
                    <button onclick="updateUser(<?= $uid ?>, 'is_active', '1')" title="Activer"
                      style="padding:0.35rem 0.6rem;background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:4px;cursor:pointer;font-size:0.8rem;">
                      <i class="fas fa-check"></i>
                    </button>
                  <?php endif; ?>
                  <button onclick="deleteUser(<?= $uid ?>, '<?= $uEmail ?>')" title="Supprimer"
                    style="padding:0.35rem 0.6rem;background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;border-radius:4px;cursor:pointer;font-size:0.8rem;">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              <?php else: ?>
                <span style="color:#999;font-size:0.8rem;">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Add User Modal -->
<div id="add-user-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:2rem;width:min(460px,90vw);box-shadow:0 8px 30px rgba(0,0,0,0.15);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
      <h3 style="margin:0;font-size:1.15rem;color:#1a1410;">Ajouter un utilisateur</h3>
      <button onclick="document.getElementById('add-user-modal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#999;">&times;</button>
    </div>
    <form onsubmit="createUser(event)">
      <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;color:#1a1410;margin-bottom:0.35rem;">Email *</label>
        <input type="email" id="new-user-email" required style="width:100%;padding:0.55rem 0.75rem;border:1px solid #e8dfd7;border-radius:6px;font-size:0.9rem;">
      </div>
      <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;color:#1a1410;margin-bottom:0.35rem;">Nom</label>
        <input type="text" id="new-user-name" style="width:100%;padding:0.55rem 0.75rem;border:1px solid #e8dfd7;border-radius:6px;font-size:0.9rem;" placeholder="Optionnel">
      </div>
      <div style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;color:#1a1410;margin-bottom:0.35rem;">Role</label>
        <select id="new-user-role" style="width:100%;padding:0.55rem 0.75rem;border:1px solid #e8dfd7;border-radius:6px;font-size:0.9rem;">
          <option value="admin">Administrateur</option>
          <option value="superuser">Super-utilisateur</option>
        </select>
      </div>
      <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
        <button type="button" onclick="document.getElementById('add-user-modal').style.display='none'"
          style="padding:0.55rem 1.2rem;background:#f4f1ed;color:#1a1410;border:1px solid #e8dfd7;border-radius:6px;cursor:pointer;font-size:0.85rem;">
          Annuler
        </button>
        <button type="submit" style="padding:0.55rem 1.2rem;background:#8B1538;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:0.85rem;font-weight:600;">
          <i class="fas fa-user-plus"></i> Creer
        </button>
      </div>
    </form>
  </div>
</div>

<!-- User Modules Modal -->
<div id="user-modules-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:2rem;width:min(580px,90vw);max-height:85vh;overflow-y:auto;box-shadow:0 8px 30px rgba(0,0,0,0.15);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
      <div>
        <h3 style="margin:0;font-size:1.15rem;color:#1a1410;">Modules autorises</h3>
        <p style="margin:0.25rem 0 0;font-size:0.82rem;color:#6b6459;" id="user-modules-subtitle"></p>
      </div>
      <button onclick="document.getElementById('user-modules-modal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#999;">&times;</button>
    </div>
    <div id="user-modules-list" style="margin-bottom:1.5rem;">
      <div style="padding:2rem;text-align:center;color:#999;">Chargement...</div>
    </div>
    <div style="display:flex;gap:0.75rem;justify-content:space-between;align-items:center;">
      <div style="display:flex;gap:0.5rem;">
        <button type="button" onclick="selectAllUserModules(true)" style="padding:0.4rem 0.8rem;background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:6px;cursor:pointer;font-size:0.8rem;">
          Tout activer
        </button>
        <button type="button" onclick="selectAllUserModules(false)" style="padding:0.4rem 0.8rem;background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;border-radius:6px;cursor:pointer;font-size:0.8rem;">
          Tout desactiver
        </button>
      </div>
      <div style="display:flex;gap:0.75rem;">
        <button type="button" onclick="document.getElementById('user-modules-modal').style.display='none'"
          style="padding:0.55rem 1.2rem;background:#f4f1ed;color:#1a1410;border:1px solid #e8dfd7;border-radius:6px;cursor:pointer;font-size:0.85rem;">
          Annuler
        </button>
        <button type="button" onclick="saveUserModules()" style="padding:0.55rem 1.2rem;background:#8B1538;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:0.85rem;font-weight:600;">
          <i class="fas fa-save"></i> Enregistrer
        </button>
      </div>
    </div>
  </div>
</div>

<script>
var _currentModulesUserId = 0;

function openUserModules(userId) {
  _currentModulesUserId = userId;
  document.getElementById('user-modules-modal').style.display = 'flex';
  document.getElementById('user-modules-list').innerHTML = '<div style="padding:2rem;text-align:center;color:#999;">Chargement...</div>';

  fetch('/admin/users/modules/' + userId, { credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) { alert(data.error || 'Erreur'); return; }

      document.getElementById('user-modules-subtitle').textContent = data.user.name + ' (' + data.user.email + ')';

      var cats = {};
      var catLabels = { principal: 'Principal', contenu: 'Contenu', communication: 'Communication', notifications: 'Notifications', marketing: 'Marketing', general: 'General' };

      data.modules.forEach(function(m) {
        var c = m.category || 'general';
        if (!cats[c]) cats[c] = [];
        cats[c].push(m);
      });

      var html = '';
      var catOrder = ['principal', 'contenu', 'communication', 'notifications', 'marketing', 'general'];
      catOrder.forEach(function(catKey) {
        if (!cats[catKey]) return;
        html += '<div style="margin-bottom:1rem;">';
        html += '<h4 style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b6459;margin:0 0 0.5rem;padding-bottom:0.3rem;border-bottom:1px solid #e8dfd7;">' + (catLabels[catKey] || catKey) + '</h4>';

        cats[catKey].forEach(function(m) {
          var disabled = !m.is_active;
          var checked = m.enabled && m.is_active;
          html += '<label style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 0.75rem;border-radius:6px;cursor:' + (disabled ? 'not-allowed' : 'pointer') + ';' + (disabled ? 'opacity:0.4;' : '') + '">';
          html += '<input type="checkbox" class="user-mod-cb" data-slug="' + m.slug + '" ' + (checked ? 'checked' : '') + ' ' + (disabled ? 'disabled' : '') + ' style="width:16px;height:16px;">';
          html += '<i class="fas ' + m.icon + '" style="width:20px;text-align:center;color:#8B1538;font-size:0.9rem;"></i>';
          html += '<span style="font-size:0.88rem;color:#1a1410;">' + m.name + '</span>';
          if (disabled) html += '<span style="font-size:0.7rem;color:#999;margin-left:auto;">Module desactive</span>';
          html += '</label>';
        });

        html += '</div>';
      });

      document.getElementById('user-modules-list').innerHTML = html;
    })
    .catch(function() { alert('Erreur reseau'); });
}

function selectAllUserModules(state) {
  document.querySelectorAll('.user-mod-cb:not(:disabled)').forEach(function(cb) { cb.checked = state; });
}

function saveUserModules() {
  var fd = new FormData();
  fd.append('user_id', _currentModulesUserId);
  document.querySelectorAll('.user-mod-cb:checked').forEach(function(cb) {
    fd.append('modules[]', cb.dataset.slug);
  });
  fetch('/admin/users/modules/save', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        document.getElementById('user-modules-modal').style.display = 'none';
        alert(data.message);
      } else {
        alert(data.error || 'Erreur');
      }
    })
    .catch(function() { alert('Erreur reseau'); });
}

function createUser(e) {
  e.preventDefault();
  var fd = new FormData();
  fd.append('email', document.getElementById('new-user-email').value);
  fd.append('name', document.getElementById('new-user-name').value);
  fd.append('role', document.getElementById('new-user-role').value);
  fetch('/admin/users/create', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) { location.reload(); }
      else { alert(data.error || 'Erreur'); }
    })
    .catch(function() { alert('Erreur reseau'); });
}

function updateUser(id, field, value) {
  var fd = new FormData();
  fd.append('id', id);
  fd.append(field, value);
  fetch('/admin/users/update', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) { location.reload(); }
      else { alert(data.error || 'Erreur'); }
    })
    .catch(function() { alert('Erreur reseau'); });
}

function deleteUser(id, email) {
  if (!confirm('Supprimer l\'utilisateur ' + email + ' ?')) return;
  var fd = new FormData();
  fd.append('id', id);
  fetch('/admin/users/delete', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) { location.reload(); }
      else { alert(data.error || 'Erreur'); }
    })
    .catch(function() { alert('Erreur reseau'); });
}
</script>
