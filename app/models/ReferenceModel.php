<?php
/**
 * Modèle commun aux cinq tables de référence (MARQUE, FAMILLE, FRAGRANCE, EAU, CONTENANCE).
 * Elles ont la même forme : un identifiant et un libellé. Chaque classe fille indique
 * seulement le nom de sa table, de sa clé, de son champ et de la table qui l'utilise.
 */
abstract class ReferenceModel extends Model
{
    protected const TABLE = '';
    protected const CLE   = '';
    protected const CHAMP = 'libelle';
    /** Table dans laquelle on compte les parfums liés. */
    protected const LIEN  = 'PARFUM';
    protected const TRI   = 'libelle';

    /** Liste complète : chaque ligne contient « id » et « libelle ». */
    public static function tous(): array
    {
        return self::requete(
            'SELECT ' . static::CLE . ' AS id, ' . static::CHAMP . ' AS libelle
               FROM ' . static::TABLE . ' ORDER BY ' . static::TRI
        )->fetchAll();
    }

    /** Liste complète avec le nombre de parfums concernés (« nbParfums »). */
    public static function tousAvecCompte(): array
    {
        return self::requete(
            'SELECT t.' . static::CLE . ' AS id, t.' . static::CHAMP . ' AS libelle,
                    (SELECT COUNT(*) FROM ' . static::LIEN . ' l WHERE l.' . static::CLE . ' = t.' . static::CLE . ') AS nbParfums
               FROM ' . static::TABLE . ' t ORDER BY ' . static::TRI
        )->fetchAll();
    }

    public static function trouver(int $id): ?array
    {
        $ligne = self::requete(
            'SELECT ' . static::CLE . ' AS id, ' . static::CHAMP . ' AS libelle
               FROM ' . static::TABLE . ' WHERE ' . static::CLE . ' = ?',
            [$id]
        )->fetch();
        return $ligne ?: null;
    }

    /** Vrai si le libellé est déjà pris (par une autre ligne que $saufId). */
    public static function existe(string $libelle, ?int $saufId = null): bool
    {
        return (bool) self::requete(
            'SELECT 1 FROM ' . static::TABLE . ' WHERE ' . static::CHAMP . ' = ? AND ' . static::CLE . ' <> ?',
            [$libelle, $saufId ?? 0]
        )->fetchColumn();
    }

    public static function creer(string $libelle): int
    {
        self::requete('INSERT INTO ' . static::TABLE . ' (' . static::CHAMP . ') VALUES (?)', [$libelle]);
        return (int) self::db()->lastInsertId();
    }

    public static function modifier(int $id, string $libelle): void
    {
        self::requete(
            'UPDATE ' . static::TABLE . ' SET ' . static::CHAMP . ' = ? WHERE ' . static::CLE . ' = ?',
            [$libelle, $id]
        );
    }

    /** Nombre de parfums qui utilisent cette ligne (bloque la suppression s'il y en a). */
    public static function nbParfums(int $id): int
    {
        return (int) self::requete(
            'SELECT COUNT(*) FROM ' . static::LIEN . ' WHERE ' . static::CLE . ' = ?',
            [$id]
        )->fetchColumn();
    }

    public static function supprimer(int $id): void
    {
        self::requete('DELETE FROM ' . static::TABLE . ' WHERE ' . static::CLE . ' = ?', [$id]);
    }

    public static function compter(): int
    {
        return (int) self::requete('SELECT COUNT(*) FROM ' . static::TABLE)->fetchColumn();
    }

    /** Parmi les identifiants reçus, ne garde que ceux qui existent vraiment. */
    public static function filtrerIds(array $ids): array
    {
        if (!$ids) {
            return [];
        }
        $existants = self::requete(
            'SELECT ' . static::CLE . ' FROM ' . static::TABLE . ' WHERE ' . static::CLE . ' IN (' . self::jokers($ids) . ')',
            $ids
        )->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', $existants);
    }
}
