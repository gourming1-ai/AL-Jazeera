<?php /** Formulaire de connexion (CU6). */ ?>
<section class="page-compte">
    <div class="container">
        <div class="carte-compte">
            <span class="monogramme"><span class="logo-aj" aria-hidden="true">A<span>J</span></span></span>
            <h1>Connexion</h1>
            <p class="intro">Retrouvez votre compte pour donner votre avis sur les parfums.</p>

            <?php if ($erreur): ?>
                <div class="alert alert-danger" role="alert"><?= e($erreur) ?></div>
            <?php endif; ?>

            <form action="<?= url('connexion') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
                <div class="mb-3">
                    <label class="form-label" for="login">Identifiant</label>
                    <input class="form-control" type="text" id="login" name="login" value="<?= e($login) ?>" required autocomplete="username" autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="pwd">Mot de passe</label>
                    <input class="form-control" type="password" id="pwd" name="pwd" required autocomplete="current-password">
                </div>
                <button class="btn btn-aj w-100" type="submit">Se connecter <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
            </form>

            <p class="text-center mt-4 mb-0">Pas encore de compte ?
                <a href="<?= url('inscription', ['redirect' => $redirect]) ?>">Créer un compte</a>
            </p>
        </div>
    </div>
</section>
