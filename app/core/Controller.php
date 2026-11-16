<?php
/**
 * Classe mère des contrôleurs : affichage des vues, redirections, sécurité.
 */
abstract class Controller
{
    /** Dossier des morceaux communs (en-tête / pied de page) utilisés autour de la vue. */
    protected string $gabarit = 'includes';

    /**
     * Affiche une vue entourée de l'en-tête et du pied de page.
     * Les clés de $donnees deviennent des variables dans la vue.
     */
    protected function render(string $vue, array $donnees = [], int $codeHttp = 200): void
    {
        http_response_code($codeHttp);
        extract($donnees, EXTR_SKIP);
        require ROOT . '/app/views/' . $this->gabarit . '/header.php';
        require ROOT . '/app/views/' . $vue . '.php';
        require ROOT . '/app/views/' . $this->gabarit . '/footer.php';
    }

    protected function redirect(string $chemin = ''): never
    {
        header('Location: ' . url($chemin));
        exit;
    }

    /** Vérifie le jeton CSRF des formulaires POST. */
    protected function verifierCsrf(): void
    {
        $jeton = $_POST['_csrf'] ?? '';
        if (!is_string($jeton) || !hash_equals(csrf_token(), $jeton)) {
            flash('danger', 'Votre session a expiré, veuillez réessayer.');
            $this->redirect(chemin_retour());
        }
    }

    /** Réserve l'action aux clients connectés. */
    protected function exigerConnexion(): void
    {
        if (!utilisateur()) {
            flash('info', 'Connectez-vous pour continuer.');
            $this->redirect('connexion?redirect=' . rawurlencode(chemin_courant()));
        }
    }

    /** Réserve l'action aux administrateurs. */
    protected function exigerAdmin(): void
    {
        $this->exigerConnexion();
        if (!est_admin()) {
            (new ErreurController())->interdit();
            exit;
        }
    }
}
