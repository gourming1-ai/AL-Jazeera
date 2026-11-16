<?php
/**
 * Connexion unique à la base MySQL (singleton PDO).
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function getInstance(): PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . config('db_host', 'localhost')
                 . ';port=' . config('db_port', 3306)
                 . ';dbname=' . config('db_name', 'parfumerie')
                 . ';charset=' . config('db_charset', 'utf8mb4');

            self::$pdo = new PDO($dsn, config('db_user', 'root'), config('db_pass', ''), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // vraies requêtes préparées
            ]);
        }
        return self::$pdo;
    }
}
