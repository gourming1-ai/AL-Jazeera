<?php
/** Formulaire d'inscription (CU5) : login, mot de passe, pseudo. */
$champ = fn (string $nom) => isset($erreurs[$nom]) ? ' is-invalid' : '';
?>
<section class="page-compte">
    <div class="container">
        <div class="carte-compte">
            <span class="monogramme"><span class="logo-aj" aria-hidden="true">A<span>J</span></span></span>
            <h1>Créer un compte</h1>
            <p class="intro">Un compte suffit pour noter et commenter les parfums du catalogue.</p>

            <form action="<?= url('inscription') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
                <div class="mb-3">
                    <label class="form-label" for="login">Identifiant</label>
                    <input class="form-control<?= $champ('login') ?>" type="text" id="login" name="login" value="<?= e($saisie['login'] ?? '') ?>"
                           required maxlength="100" autocomplete="username" aria-describedby="aide-login">
                    <div class="invalid-feedback"><?= e($erreurs['login'] ?? '') ?></div>
                    <div class="form-text" id="aide-login">Il sert à vous connecter. Votre adresse e-mail convient très bien.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="pseudo">Pseudo</label>
                    <input class="form-control<?= $champ('pseudo') ?>" type="text" id="pseudo" name="pseudo" value="<?= e($saisie['pseudo'] ?? '') ?>"
                           required maxlength="100" autocomplete="nickname" aria-describedby="aide-pseudo">
                    <div class="invalid-feedback"><?= e($erreurs['pseudo'] ?? '') ?></div>
                    <div class="form-text" id="aide-pseudo">C'est le nom affiché à côté de vos avis.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="pwd">Mot de passe</label>
                    <input class="form-control<?= $champ('pwd') ?>" type="password" id="pwd" name="pwd" required minlength="8" autocomplete="new-password" aria-describedby="aide-pwd">
                    <div class="invalid-feedback"><?= e($erreurs['pwd'] ?? '') ?></div>
                    <div class="form-text" id="aide-pwd">8 caractères au minimum.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="pwd_confirmation">Confirmer le mot de passe</label>
                    <input class="form-control<?= $champ('pwd_confirmation') ?>" type="password" id="pwd_confirmation" name="pwd_confirmation" required autocomplete="new-password">
                    <div class="invalid-feedback"><?= e($erreurs['pwd_confirmation'] ?? '') ?></div>
                </div>
                <button class="btn btn-aj w-100" type="submit">Créer mon compte <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
            </form>

            <p class="text-center mt-4 mb-0">Déjà inscrit ?
                <a href="<?= url('connexion', ['redirect' => $redirect]) ?>">Se connecter</a>
            </p>
        </div>
    </div>
</section>
