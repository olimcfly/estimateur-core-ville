<?php

/**
 * Fixture : sources RSS locales pour le site Bordeaux / Nouvelle-Aquitaine.
 * Format : tableau de sources compatibles avec AdminRssController::getDefaultSources()
 */
return [
    ['name' => 'Sud Ouest Bordeaux', 'feed_url' => 'https://www.sudouest.fr/economie/immobilier/rss.xml', 'site_url' => 'https://www.sudouest.fr', 'category' => 'presse-locale', 'zone' => 'Bordeaux/Nouvelle-Aquitaine'],
    ['name' => 'France Bleu Gironde', 'feed_url' => 'https://www.francebleu.fr/rss/gironde/infos.xml', 'site_url' => 'https://www.francebleu.fr', 'category' => 'presse-locale', 'zone' => 'Bordeaux/Nouvelle-Aquitaine'],
    ['name' => 'Rue89 Bordeaux', 'feed_url' => 'https://rue89bordeaux.com/feed/', 'site_url' => 'https://rue89bordeaux.com', 'category' => 'presse-locale', 'zone' => 'Bordeaux/Nouvelle-Aquitaine'],
    ['name' => 'Bordeaux Gazette', 'feed_url' => 'https://www.bordeaux-gazette.com/feed/', 'site_url' => 'https://www.bordeaux-gazette.com', 'category' => 'presse-locale', 'zone' => 'Bordeaux/Nouvelle-Aquitaine'],
    ['name' => 'Aqui.fr', 'feed_url' => 'https://aqui.fr/feed/', 'site_url' => 'https://aqui.fr', 'category' => 'presse-locale', 'zone' => 'Bordeaux/Nouvelle-Aquitaine'],
    ['name' => 'La Tribune Bordeaux', 'feed_url' => 'https://objectifaquitaine.latribune.fr/feed', 'site_url' => 'https://objectifaquitaine.latribune.fr', 'category' => 'presse-locale', 'zone' => 'Bordeaux/Nouvelle-Aquitaine'],
    ['name' => 'Google Actu Immobilier Bordeaux', 'feed_url' => 'https://news.google.com/rss/search?q=immobilier+bordeaux&hl=fr&gl=FR&ceid=FR:fr', 'site_url' => 'https://news.google.com', 'category' => 'presse-locale', 'zone' => 'Bordeaux/Nouvelle-Aquitaine'],
];
