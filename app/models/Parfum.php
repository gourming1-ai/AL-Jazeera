<?php
/**
 * Table PARFUM et ses associations : POSSEDE (fragrances) et A_POUR_CONTENANCE (prix).
 */
class Parfum extends Model
{
    /** Colonnes communes à la liste et à la fiche : marque, famille, eau, prix « à partir de », note. */
    private const SELECTION = '
        SELECT p.idParfum, p.nom, p.photo, p.idMarque, p.idFamille, p.idEau,
               m.nom AS marque, f.libelle AS famille, e.libelle AS eau,
               (SELECT MIN(c.prix) FROM A_POUR_CONTENANCE c WHERE c.idParfum = p.idParfum) AS prixMin,
               (SELECT AVG(a.note) FROM DONNE_UN_AVIS a WHERE a.idParfum = p.idParfum) AS noteMoyenne,
               (SELECT COUNT(*)    FROM DONNE_UN_AVIS a WHERE a.idParfum = p.idParfum) AS nbAvis
          FROM PARFUM p
          JOIN MARQUE  m ON m.idMarque  = p.idMarque
          JOIN FAMILLE f ON f.idFamille = p.idFamille
          JOIN EAU     e ON e.idEau     = p.idEau';

    private const TRIS = [
        'nom_asc'   => 'p.nom ASC',
        'nom_desc'  => 'p.nom DESC',
        'prix_asc'  => 'prixMin IS NULL, prixMin ASC, p.nom ASC',
        'prix_desc' => 'prixMin DESC, p.nom ASC',
    ];

    /**
     * Recherche du catalogue : filtres, tri et pagination.
     * $filtres : q (texte), marque, famille, eau, fragrance, contenance (tableaux d'identifiants).
     * Retourne ['parfums' => […], 'total' => n].
     */
    public static function rechercher(array $filtres, string $tri = 'nom_asc', int $page = 1, int $parPage = 12): array
    {
        $conditions = [];
        $params     = [];

        if (($filtres['q'] ?? '') !== '') {
            $motif = '%' . addcslashes($filtres['q'], '%_\\') . '%';
            $conditions[] = '(p.nom LIKE ? OR m.nom LIKE ?)';
            array_push($params, $motif, $motif);
        }
        foreach (['marque' => 'p.idMarque', 'famille' => 'p.idFamille', 'eau' => 'p.idEau'] as $cle => $colonne) {
            if (!empty($filtres[$cle])) {
                $conditions[] = $colonne . ' IN (' . self::jokers($filtres[$cle]) . ')';
                array_push($params, ...$filtres[$cle]);
            }
        }
        if (!empty($filtres['fragrance'])) {
            $conditions[] = 'EXISTS (SELECT 1 FROM POSSEDE po WHERE po.idParfum = p.idParfum
                                      AND po.idFragrance IN (' . self::jokers($filtres['fragrance']) . '))';
            array_push($params, ...$filtres['fragrance']);
        }
        if (!empty($filtres['contenance'])) {
            $conditions[] = 'EXISTS (SELECT 1 FROM A_POUR_CONTENANCE ac WHERE ac.idParfum = p.idParfum
                                      AND ac.idContenance IN (' . self::jokers($filtres['contenance']) . '))';
            array_push($params, ...$filtres['contenance']);
        }

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

        $total = (int) self::requete(
            'SELECT COUNT(*) FROM PARFUM p JOIN MARQUE m ON m.idMarque = p.idMarque' . $where,
            $params
        )->fetchColumn();

        $ordre = self::TRIS[$tri] ?? self::TRIS['nom_asc'];
        $debut = max(0, ($page - 1) * $parPage);
        $parfums = self::requete(
            self::SELECTION . $where . ' ORDER BY ' . $ordre . ' LIMIT ' . (int) $parPage . ' OFFSET ' . (int) $debut,
            $params
        )->fetchAll();

        return ['parfums' => $parfums, 'total' => $total];
    }

    public static function trouver(int $id): ?array
    {
        $parfum = self::requete(self::SELECTION . ' WHERE p.idParfum = ?', [$id])->fetch();
        return $parfum ?: null;
    }

    /** Parfums mis en avant sur l'accueil : les mieux notés d'abord. */
    public static function vedettes(int $nombre = 4): array
    {
        return self::requete(
            self::SELECTION . ' ORDER BY noteMoyenne DESC, nbAvis DESC, p.nom ASC LIMIT ' . (int) $nombre
        )->fetchAll();
    }

    /** Derniers parfums ajoutés au catalogue. */
    public static function derniers(int $nombre = 4): array
    {
        return self::requete(self::SELECTION . ' ORDER BY p.idParfum DESC LIMIT ' . (int) $nombre)->fetchAll();
    }

    /** Autres parfums de la même famille (suggestions sur la fiche). */
    public static function similaires(int $idParfum, int $idFamille, int $nombre = 4): array
    {
        return self::requete(
            self::SELECTION . ' WHERE p.idFamille = ? AND p.idParfum <> ?
                                ORDER BY noteMoyenne DESC, p.nom ASC LIMIT ' . (int) $nombre,
            [$idFamille, $idParfum]
        )->fetchAll();
    }

    /** Fragrances d'un parfum : lignes [idFragrance, libelle]. */
    public static function fragrances(int $idParfum): array
    {
        return self::requete(
            'SELECT f.idFragrance, f.libelle
               FROM POSSEDE po JOIN FRAGRANCE f ON f.idFragrance = po.idFragrance
              WHERE po.idParfum = ? ORDER BY f.libelle',
            [$idParfum]
        )->fetchAll();
    }

    /** Contenances et prix d'un parfum, de la plus petite à la plus grande. */
    public static function contenances(int $idParfum): array
    {
        return self::requete(
            'SELECT c.idContenance, c.libelle, ac.prix
               FROM A_POUR_CONTENANCE ac JOIN CONTENANCE c ON c.idContenance = ac.idContenance
              WHERE ac.idParfum = ? ORDER BY CAST(c.libelle AS UNSIGNED), c.libelle',
            [$idParfum]
        )->fetchAll();
    }

    /**
     * Crée un parfum avec ses fragrances et ses prix par contenance (une seule transaction).
     * $donnees : nom, photo, idMarque, idFamille, idEau — $prix : [idContenance => prix].
     */
    public static function creer(array $donnees, array $idsFragrances, array $prix): int
    {
        $db = self::db();
        $db->beginTransaction();
        try {
            self::requete(
                'INSERT INTO PARFUM (nom, photo, idMarque, idFamille, idEau) VALUES (?, ?, ?, ?, ?)',
                [$donnees['nom'], $donnees['photo'], $donnees['idMarque'], $donnees['idFamille'], $donnees['idEau']]
            );
            $id = (int) $db->lastInsertId();
            self::enregistrerLiens($id, $idsFragrances, $prix);
            $db->commit();
            return $id;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function modifier(int $id, array $donnees, array $idsFragrances, array $prix): void
    {
        $db = self::db();
        $db->beginTransaction();
        try {
            self::requete(
                'UPDATE PARFUM SET nom = ?, photo = ?, idMarque = ?, idFamille = ?, idEau = ? WHERE idParfum = ?',
                [$donnees['nom'], $donnees['photo'], $donnees['idMarque'], $donnees['idFamille'], $donnees['idEau'], $id]
            );
            self::requete('DELETE FROM POSSEDE WHERE idParfum = ?', [$id]);
            self::requete('DELETE FROM A_POUR_CONTENANCE WHERE idParfum = ?', [$id]);
            self::enregistrerLiens($id, $idsFragrances, $prix);
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private static function enregistrerLiens(int $id, array $idsFragrances, array $prix): void
    {
        foreach ($idsFragrances as $idFragrance) {
            self::requete('INSERT INTO POSSEDE (idParfum, idFragrance) VALUES (?, ?)', [$id, $idFragrance]);
        }
        foreach ($prix as $idContenance => $montant) {
            self::requete(
                'INSERT INTO A_POUR_CONTENANCE (idParfum, idContenance, prix) VALUES (?, ?, ?)',
                [$id, $idContenance, $montant]
            );
        }
    }

    /** Supprime un parfum et tout ce qui en dépend (fragrances, prix, avis). */
    public static function supprimer(int $id): void
    {
        $db = self::db();
        $db->beginTransaction();
        try {
            foreach (['POSSEDE', 'A_POUR_CONTENANCE', 'DONNE_UN_AVIS', 'PARFUM'] as $table) {
                self::requete('DELETE FROM ' . $table . ' WHERE idParfum = ?', [$id]);
            }
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function compter(): int
    {
        return (int) self::requete('SELECT COUNT(*) FROM PARFUM')->fetchColumn();
    }
}
