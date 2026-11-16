<?php
/**
 * Pagination. Attend : $page, $nbPages et $lienPage (fonction qui donne l'adresse d'une page).
 */
if ($nbPages > 1):
    // pages affichées : la première, la dernière et les voisines de la page courante
    $numeros = array_unique(array_filter(
        [1, $page - 2, $page - 1, $page, $page + 1, $page + 2, $nbPages],
        fn ($n) => $n >= 1 && $n <= $nbPages
    ));
    sort($numeros);
?>
<nav aria-label="Pagination" class="mt-5">
    <ul class="pagination justify-content-center flex-wrap">
        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= $page > 1 ? e($lienPage($page - 1)) : '#' ?>" aria-label="Page précédente"><i class="bi bi-chevron-left" aria-hidden="true"></i></a>
        </li>
        <?php $precedent = 0; foreach ($numeros as $n): ?>
            <?php if ($n > $precedent + 1): ?>
                <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php endif; ?>
            <li class="page-item<?= $n === $page ? ' active' : '' ?>">
                <a class="page-link" href="<?= e($lienPage($n)) ?>"<?= $n === $page ? ' aria-current="page"' : '' ?>><?= $n ?></a>
            </li>
        <?php $precedent = $n; endforeach; ?>
        <li class="page-item<?= $page >= $nbPages ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= $page < $nbPages ? e($lienPage($page + 1)) : '#' ?>" aria-label="Page suivante"><i class="bi bi-chevron-right" aria-hidden="true"></i></a>
        </li>
    </ul>
</nav>
<?php endif; ?>
