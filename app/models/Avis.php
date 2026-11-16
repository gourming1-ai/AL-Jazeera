<?php
/** Table DONNE_UN_AVIS (idParfum, idUser, note, commentaire, dateAvis). */
class Avis extends Model
{
    /** Avis d'un parfum, du plus récent au plus ancien, avec le pseudo de l'auteur. */
    public static function pourParfum(int $idParfum): array
    {
        return self::requete(
            'SELECT a.*, u.pseudo
               FROM DONNE_UN_AVIS a JOIN `USER` u ON u.idUser = a.idUser
              WHERE a.idParfum = ?
              ORDER BY a.dateAvis DESC',
            [$idParfum]
        )->fetchAll();
    }

    /** Note moyenne, nombre d'avis et répartition par note d'un parfum. */
    public static function statistiques(int $idParfum): array
    {
        $stats = ['moyenne' => 0.0, 'total' => 0, 'repartition' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]];
        $lignes = self::requete(
            'SELECT note, COUNT(*) AS nb FROM DONNE_UN_AVIS WHERE idParfum = ? GROUP BY note',
            [$idParfum]
        );
        $somme = 0;
        foreach ($lignes as $ligne) {
            $stats['repartition'][(int) $ligne['note']] = (int) $ligne['nb'];
            $stats['total'] += (int) $ligne['nb'];
            $somme += (int) $ligne['note'] * (int) $ligne['nb'];
        }
        if ($stats['total'] > 0) {
            $stats['moyenne'] = $somme / $stats['total'];
        }
        return $stats;
    }

    /** L'avis déjà déposé par ce client sur ce parfum, s'il existe (un seul autorisé). */
    public static function trouver(int $idParfum, int $idUser): ?array
    {
        $avis = self::requete(
            'SELECT * FROM DONNE_UN_AVIS WHERE idParfum = ? AND idUser = ?',
            [$idParfum, $idUser]
        )->fetch();
        return $avis ?: null;
    }

    public static function creer(int $idParfum, int $idUser, int $note, ?string $commentaire): void
    {
        self::requete(
            'INSERT INTO DONNE_UN_AVIS (idParfum, idUser, note, commentaire) VALUES (?, ?, ?, ?)',
            [$idParfum, $idUser, $note, $commentaire]
        );
    }

    public static function supprimer(int $idParfum, int $idUser): void
    {
        self::requete('DELETE FROM DONNE_UN_AVIS WHERE idParfum = ? AND idUser = ?', [$idParfum, $idUser]);
    }

    public static function compter(): int
    {
        return (int) self::requete('SELECT COUNT(*) FROM DONNE_UN_AVIS')->fetchColumn();
    }

    /** Tous les avis pour la modération (back-office), paginés. */
    public static function tous(int $page = 1, int $parPage = 15): array
    {
        $debut = max(0, ($page - 1) * $parPage);
        return self::requete(
            'SELECT a.*, u.pseudo, u.login, p.nom AS parfum, m.nom AS marque
               FROM DONNE_UN_AVIS a
               JOIN `USER` u ON u.idUser = a.idUser
               JOIN PARFUM p ON p.idParfum = a.idParfum
               JOIN MARQUE m ON m.idMarque = p.idMarque
              ORDER BY a.dateAvis DESC
              LIMIT ' . (int) $parPage . ' OFFSET ' . (int) $debut
        )->fetchAll();
    }
}
