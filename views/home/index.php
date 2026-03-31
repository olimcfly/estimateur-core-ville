<style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
    body { background: #f5f7fb; color: #1f2937; }
    .container { max-width: 1100px; margin: 0 auto; padding: 0 1rem; }
    header { background: #0f172a; color: #fff; }
    .nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; }
    .logo { font-weight: 700; font-size: 1.2rem; }
    .menu a { color: #fff; text-decoration: none; margin-left: 1rem; }
    .hero { padding: 4rem 0 2rem; text-align: center; }
    .hero h1 { font-size: 2rem; margin-bottom: 1rem; }
    .hero p { color: #4b5563; margin-bottom: 2rem; }
    .form-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,.08); max-width: 640px; margin: 0 auto; }
    .grid { display: grid; gap: 1rem; }
    input, select, button { width: 100%; padding: .8rem; border-radius: 8px; border: 1px solid #d1d5db; }
    button { background: #2563eb; border: none; color: #fff; font-weight: 600; cursor: pointer; }
    button:hover { background: #1d4ed8; }
    .services { padding: 3rem 0; }
    .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .card { background: #fff; border-radius: 10px; padding: 1rem; box-shadow: 0 8px 18px rgba(0,0,0,.06); }
    footer { background: #111827; color: #fff; margin-top: 2rem; padding: 1.5rem 0; text-align: center; }
    @media (max-width: 768px) {
        .cards { grid-template-columns: 1fr; }
        .hero h1 { font-size: 1.6rem; }
        .menu { display: none; }
    }
</style>

<header>
    <div class="container nav">
        <div class="logo">🏠 Ecosystème Immo</div>
        <nav class="menu">
            <a href="/">Accueil</a>
            <a href="/estimation">Estimation</a>
            <a href="#services">Services</a>
            <a href="#contact">Contact</a>
        </nav>
    </div>
</header>

<section class="hero container">
    <h1>Estimez votre bien immobilier</h1>
    <p>Obtenez une estimation rapide et précise selon votre ville et le type de bien.</p>

    <form class="form-card grid" method="POST" action="/estimation">
        <label>
            Ville
            <input type="text" name="ville" placeholder="Ex : Paris" required>
        </label>
        <label>
            Type de bien
            <select name="type_bien" required>
                <option value="">Sélectionner</option>
                <option value="appartement">Appartement</option>
                <option value="maison">Maison</option>
                <option value="terrain">Terrain</option>
            </select>
        </label>
        <label>
            Surface (m²)
            <input type="number" min="1" step="1" name="surface" placeholder="Ex : 80" required>
        </label>
        <button type="submit">Lancer l'estimation</button>
    </form>
</section>

<section id="services" class="services container">
    <h2 style="margin-bottom: 1rem;">Nos services</h2>
    <div class="cards">
        <article class="card">
            <h3>Estimation instantanée</h3>
            <p>Une première fourchette de prix en quelques secondes.</p>
        </article>
        <article class="card">
            <h3>Rapport détaillé</h3>
            <p>Analyse de marché et recommandations de valorisation.</p>
        </article>
        <article class="card">
            <h3>Accompagnement expert</h3>
            <p>Mise en relation avec nos conseillers immobiliers.</p>
        </article>
    </div>
</section>

<footer id="contact">
    <div class="container">
        © <?= date('Y'); ?> Ecosystème Immo — Tous droits réservés.
    </div>
</footer>
