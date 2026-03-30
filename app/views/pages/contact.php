<?php
$_contactEmail = \App\Core\Config::get('mail.admin_email') ?: \App\Core\Config::get('mail.from') ?: 'contact@example.test';
?>
<section class="section">
  <div class="container premium-grid-2">
    <article class="premium-card">
      <h1>Contactez notre équipe dédiée aux vendeurs</h1>
      <p>Nous vous répondons rapidement pour cadrer votre projet immobilier et vos priorités de vente.</p>
      <ul class="premium-bullet-list">
        <li>Email : <a href="mailto:<?= e((string) $_contactEmail) ?>"><?= e((string) $_contactEmail) ?></a></li>
        <li>Téléphone : <a href="tel:+33556000000">+33 5 56 00 00 00</a></li>
        <li>Délai moyen de réponse : 24h ouvrées</li>
      </ul>
      <a class="btn btn-secondary" href="/estimation#form-estimation">Commencer par une estimation</a>
    </article>

    <article class="premium-card">
      <h2>Parlez-nous de votre bien</h2>
      <form class="premium-form" action="/contact" method="post">
        <label for="contact-name">Nom complet</label>
        <input id="contact-name" type="text" name="nom" placeholder="Nom complet" required>

        <label for="contact-email">Email</label>
        <input id="contact-email" type="email" name="email" placeholder="Email" required>

        <label for="contact-phone">Téléphone</label>
        <input id="contact-phone" type="tel" name="telephone" placeholder="Téléphone">

        <label for="contact-message">Message</label>
        <textarea id="contact-message" name="message" rows="5" placeholder="Décrivez votre projet" required></textarea>

        <label class="premium-checkbox"><input type="checkbox" name="rgpd" required> J'accepte la politique de confidentialité.</label>
        <button class="btn btn-gold" type="submit">Envoyer ma demande</button>
      </form>
    </article>
  </div>
</section>
