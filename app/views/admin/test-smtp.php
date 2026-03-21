<style>
  .smtp-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .smtp-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .smtp-header h1 i { color: var(--admin-primary); }

  .smtp-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  @media (max-width: 900px) {
    .smtp-grid { grid-template-columns: 1fr; }
  }

  .smtp-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
  }

  .smtp-card h2 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--admin-text);
  }

  .smtp-card h2 i { color: var(--admin-primary); font-size: 0.95rem; }

  .config-table {
    width: 100%;
    border-collapse: collapse;
  }

  .config-table tr { border-bottom: 1px solid var(--admin-border); }
  .config-table tr:last-child { border-bottom: none; }

  .config-table td {
    padding: 0.6rem 0.5rem;
    font-size: 0.88rem;
    vertical-align: middle;
  }

  .config-label {
    font-weight: 600;
    color: var(--admin-muted);
    white-space: nowrap;
    width: 120px;
  }

  .config-value {
    color: var(--admin-text);
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 0.82rem;
    word-break: break-all;
  }

  .config-value.empty {
    color: var(--admin-danger);
    font-style: italic;
    font-family: inherit;
  }

  .btn-test {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.5rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
  }

  .btn-test:hover { background: #6b0f2d; }
  .btn-test:disabled { opacity: 0.6; cursor: not-allowed; }
  .btn-test i.fa-spin { animation: fa-spin 1s linear infinite; }

  .btn-send {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: var(--admin-info);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
  }

  .btn-send:hover { background: #2563eb; }
  .btn-send:disabled { opacity: 0.6; cursor: not-allowed; }

  .test-results {
    margin-top: 1.25rem;
  }

  .test-step {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--admin-border);
  }

  .test-step:last-child { border-bottom: none; }

  .step-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.75rem;
  }

  .step-icon.ok { background: #dcfce7; color: #16a34a; }
  .step-icon.error { background: #fee2e2; color: #dc2626; }
  .step-icon.pending { background: #f1f5f9; color: #94a3b8; }

  .step-content { flex: 1; min-width: 0; }

  .step-label {
    font-weight: 600;
    font-size: 0.88rem;
    color: var(--admin-text);
    margin-bottom: 0.15rem;
  }

  .step-detail {
    font-size: 0.82rem;
    color: var(--admin-muted);
    word-break: break-word;
  }

  .step-diagnostics {
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #fef2f2;
    border-radius: 4px;
    font-size: 0.82rem;
    color: #991b1b;
  }

  .step-advice {
    margin-top: 0.4rem;
    padding: 0.5rem 0.75rem;
    background: #fefce8;
    border-radius: 4px;
    font-size: 0.82rem;
    color: #854d0e;
  }

  .send-form {
    display: flex;
    gap: 0.75rem;
    align-items: flex-end;
    margin-top: 1rem;
  }

  .send-form .form-group {
    flex: 1;
  }

  .send-form label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--admin-muted);
    margin-bottom: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .send-form input {
    width: 100%;
    padding: 0.6rem 0.85rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.9rem;
    font-family: inherit;
    background: #fff;
    color: var(--admin-text);
    transition: border-color 0.15s;
  }

  .send-form input:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px var(--admin-primary-light, rgba(139,21,56,0.1));
  }

  .send-result {
    margin-top: 0.75rem;
    padding: 0.6rem 0.85rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    display: none;
  }

  .send-result.success {
    background: #dcfce7;
    color: #166534;
    display: block;
  }

  .send-result.error {
    background: #fee2e2;
    color: #991b1b;
    display: block;
  }

  .smtp-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
  }

  .smtp-status-badge.configured {
    background: #dcfce7;
    color: #166534;
  }

  .smtp-status-badge.not-configured {
    background: #fee2e2;
    color: #991b1b;
  }
</style>

<div class="smtp-header">
  <h1><i class="fas fa-envelope-open-text"></i> Test SMTP</h1>
  <?php if ($smtp_host !== ''): ?>
    <span class="smtp-status-badge configured"><i class="fas fa-check-circle"></i> Configure</span>
  <?php else: ?>
    <span class="smtp-status-badge not-configured"><i class="fas fa-times-circle"></i> Non configure</span>
  <?php endif; ?>
</div>

<div class="smtp-grid">
  <!-- Configuration Card -->
  <div class="smtp-card">
    <h2><i class="fas fa-cog"></i> Configuration actuelle</h2>
    <table class="config-table">
      <tr>
        <td class="config-label">Host</td>
        <td class="config-value <?= $smtp_host === '' ? 'empty' : '' ?>">
          <?= $smtp_host !== '' ? htmlspecialchars($smtp_host, ENT_QUOTES, 'UTF-8') : '(non defini)' ?>
        </td>
      </tr>
      <tr>
        <td class="config-label">Port</td>
        <td class="config-value"><?= $smtp_port ?></td>
      </tr>
      <tr>
        <td class="config-label">Utilisateur</td>
        <td class="config-value <?= $smtp_user === '' ? 'empty' : '' ?>">
          <?= $smtp_user !== '' ? htmlspecialchars($smtp_user, ENT_QUOTES, 'UTF-8') : '(non defini)' ?>
        </td>
      </tr>
      <tr>
        <td class="config-label">Mot de passe</td>
        <td class="config-value <?= $smtp_pass === '(vide)' ? 'empty' : '' ?>">
          <?= htmlspecialchars($smtp_pass, ENT_QUOTES, 'UTF-8') ?>
        </td>
      </tr>
      <tr>
        <td class="config-label">Encryption</td>
        <td class="config-value"><?= htmlspecialchars($smtp_enc, ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
      <tr>
        <td class="config-label">From</td>
        <td class="config-value <?= $mail_from === '' ? 'empty' : '' ?>">
          <?= $mail_from !== '' ? htmlspecialchars($mail_from, ENT_QUOTES, 'UTF-8') : '(non defini)' ?>
        </td>
      </tr>
      <tr>
        <td class="config-label">From Name</td>
        <td class="config-value <?= $mail_from_name === '' ? 'empty' : '' ?>">
          <?= $mail_from_name !== '' ? htmlspecialchars($mail_from_name, ENT_QUOTES, 'UTF-8') : '(non defini)' ?>
        </td>
      </tr>
    </table>
  </div>

  <!-- Test Connection Card -->
  <div class="smtp-card">
    <h2><i class="fas fa-plug"></i> Test de connexion</h2>
    <p style="font-size: 0.85rem; color: var(--admin-muted); margin-bottom: 1rem;">
      Teste la connexion au serveur SMTP sans envoyer de message.
    </p>
    <button class="btn-test" id="btnRunTest" onclick="runSmtpTest()">
      <i class="fas fa-play"></i> Lancer le test
    </button>

    <div class="test-results" id="testResults" style="display:none;"></div>
  </div>
</div>

<!-- Send Test Email Card -->
<div class="smtp-card" style="max-width: 700px;">
  <h2><i class="fas fa-paper-plane"></i> Envoyer un email de test</h2>
  <p style="font-size: 0.85rem; color: var(--admin-muted); margin-bottom: 0.5rem;">
    Envoyez un email de test pour verifier que tout fonctionne de bout en bout.
  </p>
  <div class="send-form">
    <div class="form-group">
      <label for="testEmail">Adresse email destinataire</label>
      <input type="email" id="testEmail" placeholder="votre@email.com" />
    </div>
    <button class="btn-send" id="btnSendTest" onclick="sendTestEmail()">
      <i class="fas fa-paper-plane"></i> Envoyer
    </button>
  </div>
  <div class="send-result" id="sendResult"></div>
</div>

<script>
function runSmtpTest() {
  const btn = document.getElementById('btnRunTest');
  const results = document.getElementById('testResults');

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Test en cours...';
  results.style.display = 'block';
  results.innerHTML = '<div class="test-step"><div class="step-icon pending"><i class="fas fa-spinner fa-spin"></i></div><div class="step-content"><div class="step-label">Connexion en cours...</div></div></div>';

  fetch('/admin/test-smtp/run', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
      let html = '';
      data.steps.forEach(step => {
        const iconClass = step.status === 'ok' ? 'ok' : 'error';
        const icon = step.status === 'ok' ? 'fa-check' : 'fa-times';
        html += '<div class="test-step">';
        html += '  <div class="step-icon ' + iconClass + '"><i class="fas ' + icon + '"></i></div>';
        html += '  <div class="step-content">';
        html += '    <div class="step-label">' + escHtml(step.label) + '</div>';
        html += '    <div class="step-detail">' + escHtml(step.detail) + '</div>';
        if (step.diagnostics && step.diagnostics.length > 0) {
          html += '    <div class="step-diagnostics">' + step.diagnostics.map(escHtml).join('<br>') + '</div>';
        }
        if (step.advice) {
          html += '    <div class="step-advice"><i class="fas fa-lightbulb"></i> ' + escHtml(step.advice) + '</div>';
        }
        html += '  </div>';
        html += '</div>';
      });
      results.innerHTML = html;
    })
    .catch(err => {
      results.innerHTML = '<div class="test-step"><div class="step-icon error"><i class="fas fa-times"></i></div><div class="step-content"><div class="step-label">Erreur reseau</div><div class="step-detail">' + escHtml(err.message) + '</div></div></div>';
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-play"></i> Lancer le test';
    });
}

function sendTestEmail() {
  const btn = document.getElementById('btnSendTest');
  const email = document.getElementById('testEmail').value.trim();
  const result = document.getElementById('sendResult');

  if (!email) {
    result.className = 'send-result error';
    result.textContent = 'Veuillez saisir une adresse email.';
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
  result.className = 'send-result';
  result.style.display = 'none';

  const formData = new FormData();
  formData.append('to', email);

  fetch('/admin/test-smtp/send', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        result.className = 'send-result success';
        result.innerHTML = '<i class="fas fa-check-circle"></i> Email envoye avec succes a ' + escHtml(email);
      } else {
        result.className = 'send-result error';
        result.innerHTML = '<i class="fas fa-times-circle"></i> ' + escHtml(data.error || 'Echec de l\'envoi.');
      }
    })
    .catch(err => {
      result.className = 'send-result error';
      result.textContent = 'Erreur reseau : ' + err.message;
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
    });
}

function escHtml(str) {
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}
</script>
