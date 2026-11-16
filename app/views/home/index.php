<?php /** Page d'accueil (maquette) : hero, « Nos parfums », bandeau « Plus qu'un parfum, une signature ». */ ?>

<section class="hero">
    <svg class="hero-feuillage" viewBox="0 0 300 380" aria-hidden="true">
        <g fill="#3f2a14">
            <path d="M150 380 C150 250 130 150 60 20 C120 110 165 200 158 380Z"/>
            <?php for ($i = 0; $i < 9; $i++): $y = 60 + $i * 32; $l = 120 - $i * 6; ?>
                <path d="M<?= 82 + $i * 8 ?> <?= $y ?> q-<?= $l * .6 ?> -<?= 20 + $i ?> -<?= $l ?> <?= 10 + $i * 3 ?> q<?= $l * .55 ?> 6 <?= $l ?> -<?= 10 + $i * 3 ?>Z"/>
                <path d="M<?= 86 + $i * 8 ?> <?= $y + 6 ?> q<?= $l * .6 ?> -<?= 34 - $i ?> <?= $l ?> -<?= 30 - $i * 2 ?> q-<?= $l * .45 ?> <?= 34 - $i ?> -<?= $l ?> <?= 30 - $i * 2 ?>Z"/>
            <?php endfor; ?>
        </g>
    </svg>

    <div class="hero-visuel" role="img" aria-label="Flacon d'eau de parfum Maison Al Jazeera posé sur du marbre, entouré de fleurs blanches et de soie"></div>

    <div class="container-xxl hero-contenu">
        <div class="hero-texte">
            <p class="hero-surtitre" aria-hidden="true">Maison</p>
            <h1 class="hero-titre"><span class="visually-hidden">Maison </span>Al Jazeera</h1>
            <svg class="hero-paraphe" viewBox="0 0 480 40" fill="none" aria-hidden="true">
                <path d="M4 30 C120 4 300 2 470 14 C380 12 250 18 150 36" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <p class="hero-slogan">L'art du parfum, une émotion intemporelle</p>
            <div class="filet-losange" aria-hidden="true"><span></span></div>
            <a class="btn btn-aj" href="<?= url('parfums') ?>">Découvrir la collection <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>

    <a class="hero-defiler" href="#nos-parfums" aria-label="Voir nos parfums">
        <span class="souris" aria-hidden="true"></span>
        <i class="bi bi-chevron-down" aria-hidden="true"></i>
    </a>
</section>

<section class="section section-marbre" id="nos-parfums">
    <div class="container-xxl">
        <h2 class="titre-section">Nos parfums</h2>

        <div class="vitrine">
            <?php foreach ($vedettes as $parfum): ?>
                <?php require ROOT . '/app/views/includes/carte-parfum.php'; ?>
            <?php endforeach; ?>

            <aside class="signature">
                <p>Plus qu'un parfum,<br>une signature.</p>
                <div class="filet-losange" aria-hidden="true"><span></span></div>
                <a class="btn btn-aj-or" href="<?= url('la-maison') ?>">À propos de la Maison <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </aside>
        </div>

        <?php if (!$vedettes): ?>
            <p class="text-center texte-doux mt-4">Le catalogue est vide pour le moment. Importez <code>database/donnees.sql</code> ou ajoutez des parfums depuis le back-office.</p>
        <?php endif; ?>
    </div>
</section>

<?php if ($familles): ?>
<section class="section section-claire">
    <div class="container-xxl">
        <h2 class="titre-section">Familles olfactives</h2>
        <p class="sous-titre-section">Chaque parfum appartient à une famille. Choisissez celle qui vous ressemble.</p>
        <div class="familles">
            <?php foreach ($familles as $famille): ?>
                <a class="famille" href="<?= url('parfums', ['famille' => [$famille['id']]]) ?>">
                    <strong><?= e($famille['libelle']) ?></strong>
                    <span><?= (int) $famille['nbParfums'] ?> parfum<?= $famille['nbParfums'] > 1 ? 's' : '' ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($nouveautes): ?>
<section class="section">
    <div class="container-xxl">
        <h2 class="titre-section">Derniers arrivés</h2>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <?php foreach ($nouveautes as $parfum): ?>
                <div class="col"><?php require ROOT . '/app/views/includes/carte-parfum.php'; ?></div>
            <?php endforeach; ?>
        </div>
        <p class="text-center mt-5 mb-0">
            <a class="btn btn-aj-contour" href="<?= url('parfums') ?>">Voir tout le catalogue <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </p>
    </div>
</section>
<?php endif; ?>

<section class="section section-marbre">
    <div class="container-xxl">
        <h2 class="titre-section">Nos valeurs</h2>
        <div class="row gx-4 gy-5">
            <div class="col-sm-6 col-lg-3 valeur">
                <i class="bi bi-gem" aria-hidden="true"></i>
                <h3>Élégance</h3>
                <p>Une présentation sobre et raffinée, sans effet de mode.</p>
            </div>
            <div class="col-sm-6 col-lg-3 valeur">
                <i class="bi bi-people" aria-hidden="true"></i>
                <h3>Mixité</h3>
                <p>Des parfums pour toutes et tous, sans cloison de genre.</p>
            </div>
            <div class="col-sm-6 col-lg-3 valeur">
                <i class="bi bi-card-list" aria-hidden="true"></i>
                <h3>Clarté</h3>
                <p>Des fiches complètes et des prix lisibles par contenance.</p>
            </div>
            <div class="col-sm-6 col-lg-3 valeur">
                <i class="bi bi-chat-quote" aria-hidden="true"></i>
                <h3>Authenticité</h3>
                <p>Des avis déposés par des utilisateurs inscrits.</p>
            </div>
        </div>
    </div>
</section>
