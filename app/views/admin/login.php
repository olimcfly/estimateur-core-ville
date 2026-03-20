<?php
use App\Controllers\AuthController;
$csrfToken = AuthController::generateCsrfToken();
?>

<section style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
  <div style="width: 100%; max-width: 440px;">

    <!-- Logo / Header -->
    <div style="text-align: center; margin-bottom: 2.5rem;">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary), #C41E3A); border-radius: 16px; margin-bottom: 1rem; box-shadow: 0 8px 24px rgba(var(--primary-rgb), 0.25);">
        <i class="fas fa-lock" style="font-size: 1.8rem; color: #fff;"></i>
      </div>
      <h1 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--text); margin: 0 0 0.5rem;">Espace Administrateur</h1>
      <p style="color: var(--muted); font-size: 0.95rem; margin: 0;">Connectez-vous pour accéder au tableau de bord</p>
    </div>

    <!-- Messages d'erreur -->
    <?php if (!empty($error_message)): ?>
    <div style="background: rgba(var(--danger-rgb, 226, 75, 74), 0.08); border: 1px solid var(--danger); color: var(--danger); padding: 1rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem;">
      <i class="fas fa-exclamation-circle"></i>
      <span><?= e($error_message) ?></span>
    </div>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form method="POST" action="/admin/login" style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(var(--neutral-rgb), 0.06);">
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

      <div style="margin-bottom: 1.5rem;">
        <label for="email" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text); margin-bottom: 0.5rem;">
          <i class="fas fa-envelope" style="color: var(--primary); margin-right: 0.4rem;"></i>Adresse email
        </label>
        <input
          type="email"
          id="email"
          name="email"
          value="<?= e((string) ($old_email ?? '')) ?>"
          placeholder="contact@estimation-immobilier-bordeaux.fr"
          required
          autocomplete="email"
          style="width: 100%; padding: 0.9rem 1rem; border: 1px solid var(--border); border-radius: 10px; font-size: 1rem; transition: all 0.2s ease; box-sizing: border-box; background: var(--bg);"
          onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(var(--primary-rgb), 0.08)'"
          onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none'"
        >
      </div>

      <div style="margin-bottom: 1.5rem;">
        <label for="password" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text); margin-bottom: 0.5rem;">
          <i class="fas fa-key" style="color: var(--primary); margin-right: 0.4rem;"></i>Mot de passe
        </label>
        <div style="position: relative;">
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Votre mot de passe"
            required
            autocomplete="current-password"
            style="width: 100%; padding: 0.9rem 3rem 0.9rem 1rem; border: 1px solid var(--border); border-radius: 10px; font-size: 1rem; transition: all 0.2s ease; box-sizing: border-box; background: var(--bg);"
            onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(var(--primary-rgb), 0.08)'"
            onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none'"
          >
          <button type="button" onclick="togglePassword()" style="position: absolute; right: 0.8rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted); padding: 0.4rem;" aria-label="Afficher le mot de passe">
            <i id="toggle-icon" class="fas fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, var(--primary), #C41E3A); color: #fff; border: none; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(var(--primary-rgb), 0.3)'" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(var(--primary-rgb), 0.2)'">
        <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>Se connecter
      </button>
    </form>

    <!-- Footer -->
    <p style="text-align: center; margin-top: 2rem; color: var(--muted); font-size: 0.85rem;">
      <i class="fas fa-shield-alt" style="margin-right: 0.3rem;"></i>Connexion sécurisée SSL
    </p>

  </div>
</section>

<script>
function togglePassword() {
  const input = document.getElementById('password');
  const icon = document.getElementById('toggle-icon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
</script>
