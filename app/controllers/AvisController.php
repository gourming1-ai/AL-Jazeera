<?php
/** Avis des clients (CU7) : une note de 1 à 5 et un commentaire, un seul avis par parfum. */
class AvisController extends Controller
{
    public function enregistrer(int $idParfum): void
    {
        $parfum = Parfum::trouver($idParfum);
        if (!$parfum) {
            (new ErreurController())->introuvable();
            return;
        }
        $fiche = 'parfums/' . $idParfum . '-' . slug($parfum['nom']);

        if (!utilisateur()) {
            flash('info', 'Connectez-vous pour donner votre avis.');
            $this->redirect('connexion?redirect=' . rawurlencode($fiche));
        }
        $this->verifierCsrf();

        $idUser      = (int) utilisateur()['idUser'];
        $note        = (int) ($_POST['note'] ?? 0);
        $commentaire = trim((string) ($_POST['commentaire'] ?? ''));

        $erreur = null;
        if ($note < 1 || $note > 5) {
            $erreur = 'Choisissez une note entre 1 et 5 étoiles.';
        } elseif (mb_strlen($commentaire) > 1000) {
            $erreur = 'Votre commentaire ne doit pas dépasser 1000 caractères.';
        } elseif (Avis::trouver($idParfum, $idUser)) {
            $erreur = 'Vous avez déjà donné votre avis sur ce parfum.';
        }

        if ($erreur) {
            $_SESSION['saisie_avis'][$idParfum] = ['note' => $note, 'commentaire' => $commentaire];
            flash('danger', $erreur);
        } else {
            Avis::creer($idParfum, $idUser, $note, $commentaire !== '' ? $commentaire : null);
            flash('success', 'Merci ! Votre avis a bien été publié.');
        }
        $this->redirect($fiche . '#avis');
    }

    /** Le client retire son propre avis. */
    public function supprimer(int $idParfum): void
    {
        $this->exigerConnexion();
        $this->verifierCsrf();
        $parfum = Parfum::trouver($idParfum);
        if (!$parfum) {
            (new ErreurController())->introuvable();
            return;
        }
        Avis::supprimer($idParfum, (int) utilisateur()['idUser']);
        flash('success', 'Votre avis a été supprimé.');
        $this->redirect('parfums/' . $idParfum . '-' . slug($parfum['nom']) . '#avis');
    }
}
