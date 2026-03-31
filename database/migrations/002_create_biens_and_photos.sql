-- ============================================
-- 002 - BIENS IMMOBILIERS & PHOTOS
-- ============================================

-- -----------------------------------------------
-- VILLES (référence géographique)
-- -----------------------------------------------
CREATE TABLE villes (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    code_postal VARCHAR(10),
    province VARCHAR(100),
    pays VARCHAR(100) DEFAULT 'Canada',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_villes_code_postal ON villes(code_postal);
CREATE INDEX idx_villes_nom ON villes(nom);

-- -----------------------------------------------
-- TYPES DE BIENS
-- -----------------------------------------------
CREATE TABLE types_biens (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,  -- maison, condo, terrain, duplex...
    slug VARCHAR(100) NOT NULL UNIQUE,
    icone VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE
);

INSERT INTO types_biens (nom, slug) VALUES
    ('Maison',          'maison'),
    ('Condo',           'condo'),
    ('Duplex',          'duplex'),
    ('Triplex',         'triplex'),
    ('Terrain',         'terrain'),
    ('Immeuble',        'immeuble'),
    ('Chalet',          'chalet'),
    ('Maison de ville', 'maison-de-ville');

-- -----------------------------------------------
-- BIENS IMMOBILIERS
-- -----------------------------------------------
CREATE TABLE biens (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),

    -- Références
    agent_id UUID REFERENCES agents(id) ON DELETE SET NULL,
    agence_id UUID REFERENCES agences(id) ON DELETE SET NULL,
    type_bien_id INTEGER REFERENCES types_biens(id) ON DELETE RESTRICT,
    ville_id INTEGER REFERENCES villes(id) ON DELETE RESTRICT,

    -- Identification
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    numero_inscription VARCHAR(100),  -- MLS ou autre

    -- Localisation
    adresse TEXT,
    code_postal VARCHAR(10),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    quartier VARCHAR(150),

    -- Caractéristiques
    nb_chambres INTEGER DEFAULT 0,
    nb_salles_bain DECIMAL(3,1) DEFAULT 0,
    nb_stationnements INTEGER DEFAULT 0,
    superficie_habitable DECIMAL(10, 2),  -- pi² ou m²
    superficie_terrain DECIMAL(10, 2),
    annee_construction INTEGER,
    nb_etages INTEGER,

    -- Prix (vente uniquement)
    prix DECIMAL(15, 2),
    prix_precedent DECIMAL(15, 2),
    taxes_annuelles DECIMAL(10, 2),

    -- Statut
    statut VARCHAR(50) DEFAULT 'actif',
    -- actif, vendu, retiré, brouillon

    -- Module activable
    module_actif BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,

    -- Dates
    date_inscription DATE,
    date_vente DATE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_biens_agent_id ON biens(agent_id);
CREATE INDEX idx_biens_agence_id ON biens(agence_id);
CREATE INDEX idx_biens_ville_id ON biens(ville_id);
CREATE INDEX idx_biens_type_id ON biens(type_bien_id);
CREATE INDEX idx_biens_statut ON biens(statut);
CREATE INDEX idx_biens_prix ON biens(prix);
CREATE INDEX idx_biens_slug ON biens(slug);

-- -----------------------------------------------
-- PHOTOS DES BIENS
-- -----------------------------------------------
CREATE TABLE photos_biens (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    bien_id UUID NOT NULL REFERENCES biens(id) ON DELETE CASCADE,
    url TEXT NOT NULL,
    url_thumbnail TEXT,
    titre VARCHAR(255),
    ordre INTEGER DEFAULT 0,
    is_principale BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_photos_bien_id ON photos_biens(bien_id);

-- -----------------------------------------------
-- CARACTERISTIQUES EXTRAS (flexible)
-- -----------------------------------------------
CREATE TABLE biens_caracteristiques (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    bien_id UUID NOT NULL REFERENCES biens(id) ON DELETE CASCADE,
    cle VARCHAR(100) NOT NULL,   -- piscine, garage, sous-sol...
    valeur TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX idx_carac_bien_id ON biens_caracteristiques(bien_id);

-- -----------------------------------------------
-- FAVORIS CLIENTS
-- -----------------------------------------------
CREATE TABLE favoris (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    bien_id UUID NOT NULL REFERENCES biens(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(user_id, bien_id)
);

-- -----------------------------------------------
-- ALERTES NOUVEAUX BIENS
-- -----------------------------------------------
CREATE TABLE alertes_biens (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    ville_id INTEGER REFERENCES villes(id) ON DELETE SET NULL,
    type_bien_id INTEGER REFERENCES types_biens(id) ON DELETE SET NULL,
    prix_min DECIMAL(15, 2),
    prix_max DECIMAL(15, 2),
    nb_chambres_min INTEGER,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);
