<?php
/**
 * Fiche détaillée d'un parfum : image, marque, famille, type d'eau, fragrances,
 * contenances et prix, note moyenne, avis et formulaire d'avis.
 */
$user  = utilisateur();
$fiche = 'parfums/' . $parfum['idParfum'] . '-' . slug($parfum['nom']);
?>

<div class="container-xxl pt-4">
    <ol class="breadcrumb fil justify-content-start mb-0">
        <li class="breadcrumb-item"><a href="<?= url() ?>">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= url('parfums') ?>">Parfums</a></li>
        <li class="breadcrumb-item"><a href="<?= url('parfums', ['marque' => [$parfum['idMarque']]]) ?>"><?= e($parfum['marque']) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= e($parfum['nom']) ?></li>
    </ol>
</div>

<article class="container-xxl py-4 py-lg-5">
    <div class="row g-4">
        <div class="col-md-6 col-lg-5">
            <div class="fiche-image">
                <img src="<?= e(photo_url($parfum['photo'])) ?>" alt="Flacon <?= e($parfum['nom']) ?> – <?= e($parfum['marque']) ?>" width="600" height="640">
            </div>
        </div>

        <div class="col-md-6 col-lg-7">
            <a class="fiche-marque" href="<?= url('parfums', ['marque' => [$parfum['idMarque']]]) ?>"><?= e($parfum['marque']) ?></a>
            <h1 class="fiche-nom"><?= e($parfum['nom']) ?></h1>

            <p class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <?php if ($stats['total'] > 0): ?>
                    <?= etoiles($stats['moyenne'], 'etoiles-grandes') ?>
                    <strong class="chiffres"><?= number_format($stats['moyenne'], 1, ',', '') ?> / 5</strong>
                    <a href="#avis">· <?= $stats['total'] ?> avis</a>
                <?php else: ?>
                    <?= etoiles(0, 'etoiles-grandes') ?>
                    <a href="#avis">Aucun avis pour le moment</a>
                <?php endif; ?>
            </p>

            <?php if ($parfum['prixMin'] !== null): ?>
                <p class="fiche-prix mb-4"><small>À partir de</small> <?= prix($parfum['prixMin']) ?></p>
            <?php endif; ?>

            <div class="fiche-bloc">
                <dl class="caracteristiques">
                    <div>
                        <dt>Marque</dt>
                        <dd><a href="<?= url('parfums', ['marque' => [$parfum['idMarque']]]) ?>"><?= e($parfum['marque']) ?></a></dd>
                    </div>
                    <div>
                        <dt>Famille olfactive</dt>
                        <dd><a href="<?= url('parfums', ['famille' => [$parfum['idFamille']]]) ?>"><?= e($parfum['famille']) ?></a></dd>
                    </div>
                    <div>
                        <dt>Type d'eau</dt>
                        <dd><a href="<?= url('parfums', ['eau' => [$parfum['idEau']]]) ?>"><?= e($parfum['eau']) ?></a></dd>
                    </div>
                </dl>
            </div>

            <div class="fiche-bloc">
                <h2>Fragrances</h2>
                <?php if ($fragrances): ?>
                    <?php foreach ($fragrances as $fragrance): ?>
                        <a class="note-fragrance" href="<?= url('parfums', ['fragrance' => [$fragrance['idFragrance']]]) ?>"
                           title="Voir les parfums à la note <?= e($fragrance['libelle']) ?>"><?= e($fragrance['libelle']) ?></a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="texte-doux mb-0">Les notes de ce parfum ne sont pas encore renseignées.</p>
                <?php endif; ?>
            </div>

            <div class="fiche-bloc">
                <h2>Contenances et prix</h2>
                <?php if ($contenances): ?>
                    <ul class="contenances">
                        <?php foreach ($contenances as $c): $volume = (float) $c['libelle']; ?>
                            <li>
                                <span class="volume"><?= e($c['libelle']) ?></span>
                                <span class="montant"><?= prix($c['prix']) ?></span>
                                <?php if ($volume > 0): ?>
                                    <span class="rapport">soit <?= prix($c['prix'] / $volume * 100) ?> / 100 ml</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="form-text mt-3 mb-0"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Prix indicatifs. Maison Al Jazeera est un catalogue : aucune vente en ligne.</p>
                <?php else: ?>
                    <p class="texte-doux mb-0">Prix non communiqué pour ce parfum.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>

<!-- Avis -->
<section class="section section-claire" id="avis">
    <div class="container-xxl">
        <h2 class="titre-section">Avis des clients</h2>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="synthese-avis">
                    <?php if ($stats['total'] > 0): ?>
                        <div class="moyenne chiffres"><?= number_format($stats['moyenne'], 1, ',', '') ?><small> / 5</small></div>
                        <div class="my-2"><?= etoiles($stats['moyenne'], 'etoiles-grandes') ?></div>
                        <p class="texte-doux mb-0"><?= $stats['total'] ?> avis</p>
                        <ul class="repartition">
                            <?php foreach ($stats['repartition'] as $note => $nb): ?>
                                <li>
                                    <span><?= $note ?> <i class="bi bi-star-fill" aria-hidden="true"></i></span>
                                    <span class="jauge"><span style="width: <?= round($nb / $stats['total'] * 100) ?>%"></span></span>
                                    <span><?= $nb ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="my-2"><?= etoiles(0, 'etoiles-grandes') ?></div>
                        <p class="texte-doux mb-0">Ce parfum n'a pas encore d'avis.<br>Soyez la première personne à le noter.</p>
                    <?php endif; ?>
                </div>

                <div class="encart mt-4">
                    <?php if (!$user): ?>
                        <h3>Donner mon avis</h3>
                        <p class="texte-doux">Les avis sont réservés aux clients inscrits : un avis par parfum.</p>
                        <div class="d-grid gap-2">
                            <a class="btn btn-aj" href="<?= url('connexion', ['redirect' => $fiche]) ?>">Se connecter <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            <a class="btn btn-aj-contour" href="<?= url('inscription', ['redirect' => $fiche]) ?>">Créer un compte</a>
                        </div>
                    <?php elseif ($monAvis): ?>
                        <h3>Votre avis est publié</h3>
                        <p class="mb-2"><?= etoiles($monAvis['note']) ?> <span class="texte-doux">· le <?= e(date_fr($monAvis['dateAvis'])) ?></span></p>
                        <p class="texte-doux">Un seul avis par parfum. Vous pouvez le retirer pour en déposer un nouveau.</p>
                        <form action="<?= url('parfums/' . $parfum['idParfum'] . '/avis/supprimer') ?>" method="post" data-confirmer="Retirer votre avis sur ce parfum ?">
                            <?= csrf_field() ?>
                            <button class="btn btn-aj-contour btn-sm" type="submit"><i class="bi bi-trash me-1" aria-hidden="true"></i> Retirer mon avis</button>
                        </form>
                    <?php else: ?>
                        <h3>Donner mon avis</h3>
                        <form action="<?= url('parfums/' . $parfum['idParfum'] . '/avis') ?>" method="post">
                            <?= csrf_field() ?>
                            <fieldset class="mb-3">
                                <legend class="form-label">Votre note</legend>
                                <div class="choix-note">
                                    <?php for ($n = 5; $n >= 1; $n--): ?>
                                        <input type="radio" name="note" id="note-<?= $n ?>" value="<?= $n ?>" required<?= (int) ($saisie['note'] ?? 0) === $n ? ' checked' : '' ?>>
                                        <label for="note-<?= $n ?>" title="<?= $n ?> étoile<?= $n > 1 ? 's' : '' ?>"><span class="visually-hidden"><?= $n ?> étoile<?= $n > 1 ? 's' : '' ?></span></label>
                                    <?php endfor; ?>
                                </div>
                            </fieldset>
                            <div class="mb-3">
                                <label class="form-label" for="commentaire">Votre commentaire <span class="text-lowercase fw-normal">(facultatif)</span></label>
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="4" maxlength="1000"
                                          placeholder="Tenue, sillage, occasion…"><?= e($saisie['commentaire'] ?? '') ?></textarea>
                            </div>
                            <button class="btn btn-aj w-100" type="submit">Publier mon avis <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-8">
                <?php if ($avis): ?>
                    <?php foreach ($avis as $a): ?>
                        <div class="avis">
                            <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2">
                                <span class="avis-auteur"><?= e($a['pseudo']) ?></span>
                                <span class="avis-date">le <?= e(date_fr($a['dateAvis'])) ?></span>
                            </div>
                            <?= etoiles($a['note']) ?>
                            <?php if ($a['commentaire'] !== null && $a['commentaire'] !== ''): ?>
                                <p><?= e($a['commentaire']) ?></p>
                            <?php else: ?>
                                <p class="sans-commentaire">Note déposée sans commentaire.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="vide">
                        <i class="bi bi-chat-quote" aria-hidden="true"></i>
                        <p class="mt-3 mb-0 texte-doux">Aucun avis pour le moment sur <?= e($parfum['nom']) ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if ($similaires): ?>
<section class="section">
    <div class="container-xxl">
        <h2 class="titre-section">Dans la même famille</h2>
        <div class="row row-cols-2 row-cols-md-4 g-3 g-md-4">
            <?php foreach ($similaires as $parfum): /* la carte lit la variable $parfum */ ?>
                <div class="col"><?php require ROOT . '/app/views/includes/carte-parfum.php'; ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
