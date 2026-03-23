<style>
  .read-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
  }
  .read-back {
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
  .read-back:hover { border-color: var(--admin-primary, #8B1538); color: var(--admin-primary); }

  .read-actions {
    margin-left: auto;
    display: flex;
    gap: 0.5rem;
  }
  .read-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: 1px solid var(--admin-border, #e8dfd7);
    background: var(--admin-surface, #fff);
    color: var(--admin-text, #1a1410);
    font-family: inherit;
    transition: all 0.15s;
  }
  .read-action-btn:hover { border-color: var(--admin-primary, #8B1538); color: var(--admin-primary); }
  .read-action-btn.reply { background: var(--admin-primary, #8B1538); color: #fff; border-color: var(--admin-primary); }
  .read-action-btn.reply:hover { background: #6b0f2d; }
  .read-action-btn.delete { color: var(--admin-danger, #e24b4a); }
  .read-action-btn.delete:hover { background: #fef2f2; border-color: var(--admin-danger, #e24b4a); }

  .email-card {
    background: var(--admin-surface, #fff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    overflow: hidden;
  }

  .email-meta {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
  }
  .email-subject-line {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    margin-bottom: 1rem;
    line-height: 1.4;
  }
  .email-meta-row {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    font-size: 0.85rem;
    margin-bottom: 0.4rem;
    color: var(--admin-muted, #6b6459);
  }
  .email-meta-row strong {
    min-width: 30px;
    color: var(--admin-text, #1a1410);
    font-weight: 600;
  }
  .email-meta-row a {
    color: var(--admin-primary, #8B1538);
    text-decoration: none;
  }
  .email-meta-row a:hover { text-decoration: underline; }

  .email-date-line {
    font-size: 0.8rem;
    color: var(--admin-muted, #6b6459);
    margin-top: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .email-body {
    padding: 1.5rem;
    font-size: 0.92rem;
    line-height: 1.7;
    color: var(--admin-text, #1a1410);
    overflow-x: auto;
  }
  .email-body img { max-width: 100%; height: auto; }

  .email-attachments {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--admin-border, #e8dfd7);
    background: #faf9f7;
  }
  .email-attachments-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--admin-muted, #6b6459);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }
  .attachment-item {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    background: var(--admin-surface, #fff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    margin-bottom: 0.4rem;
  }
  .attachment-size {
    color: var(--admin-muted, #6b6459);
    font-size: 0.72rem;
  }

  .error-box {
    padding: 1rem 1.25rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #991b1b;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    font-size: 0.9rem;
  }
</style>

<?php
  $email = $email ?? null;
  $folder = $folder ?? 'INBOX';
  $error = $error ?? null;

  function formatReadableDate(string $date): string {
    if ($date === '') return '';
    $ts = strtotime($date);
    if ($ts === false) return $date;
    $jours = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
    $mois = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    return $jours[(int) date('w', $ts)] . ' ' . date('j', $ts) . ' ' . $mois[(int) date('n', $ts)] . ' ' . date('Y', $ts) . ' à ' . date('H:i', $ts);
  }

  function formatAddresses(array $addresses): string {
    $parts = [];
    foreach ($addresses as $addr) {
      $name = $addr['name'] ?? '';
      $email = $addr['email'] ?? '';
      if ($name !== '' && $name !== $email) {
        $parts[] = htmlspecialchars($name) . ' &lt;<a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a>&gt;';
      } else {
        $parts[] = '<a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a>';
      }
    }
    return implode(', ', $parts);
  }

  function formatFileSize(int $bytes): string {
    if ($bytes < 1024) return $bytes . ' o';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' Ko';
    return round($bytes / 1048576, 1) . ' Mo';
  }
?>

<!-- HEADER -->
<div class="read-header">
  <a href="/admin/mailbox?folder=<?= urlencode($folder) ?>" class="read-back">
    <i class="fas fa-arrow-left"></i> Retour
  </a>

  <?php if ($email): ?>
    <div class="read-actions">
      <a href="/admin/mailbox/compose?reply_uid=<?= (int) $email['uid'] ?>&folder=<?= urlencode($folder) ?>" class="read-action-btn reply">
        <i class="fas fa-reply"></i> Répondre
      </a>
      <a href="/admin/mailbox/compose?reply_to=<?= urlencode($email['from'][0]['email'] ?? '') ?>&subject=<?= urlencode('Fwd: ' . $email['subject']) ?>" class="read-action-btn">
        <i class="fas fa-share"></i> Transférer
      </a>
      <button class="read-action-btn delete" onclick="deleteEmail(<?= (int) $email['uid'] ?>, '<?= htmlspecialchars($folder, ENT_QUOTES) ?>')">
        <i class="fas fa-trash"></i> Supprimer
      </button>
    </div>
  <?php endif; ?>
</div>

<?php if ($error): ?>
  <div class="error-box">
    <i class="fas fa-exclamation-triangle"></i>
    <div><?= htmlspecialchars($error) ?></div>
  </div>
<?php endif; ?>

<?php if ($email): ?>
  <div class="email-card">
    <div class="email-meta">
      <div class="email-subject-line"><?= htmlspecialchars($email['subject'] ?: '(sans sujet)') ?></div>

      <div class="email-meta-row">
        <strong>De :</strong>
        <span><?= formatAddresses($email['from'] ?? []) ?></span>
      </div>
      <div class="email-meta-row">
        <strong>À :</strong>
        <span><?= formatAddresses($email['to'] ?? []) ?></span>
      </div>
      <?php if (!empty($email['cc'])): ?>
        <div class="email-meta-row">
          <strong>Cc :</strong>
          <span><?= formatAddresses($email['cc']) ?></span>
        </div>
      <?php endif; ?>

      <div class="email-date-line">
        <i class="far fa-clock"></i>
        <?= formatReadableDate($email['date'] ?? '') ?>
      </div>
    </div>

    <div class="email-body">
      <?php
        $htmlBody = $email['body_html'] ?? '';
        $textBody = $email['body_text'] ?? '';

        if ($htmlBody !== '') {
          // Sanitize: remove scripts and event handlers
          $safe = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $htmlBody);
          $safe = preg_replace('/\s+on\w+\s*=\s*(["\']).*?\1/i', '', $safe ?? '');
          echo $safe;
        } elseif ($textBody !== '') {
          echo nl2br(htmlspecialchars($textBody));
        } else {
          echo '<em style="color:var(--admin-muted);">Aucun contenu.</em>';
        }
      ?>
    </div>

    <?php if (!empty($email['attachments'])): ?>
      <div class="email-attachments">
        <div class="email-attachments-title"><i class="fas fa-paperclip"></i> Pièces jointes (<?= count($email['attachments']) ?>)</div>
        <?php foreach ($email['attachments'] as $att): ?>
          <span class="attachment-item">
            <i class="fas fa-file"></i>
            <?= htmlspecialchars($att['name'] ?? 'fichier') ?>
            <span class="attachment-size">(<?= formatFileSize((int) ($att['size'] ?? 0)) ?>)</span>
          </span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<script>
function deleteEmail(uid, folder) {
  if (!confirm('Supprimer cet email ?')) return;

  var fd = new FormData();
  fd.append('uid', uid);
  fd.append('folder', folder);

  fetch('/admin/mailbox/delete', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        window.location.href = '/admin/mailbox?folder=' + encodeURIComponent(folder);
      } else {
        alert(data.message || 'Erreur lors de la suppression.');
      }
    })
    .catch(function() { alert('Erreur réseau.'); });
}
</script>
