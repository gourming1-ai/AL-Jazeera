<?php
/** Tableau de bord du back-office : accès aux listes et chiffres clés. */
class AdminDashboardController extends AdminController
{
    public function index(): void
    {
        $this->render('admin/dashboard', [
            'titre'    => 'Tableau de bord',
            'compteurs' => [
                ['Parfums',           Parfum::compter(),     'admin/parfums',     'bi-droplet-half'],
                ['Marques',           Marque::compter(),     'admin/marques',     'bi-award'],
                ['Familles',          Famille::compter(),    'admin/familles',    'bi-flower1'],
                ['Fragrances',        Fragrance::compter(),  'admin/fragrances',  'bi-wind'],
                ["Types d'eau",       Eau::compter(),        'admin/eaux',        'bi-moisture'],
                ['Contenances',       Contenance::compter(), 'admin/contenances', 'bi-rulers'],
                ['Avis',              Avis::compter(),       'admin/avis',        'bi-chat-quote'],
                ['Comptes inscrits',  User::compter(),       null,                'bi-people'],
            ],
            'derniersParfums' => Parfum::derniers(5),
            'derniersAvis'    => Avis::tous(1, 5),
        ]);
    }
}
