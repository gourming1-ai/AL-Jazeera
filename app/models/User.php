<?php
/** Table USER (idUser, login, pwd, pseudo, role). */
class User extends Model
{
    public static function trouverParLogin(string $login): ?array
    {
        $user = self::requete('SELECT * FROM `USER` WHERE login = ?', [$login])->fetch();
        return $user ?: null;
    }

    /** Crée un compte client ; le mot de passe est haché avec password_hash(). */
    public static function creer(string $login, string $motDePasse, string $pseudo, string $role = 'client'): int
    {
        self::requete(
            'INSERT INTO `USER` (login, pwd, pseudo, role) VALUES (?, ?, ?, ?)',
            [$login, password_hash($motDePasse, PASSWORD_DEFAULT), $pseudo, $role]
        );
        return (int) self::db()->lastInsertId();
    }

    public static function compter(): int
    {
        return (int) self::requete('SELECT COUNT(*) FROM `USER`')->fetchColumn();
    }
}
