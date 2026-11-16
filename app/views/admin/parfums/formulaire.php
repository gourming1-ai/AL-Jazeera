<?php
/**
 * Back-office : formulaire de création et d'édition d'un parfum.
 * $parfum vaut null à la création. $carte = [idFamille => fragrances autorisées] :
 * le JavaScript n'affiche que les fragrances de la famille choisie (règle de gestion),
 * et le contrôleur revérifie la règle côté serveur.
 */
$action  = $parfum ? 'admin/parfums/' . $parfum['idParfum'] . '/modifier' : 'admin/parfums/creer';
$invalide = fn (string $nom) => isset($erreurs[$nom]) ? ' is-invalid' : '';
?>

<?php if ($erreurs): ?>
    <div class="alert alert-danger" role="alert">Le formulaire contient des erreurs : corrigez les champs signalés.</div>
<?php endif; ?>

<form action="<?= url($action) ?>" method="post" enctype="multipart/form-data" novalidate id="formulaire-parfum"
      data-carte="<?= e(json_encode($carte)) ?>">
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="panneau mb-4">
                <div class="panneau-titre"><h2>Identité</h2></div>
                <div class="panneau-corps">
                    <div class="mb-3">
                        <label class="form-label" for="nom">Nom du parfum</label>
                        <input class="form-control<?= $invalide('nom') ?>" type="text" id="nom" name="nom" value="<?= e($saisie['nom']) ?>" required maxlength="150">
                        <div class="invalid-feedback"><?= e($erreurs['nom'] ?? '') ?></div>
                    </div>
                    <div class="row g-3">
                        <?php foreach ([
                            'idMarque'  => ['Marque', $marques],
                            'idFamille' => ['Famille olfactive', $familles],
                            'idEau'     => ["Type d'eau", $eaux],
                        ] as $nom => [$libelle, $options]): ?>
                            <div class="col-md-4">
                                <label class="form-label" for="<?= $nom ?>"><?= e($libelle) ?></label>
                                <select class="form-select<?= $invalide($nom) ?>" id="<?= $nom ?>" name="<?= $nom ?>" required>
                                    <option value="">Choisir…</option>
                                    <?php foreach ($options as $o): ?>
                                        <option value="<?= (int) $o['id'] ?>"<?= (int) $o['id'] === (int) $saisie[$nom] ? ' selected' : '' ?>><?= e($o['libelle']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?= e($erreurs[$nom] ?? '') ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="panneau">
                <div class="panneau-titre"><h2>Fragrances</h2><span class="texte-doux small">Au moins une, parmi celles de la famille</span></div>
                <div class="panneau-corps">
                    <?php if (isset($erreurs['fragrances'])): ?>
                        <div class="invalid-feedback d-block mb-2"><?= e($erreurs['fragrances']) ?></div>
                    <?php endif; ?>
                    <p class="texte-doux mb-2" id="aide-fragrances" hidden>Choisissez d'abord une famille olfactive pour voir ses fragrances.</p>
                    <div class="cases" id="fragrances">
                        <?php foreach ($fragrances as $f): ?>
                            <div class="form-check" data-fragrance="<?= (int) $f['id'] ?>">
                                <input class="form-check-input" type="checkbox" name="fragrances[]" value="<?= (int) $f['id'] ?>" id="fragrance-<?= (int) $f['id'] ?>"<?= in_array((int) $f['id'], $saisie['fragrances'], true) ? ' checked' : '' ?>>
                                <label class="form-check-label" for="fragrance-<?= (int) $f['id'] ?>"><?= e($f['libelle']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="form-text mt-3 mb-0">Une fragrance manque ? Rattachez-la à la famille dans
                        <a href="<?= url('admin/familles') ?>">Familles</a> ou <a href="<?= url('admin/fragrances') ?>">Fragrances</a>.</p>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="panneau mb-4">
                <div class="panneau-titre"><h2>Contenances et prix</h2><span class="texte-doux small">Cochez les flacons proposés</span></div>
                <div class="panneau-corps">
                    <?php if (isset($erreurs['prix'])): ?>
                        <div class="invalid-feedback d-block mb-2"><?= e($erreurs['prix']) ?></div>
                    <?php endif; ?>
                    <?php foreach ($contenances as $c): $id = (int) $c['id']; $coche = array_key_exists($id, $saisie['prix']); ?>
                        <div class="ligne-prix">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="contenances[]" value="<?= $id ?>" id="contenance-<?= $id ?>"<?= $coche ? ' checked' : '' ?> data-prix="#prix-<?= $id ?>">
                                <label class="form-check-label" for="contenance-<?= $id ?>"><?= e($c['libelle']) ?></label>
                            </div>
                            <div class="input-group">
                                <input class="form-control" type="text" inputmode="decimal" name="prix[<?= $id ?>]" id="prix-<?= $id ?>"
                                       value="<?= $coche ? e(str_replace('.', ',', (string) $saisie['prix'][$id])) : '' ?>" placeholder="0,00"
                                       aria-label="Prix du flacon <?= e($c['libelle']) ?>"<?= $coche ? '' : ' disabled' ?>>
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$contenances): ?>
                        <p class="texte-doux mb-0">Aucune contenance : <a href="<?= url('admin/contenances/creer') ?>">en créer une</a>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="panneau">
                <div class="panneau-titre"><h2>Photo</h2><span class="texte-doux small">JPG, PNG ou WebP · 3 Mo max.</span></div>
                <div class="panneau-corps">
                    <div class="d-flex gap-3 align-items-start">
                        <img class="apercu-photo" id="apercu-photo" src="<?= e(photo_url($saisie['photo'])) ?>" alt="Aperçu de la photo">
                        <div class="flex-grow-1">
                            <label class="form-label" for="photo">Choisir une image</label>
                            <input class="form-control<?= $invalide('photo') ?>" type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                            <div class="invalid-feedback"><?= e($erreurs['photo'] ?? '') ?></div>
                            <?php if ($saisie['photo']): ?>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="supprimer_photo" value="1" id="supprimer_photo">
                                    <label class="form-check-label" for="supprimer_photo">Retirer la photo actuelle</label>
                                </div>
                            <?php else: ?>
                                <div class="form-text">Sans photo, une illustration par défaut est affichée.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-4">
        <button class="btn btn-aj" type="submit"><i class="bi bi-check-lg me-1" aria-hidden="true"></i> <?= $parfum ? 'Enregistrer les modifications' : 'Ajouter le parfum' ?></button>
        <a class="btn btn-aj-contour" href="<?= url('admin/parfums') ?>">Annuler</a>
    </div>
</form>
