<div class="container">
    <a href="/admin/blog" class="btn btn-small btn-ghost" style="margin-bottom: 1rem; display: inline-block;">&larr; Retour Blog</a>
    <h1 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; margin: 0 0 0.5rem;">
        Assistant Creation d'Article SEO
    </h1>
    <p style="color: #666; margin-bottom: 2rem;">Repondez aux questions ci-dessous. L'IA generera un brouillon optimise SEO que vous pourrez modifier.</p>

    <form method="post" action="/admin/blog/wizard/generate" id="wizardForm">

        <!-- Step 1: Target & Goal -->
        <section class="card" id="step1" style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <span style="background: #8B1538; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">1</span>
                <h2 style="margin: 0; font-size: 1.2rem;">Cible & Objectif</h2>
            </div>

            <div class="form-grid" style="gap: 1rem;">
                <label>Persona cible <span style="color: #D4AF37;">*</span>
                    <select name="persona" id="wizPersona" required style="width: 100%;">
                        <option value="">-- Selectionnez --</option>
                        <optgroup label="Vendeurs">
                            <option value="Proprietaire hesitant">Proprietaire hesitant</option>
                            <option value="Proprietaire presse">Proprietaire presse</option>
                            <option value="Proprietaire mefiant">Proprietaire mefiant</option>
                            <option value="Succession / divorce">Succession / divorce</option>
                            <option value="Investisseur vendeur">Investisseur vendeur</option>
                            <option value="Vendeur senior">Vendeur senior</option>
                        </optgroup>
                        <optgroup label="Acheteurs">
                            <option value="Primo-accedant">Primo-accedant (jeune couple)</option>
                            <option value="Famille en expansion">Famille en expansion</option>
                            <option value="Investisseur rentabilite">Investisseur rentabilite</option>
                            <option value="Expatrie / mobilite">Expatrie / mobilite professionnelle</option>
                        </optgroup>
                    </select>
                </label>

                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                        <label style="margin: 0; font-weight: 600;">Audience cible detaillee</label>
                        <button type="button" class="ai-suggest-btn" onclick="aiSuggest('target_audience')" title="Suggestion IA">
                            <i class="fas fa-wand-magic-sparkles"></i> IA
                        </button>
                    </div>
                    <textarea name="target_audience" id="wizTargetAudience" rows="2" placeholder="Ex: Jeunes couples 25-35 ans, premiers acheteurs a Bordeaux, budget 200-300k&euro;"><?= e((string) ($_POST['target_audience'] ?? '')) ?></textarea>
                    <div class="ai-suggest-result" id="suggest-target_audience"></div>
                </div>

                <label>Objectif de l'article <span style="color: #D4AF37;">*</span>
                    <select name="article_goal_type" id="goalType" onchange="toggleCustomGoal()" style="width: 100%;">
                        <option value="generer_leads">Generer des leads (estimation)</option>
                        <option value="eduquer">Eduquer / Informer</option>
                        <option value="convertir">Convertir un prospect tiede</option>
                        <option value="notoriete">Notoriete locale / Autorite</option>
                        <option value="seo_longue_traine">SEO longue traine</option>
                        <option value="custom">Autre (personnalise)</option>
                    </select>
                </label>

                <label id="customGoalLabel" style="display: none;">Objectif personnalise
                    <textarea name="article_goal" rows="2" placeholder="Decrivez l'objectif de cet article..."><?= e((string) ($_POST['article_goal'] ?? '')) ?></textarea>
                </label>
            </div>
        </section>

        <!-- Step 2: Keywords -->
        <section class="card" id="step2" style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <span style="background: #8B1538; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">2</span>
                <h2 style="margin: 0; font-size: 1.2rem;">Mots-Cles & SEO</h2>
            </div>

            <div class="form-grid" style="gap: 1rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                        <label style="margin: 0; font-weight: 600;">Mot-cle focus principal <span style="color: #D4AF37;">*</span></label>
                        <button type="button" class="ai-suggest-btn" onclick="aiSuggest('focus_keyword')" title="Suggestion IA">
                            <i class="fas fa-wand-magic-sparkles"></i> IA
                        </button>
                    </div>
                    <input type="text" name="focus_keyword" id="wizFocusKeyword" required
                        placeholder="Ex: vendre appartement bordeaux chartrons"
                        value="<?= e((string) ($_POST['focus_keyword'] ?? '')) ?>">
                    <small style="color: #888; display: block; margin-top: 0.25rem;">
                        Formule : [Action] + [Type de bien] + [Specificite] + [Ville] + [Quartier]
                    </small>
                    <div class="ai-suggest-result" id="suggest-focus_keyword"></div>
                </div>

                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                        <label style="margin: 0; font-weight: 600;">Mots-cles secondaires / semantiques</label>
                        <button type="button" class="ai-suggest-btn" onclick="aiSuggest('secondary_keywords')" title="Suggestion IA">
                            <i class="fas fa-wand-magic-sparkles"></i> IA
                        </button>
                    </div>
                    <textarea name="secondary_keywords" id="wizSecondaryKeywords" rows="3"
                        placeholder="Separez par des virgules. Ex: estimation gratuite bordeaux, prix immobilier chartrons, vente rapide appartement"><?= e((string) ($_POST['secondary_keywords'] ?? '')) ?></textarea>
                    <small style="color: #888; display: block; margin-top: 0.25rem;">
                        Astuce : Tapez votre mot-cle dans Google et notez les "Recherches associees" en bas de page.
                    </small>
                    <div class="ai-suggest-result" id="suggest-secondary_keywords"></div>
                </div>

                <div style="background: #f8f4e8; border-left: 4px solid #D4AF37; padding: 1rem; border-radius: 4px;">
                    <strong style="color: #8B1538;">Golden Ratio du mot-cle</strong>
                    <p style="margin: 0.5rem 0 0; font-size: 0.9rem; color: #555;">
                        La densite ideale est de <strong>1.618%</strong> (Golden Ratio). Pour un article de 1500 mots,
                        cela correspond a environ <strong>24 occurrences</strong> du mot-cle focus.
                        Plage optimale : 1.0% - 2.5%.
                    </p>
                </div>
            </div>
        </section>

        <!-- Step 3: Content Strategy -->
        <section class="card" id="step3" style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <span style="background: #8B1538; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">3</span>
                <h2 style="margin: 0; font-size: 1.2rem;">Strategie de Contenu</h2>
            </div>

            <div class="form-grid" style="gap: 1rem;">
                <label>Niveau de conscience du lecteur <span style="color: #D4AF37;">*</span>
                    <select name="awareness_level" required style="width: 100%;">
                        <option value="inconscient">Inconscient - Ne sait pas qu'il a un probleme</option>
                        <option value="probleme">Probleme - Sait qu'il a un probleme</option>
                        <option value="solution">Solution - Cherche activement des solutions</option>
                        <option value="produit">Produit - Compare les options</option>
                    </select>
                </label>

                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                        <label style="margin: 0; font-weight: 600;">Sujet / Titre de l'article <span style="color: #D4AF37;">*</span></label>
                        <button type="button" class="ai-suggest-btn" onclick="aiSuggest('topic')" title="Suggestion IA">
                            <i class="fas fa-wand-magic-sparkles"></i> IA
                        </button>
                    </div>
                    <input type="text" name="topic" id="wizTopic" required
                        placeholder="Ex: Comment vendre votre appartement aux Chartrons en 2026"
                        value="<?= e((string) ($_POST['topic'] ?? '')) ?>">
                    <div class="ai-suggest-result" id="suggest-topic"></div>
                </div>

                <label>Type d'article
                    <select name="article_type" style="width: 100%;">
                        <option value="standalone">Article independant</option>
                        <option value="pilier">Article PILIER (page principale d'un silo)</option>
                        <option value="satellite">Article SATELLITE (sous-theme d'un pilier)</option>
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
            <h2 style="color: #D4AF37; margin: 0 0 1rem; font-family: 'Playfair Display', serif;">Pret a generer ?</h2>
            <p style="margin: 0 0 1.5rem; opacity: 0.9;">
                L'IA va creer un brouillon optimise SEO base sur vos reponses.<br>
                Vous pourrez ensuite modifier le texte, ajuster les mots-cles et voir le score SEO en temps reel.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <button type="submit" class="btn" style="background: #D4AF37; color: #1a1a1a; font-weight: 700; padding: 0.75rem 2rem; font-size: 1rem;">
                    Generer avec IA
                </button>
                <a href="/admin/blog/create" class="btn btn-ghost" style="color: #fff; border-color: rgba(255,255,255,0.4);">
                    Creer manuellement
                </a>
            </div>
        </section>
    </form>
</div>

<style>
.ai-suggest-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.6rem;
    font-size: 0.72rem;
    font-weight: 600;
    color: #8B1538;
    background: linear-gradient(135deg, #f8f4e8, #f0e8d8);
    border: 1px solid #D4AF37;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    line-height: 1.3;
}
.ai-suggest-btn:hover {
    background: linear-gradient(135deg, #D4AF37, #c4a030);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(212,175,55,0.3);
}
.ai-suggest-btn:disabled {
    opacity: 0.6;
    cursor: wait;
    transform: none;
}
.ai-suggest-btn i {
    font-size: 0.7rem;
}
.ai-suggest-result {
    display: none;
    margin-top: 0.5rem;
    padding: 0.75rem;
    background: #faf8f2;
    border: 1px solid #e8dfd7;
    border-left: 3px solid #D4AF37;
    border-radius: 6px;
    font-size: 0.85rem;
    color: #333;
    line-height: 1.5;
    position: relative;
}
.ai-suggest-result .suggest-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.4rem;
    padding-bottom: 0.4rem;
    border-bottom: 1px solid #e8dfd7;
}
.ai-suggest-result .suggest-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #8B1538;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.ai-suggest-result .suggest-actions {
    display: flex;
    gap: 0.4rem;
}
.ai-suggest-result .suggest-action-btn {
    padding: 0.2rem 0.5rem;
    font-size: 0.7rem;
    font-weight: 600;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: pointer;
    background: #fff;
    color: #555;
    transition: all 0.15s;
}
.ai-suggest-result .suggest-action-btn:hover {
    background: #8B1538;
    color: #fff;
    border-color: #8B1538;
}
.ai-suggest-result .suggest-action-btn.apply-btn {
    background: #D4AF37;
    color: #1a1a1a;
    border-color: #D4AF37;
}
.ai-suggest-result .suggest-action-btn.apply-btn:hover {
    background: #c4a030;
}
.ai-suggest-result .suggest-content {
    white-space: pre-wrap;
}
.ai-suggest-result .suggest-error {
    color: #e24b4a;
    font-weight: 600;
}
</style>

<script>
function toggleCustomGoal() {
    var sel = document.getElementById('goalType');
    var label = document.getElementById('customGoalLabel');
    label.style.display = sel.value === 'custom' ? 'block' : 'none';
}

function aiSuggest(field) {
    var btn = event.currentTarget;
    var resultDiv = document.getElementById('suggest-' + field);

    // Gather context from form
    var persona = document.getElementById('wizPersona').value;
    var focusKeyword = document.getElementById('wizFocusKeyword').value;
    var topic = document.getElementById('wizTopic').value;
    var goalType = document.getElementById('goalType').value;

    if (!persona && (field === 'target_audience' || field === 'focus_keyword' || field === 'topic')) {
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<span class="suggest-error"><i class="fas fa-exclamation-triangle"></i> Selectionnez d\'abord un persona cible.</span>';
        return;
    }

    if (field === 'secondary_keywords' && !focusKeyword) {
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<span class="suggest-error"><i class="fas fa-exclamation-triangle"></i> Renseignez d\'abord le mot-cle focus principal.</span>';
        return;
    }

    if (field === 'topic' && !focusKeyword) {
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<span class="suggest-error"><i class="fas fa-exclamation-triangle"></i> Renseignez d\'abord le mot-cle focus principal.</span>';
        return;
    }

    // Disable button, show loading
    btn.disabled = true;
    var origHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<span style="color:#888;"><i class="fas fa-spinner fa-spin"></i> L\'IA reflechit...</span>';

    var fd = new FormData();
    fd.append('field', field);
    fd.append('persona', persona);
    fd.append('focus_keyword', focusKeyword);
    fd.append('topic', topic);
    fd.append('article_goal_type', goalType);

    fetch('/admin/blog/api/ai-suggest', { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = origHTML;

            if (!data.success) {
                resultDiv.innerHTML = '<span class="suggest-error"><i class="fas fa-exclamation-triangle"></i> ' + escHtml(data.error) + '</span>';
                return;
            }

            var targetFieldId = {
                'target_audience': 'wizTargetAudience',
                'focus_keyword': 'wizFocusKeyword',
                'secondary_keywords': 'wizSecondaryKeywords',
                'topic': 'wizTopic'
            }[field];

            var html = '<div class="suggest-header">';
            html += '<span class="suggest-label"><i class="fas fa-wand-magic-sparkles"></i> Suggestion IA</span>';
            html += '<div class="suggest-actions">';
            html += '<button type="button" class="suggest-action-btn apply-btn" onclick="applySuggestion(\'' + field + '\', \'' + targetFieldId + '\')">Appliquer</button>';
            html += '<button type="button" class="suggest-action-btn" onclick="closeSuggestion(\'' + field + '\')">Fermer</button>';
            html += '</div></div>';
            html += '<div class="suggest-content" id="suggest-text-' + field + '">' + escHtml(data.suggestion) + '</div>';

            resultDiv.innerHTML = html;
        })
        .catch(function(err) {
            btn.disabled = false;
            btn.innerHTML = origHTML;
            resultDiv.innerHTML = '<span class="suggest-error"><i class="fas fa-exclamation-triangle"></i> Erreur reseau. Verifiez votre connexion.</span>';
        });
}

function applySuggestion(field, targetFieldId) {
    var suggestText = document.getElementById('suggest-text-' + field);
    if (!suggestText) return;

    var target = document.getElementById(targetFieldId);
    if (!target) return;

    var text = suggestText.textContent || suggestText.innerText;

    // For list fields (focus_keyword, topic), take the first suggestion line
    if (field === 'focus_keyword' || field === 'topic') {
        var lines = text.split('\n').filter(function(l) { return l.trim() !== ''; });
        if (lines.length > 0) {
            // Remove numbering like "1. " or "1) "
            text = lines[0].replace(/^\d+[\.\)]\s*/, '').trim();
        }
    }

    target.value = text.trim();
    closeSuggestion(field);
}

function closeSuggestion(field) {
    var resultDiv = document.getElementById('suggest-' + field);
    if (resultDiv) {
        resultDiv.style.display = 'none';
        resultDiv.innerHTML = '';
    }
}

function escHtml(str) {
    var d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}
</script>
