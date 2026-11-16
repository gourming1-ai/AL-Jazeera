<?php
/** Pages de présentation : La Maison, L'Univers, Contact. */
class PageController extends Controller
{
    public function maison(): void
    {
        $this->render('pages/maison', ['titre' => 'La Maison – Maison Al Jazeera']);
    }

    /** Guide des familles olfactives, des fragrances et des types d'eau du catalogue. */
    public function univers(): void
    {
        $this->render('pages/univers', [
            'titre'    => "L'Univers – Maison Al Jazeera",
            'familles' => Famille::tousAvecFragrances(),
            'eaux'     => Eau::tousAvecCompte(),
        ]);
    }

    public function contact(): void
    {
        $this->render('pages/contact', [
            'titre'   => 'Contact – Maison Al Jazeera',
            'saisie'  => [],
            'erreurs' => [],
        ]);
    }

    /** Vérifie le formulaire de contact. Projet fictif : le message n'est ni stocké ni envoyé. */
    public function envoyerContact(): void
    {
        $this->verifierCsrf();

        $saisie = [
            'nom'     => trim((string) ($_POST['nom'] ?? '')),
            'email'   => trim((string) ($_POST['email'] ?? '')),
            'message' => trim((string) ($_POST['message'] ?? '')),
        ];
        $erreurs = [];
        if (mb_strlen($saisie['nom']) < 2) {
            $erreurs['nom'] = 'Indiquez votre nom.';
        }
        if (!filter_var($saisie['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'Indiquez une adresse e-mail valide.';
        }
        if (mb_strlen($saisie['message']) < 10) {
            $erreurs['message'] = 'Votre message doit contenir au moins 10 caractères.';
        }

        if ($erreurs) {
            $this->render('pages/contact', [
                'titre'   => 'Contact – Maison Al Jazeera',
                'saisie'  => $saisie,
                'erreurs' => $erreurs,
            ], 422);
            return;
        }

        flash('success', 'Merci ' . $saisie['nom'] . ', votre message a bien été pris en compte.');
        $this->redirect('contact');
    }
}
