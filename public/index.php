<?php
// Point d'entrée unique (contrôleur frontal).
// Squelette : à compléter par l'équipe (routeur, chargement des contrôleurs).

$url = trim($_GET['url'] ?? '', '/');
$segments = $url === '' ? [] : explode('/', $url);

// Exemple : /parfums/12 => controller "parfums", paramètre "12"
echo 'Maison Al Jazeera – route demandée : /' . htmlspecialchars($url);
