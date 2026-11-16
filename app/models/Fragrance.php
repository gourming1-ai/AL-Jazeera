<?php
/** Table FRAGRANCE (idFragrance, libelle) : les notes olfactives. */
class Fragrance extends ReferenceModel
{
    protected const TABLE = 'FRAGRANCE';
    protected const CLE   = 'idFragrance';
    protected const LIEN  = 'POSSEDE';

    /** Identifiants des familles qui regroupent cette fragrance. */
    public static function famillesIds(int $idFragrance): array
    {
        return array_map('intval', self::requete(
            'SELECT idFamille FROM REGROUPE WHERE idFragrance = ?', [$idFragrance]
        )->fetchAll(PDO::FETCH_COLUMN));
    }

    /** Remplace les familles qui regroupent cette fragrance. */
    public static function definirFamilles(int $idFragrance, array $idsFamilles): void
    {
        self::requete('DELETE FROM REGROUPE WHERE idFragrance = ?', [$idFragrance]);
        foreach ($idsFamilles as $idFamille) {
            self::requete('INSERT INTO REGROUPE (idFamille, idFragrance) VALUES (?, ?)', [$idFamille, $idFragrance]);
        }
    }

    /** Familles dont un parfum porte déjà cette fragrance (on ne peut pas les décocher). */
    public static function famillesUtilisees(int $idFragrance): array
    {
        return array_map('intval', self::requete(
            'SELECT DISTINCT p.idFamille
               FROM POSSEDE po JOIN PARFUM p ON p.idParfum = po.idParfum
              WHERE po.idFragrance = ?', [$idFragrance]
        )->fetchAll(PDO::FETCH_COLUMN));
    }

    public static function supprimer(int $id): void
    {
        self::requete('DELETE FROM REGROUPE WHERE idFragrance = ?', [$id]);
        parent::supprimer($id);
    }
}
