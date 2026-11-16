</main>

<footer class="pied">
    <div class="container-xxl">
        <div class="row g-4">
            <div class="col-lg-5">
                <a class="logo" href="<?= url() ?>" aria-label="Maison Al Jazeera – accueil">
                    <span class="logo-aj" aria-hidden="true">A<span>J</span></span>
                    <span class="logo-filet" aria-hidden="true"></span>
                    <span class="logo-nom">Maison<br>Al Jazeera</span>
                </a>
                <p class="slogan">L'art du parfum, une émotion intemporelle.</p>
            </div>
            <div class="col-6 col-lg-2">
                <h2>Le catalogue</h2>
                <ul>
                    <li><a href="<?= url('parfums') ?>">Tous les parfums</a></li>
                    <li><a href="<?= url('parfums', ['tri' => 'prix_asc']) ?>">Prix croissant</a></li>
                    <li><a href="<?= url('univers') ?>">Familles olfactives</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h2>La Maison</h2>
                <ul>
                    <li><a href="<?= url('la-maison') ?>">Notre histoire</a></li>
                    <li><a href="<?= url('univers') ?>">L'Univers</a></li>
                    <li><a href="<?= url('contact') ?>">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h2>Mon compte</h2>
                <ul>
                    <?php if (utilisateur()): ?>
                        <li>Connecté : <?= e(utilisateur()['pseudo']) ?></li>
                        <?php if (est_admin()): ?><li><a href="<?= url('admin') ?>">Back-office</a></li><?php endif; ?>
                    <?php else: ?>
                        <li><a href="<?= url('connexion') ?>">Se connecter</a></li>
                        <li><a href="<?= url('inscription') ?>">Créer un compte</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <p class="mentions mb-0">
            © <?= date('Y') ?> Maison Al Jazeera — entreprise fictive, projet pédagogique. Ce site est un catalogue : aucune vente en ligne.
            Les marques et noms de parfums cités appartiennent à leurs propriétaires respectifs ; les visuels de flacons sont des illustrations.
        </p>
    </div>
</footer>

<script src="<?= asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('assets/js/main.js') ?>"></script>
</body>
</html>
