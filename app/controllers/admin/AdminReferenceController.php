<?php
/**
 * CRUD des données de référence (CU11) : marques, familles, fragrances, types d'eau, contenances.
 * Un seul contrôleur pour les cinq listes, car elles ont la même forme (identifiant + libellé).
 * Les familles et les fragrances gèrent en plus leur association REGROUPE.
 */
class AdminReferenceController extends AdminController
{
    private const RESSOURCES = [
        'marques'     => ['modele' => 'Marque',     'titre' => 'Marques',             'nom' => 'marque',            'champ' => 'Nom',     'exemple' => 'Guerlain'],
        'familles'    => ['modele' => 'Famille',    'titre' => 'Familles olfactives', 'nom' => 'famille olfactive', 'champ' => 'Libellé', 'exemple' => 'Boisée'],
        'fragrances'  => ['modele' => 'Fragrance',  'titre' => 'Fragrances',          'nom' => 'fragrance',         'champ' => 'Libellé', 'exemple' => 'Vanille'],
        'eaux'        => ['modele' => 'Eau',        'titre' => "Types d'eau",         'nom' => "type d'eau",        'champ' => 'Libellé', 'exemple' => 'Eau de parfum'],
        'contenances' => ['modele' => 'Contenance', 'titre' => 'Contenances',         'nom' => 'contenance',        'champ' => 'Libellé', 'exemple' => '50 ml'],
    ];

    public function index(string $ressource): void
    {
        $config = self::RESSOURCES[$ressource];
        $this->render('admin/references/index', [
            'titre'     => $config['titre'],
            'ressource' => $ressource,
            'config'    => $config,
            'lignes'    => $config['modele']::tousAvecCompte(),
            'carte'     => $ressource === 'familles' ? Famille::carteFragrances() : [],
        ]);
    }

    public function creer(string $ressource): void
    {
        $this->afficherFormulaire($ressource, null, ['libelle' => '', 'liens' => []]);
    }

    public function enregistrer(string $ressource): void
    {
        $modele = self::RESSOURCES[$ressource]['modele'];
        [$saisie, $erreurs] = $this->valider($ressource, null);
        if ($erreurs) {
            $this->afficherFormulaire($ressource, null, $saisie, $erreurs, 422);
            return;
        }
        $id = $modele::creer($saisie['libelle']);
        $this->enregistrerLiens($ressource, $id, $saisie['liens']);
        flash('success', 'Enregistrement effectué : ' . self::RESSOURCES[$ressource]['nom'] . ' « ' . $saisie['libelle'] . ' ».');
        $this->redirect('admin/' . $ressource);
    }

    public function modifier(string $ressource, int $id): void
    {
        $ligne = $this->trouverOu404($ressource, $id);
        $this->afficherFormulaire($ressource, $ligne, [
            'libelle' => $ligne['libelle'],
            'liens'   => $this->liensActuels($ressource, $id),
        ]);
    }

    public function mettreAJour(string $ressource, int $id): void
    {
        $ligne  = $this->trouverOu404($ressource, $id);
        $modele = self::RESSOURCES[$ressource]['modele'];
        [$saisie, $erreurs] = $this->valider($ressource, $id);
        if ($erreurs) {
            $this->afficherFormulaire($ressource, $ligne, $saisie, $erreurs, 422);
            return;
        }
        $modele::modifier($id, $saisie['libelle']);
        $this->enregistrerLiens($ressource, $id, $saisie['liens']);
        flash('success', 'Modification effectuée : ' . self::RESSOURCES[$ressource]['nom'] . ' « ' . $saisie['libelle'] . ' ».');
        $this->redirect('admin/' . $ressource);
    }

    public function supprimer(string $ressource, int $id): void
    {
        $ligne  = $this->trouverOu404($ressource, $id);
        $modele = self::RESSOURCES[$ressource]['modele'];

        // Une donnée encore utilisée par des parfums ne peut pas être supprimée (clés étrangères).
        $nb = $modele::nbParfums($id);
        if ($nb > 0) {
            flash('danger', 'Suppression impossible : « ' . $ligne['libelle'] . ' » est encore utilisé par '
                . $nb . ' parfum' . ($nb > 1 ? 's' : '') . '. Modifiez ou supprimez ces parfums d\'abord.');
        } else {
            $modele::supprimer($id);
            flash('success', 'Suppression effectuée : ' . self::RESSOURCES[$ressource]['nom'] . ' « ' . $ligne['libelle'] . ' ».');
        }
        $this->redirect('admin/' . $ressource);
    }

    /* ---------- Outils internes ---------- */

    private function trouverOu404(string $ressource, int $id): array
    {
        $ligne = self::RESSOURCES[$ressource]['modele']::trouver($id);
        if (!$ligne) {
            (new ErreurController())->introuvable();
            exit;
        }
        return $ligne;
    }

    /** Association REGROUPE vue depuis la famille (ses fragrances) ou la fragrance (ses familles). */
    private function liensActuels(string $ressource, int $id): array
    {
        return match ($ressource) {
            'familles'   => Famille::fragrancesIds($id),
            'fragrances' => Fragrance::famillesIds($id),
            default      => [],
        };
    }

    private function enregistrerLiens(string $ressource, int $id, array $liens): void
    {
        if ($ressource === 'familles') {
            Famille::definirFragrances($id, $liens);
        } elseif ($ressource === 'fragrances') {
            Fragrance::definirFamilles($id, $liens);
        }
    }

    private function afficherFormulaire(string $ressource, ?array $ligne, array $saisie, array $erreurs = [], int $code = 200): void
    {
        $config = self::RESSOURCES[$ressource];
        // Cases à cocher de l'association REGROUPE
        $options = match ($ressource) {
            'familles'   => ['titre' => 'Fragrances regroupées par cette famille', 'liste' => Fragrance::tous()],
            'fragrances' => ['titre' => 'Familles qui regroupent cette fragrance', 'liste' => Famille::tous()],
            default      => null,
        };
        $this->render('admin/references/formulaire', [
            'titre'     => ($ligne ? 'Modifier' : 'Ajouter') . ' : ' . $config['nom'],
            'ressource' => $ressource,
            'config'    => $config,
            'ligne'     => $ligne,
            'saisie'    => $saisie,
            'erreurs'   => $erreurs,
            'options'   => $options,
        ], $code);
    }

    private function valider(string $ressource, ?int $id): array
    {
        $modele = self::RESSOURCES[$ressource]['modele'];
        $saisie = [
            'libelle' => trim((string) ($_POST['libelle'] ?? '')),
            'liens'   => [],
        ];
        $erreurs = [];

        if ($saisie['libelle'] === '' || mb_strlen($saisie['libelle']) > 100) {
            $erreurs['libelle'] = 'Ce champ est obligatoire (100 caractères maximum).';
        } elseif ($modele::existe($saisie['libelle'], $id)) {
            $erreurs['libelle'] = '« ' . $saisie['libelle'] . ' » existe déjà.';
        }

        // REGROUPE : on ne retire pas un lien dont dépend un parfum existant.
        if ($ressource === 'familles') {
            $saisie['liens'] = Fragrance::filtrerIds(ids($_POST['liens'] ?? null));
            if ($id && array_diff(Famille::fragrancesUtilisees($id), $saisie['liens'])) {
                $erreurs['liens'] = 'Des parfums de cette famille portent encore une fragrance que vous avez décochée.';
            }
        } elseif ($ressource === 'fragrances') {
            $saisie['liens'] = Famille::filtrerIds(ids($_POST['liens'] ?? null));
            if ($id && array_diff(Fragrance::famillesUtilisees($id), $saisie['liens'])) {
                $erreurs['liens'] = 'Des parfums portent cette fragrance dans une famille que vous avez décochée.';
            }
        }

        return [$saisie, $erreurs];
    }
}
