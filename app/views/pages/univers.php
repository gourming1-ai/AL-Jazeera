<?php
/**
 * L'Univers : guide des familles olfactives (avec leurs fragrances, association REGROUPE)
 * et des types d'eau. Les listes viennent de la base ; les textes ci-dessous décrivent
 * les libellés connus, les autres reçoivent un texte générique.
 */
$descriptionsFamilles = [
    'Hespéridée' => "La fraîcheur des agrumes : zestes pétillants, eaux lumineuses, sensation de propre et d'énergie.",
    'Florale'    => "Le cœur de la parfumerie : un bouquet de fleurs, de la rose délicate au jasmin solaire.",
    'Fougère'    => "Un accord aromatique et net, autour de la lavande et des notes vertes. Un grand classique.",
    'Chyprée'    => "Le contraste élégant entre la fraîcheur de la bergamote et la profondeur de la mousse de chêne.",
    'Boisée'     => "La noblesse des bois : cèdre sec, santal crémeux, vétiver fumé. Chaleur et caractère.",
    'Ambrée'     => "Des notes chaudes et enveloppantes : vanille, ambre, résines et épices douces.",
    'Cuir'       => "Des accords secs et fumés, entre tabac, bois brûlé et peau tannée. Une signature affirmée.",
];
$descriptionsEaux = [
    'Eau de Cologne'     => 'La plus légère : 2 à 5 % de concentré. Fraîcheur immédiate, tenue courte.',
    'Eau de Toilette'    => 'Entre 5 et 12 % de concentré. Fraîche et facile à porter en journée.',
    'Eau de Parfum'      => 'Entre 12 et 20 % de concentré. Plus dense, elle tient jusqu\'au soir.',
    'Extrait de Parfum'  => 'Plus de 20 % de concentré. La forme la plus riche : quelques gouttes suffisent.',
];
?>

<section class="bandeau-page">
    <div class="container-xxl">
        <ol class="breadcrumb fil">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">L'Univers</li>
        </ol>
        <h1>L'Univers</h1>
        <p>Comprendre un parfum avant de le choisir : sa famille, ses notes, sa concentration.</p>
    </div>
</section>

<section class="section">
    <div class="container-xxl">
        <h2 class="titre-section">Les familles olfactives</h2>
        <p class="sous-titre-section">Une famille regroupe des parfums qui partagent un même caractère. Chacune a ses fragrances de prédilection.</p>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            <?php foreach ($familles as $famille): ?>
                <div class="col">
                    <div class="carte-famille">
                        <h3><?= e($famille['libelle']) ?></h3>
                        <p class="texte-doux mb-0"><?= e($descriptionsFamilles[$famille['libelle']] ?? 'Une famille olfactive du catalogue de la Maison.') ?></p>
                        <div class="notes">
                            <?php foreach ($famille['fragrances'] as $fragrance): ?>
                                <a href="<?= url('parfums', ['fragrance' => [$fragrance['id']]]) ?>"><?= e($fragrance['libelle']) ?></a>
                            <?php endforeach; ?>
                        </div>
                        <a class="btn btn-aj-contour btn-sm" href="<?= url('parfums', ['famille' => [$famille['id']]]) ?>">
                            <?= (int) $famille['nbParfums'] ?> parfum<?= $famille['nbParfums'] > 1 ? 's' : '' ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-marbre">
    <div class="container-xxl">
        <div class="row gx-4 gy-5">
            <div class="col-lg-6">
                <h2 class="titre-section">Les types d'eau</h2>
                <p class="texte-doux text-center">La différence tient à la concentration : plus elle est élevée, plus le parfum tient et se fait intense.</p>
                <ol class="echelle-eaux">
                    <?php foreach ($eaux as $eau): ?>
                        <li>
                            <div>
                                <h3><?= e($eau['libelle']) ?></h3>
                                <p><?= e($descriptionsEaux[$eau['libelle']] ?? "Un type d'eau proposé au catalogue.") ?></p>
                            </div>
                            <a class="btn btn-aj-contour btn-sm" href="<?= url('parfums', ['eau' => [$eau['id']]]) ?>"><?= (int) $eau['nbParfums'] ?> <span class="visually-hidden">parfums</span><i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <div class="col-lg-6">
                <h2 class="titre-section">Lire une fiche</h2>
                <div class="prose">
                    <p><strong>Les fragrances</strong> sont les notes que l'on perçoit : agrumes et aromates à l'ouverture, fleurs et épices au cœur, bois, résines et muscs en fond. Cliquez sur une note pour voir tous les parfums qui la portent.</p>
                    <p><strong>La contenance</strong> fait le prix. Chaque fiche affiche le tarif de tous les flacons disponibles, avec le prix ramené à 100 ml pour comparer facilement.</p>
                    <p><strong>Les avis</strong> sont déposés par des clients inscrits, à raison d'un avis par parfum : une note de 1 à 5 et, s'ils le souhaitent, un commentaire.</p>
                    <p class="mt-4 mb-0"><a class="btn btn-aj" href="<?= url('parfums') ?>">Explorer le catalogue <i class="bi bi-arrow-right" aria-hidden="true"></i></a></p>
                </div>
            </div>
        </div>
    </div>
</section>
