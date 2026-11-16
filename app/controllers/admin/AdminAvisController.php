<?php
/** Modération des avis (CU12) : l'administrateur peut supprimer un avis inapproprié. */
class AdminAvisController extends AdminController
{
    private const PAR_PAGE = 15;

    public function index(): void
    {
        $total   = Avis::compter();
        $nbPages = max(1, (int) ceil($total / self::PAR_PAGE));
        $page    = min($nbPages, max(1, (int) ($_GET['page'] ?? 1)));

        $this->render('admin/avis/index', [
            'titre'   => 'Avis des clients',
            'avis'    => Avis::tous($page, self::PAR_PAGE),
            'total'   => $total,
            'page'    => $page,
            'nbPages' => $nbPages,
        ]);
    }

    public function supprimer(int $idParfum, int $idUser): void
    {
        Avis::supprimer($idParfum, $idUser);
        flash('success', "L'avis a été supprimé.");
        $this->redirect('admin/avis');
    }
}
