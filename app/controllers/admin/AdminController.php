<?php
/**
 * Classe mère du back-office (CU9) : toutes ses pages sont réservées au rôle « admin »
 * et utilisent l'en-tête / pied de page de views/admin/includes.
 */
abstract class AdminController extends Controller
{
    protected string $gabarit = 'admin/includes';

    public function __construct()
    {
        if (!utilisateur()) {
            flash('info', 'Connectez-vous avec un compte administrateur.');
            $this->redirect('connexion?redirect=' . rawurlencode(chemin_courant() ?: 'admin'));
        }
        if (!est_admin()) {
            (new ErreurController())->interdit();
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifierCsrf();
        }
    }
}
