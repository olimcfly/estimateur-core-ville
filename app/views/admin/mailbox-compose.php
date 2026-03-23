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
    flex: 1;
  }

  .compose-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1rem;
    align-items: start;
  }
  @media (max-width: 1100px) {
    .compose-layout { grid-template-columns: 1fr; }
    .compose-sidebar { order: -1; }
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

  .compose-body-container { padding: 0; }

  .compose-toolbar {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem;
    background: #faf9f7;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
    flex-wrap: wrap;
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
  .toolbar-sep { width:1px; height:18px; background:var(--admin-border); margin:0 0.3rem; }

  .toolbar-dropdown {
    position: relative;
    display: inline-block;
  }
  .toolbar-dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    min-width: 180px;
    z-index: 100;
    padding: 0.3rem 0;
  }
  .toolbar-dropdown-content.show { display: block; }
  .toolbar-dropdown-content button {
    display: block;
    width: 100%;
    text-align: left;
    padding: 0.5rem 0.8rem;
    font-size: 0.82rem;
    border-radius: 0;
  }
  .toolbar-dropdown-content button:hover { background: #f5f3f0; }

  #compose-editor {
    min-height: 350px;
    padding: 1.25rem;
    font-size: 0.92rem;
    line-height: 1.7;
    outline: none;
    font-family: inherit;
    color: var(--admin-text, #1a1410);
  }
  #compose-editor:focus { background: #fffffe; }

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

  .btn-draft {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1rem;
    background: #fff;
    color: #e65100;
    border: 1px solid #e65100;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
  }
  .btn-draft:hover { background: #fff3e0; }
  .btn-draft:disabled { opacity: 0.6; cursor: not-allowed; }

  .btn-schedule {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1rem;
    background: #fff;
    color: #1565c0;
    border: 1px solid #1565c0;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
  }
  .btn-schedule:hover { background: #e3f2fd; }
  .btn-schedule:disabled { opacity: 0.6; cursor: not-allowed; }

  .schedule-picker {
    display: none;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f0f7ff;
    border-top: 1px solid #bbdefb;
    font-size: 0.82rem;
  }
  .schedule-picker.show { display: flex; }
  .schedule-picker input[type="datetime-local"] {
    padding: 0.4rem 0.6rem;
    border: 1px solid #bbdefb;
    border-radius: 5px;
    font-size: 0.82rem;
    font-family: inherit;
    outline: none;
  }
  .schedule-picker input:focus { border-color: #1565c0; }
  .schedule-confirm-btn {
    padding: 0.4rem 0.8rem;
    background: #1565c0;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
  }
  .schedule-confirm-btn:hover { background: #0d47a1; }
  .schedule-cancel-btn {
    padding: 0.4rem 0.6rem;
    background: none;
    border: none;
    color: var(--admin-muted);
    cursor: pointer;
    font-size: 0.82rem;
  }

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

  /* === SIDEBAR: AI ASSISTANT + LIBRARY === */
  .compose-sidebar { display: flex; flex-direction: column; gap: 1rem; }

  .sidebar-card {
    background: var(--admin-surface, #fff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    overflow: hidden;
  }
  .sidebar-card-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1rem;
    background: #faf9f7;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    cursor: pointer;
    user-select: none;
  }
  .sidebar-card-header i.toggle-icon { margin-left: auto; font-size: 0.75rem; transition: transform 0.2s; }
  .sidebar-card-header.collapsed i.toggle-icon { transform: rotate(-90deg); }
  .sidebar-card-body { padding: 0.75rem 1rem; }
  .sidebar-card-header.collapsed + .sidebar-card-body { display: none; }

  /* AI Actions grid */
  .ai-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.4rem;
    margin-bottom: 0.75rem;
  }
  .ai-action-btn {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.6rem;
    background: #f8f7f5;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 5px;
    font-size: 0.78rem;
    color: var(--admin-text, #1a1410);
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
  }
  .ai-action-btn:hover {
    border-color: var(--admin-primary, #8B1538);
    background: #fff;
    color: var(--admin-primary);
  }
  .ai-action-btn:disabled { opacity: 0.5; cursor: wait; }
  .ai-action-btn i { font-size: 0.72rem; color: var(--admin-primary, #8B1538); }

  .ai-freeform { margin-bottom: 0.6rem; }
  .ai-freeform textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 5px;
    font-size: 0.82rem;
    font-family: inherit;
    resize: vertical;
    min-height: 60px;
    outline: none;
  }
  .ai-freeform textarea:focus { border-color: var(--admin-primary, #8B1538); }

  .ai-generate-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    width: 100%;
    padding: 0.55rem;
    background: linear-gradient(135deg, #8B1538, #a91d45);
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: opacity 0.15s;
  }
  .ai-generate-btn:hover { opacity: 0.9; }
  .ai-generate-btn:disabled { opacity: 0.5; cursor: wait; }

  .ai-status {
    font-size: 0.75rem;
    color: var(--admin-muted);
    margin-top: 0.5rem;
    min-height: 1.2em;
  }
  .ai-status.error { color: var(--admin-danger, #e24b4a); }

  /* Library */
  .lib-filters {
    display: flex;
    gap: 0.4rem;
    margin-bottom: 0.6rem;
    flex-wrap: wrap;
  }
  .lib-filter-btn {
    padding: 0.3rem 0.6rem;
    background: #f5f3f0;
    border: 1px solid transparent;
    border-radius: 12px;
    font-size: 0.72rem;
    color: var(--admin-muted, #6b6459);
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
  }
  .lib-filter-btn:hover, .lib-filter-btn.active {
    background: var(--admin-primary, #8B1538);
    color: #fff;
  }
  .lib-search {
    width: 100%;
    padding: 0.45rem 0.6rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 5px;
    font-size: 0.82rem;
    font-family: inherit;
    outline: none;
    margin-bottom: 0.6rem;
  }
  .lib-search:focus { border-color: var(--admin-primary, #8B1538); }

  .lib-templates {
    max-height: 320px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
  }
  .lib-template-card {
    padding: 0.6rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s;
    background: #fff;
  }
  .lib-template-card:hover {
    border-color: var(--admin-primary, #8B1538);
    box-shadow: 0 2px 8px rgba(139,21,56,0.08);
  }
  .lib-template-name {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--admin-text, #1a1410);
    margin-bottom: 0.15rem;
  }
  .lib-template-subject {
    font-size: 0.75rem;
    color: var(--admin-muted, #6b6459);
    margin-bottom: 0.3rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .lib-template-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.7rem;
    color: var(--admin-muted);
  }
  .lib-template-meta .cat-badge {
    padding: 0.1rem 0.4rem;
    background: #f0ebe5;
    border-radius: 8px;
    font-size: 0.68rem;
    text-transform: capitalize;
  }

  .lib-empty {
    text-align: center;
    padding: 1.5rem 0;
    color: var(--admin-muted);
    font-size: 0.82rem;
  }

  .lib-actions {
    display: flex;
    gap: 0.4rem;
    margin-top: 0.6rem;
    padding-top: 0.6rem;
    border-top: 1px solid var(--admin-border, #e8dfd7);
  }
  .lib-save-btn {
    flex: 1;
    padding: 0.4rem;
    background: #f8f7f5;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 5px;
    font-size: 0.78rem;
    cursor: pointer;
    font-family: inherit;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    color: var(--admin-text);
    transition: all 0.15s;
  }
  .lib-save-btn:hover { border-color: var(--admin-primary); color: var(--admin-primary); }

  /* Variable chips */
  .var-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
    margin-top: 0.4rem;
  }
  .var-chip {
    padding: 0.2rem 0.5rem;
    background: #fef3e2;
    border: 1px solid #f5deb3;
    border-radius: 12px;
    font-size: 0.72rem;
    color: #92600a;
    cursor: pointer;
    font-family: monospace;
    transition: all 0.15s;
  }
  .var-chip:hover { background: #fde8c4; border-color: #e8c57a; }
</style>

<?php
  $replyTo = $replyTo ?? '';
  $replySubject = $replySubject ?? '';
  $replyBody = $replyBody ?? '';
  $fromAddress = $fromAddress ?? 'contact@estimation-immobilier-bordeaux.fr';
  $fromName = $fromName ?? 'Estimation Immobilier Bordeaux';
  $draftId = $draftId ?? 0;
  $draftCc = $draftCc ?? '';
  $draftScheduledAt = $draftScheduledAt ?? '';
  $draftStatus = $draftStatus ?? '';
?>

<!-- HEADER -->
<div class="compose-header">
  <a href="/admin/mailbox" class="compose-back">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
  <span class="compose-title"><i class="fas fa-pen" style="color:var(--admin-primary);margin-right:0.3rem;"></i> Nouveau message</span>
</div>

<div class="compose-layout">
  <!-- LEFT: COMPOSE FORM -->
  <div class="compose-main">
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
        <label>A</label>
        <input type="email" id="compose-to" placeholder="destinataire@example.com" value="<?= htmlspecialchars($replyTo) ?>" required>
        <a class="toggle-cc" onclick="document.querySelector('.cc-field').classList.toggle('show')">Cc</a>
      </div>

      <!-- CC -->
      <div class="compose-field cc-field <?= $draftCc !== '' ? 'show' : '' ?>">
        <label>Cc</label>
        <input type="text" id="compose-cc" placeholder="cc@example.com" value="<?= htmlspecialchars($draftCc) ?>">
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
          <span class="toolbar-sep"></span>
          <button type="button" onclick="execCmd('insertUnorderedList')" title="Liste"><i class="fas fa-list-ul"></i></button>
          <button type="button" onclick="execCmd('insertOrderedList')" title="Liste numerotee"><i class="fas fa-list-ol"></i></button>
          <span class="toolbar-sep"></span>
          <button type="button" onclick="insertLink()" title="Lien"><i class="fas fa-link"></i></button>

          <span class="toolbar-sep"></span>

          <!-- Heading dropdown -->
          <div class="toolbar-dropdown">
            <button type="button" onclick="toggleDropdown(this)" title="Titre"><i class="fas fa-heading"></i></button>
            <div class="toolbar-dropdown-content">
              <button type="button" onclick="execCmd('formatBlock','h2');closeDropdowns()">Titre H2</button>
              <button type="button" onclick="execCmd('formatBlock','h3');closeDropdowns()">Titre H3</button>
              <button type="button" onclick="execCmd('formatBlock','p');closeDropdowns()">Paragraphe</button>
            </div>
          </div>

          <!-- Text color -->
          <div class="toolbar-dropdown">
            <button type="button" onclick="toggleDropdown(this)" title="Couleur"><i class="fas fa-palette"></i></button>
            <div class="toolbar-dropdown-content">
              <button type="button" onclick="execCmdVal('foreColor','#8B1538');closeDropdowns()" style="color:#8B1538"><i class="fas fa-circle"></i> Bordeaux</button>
              <button type="button" onclick="execCmdVal('foreColor','#1a1410');closeDropdowns()"><i class="fas fa-circle"></i> Noir</button>
              <button type="button" onclick="execCmdVal('foreColor','#2563eb');closeDropdowns()" style="color:#2563eb"><i class="fas fa-circle"></i> Bleu</button>
              <button type="button" onclick="execCmdVal('foreColor','#16a34a');closeDropdowns()" style="color:#16a34a"><i class="fas fa-circle"></i> Vert</button>
              <button type="button" onclick="execCmdVal('foreColor','#dc2626');closeDropdowns()" style="color:#dc2626"><i class="fas fa-circle"></i> Rouge</button>
            </div>
          </div>

          <span class="toolbar-sep"></span>

          <!-- Insert variable dropdown -->
          <div class="toolbar-dropdown">
            <button type="button" onclick="toggleDropdown(this)" title="Inserer variable"><i class="fas fa-code"></i> <span style="font-size:0.75rem">Variable</span></button>
            <div class="toolbar-dropdown-content">
              <button type="button" onclick="insertVar('{{nom}}');closeDropdowns()">{{nom}}</button>
              <button type="button" onclick="insertVar('{{prenom}}');closeDropdowns()">{{prenom}}</button>
              <button type="button" onclick="insertVar('{{email}}');closeDropdowns()">{{email}}</button>
              <button type="button" onclick="insertVar('{{ville}}');closeDropdowns()">{{ville}}</button>
              <button type="button" onclick="insertVar('{{type_bien}}');closeDropdowns()">{{type_bien}}</button>
              <button type="button" onclick="insertVar('{{estimation}}');closeDropdowns()">{{estimation}}</button>
              <button type="button" onclick="insertVar('{{date}}');closeDropdowns()">{{date}}</button>
            </div>
          </div>
        </div>
        <div contenteditable="true" id="compose-editor"><?= $replyBody ?></div>
      </div>

      <!-- Footer -->
      <div class="compose-footer">
        <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
          <input type="hidden" id="draft-id" value="<?= (int) $draftId ?>">
          <button class="btn-send" id="btn-send" onclick="sendEmail()">
            <i class="fas fa-paper-plane"></i> Envoyer
          </button>
          <button class="btn-draft" id="btn-draft" onclick="saveDraft()">
            <i class="fas fa-file-alt"></i> Brouillon
          </button>
          <button class="btn-schedule" id="btn-schedule" onclick="toggleSchedulePicker()">
            <i class="fas fa-clock"></i> Planifier
          </button>
          <span class="compose-status" id="compose-status"></span>
        </div>
        <button class="btn-discard" onclick="discardDraft()">
          <i class="fas fa-trash"></i> Supprimer
        </button>
      </div>
      <!-- Schedule datetime picker -->
      <div class="schedule-picker" id="schedule-picker">
        <i class="fas fa-clock" style="color:#1565c0;"></i>
        <span>Envoyer le :</span>
        <input type="datetime-local" id="schedule-datetime" value="<?= $draftScheduledAt ? date('Y-m-d\TH:i', strtotime($draftScheduledAt)) : '' ?>" min="<?= date('Y-m-d\TH:i') ?>">
        <button class="schedule-confirm-btn" onclick="scheduleEmail()">Confirmer</button>
        <button class="schedule-cancel-btn" onclick="toggleSchedulePicker()"><i class="fas fa-times"></i></button>
      </div>
    </div>
  </div>

  <!-- RIGHT: SIDEBAR -->
  <div class="compose-sidebar">

    <!-- AI ASSISTANT CARD -->
    <div class="sidebar-card">
      <div class="sidebar-card-header" onclick="this.classList.toggle('collapsed')">
        <i class="fas fa-robot" style="color:var(--admin-primary)"></i>
        Assistant IA
        <i class="fas fa-chevron-down toggle-icon"></i>
      </div>
      <div class="sidebar-card-body">
        <!-- Quick AI actions -->
        <div class="ai-actions">
          <button class="ai-action-btn" onclick="aiAction('rewrite')" title="Ameliorer le texte actuel">
            <i class="fas fa-magic"></i> Ameliorer
          </button>
          <button class="ai-action-btn" onclick="aiAction('shorter')" title="Raccourcir">
            <i class="fas fa-compress-alt"></i> Raccourcir
          </button>
          <button class="ai-action-btn" onclick="aiAction('longer')" title="Developper">
            <i class="fas fa-expand-alt"></i> Developper
          </button>
          <button class="ai-action-btn" onclick="aiAction('formal')" title="Ton formel">
            <i class="fas fa-user-tie"></i> Formel
          </button>
          <button class="ai-action-btn" onclick="aiAction('friendly')" title="Ton amical">
            <i class="fas fa-smile"></i> Amical
          </button>
          <button class="ai-action-btn" onclick="aiAction('fix_grammar')" title="Corriger l'orthographe">
            <i class="fas fa-spell-check"></i> Corriger
          </button>
          <button class="ai-action-btn" onclick="aiAction('translate_en')" title="Traduire en anglais">
            <i class="fas fa-globe"></i> EN
          </button>
          <button class="ai-action-btn" onclick="aiAction('translate_fr')" title="Traduire en francais">
            <i class="fas fa-globe"></i> FR
          </button>
        </div>

        <!-- Freeform AI input -->
        <div class="ai-freeform">
          <textarea id="ai-instructions" placeholder="Decrivez l'email a rediger, ex: &laquo; Email de relance pour un proprietaire qui a demande une estimation il y a 2 semaines &raquo;"></textarea>
        </div>

        <button class="ai-generate-btn" onclick="aiAction('write')" id="ai-write-btn">
          <i class="fas fa-pen-fancy"></i> Rediger avec l'IA
        </button>

        <button class="ai-generate-btn" onclick="aiAction('subject_ideas')" style="margin-top:0.4rem;background:linear-gradient(135deg,#6b6459,#8b8279)">
          <i class="fas fa-lightbulb"></i> Suggestions d'objets
        </button>

        <div class="ai-status" id="ai-status"></div>
      </div>
    </div>

    <!-- EMAIL LIBRARY CARD -->
    <div class="sidebar-card">
      <div class="sidebar-card-header" onclick="this.classList.toggle('collapsed')">
        <i class="fas fa-book-open" style="color:var(--admin-primary)"></i>
        Bibliotheque d'emails
        <i class="fas fa-chevron-down toggle-icon"></i>
      </div>
      <div class="sidebar-card-body">
        <input type="text" class="lib-search" id="lib-search" placeholder="Rechercher un modele..." oninput="loadLibrary()">

        <div class="lib-filters" id="lib-filters">
          <button class="lib-filter-btn active" data-cat="" onclick="filterLibrary(this,'')">Tous</button>
          <button class="lib-filter-btn" data-cat="prospection" onclick="filterLibrary(this,'prospection')">Prospection</button>
          <button class="lib-filter-btn" data-cat="relance" onclick="filterLibrary(this,'relance')">Relance</button>
          <button class="lib-filter-btn" data-cat="estimation" onclick="filterLibrary(this,'estimation')">Estimation</button>
          <button class="lib-filter-btn" data-cat="bienvenue" onclick="filterLibrary(this,'bienvenue')">Bienvenue</button>
          <button class="lib-filter-btn" data-cat="suivi" onclick="filterLibrary(this,'suivi')">Suivi</button>
          <button class="lib-filter-btn" data-cat="partenaire" onclick="filterLibrary(this,'partenaire')">Partenaire</button>
          <button class="lib-filter-btn" data-cat="marketing" onclick="filterLibrary(this,'marketing')">Marketing</button>
        </div>

        <div class="lib-templates" id="lib-templates">
          <div class="lib-empty"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>
        </div>

        <div class="lib-actions">
          <button class="lib-save-btn" onclick="saveCurrentAsTemplate()">
            <i class="fas fa-save"></i> Sauvegarder comme modele
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
/* === EDITOR COMMANDS === */
function execCmd(command, value) {
  if (value) {
    document.execCommand('formatBlock', false, '<' + value + '>');
  } else {
    document.execCommand(command, false, null);
  }
  document.getElementById('compose-editor').focus();
}

function execCmdVal(command, value) {
  document.execCommand(command, false, value);
  document.getElementById('compose-editor').focus();
}

function insertLink() {
  var url = prompt('URL du lien :', 'https://');
  if (url) {
    document.execCommand('createLink', false, url);
    document.getElementById('compose-editor').focus();
  }
}

function insertVar(variable) {
  document.getElementById('compose-editor').focus();
  document.execCommand('insertText', false, variable);
}

/* === TOOLBAR DROPDOWNS === */
function toggleDropdown(btn) {
  var dd = btn.parentElement.querySelector('.toolbar-dropdown-content');
  var wasOpen = dd.classList.contains('show');
  closeDropdowns();
  if (!wasOpen) dd.classList.add('show');
}
function closeDropdowns() {
  document.querySelectorAll('.toolbar-dropdown-content.show').forEach(function(d) { d.classList.remove('show'); });
}
document.addEventListener('click', function(e) {
  if (!e.target.closest('.toolbar-dropdown')) closeDropdowns();
});

/* === SEND EMAIL === */
function sendEmail() {
  var to = document.getElementById('compose-to').value.trim();
  var cc = document.getElementById('compose-cc').value.trim();
  var subject = document.getElementById('compose-subject').value.trim();
  var body = document.getElementById('compose-editor').innerHTML.trim();
  var btn = document.getElementById('btn-send');
  var status = document.getElementById('compose-status');
  var draftId = document.getElementById('draft-id').value;

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
        status.innerHTML = '<i class="fas fa-check-circle"></i> ' + (data.message || 'Envoye !');
        btn.innerHTML = '<i class="fas fa-check"></i> Envoye';
        // Delete draft if it existed
        if (draftId && parseInt(draftId) > 0) {
          var dfd = new FormData();
          dfd.append('id', draftId);
          fetch('/admin/mailbox/delete-draft', { method: 'POST', body: dfd, credentials: 'same-origin' });
        }
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
      status.innerHTML = '<i class="fas fa-times-circle"></i> Erreur reseau.';
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
    });
}

/* === SAVE DRAFT === */
function saveDraft() {
  var to = document.getElementById('compose-to').value.trim();
  var cc = document.getElementById('compose-cc').value.trim();
  var subject = document.getElementById('compose-subject').value.trim();
  var body = document.getElementById('compose-editor').innerHTML.trim();
  var btn = document.getElementById('btn-draft');
  var status = document.getElementById('compose-status');
  var draftId = document.getElementById('draft-id').value;

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';

  var fd = new FormData();
  fd.append('to', to);
  fd.append('cc', cc);
  fd.append('subject', subject);
  fd.append('body', body);
  if (draftId && parseInt(draftId) > 0) fd.append('draft_id', draftId);

  fetch('/admin/mailbox/save-draft', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-file-alt"></i> Brouillon';
      if (data.success) {
        if (data.draft_id) document.getElementById('draft-id').value = data.draft_id;
        status.className = 'compose-status visible success';
        status.innerHTML = '<i class="fas fa-check-circle"></i> Brouillon sauvegarde';
        setTimeout(function() { status.className = 'compose-status'; }, 3000);
      } else {
        status.className = 'compose-status visible error';
        status.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'Erreur.');
      }
    })
    .catch(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-file-alt"></i> Brouillon';
      status.className = 'compose-status visible error';
      status.innerHTML = '<i class="fas fa-times-circle"></i> Erreur reseau.';
    });
}

/* === SCHEDULE EMAIL === */
function toggleSchedulePicker() {
  var picker = document.getElementById('schedule-picker');
  picker.classList.toggle('show');
  if (picker.classList.contains('show') && !document.getElementById('schedule-datetime').value) {
    // Default to tomorrow at 9:00
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(9, 0, 0, 0);
    document.getElementById('schedule-datetime').value = tomorrow.toISOString().slice(0, 16);
  }
}

function scheduleEmail() {
  var to = document.getElementById('compose-to').value.trim();
  var cc = document.getElementById('compose-cc').value.trim();
  var subject = document.getElementById('compose-subject').value.trim();
  var body = document.getElementById('compose-editor').innerHTML.trim();
  var scheduledAt = document.getElementById('schedule-datetime').value;
  var status = document.getElementById('compose-status');
  var draftId = document.getElementById('draft-id').value;
  var btn = document.getElementById('btn-schedule');

  if (!to) { alert('Veuillez saisir un destinataire.'); return; }
  if (!subject) { alert('Veuillez saisir un objet.'); return; }
  if (!body || body === '<br>') { alert('Veuillez saisir un message.'); return; }
  if (!scheduledAt) { alert('Veuillez choisir une date.'); return; }

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Planification...';

  var fd = new FormData();
  fd.append('to', to);
  fd.append('cc', cc);
  fd.append('subject', subject);
  fd.append('body', body);
  fd.append('scheduled_at', scheduledAt.replace('T', ' ') + ':00');
  if (draftId && parseInt(draftId) > 0) fd.append('draft_id', draftId);

  fetch('/admin/mailbox/schedule', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-clock"></i> Planifier';
      if (data.success) {
        if (data.draft_id) document.getElementById('draft-id').value = data.draft_id;
        status.className = 'compose-status visible success';
        status.innerHTML = '<i class="fas fa-check-circle"></i> ' + (data.message || 'Email planifie !');
        setTimeout(function() { window.location.href = '/admin/mailbox?folder=_scheduled'; }, 2000);
      } else {
        status.className = 'compose-status visible error';
        status.innerHTML = '<i class="fas fa-times-circle"></i> ' + (data.message || 'Erreur.');
      }
    })
    .catch(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-clock"></i> Planifier';
      status.className = 'compose-status visible error';
      status.innerHTML = '<i class="fas fa-times-circle"></i> Erreur reseau.';
    });
}

/* === DISCARD DRAFT === */
function discardDraft() {
  if (!confirm('Supprimer ce brouillon ?')) return;
  var draftId = document.getElementById('draft-id').value;
  if (draftId && parseInt(draftId) > 0) {
    var fd = new FormData();
    fd.append('id', draftId);
    fetch('/admin/mailbox/delete-draft', { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function() { window.location.href = '/admin/mailbox?folder=_drafts'; });
  } else {
    window.location.href = '/admin/mailbox';
  }
}

/* === AI ASSISTANT === */
var aiBusy = false;

function aiAction(action) {
  if (aiBusy) return;

  var editor = document.getElementById('compose-editor');
  var content = editor.innerHTML.trim();
  var instructions = document.getElementById('ai-instructions').value.trim();
  var recipient = document.getElementById('compose-to').value.trim();
  var subject = document.getElementById('compose-subject').value.trim();
  var statusEl = document.getElementById('ai-status');

  // For write action, instructions are required
  if (action === 'write' && !instructions) {
    statusEl.className = 'ai-status error';
    statusEl.textContent = 'Decrivez l\'email a rediger dans le champ ci-dessus.';
    return;
  }

  // For transform actions, content is required
  if (action !== 'write' && action !== 'subject_ideas' && (!content || content === '<br>')) {
    statusEl.className = 'ai-status error';
    statusEl.textContent = 'Redigez d\'abord un email, puis utilisez l\'IA pour l\'ameliorer.';
    return;
  }

  aiBusy = true;
  statusEl.className = 'ai-status';
  statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> L\'IA redige...';
  disableAiButtons(true);

  var fd = new FormData();
  fd.append('action', action);
  fd.append('content', content);
  fd.append('instructions', instructions);
  fd.append('recipient', recipient);
  fd.append('subject', subject);

  fetch('/admin/mailbox/ai-assist', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      aiBusy = false;
      disableAiButtons(false);
      if (data.success) {
        if (action === 'subject_ideas') {
          // Show suggestions in a temp area inside the editor
          var prev = editor.innerHTML;
          var separator = prev && prev !== '<br>' ? '<hr style="margin:1rem 0;border-color:#e8dfd7;">' : '';
          editor.innerHTML = prev + separator + '<div style="background:#fef3e2;padding:0.8rem;border-radius:6px;border:1px solid #f5deb3;margin-top:0.5rem;"><strong style="color:#92600a;">Suggestions d\'objets :</strong><br>' + data.content + '</div>';
        } else {
          editor.innerHTML = data.content;
        }
        var provider = data.provider === 'claude' ? 'Claude' : 'OpenAI';
        statusEl.className = 'ai-status';
        statusEl.innerHTML = '<i class="fas fa-check-circle" style="color:var(--admin-success)"></i> Genere via ' + provider;
      } else {
        statusEl.className = 'ai-status error';
        statusEl.textContent = data.message || 'Erreur IA.';
      }
    })
    .catch(function() {
      aiBusy = false;
      disableAiButtons(false);
      statusEl.className = 'ai-status error';
      statusEl.textContent = 'Erreur reseau.';
    });
}

function disableAiButtons(disabled) {
  document.querySelectorAll('.ai-action-btn, .ai-generate-btn').forEach(function(btn) {
    btn.disabled = disabled;
  });
}

/* === EMAIL LIBRARY === */
var libCategory = '';
var libDebounce = null;

function loadLibrary() {
  clearTimeout(libDebounce);
  libDebounce = setTimeout(function() { fetchLibrary(); }, 250);
}

function filterLibrary(btn, cat) {
  document.querySelectorAll('.lib-filter-btn').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  libCategory = cat;
  fetchLibrary();
}

function fetchLibrary() {
  var search = document.getElementById('lib-search').value.trim();
  var container = document.getElementById('lib-templates');

  var url = '/admin/mailbox/email-library?category=' + encodeURIComponent(libCategory) + '&q=' + encodeURIComponent(search);

  fetch(url, { credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success || !data.templates || data.templates.length === 0) {
        container.innerHTML = '<div class="lib-empty"><i class="fas fa-inbox"></i><br>Aucun modele trouve</div>';
        return;
      }

      var html = '';
      data.templates.forEach(function(t) {
        html += '<div class="lib-template-card" onclick="useTemplate(' + t.id + ',this)" data-subject="' + escHtml(t.subject) + '" data-body="' + escHtml(t.body_html) + '">';
        html += '<div class="lib-template-name">' + escHtml(t.name) + '</div>';
        html += '<div class="lib-template-subject">' + escHtml(t.subject) + '</div>';
        html += '<div class="lib-template-meta"><span class="cat-badge">' + escHtml(t.category) + '</span>';
        if (t.usage_count > 0) html += '<span><i class="fas fa-chart-bar"></i> ' + t.usage_count + 'x</span>';
        html += '</div></div>';
      });

      container.innerHTML = html;
    })
    .catch(function() {
      container.innerHTML = '<div class="lib-empty">Erreur de chargement</div>';
    });
}

function escHtml(str) {
  if (!str) return '';
  var div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML.replace(/"/g, '&quot;');
}

function useTemplate(id, card) {
  var subject = card.getAttribute('data-subject');
  var body = card.getAttribute('data-body');

  var subjectInput = document.getElementById('compose-subject');
  var editor = document.getElementById('compose-editor');

  // If fields are not empty, confirm replacement
  var currentBody = editor.innerHTML.trim();
  if ((subjectInput.value.trim() || (currentBody && currentBody !== '<br>')) &&
      !confirm('Remplacer le contenu actuel par ce modele ?')) {
    return;
  }

  if (subject) subjectInput.value = subject;
  if (body) editor.innerHTML = body;

  // Track usage
  var fd = new FormData();
  fd.append('id', id);
  fetch('/admin/mailbox/email-library/use', { method: 'POST', body: fd, credentials: 'same-origin' });
}

function saveCurrentAsTemplate() {
  var subject = document.getElementById('compose-subject').value.trim();
  var body = document.getElementById('compose-editor').innerHTML.trim();

  if (!subject && (!body || body === '<br>')) {
    alert('Redigez un email avant de le sauvegarder comme modele.');
    return;
  }

  var name = prompt('Nom du modele :', '');
  if (!name) return;

  var category = prompt('Categorie (prospection, relance, estimation, bienvenue, suivi, partenaire, marketing, autre) :', 'autre');
  if (!category) category = 'autre';

  var tags = prompt('Tags (separes par des virgules, optionnel) :', '');

  var fd = new FormData();
  fd.append('name', name);
  fd.append('category', category);
  fd.append('subject', subject);
  fd.append('body_html', body);
  fd.append('tags', tags || '');

  fetch('/admin/mailbox/email-library/save', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        alert('Modele sauvegarde !');
        fetchLibrary();
      } else {
        alert('Erreur : ' + (data.message || 'Erreur'));
      }
    })
    .catch(function() { alert('Erreur reseau.'); });
}

/* === AUTO-SAVE DRAFT === */
var autoSaveTimer = null;
function scheduleAutoSave() {
  clearTimeout(autoSaveTimer);
  autoSaveTimer = setTimeout(function() {
    var body = document.getElementById('compose-editor').innerHTML.trim();
    var subject = document.getElementById('compose-subject').value.trim();
    if (body && body !== '<br>' || subject) {
      saveDraftSilent();
    }
  }, 30000); // Auto-save every 30s of inactivity
}

function saveDraftSilent() {
  var to = document.getElementById('compose-to').value.trim();
  var cc = document.getElementById('compose-cc').value.trim();
  var subject = document.getElementById('compose-subject').value.trim();
  var body = document.getElementById('compose-editor').innerHTML.trim();
  var draftId = document.getElementById('draft-id').value;
  var status = document.getElementById('compose-status');

  var fd = new FormData();
  fd.append('to', to);
  fd.append('cc', cc);
  fd.append('subject', subject);
  fd.append('body', body);
  if (draftId && parseInt(draftId) > 0) fd.append('draft_id', draftId);

  fetch('/admin/mailbox/save-draft', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success && data.draft_id) {
        document.getElementById('draft-id').value = data.draft_id;
        status.className = 'compose-status visible';
        status.innerHTML = '<i class="fas fa-save" style="color:var(--admin-muted)"></i> Sauvegarde auto';
        setTimeout(function() { status.className = 'compose-status'; }, 2000);
      }
    })
    .catch(function() {});
}

/* === INIT === */
document.addEventListener('DOMContentLoaded', function() {
  var to = document.getElementById('compose-to');
  if (to && !to.value) {
    to.focus();
  } else {
    document.getElementById('compose-editor').focus();
  }

  // Load email library
  fetchLibrary();

  // Setup auto-save listeners
  document.getElementById('compose-editor').addEventListener('input', scheduleAutoSave);
  document.getElementById('compose-subject').addEventListener('input', scheduleAutoSave);
  document.getElementById('compose-to').addEventListener('input', scheduleAutoSave);
});
</script>
