<?php
/**
 * Chargement automatique des classes (core, contrôleurs, modèles).
 * Une classe = un fichier du même nom.
 */
class Autoload
{
    /** Dossiers parcourus, relatifs à app/ */
    private const DOSSIERS = ['core', 'controllers', 'controllers/admin', 'models'];

    public static function register(): void
    {
        spl_autoload_register([self::class, 'charger']);
    }

    public static function charger(string $classe): void
    {
        foreach (self::DOSSIERS as $dossier) {
            $fichier = ROOT . '/app/' . $dossier . '/' . $classe . '.php';
            if (is_file($fichier)) {
                require_once $fichier;
                return;
            }
        }
    }
}
