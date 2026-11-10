<?php
// Modèle de configuration.
// Copier ce fichier en config/config.php puis renseigner vos valeurs locales.
// config.php est ignoré par Git : ne jamais y mettre de mot de passe dans un fichier commité.

return [
    'db_host'    => 'localhost',
    'db_name'    => 'parfumerie',
    'db_user'    => 'root',
    'db_pass'    => '',
    'db_charset' => 'utf8mb4',
    'base_url'   => '/',   // adapter si le site est dans un sous-dossier, ex. '/AL-Jazeera/public/'
];
