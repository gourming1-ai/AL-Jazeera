<?php
/** Carte d'un parfum dans une grille. Attend la variable $parfum (ligne de Parfum::rechercher). */
?>
<article class="carte-parfum">
    <a href="<?= url_parfum($parfum) ?>">
        <div class="carte-parfum-image">
            <img src="<?= e(photo_url($parfum['photo'])) ?>" alt="Flacon <?= e($parfum['nom']) ?>" width="600" height="640" loading="lazy">
        </div>
        <div class="carte-parfum-texte">
            <p class="carte-parfum-marque"><?= e($parfum['marque']) ?></p>
            <h3 class="carte-parfum-nom"><?= e($parfum['nom']) ?></h3>
            <p class="carte-parfum-detail"><?= e($parfum['eau']) ?> · <?= e($parfum['famille']) ?></p>
            <?php if ((int) $parfum['nbAvis'] > 0): ?>
                <p class="carte-parfum-note mb-0">
                    <?= etoiles($parfum['noteMoyenne']) ?>
                    <span>(<?= (int) $parfum['nbAvis'] ?>)</span>
                </p>
            <?php endif; ?>
            <p class="carte-parfum-prix">
                <?php if ($parfum['prixMin'] !== null): ?>
                    À partir de <strong><?= prix($parfum['prixMin']) ?></strong>
                <?php else: ?>
                    Prix non communiqué
                <?php endif; ?>
            </p>
        </div>
    </a>
</article>
