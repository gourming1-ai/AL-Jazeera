<?php
/** Catalogue (CU1 à CU3) et fiche détaillée d'un parfum (CU4). */
class ParfumController extends Controller
{
    private const PAR_PAGE = 12;

    private const TRIS = [
        'nom_asc'   => 'Nom : A à Z',
        'nom_desc'  => 'Nom : Z à A',
        'prix_asc'  => 'Prix croissant',
        'prix_desc' => 'Prix décroissant',
    ];

    /** Liste des parfums avec filtres, tri, recherche et pagination. */
    public function index(): void
    {
        $q = $_GET['q'] ?? '';
        $filtres = [
            'q'          => is_string($q) ? mb_substr(trim($q), 0, 100) : '',
            'marque'     => ids($_GET['marque'] ?? null),
            'famille'    => ids($_GET['famille'] ?? null),
            'eau'        => ids($_GET['eau'] ?? null),
            'fragrance'  => ids($_GET['fragrance'] ?? null),
            'contenance' => ids($_GET['contenance'] ?? null),
        ];
        $tri  = $_GET['tri'] ?? '';
        $tri  = is_string($tri) && isset(self::TRIS[$tri]) ? $tri : 'nom_asc';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $resultat = Parfum::rechercher($filtres, $tri, $page, self::PAR_PAGE);
        $nbPages  = max(1, (int) ceil($resultat['total'] / self::PAR_PAGE));
        if ($page > $nbPages) { // page trop grande : on revient à la dernière
            $page     = $nbPages;
            $resultat = Parfum::rechercher($filtres, $tri, $page, self::PAR_PAGE);
        }

        $this->render('parfums/index', [
            'titre'    => 'Nos parfums – Maison Al Jazeera',
            'parfums'  => $resultat['parfums'],
            'total'    => $resultat['total'],
            'page'     => $page,
            'nbPages'  => $nbPages,
            'filtres'  => $filtres,
            'tri'      => $tri,
            'tris'     => self::TRIS,
            // paramètres d'URL à conserver dans les liens (pagination, tri, retrait d'un filtre)
            'params'   => $filtres + ['tri' => $tri === 'nom_asc' ? '' : $tri],
            'groupes'  => [
                'marque'     => ['titre' => 'Marque',            'options' => Marque::tousAvecCompte()],
                'famille'    => ['titre' => 'Famille olfactive', 'options' => Famille::tousAvecCompte()],
                'eau'        => ['titre' => "Type d'eau",        'options' => Eau::tousAvecCompte()],
                'fragrance'  => ['titre' => 'Fragrance',         'options' => Fragrance::tousAvecCompte()],
                'contenance' => ['titre' => 'Contenance',        'options' => Contenance::tousAvecCompte()],
            ],
        ]);
    }

    /** Fiche d'un parfum : image, contenances et prix, fragrances, note moyenne et avis. */
    public function show(int $id, $slug = null): void
    {
        $parfum = Parfum::trouver($id);
        if (!$parfum) {
            (new ErreurController())->introuvable();
            return;
        }

        $user = utilisateur();
        $this->render('parfums/show', [
            'titre'       => $parfum['nom'] . ' – ' . $parfum['marque'] . ' | Maison Al Jazeera',
            'parfum'      => $parfum,
            'fragrances'  => Parfum::fragrances($id),
            'contenances' => Parfum::contenances($id),
            'avis'        => Avis::pourParfum($id),
            'stats'       => Avis::statistiques($id),
            'monAvis'     => $user ? Avis::trouver($id, (int) $user['idUser']) : null,
            'similaires'  => Parfum::similaires($id, (int) $parfum['idFamille'], 4),
            'saisie'      => $_SESSION['saisie_avis'][$id] ?? [],
        ]);
        unset($_SESSION['saisie_avis'][$id]);
    }
}
