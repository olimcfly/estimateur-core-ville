<?php
/** Guide des bonnes pratiques Google My Business */
?>

<style>
  .gmb-guide-wrap { max-width: 820px; margin: 0 auto; }
  .gmb-guide-wrap .admin-card { transition: none; }
  .gmb-guide-wrap .admin-card:hover { transform: none; box-shadow: none; }
  .gmb-guide-wrap .admin-card-header h2 { font-family: 'DM Sans', sans-serif; }
  .gmb-guide-wrap .admin-card-header--primary {
    background: linear-gradient(135deg, var(--admin-primary), #6b0f2d);
    color: #fff;
    border-bottom: none;
  }
  .gmb-guide-wrap .admin-card-header--primary h2 { color: #fff; }
  .gmb-guide-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .gmb-guide-type-card {
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1rem;
    background: var(--admin-surface);
  }
  .gmb-guide-type-card h4 {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    margin: 0 0 0.4rem;
    color: var(--admin-text);
  }
  .gmb-guide-type-card p { font-size: 0.85rem; color: var(--admin-muted); margin: 0; line-height: 1.5; }
  .gmb-guide-type-card i { margin-right: 0.3rem; }
  .gmb-guide-alert {
    padding: 0.85rem 1rem;
    border-radius: var(--admin-radius);
    font-size: 0.88rem;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    line-height: 1.5;
  }
  .gmb-guide-alert i { margin-top: 0.15rem; flex-shrink: 0; }
  .gmb-guide-alert--info {
    background: rgba(59, 130, 246, 0.08);
    color: #1e40af;
    border: 1px solid rgba(59, 130, 246, 0.15);
  }
  .gmb-guide-alert--warning {
    background: rgba(245, 158, 11, 0.08);
    color: #92400e;
    border: 1px solid rgba(245, 158, 11, 0.15);
  }
  @media (max-width: 640px) {
    .gmb-guide-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="gmb-guide-wrap">
    <div style="margin-bottom: 1rem;">
        <a href="/admin/gmb" class="admin-btn admin-btn-secondary" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
            <i class="fas fa-arrow-left"></i> Retour aux publications
        </a>
    </div>

    <!-- Introduction -->
    <div class="admin-card">
        <div class="admin-card-header admin-card-header--primary">
            <h2><i class="fab fa-google" style="margin-right: 0.4rem;"></i> Guide Google My Business pour l'immobilier</h2>
        </div>
        <div class="admin-card-body">
            <p style="margin: 0 0 1rem; line-height: 1.65;">Google My Business (Google Business Profile) est un outil gratuit essentiel pour les professionnels de l'immobilier.
               Les publications GMB apparaissent directement dans les resultats de recherche Google et Google Maps, vous offrant une visibilite locale maximale.</p>
            <div class="gmb-guide-alert gmb-guide-alert--info">
                <i class="fas fa-chart-line"></i>
                <span><strong>Impact mesure :</strong> Les fiches GMB actives avec des publications regulieres recoivent en moyenne <strong>70% plus de visites</strong> que les fiches inactives.</span>
            </div>
        </div>
    </div>

    <!-- Types de publications -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>1. Types de publications</h2>
        </div>
        <div class="admin-card-body">
            <div class="gmb-guide-grid">
                <div class="gmb-guide-type-card">
                    <h4><i class="fas fa-bullhorn" style="color: var(--admin-primary);"></i> Nouveaute (Update)</h4>
                    <p>Le type le plus courant. Partagez vos actualites, conseils, et mises a jour du marche immobilier local.</p>
                </div>
                <div class="gmb-guide-type-card">
                    <h4><i class="fas fa-calendar-alt" style="color: #16a34a;"></i> Evenement</h4>
                    <p>Portes ouvertes, journees estimation gratuite, webinaires. Incluez toujours les dates.</p>
                </div>
                <div class="gmb-guide-type-card">
                    <h4><i class="fas fa-tag" style="color: #d97706;"></i> Offre</h4>
                    <p>Offres speciales : estimation gratuite, frais d'agence reduits, etc.</p>
                </div>
                <div class="gmb-guide-type-card">
                    <h4><i class="fas fa-home" style="color: #0891b2;"></i> Produit / Service</h4>
                    <p>Mettez en avant vos services : estimation, accompagnement vendeur, recherche acheteur.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bonnes pratiques contenu -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>2. Bonnes pratiques de redaction</h2>
        </div>
        <div class="admin-card-body">
            <h4 style="font-family: 'DM Sans', sans-serif; font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem;">Structure ideale d'une publication :</h4>
            <ol style="margin: 0 0 1.5rem; padding-left: 1.25rem; line-height: 1.8; font-size: 0.9rem;">
                <li><strong>Accroche percutante</strong> (1ere ligne visible) - Question ou chiffre cle</li>
                <li><strong>Corps du message</strong> - Information utile, conseil pratique</li>
                <li><strong>Appel a l'action</strong> - Invitez a vous contacter ou visiter votre site</li>
            </ol>

            <h4 style="font-family: 'DM Sans', sans-serif; font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem;">Regles a respecter :</h4>
            <div class="admin-table-responsive">
                <table class="admin-table" style="margin-bottom: 1.5rem;">
                    <tbody>
                        <tr><td style="font-weight: 700; white-space: nowrap;">Longueur contenu</td><td>1500 caracteres max (ideal : 150-300)</td></tr>
                        <tr><td style="font-weight: 700; white-space: nowrap;">Longueur titre</td><td>58 caracteres max</td></tr>
                        <tr><td style="font-weight: 700; white-space: nowrap;">Frequence</td><td>2 a 3 publications par semaine minimum</td></tr>
                        <tr><td style="font-weight: 700; white-space: nowrap;">Images</td><td>720x540 px minimum, format JPG/PNG</td></tr>
                        <tr><td style="font-weight: 700; white-space: nowrap;">CTA</td><td>Toujours inclure un bouton d'action</td></tr>
                    </tbody>
                </table>
            </div>

            <h4 style="font-family: 'DM Sans', sans-serif; font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem;">Exemples de sujets pour l'immobilier :</h4>
            <ul style="margin: 0; padding-left: 1.25rem; line-height: 1.8; font-size: 0.9rem;">
                <li>Evolution des prix dans votre quartier ce trimestre</li>
                <li>Conseils pour preparer sa maison avant une visite</li>
                <li>Nouveau bien vendu (temoignage client)</li>
                <li>Actualite du marche immobilier local</li>
                <li>Guide : les etapes d'une vente immobiliere</li>
                <li>Estimation gratuite - pourquoi et comment</li>
            </ul>
        </div>
    </div>

    <!-- Calendrier de publication -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>3. Calendrier de publication recommande</h2>
        </div>
        <div class="admin-card-body">
            <p style="margin: 0 0 1rem; line-height: 1.65; font-size: 0.9rem;">Un rythme regulier est plus important que la quantite. Voici un planning type :</p>
            <div class="admin-table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr><th>Jour</th><th>Type de contenu</th><th>Exemple</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Lundi</strong></td>
                            <td>Actualite marche</td>
                            <td>"Cette semaine a Bordeaux : les taux immobiliers restent stables a..."</td>
                        </tr>
                        <tr>
                            <td><strong>Mercredi</strong></td>
                            <td>Conseil / Guide</td>
                            <td>"5 erreurs a eviter quand vous vendez votre appartement..."</td>
                        </tr>
                        <tr>
                            <td><strong>Vendredi</strong></td>
                            <td>Mise en avant service</td>
                            <td>"Estimation gratuite de votre bien a Bordeaux en 48h..."</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SEO local -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>4. Impact SEO local</h2>
        </div>
        <div class="admin-card-body">
            <p style="margin: 0 0 0.75rem; line-height: 1.65; font-size: 0.9rem;">Les publications GMB contribuent a votre referencement local :</p>
            <ul style="margin: 0 0 1.25rem; padding-left: 1.25rem; line-height: 1.8; font-size: 0.9rem;">
                <li><strong>Mots-cles locaux</strong> : Mentionnez "Bordeaux", vos quartiers cibles, la Gironde</li>
                <li><strong>Signaux d'activite</strong> : Google favorise les fiches actives dans le pack local</li>
                <li><strong>Liens vers votre site</strong> : Chaque publication avec CTA renvoie du trafic</li>
                <li><strong>Contenu frais</strong> : Les publications expirent apres 7 jours, restez regulier</li>
            </ul>

            <div class="gmb-guide-alert gmb-guide-alert--warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span><strong>Important :</strong> Les publications GMB expirent automatiquement apres 7 jours (sauf les evenements). C'est pourquoi la regularite est cruciale.</span>
            </div>
        </div>
    </div>

    <!-- Workflow avec cet outil -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>5. Workflow avec cet outil</h2>
        </div>
        <div class="admin-card-body">
            <ol style="margin: 0 0 1.25rem; padding-left: 1.25rem; line-height: 1.8; font-size: 0.9rem;">
                <li><strong>Generation automatique</strong> : Quand vous publiez un article ou une actualite, une publication GMB est automatiquement creee en brouillon</li>
                <li><strong>Revision</strong> : Verifiez et adaptez le contenu genere (max 1500 car.)</li>
                <li><strong>Planification</strong> : Choisissez la date et passez en statut "Planifie"</li>
                <li><strong>Notification</strong> : Le jour J, vous recevez un email avec le contenu a copier-coller sur GMB</li>
                <li><strong>Publication</strong> : Copiez le contenu sur votre fiche Google, puis marquez comme "Publie"</li>
            </ol>
            <div class="gmb-guide-alert gmb-guide-alert--info">
                <i class="fas fa-info-circle"></i>
                <span>L'API Google My Business n'etant pas utilisee directement, la publication se fait manuellement via copier-coller. Le systeme vous notifie et vous guide.</span>
            </div>
        </div>
    </div>
</div>
