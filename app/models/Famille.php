<?php
/** Table FAMILLE (idFamille, libelle) et association REGROUPE avec les fragrances. */
class Famille extends ReferenceModel
{
    protected const TABLE = 'FAMILLE';
    protected const CLE   = 'idFamille';

    /** Identifiants des fragrances regroupées par la famille. */
    public static function fragrancesIds(int $idFamille): array
    {
        return array_map('intval', self::requete(
            'SELECT idFragrance FROM REGROUPE WHERE idFamille = ?', [$idFamille]
        )->fetchAll(PDO::FETCH_COLUMN));
    }

    /** Remplace les fragrances regroupées par la famille. */
    public static function definirFragrances(int $idFamille, array $idsFragrances): void
    {
        self::requete('DELETE FROM REGROUPE WHERE idFamille = ?', [$idFamille]);
        foreach ($idsFragrances as $idFragrance) {
            self::requete('INSERT INTO REGROUPE (idFamille, idFragrance) VALUES (?, ?)', [$idFamille, $idFragrance]);
        }
    }

    /** Fragrances de la famille déjà portées par un de ses parfums (on ne peut pas les retirer). */
    public static function fragrancesUtilisees(int $idFamille): array
    {
        return array_map('intval', self::requete(
            'SELECT DISTINCT po.idFragrance
               FROM POSSEDE po JOIN PARFUM p ON p.idParfum = po.idParfum
              WHERE p.idFamille = ?', [$idFamille]
        )->fetchAll(PDO::FETCH_COLUMN));
    }

    /** Tableau [idFamille => [idFragrance, …]] pour tout le référentiel. */
    public static function carteFragrances(): array
    {
        $carte = [];
        foreach (self::requete('SELECT idFamille, idFragrance FROM REGROUPE') as $ligne) {
            $carte[(int) $ligne['idFamille']][] = (int) $ligne['idFragrance'];
        }
        return $carte;
    }

    /** Familles avec le nom de leurs fragrances (page « L'Univers »). */
    public static function tousAvecFragrances(): array
    {
        $familles = self::tousAvecCompte();
        $noms = [];
        $lignes = self::requete(
            'SELECT r.idFamille, f.idFragrance, f.libelle
               FROM REGROUPE r JOIN FRAGRANCE f ON f.idFragrance = r.idFragrance
              ORDER BY f.libelle'
        );
        foreach ($lignes as $ligne) {
            $noms[(int) $ligne['idFamille']][] = ['id' => (int) $ligne['idFragrance'], 'libelle' => $ligne['libelle']];
        }
        foreach ($familles as &$famille) {
            $famille['fragrances'] = $noms[(int) $famille['id']] ?? [];
        }
        return $familles;
    }

    public static function supprimer(int $id): void
    {
        self::requete('DELETE FROM REGROUPE WHERE idFamille = ?', [$id]);
        parent::supprimer($id);
    }
}
