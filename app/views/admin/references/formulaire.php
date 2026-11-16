<?php
/**
 * Back-office : formulaire d'une donnée de référence.
 * Pour une famille ou une fragrance, $options contient les cases à cocher de l'association REGROUPE.
 */
$action = 'admin/' . $ressource . ($ligne ? '/' . $ligne['id'] . '/modifier' : '/creer');
?>

<form action="<?= url($action) ?>" method="post" novalidate style="max-width: 52rem">
    <?= csrf_field() ?>

    <div class="panneau mb-4">
        <div class="panneau-titre"><h2><?= e($config['titre']) ?></h2></div>
        <div class="panneau-corps">
            <label class="form-label" for="libelle"><?= e($config['champ']) ?></label>
            <input class="form-control<?= isset($erreurs['libelle']) ? ' is-invalid' : '' ?>" type="text" id="libelle" name="libelle"
                   value="<?= e($saisie['libelle']) ?>" required maxlength="100" placeholder="Ex. <?= e($config['exemple']) ?>" autofocus>
            <div class="invalid-feedback"><?= e($erreurs['libelle'] ?? '') ?></div>
        </div>
    </div>

    <?php if ($options): ?>
        <div class="panneau mb-4">
            <div class="panneau-titre"><h2><?= e($options['titre']) ?></h2></div>
            <div class="panneau-corps">
                <?php if (isset($erreurs['liens'])): ?>
                    <div class="invalid-feedback d-block mb-2"><?= e($erreurs['liens']) ?></div>
                <?php endif; ?>
                <div class="cases">
                    <?php foreach ($options['liste'] as $o): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="liens[]" value="<?= (int) $o['id'] ?>" id="lien-<?= (int) $o['id'] ?>"<?= in_array((int) $o['id'], $saisie['liens'], true) ? ' checked' : '' ?>>
                            <label class="form-check-label" for="lien-<?= (int) $o['id'] ?>"><?= e($o['libelle']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="form-text mt-3 mb-0">Un parfum ne peut porter que des fragrances regroupées par sa famille.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-aj" type="submit"><i class="bi bi-check-lg me-1" aria-hidden="true"></i> Enregistrer</button>
        <a class="btn btn-aj-contour" href="<?= url('admin/' . $ressource) ?>">Annuler</a>
    </div>
</form>
