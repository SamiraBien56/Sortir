<?php

namespace App\Controller\Admin;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\utils\SortieManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name ="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/sorties", name="sorties")
     */
    public function sorties(SortieManager $sortieManager): Response
    {
        $sorties = $sortieManager->getAllSorties();

        return $this->render('admin/sorties.html.twig', [
            'sorties' => $sorties
        ]);
    }

    /**
     * @Route("/sorties/annuler/{id}", name="sorties_annuler")
     */
    public function annuler($id, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $etat = $etatRepository->find(6);
        $sortie->setEtat($etat);
        $entityManager->flush();
        $this->addFlash('success', 'La sortie a bien été annulée');
        return $this->redirectToRoute('admin_sorties');
    }
}
