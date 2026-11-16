<?php
/** Pages d'erreur : 404 (introuvable) et 403 (accès interdit). */
class ErreurController extends Controller
{
    public function introuvable(): void
    {
        $this->render('erreurs/404', ['titre' => 'Page introuvable'], 404);
    }

    public function interdit(): void
    {
        $this->render('erreurs/403', ['titre' => 'Accès réservé'], 403);
    }
}
