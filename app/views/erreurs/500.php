<?php
/**
 * Page affichée quand une erreur imprévue survient (le plus souvent : base non importée
 * ou identifiants MySQL incorrects). Variables : $erreur (Throwable), $baseDeDonnees (bool).
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur – Maison Al Jazeera</title>
    <link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<main class="container py-5" style="max-width: 46rem">
    <p class="text-center"><span class="logo-aj">A<span>J</span></span></p>
    <h1 class="h3 capitales text-center mb-4">Le site est momentanément indisponible</h1>

    <?php if ($baseDeDonnees): ?>
        <div class="encart">
            <h2 class="h5">La base de données ne répond pas</h2>
            <p>Vérifiez l'installation :</p>
            <ol class="mb-0">
                <li>MySQL est démarré (XAMPP, WAMP ou MAMP).</li>
                <li>Les scripts <code>database/parfumerie.sql</code> puis <code>database/donnees.sql</code> ont été importés.</li>
                <li>Les identifiants de <code>config/config.php</code> sont corrects (copie de <code>config.example.php</code>).</li>
            </ol>
        </div>
    <?php else: ?>
        <p class="text-center texte-doux">Une erreur inattendue est survenue. Réessayez dans un instant.</p>
    <?php endif; ?>

    <?php if (config('debug', false)): ?>
        <pre class="encart mt-4 small" style="white-space: pre-wrap"><?= e(get_class($erreur) . ' : ' . $erreur->getMessage()) . "\n" . e($erreur->getFile() . ':' . $erreur->getLine()) ?></pre>
    <?php endif; ?>
</main>
</body>
</html>
