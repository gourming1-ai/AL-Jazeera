<?php
/**
 * Classe mère des modèles : accès à PDO et raccourci pour les requêtes préparées.
 */
abstract class Model
{
    protected static function db(): PDO
    {
        return Database::getInstance();
    }

    /** Prépare et exécute une requête (paramètres positionnels « ? »). */
    protected static function requete(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute(array_values($params));
        return $stmt;
    }

    /** Fabrique « ?,?,? » pour une clause IN. */
    protected static function jokers(array $valeurs): string
    {
        return implode(',', array_fill(0, count($valeurs), '?'));
    }
}
