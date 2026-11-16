<?php
/** CRUD des parfums (CU10) : création, modification, suppression, contenances, prix et fragrances. */
class AdminParfumController extends AdminController
{
    private const PAR_PAGE     = 15;
    private const TAILLE_MAX   = 3 * 1024 * 1024; // 3 Mo
    private const TYPES_PHOTO  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public function index(): void
    {
        $q    = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 100) : '';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $resultat = Parfum::rechercher(['q' => $q], 'nom_asc', $page, self::PAR_PAGE);
        $nbPages  = max(1, (int) ceil($resultat['total'] / self::PAR_PAGE));
        if ($page > $nbPages) {
            $page     = $nbPages;
            $resultat = Parfum::rechercher(['q' => $q], 'nom_asc', $page, self::PAR_PAGE);
        }

        $this->render('admin/parfums/index', [
            'titre'   => 'Parfums',
            'parfums' => $resultat['parfums'],
            'total'   => $resultat['total'],
            'page'    => $page,
            'nbPages' => $nbPages,
            'q'       => $q,
        ]);
    }

    public function creer(): void
    {
        $this->afficherFormulaire(null, [
            'nom' => '', 'idMarque' => 0, 'idFamille' => 0, 'idEau' => 0, 'photo' => null,
            'fragrances' => [], 'prix' => [],
        ]);
    }

    public function enregistrer(): void
    {
        [$saisie, $erreurs] = $this->valider(null);
        if ($erreurs) {
            $this->afficherFormulaire(null, $saisie, $erreurs, 422);
            return;
        }
        $saisie['photo'] = $this->enregistrerPhoto(null);
        Parfum::creer($saisie, $saisie['fragrances'], $saisie['prix']);
        flash('success', 'Le parfum « ' . $saisie['nom'] . ' » a été ajouté au catalogue.');
        $this->redirect('admin/parfums');
    }

    public function modifier(int $id): void
    {
        $parfum = $this->trouverOu404($id);
        $prix = [];
        foreach (Parfum::contenances($id) as $c) {
            $prix[(int) $c['idContenance']] = $c['prix'];
        }
        $this->afficherFormulaire($parfum, [
            'nom'        => $parfum['nom'],
            'idMarque'   => (int) $parfum['idMarque'],
            'idFamille'  => (int) $parfum['idFamille'],
            'idEau'      => (int) $parfum['idEau'],
            'photo'      => $parfum['photo'],
            'fragrances' => array_map(fn ($f) => (int) $f['idFragrance'], Parfum::fragrances($id)),
            'prix'       => $prix,
        ]);
    }

    public function mettreAJour(int $id): void
    {
        $parfum = $this->trouverOu404($id);
        [$saisie, $erreurs] = $this->valider($parfum);
        if ($erreurs) {
            $saisie['photo'] = $parfum['photo'];
            $this->afficherFormulaire($parfum, $saisie, $erreurs, 422);
            return;
        }
        $saisie['photo'] = $this->enregistrerPhoto($parfum['photo']);
        Parfum::modifier($id, $saisie, $saisie['fragrances'], $saisie['prix']);
        flash('success', 'Le parfum « ' . $saisie['nom'] . ' » a été mis à jour.');
        $this->redirect('admin/parfums');
    }

    public function supprimer(int $id): void
    {
        $parfum = $this->trouverOu404($id);
        Parfum::supprimer($id);
        $this->effacerFichier($parfum['photo']);
        flash('success', 'Le parfum « ' . $parfum['nom'] . ' » a été supprimé.');
        $this->redirect('admin/parfums');
    }

    /* ---------- Outils internes ---------- */

    private function trouverOu404(int $id): array
    {
        $parfum = Parfum::trouver($id);
        if (!$parfum) {
            (new ErreurController())->introuvable();
            exit;
        }
        return $parfum;
    }

    private function afficherFormulaire(?array $parfum, array $saisie, array $erreurs = [], int $code = 200): void
    {
        $this->render('admin/parfums/formulaire', [
            'titre'       => $parfum ? 'Modifier « ' . $parfum['nom'] . ' »' : 'Nouveau parfum',
            'parfum'      => $parfum,
            'saisie'      => $saisie,
            'erreurs'     => $erreurs,
            'marques'     => Marque::tous(),
            'familles'    => Famille::tous(),
            'eaux'        => Eau::tous(),
            'fragrances'  => Fragrance::tous(),
            'contenances' => Contenance::tous(),
            'carte'       => Famille::carteFragrances(), // [idFamille => fragrances autorisées]
        ], $code);
    }

    /**
     * Contrôle le formulaire et applique les règles de gestion du cahier des charges :
     * au moins une fragrance, fragrances appartenant à la famille, prix >= 0 par contenance.
     * Retourne [saisie nettoyée, erreurs].
     */
    private function valider(?array $parfum): array
    {
        $saisie = [
            'nom'        => trim((string) ($_POST['nom'] ?? '')),
            'idMarque'   => (int) ($_POST['idMarque'] ?? 0),
            'idFamille'  => (int) ($_POST['idFamille'] ?? 0),
            'idEau'      => (int) ($_POST['idEau'] ?? 0),
            'photo'      => $parfum['photo'] ?? null,
            'fragrances' => Fragrance::filtrerIds(ids($_POST['fragrances'] ?? null)),
            'prix'       => [],
        ];
        $erreurs = [];

        if ($saisie['nom'] === '' || mb_strlen($saisie['nom']) > 150) {
            $erreurs['nom'] = 'Le nom est obligatoire (150 caractères maximum).';
        }
        if (!Marque::trouver($saisie['idMarque'])) {
            $erreurs['idMarque'] = 'Choisissez une marque.';
        }
        if (!Famille::trouver($saisie['idFamille'])) {
            $erreurs['idFamille'] = 'Choisissez une famille olfactive.';
        }
        if (!Eau::trouver($saisie['idEau'])) {
            $erreurs['idEau'] = "Choisissez un type d'eau.";
        }

        // Règle : un parfum possède au moins une fragrance, prise dans celles de sa famille.
        if (!$saisie['fragrances']) {
            $erreurs['fragrances'] = 'Sélectionnez au moins une fragrance.';
        } elseif (!isset($erreurs['idFamille'])
            && array_diff($saisie['fragrances'], Famille::fragrancesIds($saisie['idFamille']))) {
            $erreurs['fragrances'] = 'Toutes les fragrances doivent appartenir à la famille choisie.';
        }

        // Prix par contenance : seules les contenances cochées sont enregistrées.
        $prixSaisis = is_array($_POST['prix'] ?? null) ? $_POST['prix'] : [];
        foreach (Contenance::filtrerIds(ids($_POST['contenances'] ?? null)) as $idContenance) {
            $brut = $prixSaisis[$idContenance] ?? '';
            $brut = is_string($brut) ? str_replace([',', ' '], ['.', ''], trim($brut)) : '';
            $saisie['prix'][$idContenance] = $brut;
            if (!is_numeric($brut) || (float) $brut < 0 || (float) $brut > 99999999.99) {
                $erreurs['prix'] = 'Indiquez un prix valide (positif) pour chaque contenance cochée.';
            } else {
                $saisie['prix'][$idContenance] = number_format((float) $brut, 2, '.', '');
            }
        }

        $photo = $_FILES['photo'] ?? null;
        if ($photo && $photo['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($photo['error'] !== UPLOAD_ERR_OK || $photo['size'] > self::TAILLE_MAX) {
                $erreurs['photo'] = 'La photo est trop lourde ou illisible (3 Mo maximum).';
            } elseif (!isset(self::TYPES_PHOTO[$this->typeMime($photo['tmp_name'])])) {
                $erreurs['photo'] = 'Format accepté : JPG, PNG ou WebP.';
            }
        }

        return [$saisie, $erreurs];
    }

    private function typeMime(string $fichier): string
    {
        return (string) (new finfo(FILEINFO_MIME_TYPE))->file($fichier);
    }

    /** Déplace la photo envoyée dans public/uploads et retourne son chemin (ou garde l'ancienne). */
    private function enregistrerPhoto(?string $actuelle): ?string
    {
        $photo = $_FILES['photo'] ?? null;
        if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
            $extension = self::TYPES_PHOTO[$this->typeMime($photo['tmp_name'])];
            $chemin    = 'uploads/parfum-' . bin2hex(random_bytes(8)) . '.' . $extension;
            if (move_uploaded_file($photo['tmp_name'], ROOT . '/public/' . $chemin)) {
                $this->effacerFichier($actuelle);
                return $chemin;
            }
            flash('warning', "La photo n'a pas pu être enregistrée (droits d'écriture du dossier public/uploads).");
            return $actuelle;
        }
        if (!empty($_POST['supprimer_photo'])) {
            $this->effacerFichier($actuelle);
            return null;
        }
        return $actuelle;
    }

    /** Efface une photo envoyée par l'admin (jamais les images fournies avec le site). */
    private function effacerFichier(?string $chemin): void
    {
        if ($chemin && str_starts_with($chemin, 'uploads/') && !str_contains($chemin, '..')) {
            $fichier = ROOT . '/public/' . $chemin;
            if (is_file($fichier)) {
                @unlink($fichier);
            }
        }
    }
}
