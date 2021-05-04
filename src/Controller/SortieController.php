<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreerUneSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\utils\SortieManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/create", name = "sortie_create")
     */
    public function createSortie(request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie= new Sortie();
        $etat = $etatRepository->find(1);
        $sortie->setEtat($etat);

        $sortieForm = $this ->createForm(CreerUneSortieType::class,$sortie);
        $sortieForm-> handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été enregistrer Bien joué!!!');
            return $this-> redirectToRoute('main_home', ['id'=> $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm -> createView(),
        ]);
    }
    /**
     * @Route("/sortie/afficher/{id}", name = "sortie_afficher")
     */
    public function details(int $id, SortieRepository $sortieRepository, SortieManager $sortieManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        $this->getUser();

        $listAllParticipants = $sortieManager->AllParticipants($id);

        return $this->render('sortie/afficher.html.twig', [
            "sortie"=> $sortie,
            "user"=>$user,
            'listAllParticipants' => $listAllParticipants
        ]);

    }

    /**
     * @Route ("/sortie/sinscrire", name = "sortie_sinscrire")
     */

    public function sinscrire(Sortie $sortie, EntityManagerInterface $entityManager){


    }
}
