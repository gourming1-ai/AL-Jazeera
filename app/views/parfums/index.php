<?php
/**
 * Catalogue : grille de parfums, filtres latéraux, tri, recherche, pagination.
 * Les filtres sont un formulaire GET : l'adresse de la page décrit toute la recherche.
 */

// Adresse du catalogue avec les paramètres courants, modifiés par $changements
$lien = fn (array $changements = []) => url('parfums', array_merge($params, $changements));
$lienPage = fn (int $n) => $lien(['page' => $n > 1 ? $n : '']);

$nbFiltres = 0;
foreach (array_keys($groupes) as $cle) {
    $nbFiltres += count($filtres[$cle]);
}
?>

<section class="bandeau-page">
    <div class="container-xxl">
        <ol class="breadcrumb fil">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Parfums</li>
        </ol>
        <h1>Nos parfums</h1>
        <p>Toutes les marques, classées par famille olfactive, fragrance, type d'eau et contenance.</p>
    </div>
</section>

<div class="container-xxl py-5">
    <div class="row gx-4 gy-5">

        <!-- Filtres latéraux (tiroir sur mobile) -->
        <div class="col-lg-3">
            <div class="offcanvas-lg offcanvas-start filtres-colonne" tabindex="-1" id="tiroir-filtres" aria-labelledby="titre-filtres">
                <div class="offcanvas-header border-bottom">
                    <h2 class="filtres-titre" id="titre-filtres">Filtrer</h2>
                    <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#tiroir-filtres" aria-label="Fermer"></button>
                </div>
                <div class="offcanvas-body d-block">
                    <form id="filtres" action="<?= url('parfums') ?>" method="get" data-envoi-auto>
                        <?php foreach ($groupes as $cle => $groupe): ?>
                            <details class="filtre"<?= $filtres[$cle] || in_array($cle, ['marque', 'famille'], true) ? ' open' : '' ?>>
                                <summary>
                                    <span><?= e($groupe['titre']) ?><?php if ($filtres[$cle]): ?><span class="filtre-badge"><?= count($filtres[$cle]) ?></span><?php endif; ?></span>
                                </summary>
                                <div class="filtre-options">
                                    <?php foreach ($groupe['options'] as $option): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="<?= $cle ?>[]" value="<?= (int) $option['id'] ?>"
                                                   id="<?= $cle ?>-<?= (int) $option['id'] ?>"<?= in_array((int) $option['id'], $filtres[$cle], true) ? ' checked' : '' ?>>
                                            <label class="form-check-label" for="<?= $cle ?>-<?= (int) $option['id'] ?>">
                                                <span><?= e($option['libelle']) ?></span>
                                                <small><?= (int) $option['nbParfums'] ?></small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </details>
                        <?php endforeach; ?>

                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-aj" type="submit">Appliquer les filtres</button>
                            <?php if ($nbFiltres || $filtres['q'] !== ''): ?>
                                <a class="btn btn-aj-contour" href="<?= url('parfums') ?>">Tout effacer</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Résultats -->
        <div class="col-lg-9">
            <div class="barre-catalogue">
                <p class="resultats" aria-live="polite">
                    <strong><?= (int) $total ?></strong> parfum<?= $total > 1 ? 's' : '' ?>
                    <?php if ($nbPages > 1): ?> · page <?= $page ?> sur <?= $nbPages ?><?php endif; ?>
                </p>

                <button class="btn btn-aj-contour btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#tiroir-filtres" aria-controls="tiroir-filtres">
                    <i class="bi bi-sliders me-1" aria-hidden="true"></i> Filtres<?= $nbFiltres ? ' (' . $nbFiltres . ')' : '' ?>
                </button>

                <div class="champ-recherche">
                    <label class="visually-hidden" for="q">Rechercher un parfum par son nom</label>
                    <input class="form-control" type="search" id="q" name="q" form="filtres" value="<?= e($filtres['q']) ?>" placeholder="Nom du parfum ou marque…" maxlength="100">
                    <button type="submit" form="filtres" aria-label="Lancer la recherche"><i class="bi bi-search" aria-hidden="true"></i></button>
                </div>

                <div class="tri">
                    <label for="tri">Trier par</label>
                    <select class="form-select" id="tri" name="tri" form="filtres">
                        <?php foreach ($tris as $cle => $libelle): ?>
                            <option value="<?= $cle ?>"<?= $cle === $tri ? ' selected' : '' ?>><?= e($libelle) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if ($nbFiltres || $filtres['q'] !== ''): ?>
                <div class="pastilles" aria-label="Filtres actifs">
                    <?php if ($filtres['q'] !== ''): ?>
                        <a class="pastille" href="<?= e($lien(['q' => ''])) ?>" title="Retirer ce filtre">
                            <small>Recherche</small> « <?= e($filtres['q']) ?> » <i class="bi bi-x" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                    <?php foreach ($groupes as $cle => $groupe): ?>
                        <?php foreach ($groupe['options'] as $option): ?>
                            <?php if (in_array((int) $option['id'], $filtres[$cle], true)): ?>
                                <a class="pastille" title="Retirer ce filtre"
                                   href="<?= e($lien([$cle => array_values(array_diff($filtres[$cle], [(int) $option['id']]))])) ?>">
                                    <small><?= e($groupe['titre']) ?></small> <?= e($option['libelle']) ?> <i class="bi bi-x" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <a class="btn-lien ms-2" href="<?= url('parfums') ?>">Tout effacer</a>
                </div>
            <?php endif; ?>

            <?php if ($parfums): ?>
                <div class="row row-cols-2 row-cols-md-3 g-3 g-md-4">
                    <?php foreach ($parfums as $parfum): ?>
                        <div class="col"><?php require ROOT . '/app/views/includes/carte-parfum.php'; ?></div>
                    <?php endforeach; ?>
                </div>
                <?php require ROOT . '/app/views/includes/pagination.php'; ?>
            <?php else: ?>
                <div class="vide">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <h2 class="h4 mt-3">Aucun parfum ne correspond</h2>
                    <p class="texte-doux">Essayez un autre nom ou retirez un filtre.</p>
                    <a class="btn btn-aj" href="<?= url('parfums') ?>">Voir tous les parfums <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
