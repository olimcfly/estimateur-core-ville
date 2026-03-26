<style>
  .admin-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .admin-page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .admin-page-header h1 i { color: var(--admin-primary); }

  .btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.85rem;
    color: var(--admin-muted);
    text-decoration: none;
    transition: all 0.15s;
  }

  .btn-back:hover { border-color: var(--admin-primary); color: var(--admin-primary); }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
  }

  .form-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .form-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--admin-text);
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-card-title i { color: var(--admin-primary); }

  .form-group {
    margin-bottom: 1rem;
  }

  .form-group label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--admin-text);
    margin-bottom: 0.35rem;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 0.55rem 0.75rem;
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    font-size: 0.88rem;
    color: var(--admin-text);
    background: #fff;
    transition: border-color 0.15s;
    box-sizing: border-box;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: var(--admin-primary);
  }

  .form-group textarea { min-height: 80px; resize: vertical; }

  .form-group .hint {
    font-size: 0.78rem;
    color: var(--admin-muted);
    margin-top: 0.25rem;
  }

  .checkbox-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
  }

  .checkbox-group input[type="checkbox"] { width: auto; }

  .btn-save {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.65rem 1.5rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .btn-save:hover { opacity: 0.9; }

  .error-list {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.2);
    border-radius: var(--admin-radius);
    padding: 0.85rem 1.25rem;
    margin-bottom: 1.5rem;
    color: #dc2626;
    font-size: 0.9rem;
  }

  .form-full { grid-column: 1 / -1; }

  @media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
  }
</style>

<?php
  $d = $demande ?? [];
  $errs = $errors ?? [];
  $sLabels = $statutLabels ?? [];
  $tpLabels = $typeProjetLabels ?? [];
  $spLabels = $situationProLabels ?? [];
  $isEdit = !empty($d['id']);

  $val = function(string $key, string $default = '') use ($d) {
      return htmlspecialchars((string) ($d[$key] ?? $default), ENT_QUOTES, 'UTF-8');
  };
?>

<!-- ERRORS -->
<?php if (!empty($errs)): ?>
  <div class="error-list">
    <?php foreach ($errs as $err): ?>
      <div><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-credit-card"></i> <?= $isEdit ? 'Modifier la Demande' : 'Nouvelle Demande de Financement' ?></h1>
  <a href="/admin/financement" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<form method="POST" action="/admin/financement/save">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
  <?php endif; ?>

  <!-- IDENTITE -->
  <div class="form-card">
    <div class="form-card-title"><i class="fas fa-user"></i> Identite du demandeur</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="nom">Nom *</label>
        <input type="text" id="nom" name="nom" value="<?= $val('nom') ?>" required>
      </div>
      <div class="form-group">
        <label for="prenom">Prenom</label>
        <input type="text" id="prenom" name="prenom" value="<?= $val('prenom') ?>">
      </div>
      <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="<?= $val('email') ?>" required>
      </div>
      <div class="form-group">
        <label for="telephone">Telephone</label>
        <input type="tel" id="telephone" name="telephone" value="<?= $val('telephone') ?>">
      </div>
    </div>
  </div>

  <!-- SITUATION PROFESSIONNELLE -->
  <div class="form-card">
    <div class="form-card-title"><i class="fas fa-briefcase"></i> Situation professionnelle</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="situation_pro">Situation</label>
        <select id="situation_pro" name="situation_pro">
          <option value="">-- Selectionner --</option>
          <?php foreach ($spLabels as $key => $label): ?>
            <option value="<?= $key ?>" <?= ($d['situation_pro'] ?? '') === $key ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="revenus_mensuels">Revenus mensuels nets (&euro;)</label>
        <input type="number" id="revenus_mensuels" name="revenus_mensuels" value="<?= $val('revenus_mensuels') ?>" step="0.01" min="0">
      </div>
      <div class="form-group">
        <label>Co-emprunteur</label>
        <div class="checkbox-group">
          <input type="checkbox" id="co_emprunteur" name="co_emprunteur" value="1" <?= !empty($d['co_emprunteur']) ? 'checked' : '' ?>>
          <label for="co_emprunteur" style="margin-bottom:0;font-weight:400;">Oui, il y a un co-emprunteur</label>
        </div>
      </div>
      <div class="form-group">
        <label for="revenus_co_emprunteur">Revenus co-emprunteur (&euro;/mois)</label>
        <input type="number" id="revenus_co_emprunteur" name="revenus_co_emprunteur" value="<?= $val('revenus_co_emprunteur') ?>" step="0.01" min="0">
      </div>
    </div>
  </div>

  <!-- PROJET IMMOBILIER -->
  <div class="form-card">
    <div class="form-card-title"><i class="fas fa-home"></i> Projet immobilier</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="type_projet">Type de projet</label>
        <select id="type_projet" name="type_projet">
          <?php foreach ($tpLabels as $key => $label): ?>
            <option value="<?= $key ?>" <?= ($d['type_projet'] ?? 'achat_residence_principale') === $key ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="type_bien">Type de bien</label>
        <input type="text" id="type_bien" name="type_bien" value="<?= $val('type_bien') ?>" placeholder="Appartement, maison...">
      </div>
      <div class="form-group">
        <label for="montant_projet">Montant du projet (&euro;)</label>
        <input type="number" id="montant_projet" name="montant_projet" value="<?= $val('montant_projet') ?>" step="0.01" min="0">
      </div>
      <div class="form-group">
        <label for="apport_personnel">Apport personnel (&euro;)</label>
        <input type="number" id="apport_personnel" name="apport_personnel" value="<?= $val('apport_personnel') ?>" step="0.01" min="0">
      </div>
      <div class="form-group">
        <label for="montant_pret_souhaite">Montant du pret souhaite (&euro;)</label>
        <input type="number" id="montant_pret_souhaite" name="montant_pret_souhaite" value="<?= $val('montant_pret_souhaite') ?>" step="0.01" min="0">
      </div>
      <div class="form-group">
        <label for="duree_souhaitee_mois">Duree souhaitee (mois)</label>
        <input type="number" id="duree_souhaitee_mois" name="duree_souhaitee_mois" value="<?= $val('duree_souhaitee_mois') ?>" min="12" max="360" placeholder="240 = 20 ans">
      </div>
      <div class="form-group">
        <label for="ville">Ville</label>
        <input type="text" id="ville" name="ville" value="<?= $val('ville', site('city', '')) ?>">
      </div>
      <div class="form-group">
        <label for="quartier">Quartier</label>
        <input type="text" id="quartier" name="quartier" value="<?= $val('quartier') ?>">
      </div>
    </div>
  </div>

  <!-- GESTION -->
  <div class="form-card">
    <div class="form-card-title"><i class="fas fa-cog"></i> Gestion de la demande</div>
    <div class="form-grid">
      <div class="form-group">
        <label for="statut">Statut</label>
        <select id="statut" name="statut">
          <?php foreach ($sLabels as $key => $label): ?>
            <option value="<?= $key ?>" <?= ($d['statut'] ?? 'nouvelle') === $key ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="courtier_reference">Courtier de reference</label>
        <input type="text" id="courtier_reference" name="courtier_reference" value="<?= $val('courtier_reference', '2L Courtage') ?>">
      </div>
      <?php if ($isEdit): ?>
      <div class="form-group">
        <label for="date_transmission">Date de transmission</label>
        <input type="date" id="date_transmission" name="date_transmission" value="<?= $val('date_transmission') ?>">
      </div>
      <?php endif; ?>
      <div class="form-group form-full">
        <label for="notes_internes">Notes internes</label>
        <textarea id="notes_internes" name="notes_internes" rows="3"><?= $val('notes_internes') ?></textarea>
      </div>
      <?php if ($isEdit): ?>
      <div class="form-group form-full">
        <label for="notes_courtier">Notes courtier (retour 2L Courtage)</label>
        <textarea id="notes_courtier" name="notes_courtier" rows="3"><?= $val('notes_courtier') ?></textarea>
        <div class="hint">Notes de suivi recues du courtier partenaire.</div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <button type="submit" class="btn-save"><i class="fas fa-save"></i> <?= $isEdit ? 'Mettre a jour' : 'Enregistrer' ?></button>
</form>
