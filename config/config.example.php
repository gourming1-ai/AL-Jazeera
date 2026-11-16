<?php
// Modèle de configuration.
// Copier ce fichier en config/config.php puis renseigner vos valeurs locales.
// config.php est ignoré par Git : ne jamais y mettre de mot de passe dans un fichier commité.
// Tant que config.php n'existe pas, le site utilise les valeurs ci-dessous (celles de XAMPP par défaut).

return [
    'db_host'    => 'localhost',
    'db_port'    => 3306,
    'db_name'    => 'parfumerie',
    'db_user'    => 'root',
    'db_pass'    => '',
    'db_charset' => 'utf8mb4',

    // Racine du site. null = détection automatique (recommandé).
    // À renseigner seulement si la détection échoue, ex. '/AL-Jazeera/public/'.
    'base_url'   => null,

    // true : affiche le détail des erreurs (développement). false : message générique (démonstration).
    'debug'      => true,
];
