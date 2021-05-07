<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use App\Form\CreeParticipantType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\utils\SortieManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * Créer un Participant
     * @Route("/creerParticipant", name="admin_creerParticipant")
     */
    public function addUser(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder) {

        // traiter le formulaire utilisateur

        $participant = new Participant();
        $formAddParticipant = $this->createForm(CreeParticipantType::class, $participant, ['form_action' => 'addUser']);
        $formAddParticipant->handleRequest($request);

        // Set champs obligatoires
        $participant->setAdministrateur(0);
        $participant->setActif(1);
        $participant->setRoles(["ROLE_USER"]);
        $participant->setPassword(
            $passwordEncoder->encodePassword(
                $participant,
                $participant->getPrenom().$participant->getNom()
            )
        );

        if($formAddParticipant->isSubmitted() && $formAddParticipant->isValid()){
            //sauvegarder les données dans la base
            $em->persist($participant);
            $em->flush();
            $this->addFlash('success', 'Participant inscrit!');
            return $this-> redirectToRoute('admin_dashboard');


        }

        return $this->render('admin/creeParticipant.html.twig', [
            'formAddParticipant' => $formAddParticipant->createView()
        ]);
    }

    /**
     * Désactiver un utilisateur
     * @Route("/desactiver/{id}", name="utilisateur_desactiver")
     */
    public function desactiverUnParticipant(Request $request, EntityManagerInterface $em, $id) {

        $participant= $em->getRepository(Participant::class)->find($id);

        if($participant == null) {
            throw $this->createNotFoundException('Participant inconnu ');
        }

        // Setter le champs actif à Zéro pour la table Utilisateur
        $participant->setActif(0);

        //sauvegarder les données dans la base
        $em->persist($participant);
        $em->flush();

        return $this->redirectToRoute('admin_dashboard');
    }
}
