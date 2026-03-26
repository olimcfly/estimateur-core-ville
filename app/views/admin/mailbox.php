<style>
  .mailbox-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }
  .mailbox-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
  }
  .mailbox-header h1 i { color: var(--admin-primary, #8B1538); }

  .mailbox-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
  }

  .btn-compose {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: var(--admin-primary, #8B1538);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    font-family: inherit;
    transition: background 0.15s;
  }
  .btn-compose:hover { background: #6b0f2d; color: #fff; }

  .mailbox-search {
    display: flex;
    gap: 0;
  }
  .mailbox-search input {
    padding: 0.55rem 1rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-right: none;
    border-radius: 6px 0 0 6px;
    font-size: 0.88rem;
    font-family: inherit;
    width: 240px;
    outline: none;
  }
  .mailbox-search input:focus { border-color: var(--admin-primary, #8B1538); }
  .mailbox-search button {
    padding: 0.55rem 1rem;
    background: var(--admin-primary, #8B1538);
    color: #fff;
    border: none;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
    font-size: 0.88rem;
  }
  .mailbox-search button:hover { background: #6b0f2d; }

  /* Folder sidebar */
  .mailbox-layout {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 1.25rem;
  }

  .mailbox-folders {
    background: var(--admin-surface, #fff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    padding: 0.75rem 0;
  }
  .mailbox-folder-title {
    padding: 0.5rem 1rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--admin-muted, #6b6459);
  }
  .mailbox-folder-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    color: var(--admin-text, #1a1410);
    text-decoration: none;
    transition: all 0.12s;
  }
  .mailbox-folder-link:hover { background: rgba(139,21,56,0.05); }
  .mailbox-folder-link.active {
    background: rgba(139,21,56,0.08);
    color: var(--admin-primary, #8B1538);
    font-weight: 600;
  }
  .mailbox-folder-link .badge {
    margin-left: auto;
    background: var(--admin-primary, #8B1538);
    color: #fff;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.1rem 0.45rem;
    border-radius: 8px;
    min-width: 18px;
    text-align: center;
  }

  .folder-icon-map { font-size: 0.9rem; opacity: 0.7; width: 18px; text-align: center; }

  /* Email list */
  .mailbox-list {
    background: var(--admin-surface, #fff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    overflow: hidden;
  }

  .mailbox-list-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 1rem;
    background: #f8f7f5;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
    font-size: 0.8rem;
    color: var(--admin-muted, #6b6459);
  }

  .email-row {
    display: grid;
    grid-template-columns: 200px 1fr 140px;
    align-items: center;
    padding: 0.7rem 1rem;
    border-bottom: 1px solid #f4f1ed;
    cursor: pointer;
    transition: background 0.1s;
    text-decoration: none;
    color: inherit;
  }
  .email-row:hover { background: #faf9f7; }
  .email-row.unread {
    background: #fef7f0;
    font-weight: 600;
  }
  .email-row.unread:hover { background: #fdf0e0; }

  .email-from {
    font-size: 0.85rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 0.75rem;
  }

  .email-subject-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    overflow: hidden;
  }
  .email-subject {
    font-size: 0.85rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .email-preview {
    font-size: 0.8rem;
    color: var(--admin-muted, #6b6459);
    font-weight: 400;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .email-attachment-icon {
    color: var(--admin-muted, #6b6459);
    font-size: 0.78rem;
    flex-shrink: 0;
  }

  .email-date {
    font-size: 0.78rem;
    color: var(--admin-muted, #6b6459);
    text-align: right;
    white-space: nowrap;
    font-weight: 400;
  }

  .email-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.12rem 0.5rem;
    border-radius: 10px;
    font-size: 0.68rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
  }
  .badge-sent {
    background: #e8f5e9;
    color: #2e7d32;
  }
  .badge-draft {
    background: #fff3e0;
    color: #e65100;
  }
  .badge-scheduled {
    background: #e3f2fd;
    color: #1565c0;
  }
  .badge-failed {
    background: #ffebee;
    color: #c62828;
  }

  /* Pagination */
  .mailbox-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
  }
  .mailbox-pagination a, .mailbox-pagination span {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.82rem;
    text-decoration: none;
    color: var(--admin-text, #1a1410);
    border: 1px solid var(--admin-border, #e8dfd7);
  }
  .mailbox-pagination a:hover { background: #f4f1ed; }
  .mailbox-pagination .active {
    background: var(--admin-primary, #8B1538);
    color: #fff;
    border-color: var(--admin-primary, #8B1538);
  }
  .mailbox-pagination .disabled {
    opacity: 0.4;
    pointer-events: none;
  }

  .mailbox-info {
    text-align: center;
    padding: 0.5rem;
    font-size: 0.78rem;
    color: var(--admin-muted, #6b6459);
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted, #6b6459);
  }
  .empty-state i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.3;
    display: block;
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

  @media (max-width: 768px) {
    .mailbox-layout { grid-template-columns: 1fr; }
    .email-row { grid-template-columns: 1fr auto; }
    .email-subject-preview { display: none; }
    .mailbox-search input { width: 160px; }
  }
</style>

<?php
  $emails = $emails ?? [];
  $total = $total ?? 0;
  $page = $page ?? 1;
  $totalPages = $totalPages ?? 1;
  $folder = $folder ?? 'INBOX';
  $folders = $folders ?? [];
  $search = $search ?? '';
  $unreadCount = $unreadCount ?? 0;
  $error = $error ?? null;
  $mailAddress = $mailAddress ?? ('contact@' . (site('domain', '') ?: 'example.test'));
  $drafts = $drafts ?? [];
  $scheduledEmails = $scheduledEmails ?? [];
  $draftCount = $draftCount ?? 0;
  $scheduledCount = $scheduledCount ?? 0;

  // Determine folder type for status badges
  $folderUpper = strtoupper($folder);
  $isSentFolder = in_array($folderUpper, ['SENT', 'INBOX.SENT']);
  $isDraftFolder = ($folder === '_drafts');
  $isScheduledFolder = ($folder === '_scheduled');

  $folderIcons = [
    'INBOX' => 'fa-inbox',
    'Sent' => 'fa-paper-plane',
    'INBOX.Sent' => 'fa-paper-plane',
    'Drafts' => 'fa-file-alt',
    'INBOX.Drafts' => 'fa-file-alt',
    'Trash' => 'fa-trash',
    'INBOX.Trash' => 'fa-trash',
    'Spam' => 'fa-ban',
    'INBOX.Spam' => 'fa-ban',
    'Junk' => 'fa-ban',
    'INBOX.Junk' => 'fa-ban',
  ];

  function formatMailboxDate(string $date): string {
    if ($date === '') return '';
    $ts = strtotime($date);
    if ($ts === false) return $date;
    $today = strtotime('today');
    if ($ts >= $today) {
      return date('H:i', $ts);
    }
    if ($ts >= $today - 86400 * 6) {
      $jours = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
      return $jours[(int) date('w', $ts)] . ' ' . date('H:i', $ts);
    }
    return date('d/m/Y', $ts);
  }

  function formatSender(array $from): string {
    if (empty($from)) return 'Inconnu';
    $first = $from[0];
    $name = $first['name'] ?? '';
    return $name !== '' ? $name : ($first['email'] ?? 'Inconnu');
  }
?>

<!-- HEADER -->
<div class="mailbox-header">
  <h1><i class="fas fa-envelope"></i> Boîte Email</h1>
  <div class="mailbox-actions">
    <form class="mailbox-search" method="get" action="/admin/mailbox">
      <input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
      <input type="text" name="q" placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <a href="/admin/mailbox/compose" class="btn-compose">
      <i class="fas fa-pen"></i> Nouveau message
    </a>
  </div>
</div>

<?php if ($error): ?>
  <div class="error-box">
    <i class="fas fa-exclamation-triangle"></i>
    <div><?= htmlspecialchars($error) ?></div>
  </div>
<?php endif; ?>

<?php if ($search !== ''): ?>
  <div style="margin-bottom:1rem;font-size:0.88rem;color:var(--admin-muted);">
    <i class="fas fa-search"></i> Résultats pour « <strong><?= htmlspecialchars($search) ?></strong> » — <?= count($emails) ?> résultat(s)
    <a href="/admin/mailbox?folder=<?= urlencode($folder) ?>" style="margin-left:0.5rem;color:var(--admin-primary);text-decoration:none;">Effacer</a>
  </div>
<?php endif; ?>

<!-- LAYOUT -->
<div class="mailbox-layout">
  <!-- FOLDERS -->
  <div class="mailbox-folders">
    <div class="mailbox-folder-title">Dossiers</div>
    <?php if (empty($folders)): ?>
      <a href="/admin/mailbox?folder=INBOX" class="mailbox-folder-link active">
        <i class="fas fa-inbox folder-icon-map"></i> Boîte de réception
      </a>
    <?php else: ?>
      <?php foreach ($folders as $f):
        $fPath = $f['path'] ?? $f['name'];
        $fName = $f['name'] ?? $fPath;
        $isActive = ($fPath === $folder);
        $icon = $folderIcons[$fPath] ?? ($folderIcons[$fName] ?? 'fa-folder');
        // Friendly names
        $displayName = match(strtoupper($fName)) {
          'INBOX' => 'Boîte de réception',
          'SENT', 'INBOX.SENT' => 'Envoyés',
          'DRAFTS', 'INBOX.DRAFTS' => 'Brouillons',
          'TRASH', 'INBOX.TRASH' => 'Corbeille',
          'SPAM', 'JUNK', 'INBOX.SPAM', 'INBOX.JUNK' => 'Spam',
          default => $fName,
        };
      ?>
        <a href="/admin/mailbox?folder=<?= urlencode($fPath) ?>" class="mailbox-folder-link <?= $isActive ? 'active' : '' ?>">
          <i class="fas <?= $icon ?> folder-icon-map"></i> <?= htmlspecialchars($displayName) ?>
          <?php if ($isActive && $unreadCount > 0): ?>
            <span class="badge"><?= $unreadCount ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Virtual folders: Brouillons & Planifiés (from DB) -->
    <div class="mailbox-folder-title" style="margin-top:0.5rem;">Local</div>
    <a href="/admin/mailbox?folder=_drafts" class="mailbox-folder-link <?= $isDraftFolder ? 'active' : '' ?>">
      <i class="fas fa-file-alt folder-icon-map"></i> Brouillons
      <?php if ($draftCount > 0): ?>
        <span class="badge" style="background:#e65100;"><?= $draftCount ?></span>
      <?php endif; ?>
    </a>
    <a href="/admin/mailbox?folder=_scheduled" class="mailbox-folder-link <?= $isScheduledFolder ? 'active' : '' ?>">
      <i class="fas fa-clock folder-icon-map"></i> Planifiés
      <?php if ($scheduledCount > 0): ?>
        <span class="badge" style="background:#1565c0;"><?= $scheduledCount ?></span>
      <?php endif; ?>
    </a>
  </div>

  <!-- EMAIL LIST -->
  <div class="mailbox-list">
    <div class="mailbox-list-header">
      <span>
        <?php if ($isDraftFolder): ?>
          <?= $draftCount ?> brouillon(s)
        <?php elseif ($isScheduledFolder): ?>
          <?= $scheduledCount ?> email(s) planifié(s)
        <?php else: ?>
          <?= $total ?> message(s)<?= $unreadCount > 0 ? ' — <strong>' . $unreadCount . ' non lu(s)</strong>' : '' ?>
        <?php endif; ?>
      </span>
      <span><?= htmlspecialchars($mailAddress) ?></span>
    </div>

    <?php if ($isDraftFolder || $isScheduledFolder): ?>
      <?php
        $localEmails = $isDraftFolder ? $drafts : $scheduledEmails;
        if (empty($localEmails)):
      ?>
        <div class="empty-state">
          <i class="fas <?= $isDraftFolder ? 'fa-file-alt' : 'fa-clock' ?>"></i>
          <p><?= $isDraftFolder ? 'Aucun brouillon.' : 'Aucun email planifié.' ?></p>
        </div>
      <?php else: ?>
        <?php foreach ($localEmails as $draft): ?>
          <a href="/admin/mailbox/compose?draft_id=<?= (int) $draft['id'] ?>" class="email-row">
            <div class="email-from" title="<?= htmlspecialchars($draft['recipient'] ?? 'Sans destinataire') ?>">
              <?= htmlspecialchars($draft['recipient'] ?: 'Sans destinataire') ?>
            </div>
            <div class="email-subject-preview">
              <?php if ($draft['status'] === 'draft'): ?>
                <span class="email-status-badge badge-draft"><i class="fas fa-file-alt"></i> Brouillon</span>
              <?php elseif ($draft['status'] === 'scheduled'): ?>
                <span class="email-status-badge badge-scheduled"><i class="fas fa-clock"></i> Planifié</span>
              <?php elseif ($draft['status'] === 'sent'): ?>
                <span class="email-status-badge badge-sent"><i class="fas fa-check"></i> Envoyé</span>
              <?php elseif ($draft['status'] === 'failed'): ?>
                <span class="email-status-badge badge-failed"><i class="fas fa-times"></i> Échoué</span>
              <?php endif; ?>
              <span class="email-subject"><?= htmlspecialchars($draft['subject'] ?: '(sans sujet)') ?></span>
            </div>
            <div class="email-date">
              <?php if ($draft['status'] === 'scheduled' && $draft['scheduled_at']): ?>
                <i class="fas fa-clock" style="font-size:0.7rem;margin-right:0.2rem;"></i><?= formatMailboxDate($draft['scheduled_at']) ?>
              <?php else: ?>
                <?= formatMailboxDate($draft['updated_at'] ?? $draft['created_at'] ?? '') ?>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>

    <?php elseif (empty($emails) && $error === null): ?>
      <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p><?= $search !== '' ? 'Aucun résultat pour cette recherche.' : 'Aucun email dans ce dossier.' ?></p>
      </div>
    <?php else: ?>
      <?php foreach ($emails as $email):
        $uid = (int) ($email['uid'] ?? 0);
        $isSeen = (bool) ($email['is_seen'] ?? false);
        $from = formatSender($email['from'] ?? []);
        $subject = $email['subject'] ?? '(sans sujet)';
        $date = formatMailboxDate($email['date'] ?? '');
        $hasAttach = (bool) ($email['has_attachments'] ?? false);
      ?>
        <a href="/admin/mailbox/read?uid=<?= $uid ?>&folder=<?= urlencode($folder) ?>" class="email-row <?= $isSeen ? '' : 'unread' ?>">
          <div class="email-from" title="<?= htmlspecialchars($from) ?>">
            <?= htmlspecialchars($from) ?>
          </div>
          <div class="email-subject-preview">
            <?php if ($isSentFolder): ?>
              <span class="email-status-badge badge-sent"><i class="fas fa-check"></i> Envoyé</span>
            <?php endif; ?>
            <?php if ($hasAttach): ?>
              <i class="fas fa-paperclip email-attachment-icon"></i>
            <?php endif; ?>
            <span class="email-subject"><?= htmlspecialchars($subject) ?></span>
          </div>
          <div class="email-date"><?= htmlspecialchars($date) ?></div>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($totalPages > 1 && $search === ''): ?>
      <div class="mailbox-pagination">
        <?php if ($page > 1): ?>
          <a href="/admin/mailbox?folder=<?= urlencode($folder) ?>&page=<?= $page - 1 ?>"><i class="fas fa-chevron-left"></i></a>
        <?php else: ?>
          <span class="disabled"><i class="fas fa-chevron-left"></i></span>
        <?php endif; ?>

        <?php
          $startPage = max(1, $page - 2);
          $endPage = min($totalPages, $page + 2);
          for ($p = $startPage; $p <= $endPage; $p++):
        ?>
          <?php if ($p === $page): ?>
            <span class="active"><?= $p ?></span>
          <?php else: ?>
            <a href="/admin/mailbox?folder=<?= urlencode($folder) ?>&page=<?= $p ?>"><?= $p ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <a href="/admin/mailbox?folder=<?= urlencode($folder) ?>&page=<?= $page + 1 ?>"><i class="fas fa-chevron-right"></i></a>
        <?php else: ?>
          <span class="disabled"><i class="fas fa-chevron-right"></i></span>
        <?php endif; ?>
      </div>
      <div class="mailbox-info">
        Page <?= $page ?> / <?= $totalPages ?>
      </div>
    <?php endif; ?>
  </div>
</div>
