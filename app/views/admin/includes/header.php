<?php
/** En-tête du back-office : menu latéral et barre de titre. */
$user = utilisateur();
$menu = [
    'Catalogue' => [
        ['admin',             'Tableau de bord',   'bi-speedometer2', true],
        ['admin/parfums',     'Parfums',           'bi-droplet-half', false],
    ],
    'Données de référence' => [
        ['admin/marques',     'Marques',           'bi-award',        false],
        ['admin/familles',    'Familles',          'bi-flower1',      false],
        ['admin/fragrances',  'Fragrances',        'bi-wind',         false],
        ['admin/eaux',        "Types d'eau",       'bi-moisture',     false],
        ['admin/contenances', 'Contenances',       'bi-rulers',       false],
    ],
    'Clients' => [
        ['admin/avis',        'Avis',              'bi-chat-quote',   false],
    ],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titre ?? 'Back-office') ?> – Back-office Maison Al Jazeera</title>
    <meta name="robots" content="noindex">
    <link rel="icon" type="image/svg+xml" href="<?= asset('assets/img/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="admin">
    <aside class="admin-menu">
        <a class="logo" href="<?= url('admin') ?>" aria-label="Back-office – tableau de bord">
            <span class="logo-aj" aria-hidden="true">A<span>J</span></span>
            <span class="logo-filet" aria-hidden="true"></span>
            <span class="logo-nom">Back-office</span>
        </a>
        <nav aria-label="Menu du back-office">
            <?php foreach ($menu as $section => $liens): ?>
                <h2><?= e($section) ?></h2>
                <?php foreach ($liens as [$chemin, $libelle, $icone, $exact]): ?>
                    <a class="lien<?= actif($chemin, $exact) ?>" href="<?= url($chemin) ?>"><i class="bi <?= $icone ?>" aria-hidden="true"></i><?= e($libelle) ?></a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
        <div class="bas">
            <a class="lien" href="<?= url() ?>"><i class="bi bi-shop-window" aria-hidden="true"></i>Voir le site</a>
            <form action="<?= url('deconnexion') ?>" method="post">
                <?= csrf_field() ?>
                <button class="lien" type="submit" style="display:flex;align-items:center;gap:.8rem;padding:.62rem 1.4rem;color:#e3d5c3;font-size:.93rem">
                    <i class="bi bi-box-arrow-right" aria-hidden="true" style="color:var(--or-clair);font-size:1.05rem"></i>Se déconnecter
                </button>
            </form>
        </div>
    </aside>

    <div class="admin-corps">
        <header class="admin-barre">
            <h1><?= e($titre ?? 'Back-office') ?></h1>
            <span class="compte"><i class="bi bi-person-circle me-1" aria-hidden="true"></i><?= e($user['pseudo'] ?? '') ?> · administrateur</span>
        </header>

        <main class="admin-contenu" id="contenu">
            <?php foreach (messages_flash() as $m): ?>
                <div class="alert alert-<?= e($m['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($m['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            <?php endforeach; ?>
