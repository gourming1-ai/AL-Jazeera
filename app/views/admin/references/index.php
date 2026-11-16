<?php
/** Back-office : liste d'une donnée de référence (marques, familles, fragrances, eaux, contenances). */
?>

<div class="panneau">
    <div class="panneau-titre">
        <span class="texte-doux"><?= count($lignes) ?> <?= count($lignes) > 1 ? 'éléments' : 'élément' ?></span>
        <a class="btn btn-aj btn-sm" href="<?= url('admin/' . $ressource . '/creer') ?>"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Ajouter</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col" style="width: 5rem">N°</th>
                <th scope="col"><?= e($config['champ']) ?></th>
                <?php if ($ressource === 'familles'): ?><th scope="col" class="text-end">Fragrances</th><?php endif; ?>
                <th scope="col" class="text-end">Parfums</th>
                <th scope="col"><span class="visually-hidden">Actions</span></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($lignes as $ligne): $id = (int) $ligne['id']; ?>
                <tr>
                    <td class="texte-doux chiffres"><?= $id ?></td>
                    <td><strong><?= e($ligne['libelle']) ?></strong></td>
                    <?php if ($ressource === 'familles'): ?><td class="text-end chiffres"><?= count($carte[$id] ?? []) ?></td><?php endif; ?>
                    <td class="text-end chiffres"><?= (int) $ligne['nbParfums'] ?></td>
                    <td class="actions">
                        <a class="btn btn-aj-contour btn-icone" href="<?= url('admin/' . $ressource . '/' . $id . '/modifier') ?>" title="Modifier" aria-label="Modifier <?= e($ligne['libelle']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                        <form action="<?= url('admin/' . $ressource . '/' . $id . '/supprimer') ?>" method="post" data-confirmer="Supprimer « <?= e($ligne['libelle']) ?> » ?">
                            <?= csrf_field() ?>
                            <button class="btn btn-supprimer btn-icone" type="submit" aria-label="Supprimer <?= e($ligne['libelle']) ?>"
                                <?= $ligne['nbParfums'] > 0 ? 'disabled title="Encore utilisé par ' . (int) $ligne['nbParfums'] . ' parfum(s)"' : 'title="Supprimer"' ?>><i class="bi bi-trash" aria-hidden="true"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$lignes): ?>
                <tr><td colspan="5" class="text-center texte-doux py-5">Rien pour le moment. Cliquez sur « Ajouter ».</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<p class="form-text mt-3">Un élément utilisé par au moins un parfum ne peut pas être supprimé.</p>
