<?php /** Tableau de bord : accès aux listes (parfums, marques, familles, fragrances, eaux, contenances, avis). */ ?>

<div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
    <?php foreach ($compteurs as [$intitule, $nombre, $chemin, $icone]): ?>
        <div class="col">
            <?php if ($chemin): ?><a class="compteur" href="<?= url($chemin) ?>"><?php else: ?><div class="compteur"><?php endif; ?>
                <i class="bi <?= $icone ?>" aria-hidden="true"></i>
                <span>
                    <span class="nombre d-block"><?= (int) $nombre ?></span>
                    <span class="intitule"><?= e($intitule) ?></span>
                </span>
            <?php if ($chemin): ?></a><?php else: ?></div><?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="panneau h-100">
            <div class="panneau-titre">
                <h2>Derniers parfums ajoutés</h2>
                <a class="btn btn-aj btn-sm" href="<?= url('admin/parfums/creer') ?>"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Nouveau parfum</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <tbody>
                    <?php foreach ($derniersParfums as $p): ?>
                        <tr>
                            <td style="width: 62px"><img class="vignette" src="<?= e(photo_url($p['photo'])) ?>" alt=""></td>
                            <td><strong><?= e($p['nom']) ?></strong><br><small class="texte-doux"><?= e($p['marque']) ?></small></td>
                            <td class="text-end chiffres"><?= $p['prixMin'] !== null ? 'dès ' . prix($p['prixMin']) : '—' ?></td>
                            <td class="actions"><a class="btn btn-aj-contour btn-icone" href="<?= url('admin/parfums/' . $p['idParfum'] . '/modifier') ?>" aria-label="Modifier <?= e($p['nom']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$derniersParfums): ?>
                        <tr><td class="text-center texte-doux py-4">Aucun parfum pour le moment.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="panneau h-100">
            <div class="panneau-titre">
                <h2>Derniers avis</h2>
                <a class="btn btn-aj-contour btn-sm" href="<?= url('admin/avis') ?>">Modérer <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <tbody>
                    <?php foreach ($derniersAvis as $a): ?>
                        <tr>
                            <td>
                                <strong><?= e($a['pseudo']) ?></strong> <span class="texte-doux">sur <?= e($a['parfum']) ?></span><br>
                                <small class="texte-doux"><?= e(mb_strimwidth((string) $a['commentaire'], 0, 90, '…')) ?: 'Sans commentaire' ?></small>
                            </td>
                            <td class="text-end"><?= etoiles($a['note']) ?><br><small class="texte-doux"><?= e(date_fr($a['dateAvis'])) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$derniersAvis): ?>
                        <tr><td class="text-center texte-doux py-4">Aucun avis pour le moment.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
