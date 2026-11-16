<?php
/**
 * Fonctions utilitaires disponibles dans les contrôleurs et les vues.
 */

/** Lit une valeur de config/config.php (ou du modèle config.example.php s'il n'a pas été copié). */
function config(string $cle, mixed $defaut = null): mixed
{
    static $config = null;
    if ($config === null) {
        $fichier = is_file(ROOT . '/config/config.php') ? 'config.php' : 'config.example.php';
        $config  = require ROOT . '/config/' . $fichier;
    }
    return $config[$cle] ?? $defaut;
}

/** Échappe une valeur avant affichage HTML (protection XSS). */
function e(mixed $valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

/** Racine du site, détectée automatiquement si base_url n'est pas renseignée. */
function base_url(): string
{
    static $base = null;
    if ($base === null) {
        $config = config('base_url');
        if (is_string($config) && $config !== '') {
            $base = '/' . trim($config, '/') . '/';
            $base = $base === '//' ? '/' : $base;
        } else {
            $dossier = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
            $base    = rtrim($dossier, '/') . '/';
        }
    }
    return $base;
}

/** Adresse d'une page du site : url('parfums') => /…/parfums */
function url(string $chemin = '', array $params = []): string
{
    $requete = chaine_requete($params);
    return base_url() . ltrim($chemin, '/') . ($requete !== '' ? '?' . $requete : '');
}

/** Adresse d'un fichier public (css, js, images). */
function asset(string $chemin): string
{
    return base_url() . ltrim($chemin, '/');
}

/** Construit « a=1&b[]=2&b[]=3 » en ignorant les valeurs vides. */
function chaine_requete(array $params): string
{
    $morceaux = [];
    foreach ($params as $cle => $valeur) {
        if (is_array($valeur)) {
            foreach ($valeur as $v) {
                $morceaux[] = rawurlencode($cle) . '[]=' . rawurlencode((string) $v);
            }
        } elseif ($valeur !== null && $valeur !== '') {
            $morceaux[] = rawurlencode($cle) . '=' . rawurlencode((string) $valeur);
        }
    }
    return implode('&', $morceaux);
}

/** Chemin demandé (sans la racine du site), ex. « parfums/12-sauvage ». */
function chemin_courant(): string
{
    return $GLOBALS['cheminCourant'] ?? '';
}

/** Page d'où vient le visiteur, si elle appartient au site (sinon l'accueil). */
function chemin_retour(): string
{
    $referent = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_PATH) ?: '';
    $base     = base_url();
    if ($referent !== '' && str_starts_with($referent, $base)) {
        return chemin_sur(substr($referent, strlen($base)));
    }
    return '';
}

/** N'accepte qu'un chemin interne simple (évite les redirections vers un autre site). */
function chemin_sur(?string $chemin): string
{
    $chemin = trim((string) $chemin, '/');
    return preg_match('#^[A-Za-z0-9/_-]*$#', $chemin) ? $chemin : '';
}

/** Vrai si le chemin courant commence par $prefixe (menu actif). */
function actif(string $prefixe, bool $exact = false): string
{
    $courant = chemin_courant();
    $ok = $exact ? $courant === $prefixe : ($courant === $prefixe || str_starts_with($courant, $prefixe . '/'));
    return $ok ? ' active' : '';
}

/* ---------- Session : utilisateur, messages flash, CSRF ---------- */

function utilisateur(): ?array
{
    return $_SESSION['user'] ?? null;
}

function est_admin(): bool
{
    return (utilisateur()['role'] ?? '') === 'admin';
}

/** Enregistre un message affiché une seule fois sur la page suivante. */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function messages_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/* ---------- Mise en forme ---------- */

/** 92.5 => « 92,50 € » */
function prix(mixed $montant): string
{
    return number_format((float) $montant, 2, ',', "\u{202F}") . "\u{00A0}€";
}

/** Transforme un nom en morceau d'URL : « L'Interdit » => « l-interdit ». */
function slug(string $texte): string
{
    $texte = strtr(mb_strtolower($texte, 'UTF-8'), [
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ç' => 'c',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ô' => 'o', 'ö' => 'o', 'ó' => 'o',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u', 'ñ' => 'n', 'œ' => 'oe', 'æ' => 'ae', '°' => '',
    ]);
    $texte = trim(preg_replace('/[^a-z0-9]+/', '-', $texte), '-');
    return $texte !== '' ? $texte : 'parfum';
}

/** Adresse de la fiche d'un parfum : parfums/12-sauvage */
function url_parfum(array $parfum): string
{
    return url('parfums/' . $parfum['idParfum'] . '-' . slug($parfum['nom']));
}

/** Adresse de la photo d'un parfum, ou de l'image par défaut si elle manque. */
function photo_url(?string $photo): string
{
    if ($photo && is_file(ROOT . '/public/' . $photo)) {
        return asset($photo);
    }
    return asset('assets/img/parfum-defaut.svg');
}

/** Affiche une note sur 5 sous forme d'étoiles (demi-étoiles comprises). */
function etoiles(mixed $note, string $classe = ''): string
{
    $note = (float) $note;
    $html = '<span class="etoiles ' . e($classe) . '" role="img" aria-label="'
          . e(number_format($note, 1, ',', '')) . ' sur 5">';
    for ($i = 1; $i <= 5; $i++) {
        $icone = $note >= $i - 0.25 ? 'bi-star-fill' : ($note >= $i - 0.75 ? 'bi-star-half' : 'bi-star');
        $html .= '<i class="bi ' . $icone . '" aria-hidden="true"></i>';
    }
    return $html . '</span>';
}

/** Date française courte : 2026-11-09 14:00:00 => 9 novembre 2026 */
function date_fr(string $date): string
{
    $mois = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet',
             'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $t = strtotime($date);
    return $t ? (int) date('j', $t) . ' ' . $mois[(int) date('n', $t)] . ' ' . date('Y', $t) : '';
}

/** Lit un tableau d'identifiants envoyé par un formulaire (cases à cocher). */
function ids(mixed $valeur): array
{
    $valeurs = is_array($valeur) ? $valeur : ($valeur === null || $valeur === '' ? [] : [$valeur]);
    $ids = [];
    foreach ($valeurs as $v) {
        if (is_scalar($v) && ctype_digit((string) $v) && (int) $v > 0) {
            $ids[(int) $v] = (int) $v;
        }
    }
    return array_values($ids);
}
