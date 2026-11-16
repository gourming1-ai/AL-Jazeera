<?php
/** Back-office : modération des avis (CU12). */
$lienPage = fn (int $n) => url('admin/avis', ['page' => $n > 1 ? $n : '']);
?>

<div class="panneau">
    <div class="panneau-titre">
        <span class="texte-doux"><?= (int) $total ?> avis · du plus récent au plus ancien</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Client</th>
                <th scope="col">Parfum</th>
                <th scope="col">Note</th>
                <th scope="col">Commentaire</th>
                <th scope="col"><span class="visually-hidden">Actions</span></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($avis as $a): ?>
                <tr>
                    <td class="text-nowrap chiffres"><?= e(date_fr($a['dateAvis'])) ?></td>
                    <td><strong><?= e($a['pseudo']) ?></strong><br><small class="texte-doux"><?= e($a['login']) ?></small></td>
                    <td><a href="<?= url('parfums/' . $a['idParfum'] . '-' . slug($a['parfum'])) ?>#avis"><?= e($a['parfum']) ?></a><br><small class="texte-doux"><?= e($a['marque']) ?></small></td>
                    <td><?= etoiles($a['note']) ?></td>
                    <td style="min-width: 16rem"><?= $a['commentaire'] !== null && $a['commentaire'] !== '' ? e($a['commentaire']) : '<span class="texte-doux">Sans commentaire</span>' ?></td>
                    <td class="actions">
                        <form action="<?= url('admin/avis/' . $a['idParfum'] . '/' . $a['idUser'] . '/supprimer') ?>" method="post"
                              data-confirmer="Supprimer l'avis de <?= e($a['pseudo']) ?> sur « <?= e($a['parfum']) ?> » ?">
                            <?= csrf_field() ?>
                            <button class="btn btn-supprimer btn-icone" type="submit" title="Supprimer cet avis" aria-label="Supprimer l'avis de <?= e($a['pseudo']) ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$avis): ?>
                <tr><td colspan="6" class="text-center texte-doux py-5">Aucun avis à modérer.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require ROOT . '/app/views/includes/pagination.php'; ?>
