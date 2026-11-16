<?php
/** Contact : coordonnées (fictives) et formulaire. Le message est vérifié mais ni stocké ni envoyé. */
$champ = fn (string $nom) => isset($erreurs[$nom]) ? ' is-invalid' : '';
?>

<section class="bandeau-page">
    <div class="container-xxl">
        <ol class="breadcrumb fil">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contact</li>
        </ol>
        <h1>Contact</h1>
        <p>Une question sur un parfum ou sur la Maison ? Écrivez-nous.</p>
    </div>
</section>

<section class="section">
    <div class="container-xxl">
        <div class="row gx-4 gy-5">
            <div class="col-lg-5">
                <h2 class="h3 capitales mb-4">La Maison</h2>
                <ul class="coordonnees">
                    <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span>12, rue de l'Île<br>75004 Paris</span></li>
                    <li><i class="bi bi-envelope" aria-hidden="true"></i><span>contact@maison-aljazeera.example</span></li>
                    <li><i class="bi bi-telephone" aria-hidden="true"></i><span>01 23 45 67 89</span></li>
                    <li><i class="bi bi-clock" aria-hidden="true"></i><span>Du lundi au samedi, de 10 h à 19 h</span></li>
                </ul>
                <p class="form-text mt-3">Maison Al Jazeera est une entreprise fictive : ces coordonnées sont données à titre d'exemple.</p>
            </div>

            <div class="col-lg-7">
                <div class="encart p-4 p-lg-5">
                    <h2 class="h3 capitales mb-4">Nous écrire</h2>
                    <form action="<?= url('contact') ?>" method="post" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="nom">Nom</label>
                                <input class="form-control<?= $champ('nom') ?>" type="text" id="nom" name="nom" value="<?= e($saisie['nom'] ?? '') ?>" required maxlength="100" autocomplete="name">
                                <div class="invalid-feedback"><?= e($erreurs['nom'] ?? '') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">E-mail</label>
                                <input class="form-control<?= $champ('email') ?>" type="email" id="email" name="email" value="<?= e($saisie['email'] ?? '') ?>" required maxlength="150" autocomplete="email">
                                <div class="invalid-feedback"><?= e($erreurs['email'] ?? '') ?></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="message">Message</label>
                                <textarea class="form-control<?= $champ('message') ?>" id="message" name="message" rows="6" required maxlength="2000"><?= e($saisie['message'] ?? '') ?></textarea>
                                <div class="invalid-feedback"><?= e($erreurs['message'] ?? '') ?></div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-aj" type="submit">Envoyer <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
