<?php
/**
 * Point d'entrée unique du site (contrôleur frontal).
 * Le fichier .htaccess renvoie ici toutes les URL : /parfums/12-sauvage => index.php?url=parfums/12-sauvage
 */

define('ROOT', dirname(__DIR__));

require ROOT . '/app/core/Autoload.php';
require ROOT . '/app/core/helpers.php';
Autoload::register();

/* ---------- Session ---------- */
session_name('aljazeera');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/']);
session_start();

/* ---------- Chemin demandé ---------- */
if (isset($_GET['url']) && is_string($_GET['url'])) {
    $chemin = $_GET['url'];                       // cas normal : réécriture Apache
} else {
    $chemin = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $chemin = rawurldecode($chemin);
    $racine = base_url();                                  // ex. /AL-Jazeera/public/
    $parent = preg_replace('#[^/]+/$#', '', $racine);      // ex. /AL-Jazeera/ (dossier du projet)
    if (str_starts_with($chemin, $racine)) {               // serveur sans réécriture (php -S)
        $chemin = substr($chemin, strlen($racine));
    } elseif (str_starts_with($chemin, $parent)) {         // dossier du projet ouvert directement
        $chemin = substr($chemin, strlen($parent));
    }
}
$chemin = trim($chemin, '/');
$chemin = $chemin === 'index.php' ? '' : $chemin;
$GLOBALS['cheminCourant'] = $chemin;

/* ---------- Routage ---------- */
$router = new Router();
require ROOT . '/config/routes.php';

ob_start(); // la page est gardée en mémoire : en cas d'erreur, on peut la remplacer
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $chemin);
} catch (Throwable $erreur) {
    // Erreur imprévue (base non importée, mauvais identifiants…) : page d'aide plutôt qu'une page blanche.
    error_log($erreur);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);
    $baseDeDonnees = $erreur instanceof PDOException;
    require ROOT . '/app/views/erreurs/500.php';
}
