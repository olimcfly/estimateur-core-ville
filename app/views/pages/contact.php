<?php
$page_title = 'Contact | Estimation Immobilière Premium';
$_contactEmail = \App\Core\Config::get('mail.admin_email') ?: \App\Core\Config::get('mail.from') ?: 'contact@example.test';
?>
<section class="section">
  <div class="container premium-grid-2">
    <article class="premium-card">
      <h1>Contactez notre équipe vendeurs</h1>
      <p>Un conseiller vous répond sous 24h pour cadrer votre projet de vente.</p>
      <p><strong>Email :</strong> <a href="mailto:<?= e((string) $_contactEmail) ?>"><?= e((string) $_contactEmail) ?></a></p>
      <p><strong>Téléphone :</strong> <a href="tel:+33556000000">+33 5 56 00 00 00</a></p>
    </article>
    <article class="premium-card">
      <h2>Parlez-nous de votre bien</h2>
      <form class="premium-form" action="/contact" method="post">
        <input type="text" name="nom" placeholder="Nom complet" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="tel" name="telephone" placeholder="Téléphone">
        <textarea name="message" rows="5" placeholder="Votre besoin" required></textarea>
        <label><input type="checkbox" name="rgpd" required> J'accepte la politique de confidentialité.</label>
        <button class="btn btn-gold" type="submit">Envoyer</button>
      </form>
    </article>
  </div>
</section>
