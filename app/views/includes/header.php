<?php
/** En-tête commun du site public : <head>, logo, menu, recherche, accès au compte. */
$user = utilisateur();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titre ?? 'Maison Al Jazeera') ?></title>
    <meta name="description" content="Maison Al Jazeera : catalogue de parfums de toutes marques. L'art du parfum, une émotion intemporelle.">
    <link rel="icon" type="image/svg+xml" href="<?= asset('assets/img/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body class="<?= !empty($accueil) ? 'page-accueil' : '' ?>">
<a class="visually-hidden-focusable" href="#contenu">Aller au contenu</a>

<header class="entete">
    <nav class="navbar navbar-expand-lg" aria-label="Navigation principale">
        <div class="container-xxl">
            <a class="logo" href="<?= url() ?>" aria-label="Maison Al Jazeera – accueil">
                <span class="logo-aj" aria-hidden="true">A<span>J</span></span>
                <span class="logo-filet" aria-hidden="true"></span>
                <span class="logo-nom">Maison<br>Al Jazeera</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
                    aria-controls="menu" aria-expanded="false" aria-label="Ouvrir le menu">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link<?= actif('', true) ?>" href="<?= url() ?>">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link<?= actif('parfums') ?>" href="<?= url('parfums') ?>">Parfums</a></li>
                    <li class="nav-item"><a class="nav-link<?= actif('la-maison') ?>" href="<?= url('la-maison') ?>">La Maison</a></li>
                    <li class="nav-item"><a class="nav-link<?= actif('univers') ?>" href="<?= url('univers') ?>">L'Univers</a></li>
                    <li class="nav-item"><a class="nav-link<?= actif('contact') ?>" href="<?= url('contact') ?>">Contact</a></li>
                </ul>

                <div class="outils">
                    <form class="recherche" action="<?= url('parfums') ?>" method="get" role="search">
                        <label class="visually-hidden" for="recherche-rapide">Rechercher un parfum</label>
                        <input type="search" id="recherche-rapide" name="q" placeholder="Rechercher un parfum…" maxlength="100">
                        <button class="outil" type="submit" aria-label="Rechercher"><i class="bi bi-search" aria-hidden="true"></i></button>
                    </form>

                    <?php if ($user): ?>
                        <div class="dropdown">
                            <button class="outil dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person" aria-hidden="true"></i><small><?= e($user['pseudo']) ?></small>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (est_admin()): ?>
                                    <li><a class="dropdown-item" href="<?= url('admin') ?>"><i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>Back-office</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <form action="<?= url('deconnexion') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Se déconnecter</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="outil" href="<?= url('connexion') ?>" aria-label="Se connecter ou créer un compte">
                            <i class="bi bi-person" aria-hidden="true"></i><small class="d-lg-none">Mon compte</small>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<?php $messages = messages_flash(); ?>
<?php if ($messages): ?>
    <div class="messages">
        <div class="container-xxl pt-3">
            <?php foreach ($messages as $m): ?>
                <div class="alert alert-<?= e($m['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($m['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<main id="contenu">
