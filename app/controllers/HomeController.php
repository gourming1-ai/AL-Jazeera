<?php
/** Page d'accueil : hero, parfums mis en avant, bandeau de présentation. */
class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home/index', [
            'titre'      => "Maison Al Jazeera – L'art du parfum, une émotion intemporelle",
            'accueil'    => true,
            'vedettes'   => Parfum::vedettes(4),
            'nouveautes' => Parfum::derniers(4),
            'familles'   => Famille::tousAvecCompte(),
        ]);
    }
}
