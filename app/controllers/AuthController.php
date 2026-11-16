<?php
/** Comptes clients : inscription (CU5), connexion (CU6) et déconnexion (CU8). */
class AuthController extends Controller
{
    public function formulaireInscription(): void
    {
        if (utilisateur()) {
            $this->redirect('');
        }
        $this->render('auth/inscription', [
            'titre'    => 'Créer un compte – Maison Al Jazeera',
            'saisie'   => [],
            'erreurs'  => [],
            'redirect' => chemin_sur($_GET['redirect'] ?? ''),
        ]);
    }

    public function inscription(): void
    {
        $this->verifierCsrf();

        $login        = trim((string) ($_POST['login'] ?? ''));
        $pseudo       = trim((string) ($_POST['pseudo'] ?? ''));
        $motDePasse   = (string) ($_POST['pwd'] ?? '');
        $confirmation = (string) ($_POST['pwd_confirmation'] ?? '');
        $redirect     = chemin_sur($_POST['redirect'] ?? '');

        $erreurs = [];
        if (!preg_match('/^[A-Za-z0-9._@-]{3,100}$/', $login)) {
            $erreurs['login'] = 'Identifiant de 3 à 100 caractères : lettres, chiffres, point, tiret, @.';
        } elseif (User::trouverParLogin($login)) {
            $erreurs['login'] = 'Cet identifiant est déjà utilisé.';
        }
        if (mb_strlen($pseudo) < 2 || mb_strlen($pseudo) > 100) {
            $erreurs['pseudo'] = 'Le pseudo doit contenir entre 2 et 100 caractères.';
        }
        if (strlen($motDePasse) < 8) {
            $erreurs['pwd'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($motDePasse !== $confirmation) {
            $erreurs['pwd_confirmation'] = 'Les deux mots de passe ne sont pas identiques.';
        }

        if ($erreurs) {
            $this->render('auth/inscription', [
                'titre'    => 'Créer un compte – Maison Al Jazeera',
                'saisie'   => ['login' => $login, 'pseudo' => $pseudo],
                'erreurs'  => $erreurs,
                'redirect' => $redirect,
            ], 422);
            return;
        }

        $id = User::creer($login, $motDePasse, $pseudo);
        $this->ouvrirSession(['idUser' => $id, 'login' => $login, 'pseudo' => $pseudo, 'role' => 'client']);
        flash('success', 'Bienvenue ' . $pseudo . ' ! Votre compte est créé.');
        $this->redirect($redirect);
    }

    public function formulaireConnexion(): void
    {
        if (utilisateur()) {
            $this->redirect('');
        }
        $this->render('auth/connexion', [
            'titre'    => 'Connexion – Maison Al Jazeera',
            'login'    => '',
            'erreur'   => null,
            'redirect' => chemin_sur($_GET['redirect'] ?? ''),
        ]);
    }

    public function connexion(): void
    {
        $this->verifierCsrf();

        $login      = trim((string) ($_POST['login'] ?? ''));
        $motDePasse = (string) ($_POST['pwd'] ?? '');
        $redirect   = chemin_sur($_POST['redirect'] ?? '');

        $user = $login !== '' ? User::trouverParLogin($login) : null;
        if (!$user || !password_verify($motDePasse, $user['pwd'])) {
            $this->render('auth/connexion', [
                'titre'    => 'Connexion – Maison Al Jazeera',
                'login'    => $login,
                'erreur'   => 'Identifiant ou mot de passe incorrect.',
                'redirect' => $redirect,
            ], 422);
            return;
        }

        $this->ouvrirSession($user);
        flash('success', 'Heureux de vous revoir, ' . $user['pseudo'] . '.');
        // l'administrateur arrive sur le back-office, le client revient d'où il vient
        $this->redirect($redirect !== '' ? $redirect : ($user['role'] === 'admin' ? 'admin' : ''));
    }

    public function deconnexion(): void
    {
        $this->verifierCsrf();
        unset($_SESSION['user']);
        session_regenerate_id(true);
        flash('success', 'Vous êtes déconnecté. À bientôt !');
        $this->redirect('');
    }

    private function ouvrirSession(array $user): void
    {
        session_regenerate_id(true); // nouvel identifiant de session après authentification
        $_SESSION['user'] = [
            'idUser' => (int) $user['idUser'],
            'login'  => $user['login'],
            'pseudo' => $user['pseudo'],
            'role'   => $user['role'],
        ];
    }
}
