<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Form\CreerUneSortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
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
    public function createSortie(request $request, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository, VilleRepository $villeRepository): Response
    {
        $user = $this->getUser()->getId();
        $organisateur = $participantRepository->find($user);
        $sortie= new Sortie();
        $etat = $etatRepository->find(1);
        $sortie->setEtat($etat);
        $sortie->setOrganisateur($organisateur);
        $sortie->setCampus($organisateur->getCampus());

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
     * @Route("/sortie/modifier/{id}", name = "sortie_modifier")
     */
    public function modifier(int $id, Request $request, SortieRepository $sortieRepository, SortieManager $sortieManager, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        $this->getUser();

        $sortieForm = $this ->createForm(CreerUneSortieType::class,$sortie);
        $sortieForm-> handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été modifiée !!!');
            return $this-> redirectToRoute('main_home', ['id'=> $sortie->getId()]);
        }


        return $this->render('sortie/modifier.html.twig', [
            "sortie"=> $sortie,
            "user"=>$user,
            "sortieForm" => $sortieForm->createView()
        ]);

    }

    /**
     * @Route("/sortie/annuler/{id}", name = "sortie_annuler")
     */
    public function annuler(int $id, Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $sortieForm = $this ->createForm(AnnulerSortieType::class,$sortie);
        $sortieForm-> handleRequest($request);
        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $etat = $etatRepository->find(6);
            $sortie->setEtat($etat);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été annulée');
            return $this->redirectToRoute('main_home');
        }

            return $this->render('sortie/annuler.html.twig', [
            "sortie"=> $sortie,
            'sortieForm'=>$sortieForm->createView()
        ]);

    }

    /**
     * @Route("/sortie/publier/{id}", name = "sortie_publier")
     */
    public function publier(int $id, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $etat = $etatRepository->find(2);
        $sortie->setEtat($etat);
        $entityManager->flush();
        $this->addFlash('success', 'La sortie a bien été publiée');
        return $this->redirectToRoute('main_home');
    }



    /**
     * @Route("/sortie/sinscrire/{id}", name = "sortie_sinscrire")
     */
    public function sinscrire( int $id,EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, SortieRepository $sortieRepository){
        $userId = $this->getUser()->getId();
        $user = $participantRepository->find($userId);
        $sortie = $sortieRepository->find($id);
        $user->addInscription($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('main_home');

    }
    /**
     * @Route("/sortie/sedesister/{id}", name = "sortie_sedesister")
     */
    public function seDesister( int $id,EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, SortieRepository $sortieRepository){
        $userId = $this->getUser()->getId();
        $user = $participantRepository->find($userId);
        $sortie = $sortieRepository->find($id);
        $user->removeInscription($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('main_home');

    }

}
