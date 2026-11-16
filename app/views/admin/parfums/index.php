<?php
/** Back-office : liste des parfums avec modifier et supprimer. */
$lienPage = fn (int $n) => url('admin/parfums', ['q' => $q, 'page' => $n > 1 ? $n : '']);
?>

<div class="panneau">
    <div class="panneau-titre flex-wrap">
        <form class="champ-recherche" action="<?= url('admin/parfums') ?>" method="get" role="search">
            <label class="visually-hidden" for="q">Rechercher un parfum</label>
            <input class="form-control" type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="Nom du parfum ou marque…">
            <button type="submit" aria-label="Rechercher"><i class="bi bi-search" aria-hidden="true"></i></button>
        </form>
        <span class="texte-doux me-auto"><?= (int) $total ?> parfum<?= $total > 1 ? 's' : '' ?><?php if ($q !== ''): ?> · <a href="<?= url('admin/parfums') ?>">tout afficher</a><?php endif; ?></span>
        <a class="btn btn-aj btn-sm" href="<?= url('admin/parfums/creer') ?>"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Nouveau parfum</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">Photo</th>
                <th scope="col">Parfum</th>
                <th scope="col">Famille</th>
                <th scope="col">Type d'eau</th>
                <th scope="col" class="text-end">Prix dès</th>
                <th scope="col" class="text-end">Avis</th>
                <th scope="col"><span class="visually-hidden">Actions</span></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($parfums as $p): ?>
                <tr>
                    <td><img class="vignette" src="<?= e(photo_url($p['photo'])) ?>" alt=""></td>
                    <td><strong><?= e($p['nom']) ?></strong><br><small class="texte-doux"><?= e($p['marque']) ?></small></td>
                    <td><?= e($p['famille']) ?></td>
                    <td><?= e($p['eau']) ?></td>
                    <td class="text-end chiffres"><?= $p['prixMin'] !== null ? prix($p['prixMin']) : '<span class="texte-doux">—</span>' ?></td>
                    <td class="text-end chiffres"><?= (int) $p['nbAvis'] ?></td>
                    <td class="actions">
                        <a class="btn btn-aj-contour btn-icone" href="<?= url_parfum($p) ?>" title="Voir la fiche" aria-label="Voir la fiche de <?= e($p['nom']) ?>"><i class="bi bi-eye" aria-hidden="true"></i></a>
                        <a class="btn btn-aj-contour btn-icone" href="<?= url('admin/parfums/' . $p['idParfum'] . '/modifier') ?>" title="Modifier" aria-label="Modifier <?= e($p['nom']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                        <form action="<?= url('admin/parfums/' . $p['idParfum'] . '/supprimer') ?>" method="post"
                              data-confirmer="Supprimer « <?= e($p['nom']) ?> » ? Ses prix et ses <?= (int) $p['nbAvis'] ?> avis seront aussi supprimés.">
                            <?= csrf_field() ?>
                            <button class="btn btn-supprimer btn-icone" type="submit" title="Supprimer" aria-label="Supprimer <?= e($p['nom']) ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$parfums): ?>
                <tr><td colspan="7" class="text-center texte-doux py-5">Aucun parfum trouvé.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require ROOT . '/app/views/includes/pagination.php'; ?>
