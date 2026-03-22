<div class="container">
    <a href="/admin/blog" class="btn btn-small btn-ghost" style="margin-bottom: 1rem; display: inline-block;">&larr; Retour Blog</a>
    <h1 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; margin: 0 0 0.5rem;">
        Assistant Création d'Article SEO
    </h1>
    <p style="color: #666; margin-bottom: 2rem;">Répondez aux questions ci-dessous. L'IA générera un brouillon optimisé SEO que vous pourrez modifier.</p>

    <form method="post" action="/admin/blog/wizard/generate" id="wizardForm">

        <!-- Step 1: Target & Goal -->
        <section class="card" id="step1" style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <span style="background: #8B1538; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">1</span>
                <h2 style="margin: 0; font-size: 1.2rem;">Cible & Objectif</h2>
            </div>

            <div class="form-grid" style="gap: 1rem;">
                <label>Persona cible <span style="color: #D4AF37;">*</span>
                    <select name="persona" required style="width: 100%;">
                        <option value="">-- Sélectionnez --</option>
                        <optgroup label="Vendeurs">
                            <option value="Propriétaire hésitant">Propriétaire hésitant</option>
                            <option value="Propriétaire pressé">Propriétaire pressé</option>
                            <option value="Propriétaire méfiant">Propriétaire méfiant</option>
                            <option value="Succession / divorce">Succession / divorce</option>
                            <option value="Investisseur vendeur">Investisseur vendeur</option>
                            <option value="Vendeur senior">Vendeur senior</option>
                        </optgroup>
                        <optgroup label="Acheteurs">
                            <option value="Primo-accédant">Primo-accédant (jeune couple)</option>
                            <option value="Famille en expansion">Famille en expansion</option>
                            <option value="Investisseur rentabilité">Investisseur rentabilité</option>
                            <option value="Expatrié / mobilité">Expatrié / mobilité professionnelle</option>
                        </optgroup>
                    </select>
                </label>

                <label>Audience cible détaillée
                    <textarea name="target_audience" rows="2" placeholder="Ex: Jeunes couples 25-35 ans, premiers acheteurs à Bordeaux, budget 200-300k€"><?= e((string) ($_POST['target_audience'] ?? '')) ?></textarea>
                </label>

                <label>Objectif de l'article <span style="color: #D4AF37;">*</span>
                    <select name="article_goal_type" id="goalType" onchange="toggleCustomGoal()" style="width: 100%;">
                        <option value="generer_leads">Générer des leads (estimation)</option>
                        <option value="eduquer">Éduquer / Informer</option>
                        <option value="convertir">Convertir un prospect tiède</option>
                        <option value="notoriete">Notoriété locale / Autorité</option>
                        <option value="seo_longue_traine">SEO longue traîne</option>
                        <option value="custom">Autre (personnalisé)</option>
                    </select>
                </label>

                <label id="customGoalLabel" style="display: none;">Objectif personnalisé
                    <textarea name="article_goal" rows="2" placeholder="Décrivez l'objectif de cet article..."><?= e((string) ($_POST['article_goal'] ?? '')) ?></textarea>
                </label>
            </div>
        </section>

        <!-- Step 2: Keywords -->
        <section class="card" id="step2" style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <span style="background: #8B1538; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">2</span>
                <h2 style="margin: 0; font-size: 1.2rem;">Mots-Clés & SEO</h2>
            </div>

            <div class="form-grid" style="gap: 1rem;">
                <label>Mot-clé focus principal <span style="color: #D4AF37;">*</span>
                    <input type="text" name="focus_keyword" required
                        placeholder="Ex: vendre appartement bordeaux chartrons"
                        value="<?= e((string) ($_POST['focus_keyword'] ?? '')) ?>">
                    <small style="color: #888; display: block; margin-top: 0.25rem;">
                        Formule : [Action] + [Type de bien] + [Spécificité] + [Ville] + [Quartier]
                    </small>
                </label>

                <label>Mots-clés secondaires / sémantiques
                    <textarea name="secondary_keywords" rows="3"
                        placeholder="Séparez par des virgules. Ex: estimation gratuite bordeaux, prix immobilier chartrons, vente rapide appartement"><?= e((string) ($_POST['secondary_keywords'] ?? '')) ?></textarea>
                    <small style="color: #888; display: block; margin-top: 0.25rem;">
                        Astuce : Tapez votre mot-clé dans Google et notez les "Recherches associées" en bas de page.
                    </small>
                </label>

                <div style="background: #f8f4e8; border-left: 4px solid #D4AF37; padding: 1rem; border-radius: 4px;">
                    <strong style="color: #8B1538;">Golden Ratio du mot-clé</strong>
                    <p style="margin: 0.5rem 0 0; font-size: 0.9rem; color: #555;">
                        La densité idéale est de <strong>1.618%</strong> (Golden Ratio). Pour un article de 1500 mots,
                        cela correspond à environ <strong>24 occurrences</strong> du mot-clé focus.
                        Plage optimale : 1.0% - 2.5%.
                    </p>
                </div>
            </div>
        </section>

        <!-- Step 3: Content Strategy -->
        <section class="card" id="step3" style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <span style="background: #8B1538; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">3</span>
                <h2 style="margin: 0; font-size: 1.2rem;">Stratégie de Contenu</h2>
            </div>

            <div class="form-grid" style="gap: 1rem;">
                <label>Niveau de conscience du lecteur <span style="color: #D4AF37;">*</span>
                    <select name="awareness_level" required style="width: 100%;">
                        <option value="inconscient">Inconscient - Ne sait pas qu'il a un problème</option>
                        <option value="problème">Problème - Sait qu'il a un problème</option>
                        <option value="solution">Solution - Cherche activement des solutions</option>
                        <option value="produit">Produit - Compare les options</option>
                    </select>
                </label>

                <label>Sujet / Titre de l'article <span style="color: #D4AF37;">*</span>
                    <input type="text" name="topic" required
                        placeholder="Ex: Comment vendre votre appartement aux Chartrons en 2026"
                        value="<?= e((string) ($_POST['topic'] ?? '')) ?>">
                </label>

                <label>Type d'article
                    <select name="article_type" style="width: 100%;">
                        <option value="standalone">Article indépendant</option>
                        <option value="pilier">Article PILIER (page principale d'un silo)</option>
                        <option value="satellite">Article SATELLITE (sous-thème d'un pilier)</option>
                    </select>
                </label>

                <?php if (!empty($silos)): ?>
                <label>Silo SEO (optionnel)
                    <select name="silo_id" style="width: 100%;">
                        <option value="">-- Aucun silo --</option>
                        <?php foreach ($silos as $silo): ?>
                            <option value="<?= (int) $silo['id'] ?>"><?= e((string) $silo['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php endif; ?>
            </div>
        </section>

        <!-- Summary & Generate -->
        <section class="card" style="background: linear-gradient(135deg, #8B1538 0%, #6b0f2a 100%); color: #fff; text-align: center; padding: 2rem;">
            <h2 style="color: #D4AF37; margin: 0 0 1rem; font-family: 'Playfair Display', serif;">Prêt à générer ?</h2>
            <p style="margin: 0 0 1.5rem; opacity: 0.9;">
                L'IA va créer un brouillon optimisé SEO basé sur vos réponses.<br>
                Vous pourrez ensuite modifier le texte, ajuster les mots-clés et voir le score SEO en temps réel.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <button type="submit" class="btn" style="background: #D4AF37; color: #1a1a1a; font-weight: 700; padding: 0.75rem 2rem; font-size: 1rem;">
                    Générer avec IA
                </button>
                <a href="/admin/blog/create" class="btn btn-ghost" style="color: #fff; border-color: rgba(255,255,255,0.4);">
                    Créer manuellement
                </a>
            </div>
        </section>
    </form>
</div>

<script>
function toggleCustomGoal() {
    const sel = document.getElementById('goalType');
    const label = document.getElementById('customGoalLabel');
    label.style.display = sel.value === 'custom' ? 'block' : 'none';
}
</script>
