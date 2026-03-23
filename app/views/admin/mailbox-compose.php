<style>
  .compose-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
  }
  .compose-back {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 1rem;
    background: var(--admin-surface, #fff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    color: var(--admin-text, #1a1410);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.15s;
  }
  .compose-back:hover { border-color: var(--admin-primary, #8B1538); color: var(--admin-primary); }

  .compose-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
  }

  .compose-card {
    background: var(--admin-surface, #fff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    overflow: hidden;
  }

  .compose-field {
    display: flex;
    align-items: center;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
    padding: 0;
  }
  .compose-field label {
    padding: 0.7rem 1rem;
    min-width: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--admin-muted, #6b6459);
    background: #faf9f7;
    border-right: 1px solid var(--admin-border, #e8dfd7);
  }
  .compose-field input {
    flex: 1;
    padding: 0.7rem 1rem;
    border: none;
    font-size: 0.88rem;
    font-family: inherit;
    outline: none;
    background: transparent;
  }

  .compose-from-info {
    padding: 0.7rem 1rem;
    flex: 1;
    font-size: 0.85rem;
    color: var(--admin-muted, #6b6459);
  }

  .compose-body-container {
    padding: 0;
  }

  .compose-toolbar {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem;
    background: #faf9f7;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
  }
  .compose-toolbar button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.35rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
    color: var(--admin-muted, #6b6459);
    transition: all 0.1s;
  }
  .compose-toolbar button:hover {
    background: rgba(0,0,0,0.06);
    color: var(--admin-text, #1a1410);
  }

  #compose-editor {
    min-height: 350px;
    padding: 1.25rem;
    font-size: 0.92rem;
    line-height: 1.7;
    outline: none;
    font-family: inherit;
    color: var(--admin-text, #1a1410);
  }
  #compose-editor:focus {
    background: #fffffe;
  }

  .compose-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    border-top: 1px solid var(--admin-border, #e8dfd7);
    background: #faf9f7;
  }

  .btn-send {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.5rem;
    background: var(--admin-primary, #8B1538);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
  }
  .btn-send:hover { background: #6b0f2d; }
  .btn-send:disabled { opacity: 0.6; cursor: not-allowed; }

  .btn-discard {
    padding: 0.5rem 1rem;
    background: none;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    font-size: 0.82rem;
    color: var(--admin-muted, #6b6459);
    cursor: pointer;
    font-family: inherit;
  }
  .btn-discard:hover { border-color: var(--admin-danger, #e24b4a); color: var(--admin-danger); }

  .compose-status {
    font-size: 0.82rem;
    color: var(--admin-muted, #6b6459);
    display: none;
    align-items: center;
    gap: 0.4rem;
  }
  .compose-status.visible { display: inline-flex; }
  .compose-status.success { color: var(--admin-success, #22c55e); }
  .compose-status.error { color: var(--admin-danger, #e24b4a); }

  .toggle-cc {
    font-size: 0.8rem;
    color: var(--admin-primary, #8B1538);
    cursor: pointer;
    text-decoration: none;
    padding: 0.7rem 1rem;
  }
  .toggle-cc:hover { text-decoration: underline; }

  .cc-field { display: none; }
  .cc-field.show { display: flex; }
</style>

<?php
  $replyTo = $replyTo ?? '';
  $replySubject = $replySubject ?? '';
  $replyBody = $replyBody ?? '';
  $fromAddress = $fromAddress ?? 'contact@estimation-immobilier-bordeaux.fr';
  $fromName = $fromName ?? 'Estimation Immobilier Bordeaux';
?>

<!-- HEADER -->
<div class="compose-header">
  <a href="/admin/mailbox" class="compose-back">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
  <span class="compose-title"><i class="fas fa-pen" style="color:var(--admin-primary);margin-right:0.3rem;"></i> Nouveau message</span>
</div>

<!-- COMPOSE FORM -->
<div class="compose-card">
  <!-- From -->
  <div class="compose-field">
    <label>De</label>
    <div class="compose-from-info">
      <strong><?= htmlspecialchars($fromName) ?></strong> &lt;<?= htmlspecialchars($fromAddress) ?>&gt;
    </div>
  </div>

  <!-- To -->
  <div class="compose-field">
    <label>À</label>
    <input type="email" id="compose-to" placeholder="destinataire@example.com" value="<?= htmlspecialchars($replyTo) ?>" required>
    <a class="toggle-cc" onclick="document.querySelector('.cc-field').classList.toggle('show')">Cc</a>
  </div>

  <!-- CC -->
  <div class="compose-field cc-field">
    <label>Cc</label>
    <input type="text" id="compose-cc" placeholder="cc@example.com">
  </div>

  <!-- Subject -->
  <div class="compose-field">
    <label>Objet</label>
    <input type="text" id="compose-subject" placeholder="Objet du message" value="<?= htmlspecialchars($replySubject) ?>">
  </div>

  <!-- Body -->
  <div class="compose-body-container">
    <div class="compose-toolbar">
      <button type="button" onclick="execCmd('bold')" title="Gras"><i class="fas fa-bold"></i></button>
      <button type="button" onclick="execCmd('italic')" title="Italique"><i class="fas fa-italic"></i></button>
      <button type="button" onclick="execCmd('underline')" title="Souligner"><i class="fas fa-underline"></i></button>
      <span style="width:1px;height:18px;background:var(--admin-border);margin:0 0.3rem;"></span>
      <button type="button" onclick="execCmd('insertUnorderedList')" title="Liste"><i class="fas fa-list-ul"></i></button>
      <button type="button" onclick="execCmd('insertOrderedList')" title="Liste numérotée"><i class="fas fa-list-ol"></i></button>
      <span style="width:1px;height:18px;background:var(--admin-border);margin:0 0.3rem;"></span>
      <button type="button" onclick="insertLink()" title="Lien"><i class="fas fa-link"></i></button>
    </div>
    <div contenteditable="true" id="compose-editor"><?= $replyBody ?></div>
  </div>

  <!-- Footer -->
  <div class="compose-footer">
    <div style="display:flex;align-items:center;gap:0.75rem;">
      <button class="btn-send" id="btn-send" onclick="sendEmail()">
        <i class="fas fa-paper-plane"></i> Envoyer
      </button>
      <span class="compose-status" id="compose-status"></span>
    </div>
    <button class="btn-discard" onclick="if(confirm('Supprimer ce brouillon ?')) window.location.href='/admin/mailbox'">
      <i class="fas fa-trash"></i> Supprimer
    </button>
  </div>
</div>

<script>
function execCmd(command) {
  document.execCommand(command, false, null);
  document.getElementById('compose-editor').focus();
}

function insertLink() {
  var url = prompt('URL du lien :', 'https://');
  if (url) {
    document.execCommand('createLink', false, url);
    document.getElementById('compose-editor').focus();
  }
}

function sendEmail() {
  var to = document.getElementById('compose-to').value.trim();
  var cc = document.getElementById('compose-cc').value.trim();
  var subject = document.getElementById('compose-subject').value.trim();
  var body = document.getElementById('compose-editor').innerHTML.trim();
  var btn = document.getElementById('btn-send');
  var status = document.getElementById('compose-status');

  if (!to) { alert('Veuillez saisir un destinataire.'); return; }
  if (!subject) { alert('Veuillez saisir un objet.'); return; }
  if (!body || body === '<br>') { alert('Veuillez saisir un message.'); return; }

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
  status.className = 'compose-status visible';
  status.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

  var fd = new FormData();
  fd.append('to', to);
  fd.append('cc', cc);
  fd.append('subject', subject);
  fd.append('body', body);

  fetch('/admin/mailbox/send', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        status.className = 'compose-status visible success';
        status.innerHTML = '<i class="fas fa-check-circle"></i> ' + (data.message || 'Envoyé !');
        btn.innerHTML = '<i class="fas fa-check"></i> Envoyé';
        setTimeout(function() { window.location.href = '/admin/mailbox'; }, 1500);
      } else {
        status.className = 'compose-status visible error';
        status.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'Erreur.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
      }
    })
    .catch(function() {
      status.className = 'compose-status visible error';
      status.innerHTML = '<i class="fas fa-times-circle"></i> Erreur réseau.';
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
    });
}

// Auto-focus
document.addEventListener('DOMContentLoaded', function() {
  var to = document.getElementById('compose-to');
  if (to && !to.value) {
    to.focus();
  } else {
    document.getElementById('compose-editor').focus();
  }
});
</script>
