<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use App\Form\CreeParticipantType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\utils\SortieManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
     * @Route("/sorties/activer/{id}", name="sorties_activer")
     */
    public function activer($id, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $sortie = $sortieRepository->find($id);
        $etat = $etatRepository->find(1);
        $sortie->setEtat($etat);
        $entityManager->flush();
        $this->addFlash('success', 'La sortie a bien été activée');
        return $this->redirectToRoute('admin_sorties');
    }

    /**
     * Créer un Participant
     * @Route("/creerParticipant", name="admin_creerParticipant")
     */
    public function addUser(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $participant = new Participant();
        $formAddParticipant = $this->createForm(CreeParticipantType::class, $participant, ['form_action' => 'addUser']);
        $formAddParticipant->handleRequest($request);

        // Set champs obligatoires
        $participant->setAdministrateur(0);
        $participant->setActif(1);
        $participant->setRoles(["ROLE_USER"]);


        //$participant->setImage('$fichier');
        $participant->setPassword(
            $passwordEncoder->encodePassword(
                $participant,
                $participant->getPrenom() . $participant->getNom()
            )

        );

        if ($formAddParticipant->isSubmitted() && $formAddParticipant->isValid()) {
            //sauvegarder les données dans la base
            $em->persist($participant);
            $em->flush();
            $this->addFlash('success', 'Participant inscrit!');
            return $this->redirectToRoute('admin_dashboard');


        }

        return $this->render('admin/creeParticipant.html.twig', [
            'formAddParticipant' => $formAddParticipant->createView()
        ]);
    }


    /**
     * list des participant
     * @Route("/listParticipant", name="admin_listParticipant")
     */
    public function selectParticipant(ParticipantRepository $participantRepository,EntityManagerInterface $entityManager){
        $listAllParticipant = $participantRepository->findAll();
        return $this->render('admin/listParticipant.html.twig',[
            'listParticipants'=>$listAllParticipant
        ]);

    }
    /**
     * désactiver des participant
     * @Route("/desactiverParticipant/{id}", name="desactiverParticipant")
     */
    public function desactiverParticipant(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository){

        $participant = $participantRepository->find($id);
        $participant->setActif(0);
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur a bien été désactivé");
        return $this->redirectToRoute('admin_admin_listParticipant')
        ;
    }

    /**
     * Réactiver des participant
     * @Route("/reactiverParticipant/{id}", name="reactiverParticipant")
     */
    public function reactiverParticipant(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository){

        $participant = $participantRepository->find($id);
        $participant->setActif(1);
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur a bien été réactivé");
        return $this->redirectToRoute('admin_admin_listParticipant')
            ;
    }

    /**
     * désactiver des participant
     * @Route("/supprimerParticipant/{id}", name="admin_supprimerParticipant")
     */
    public function supprimerParticipant(int $id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository){

        $participant = $participantRepository->find($id);

        $toutesInscriptions =$participant ->getInscriptions();

        foreach ($toutesInscriptions as $inscription){
        $participant->removeInscription($inscription);
        }

        $touteSorties= $participant->getSorties();
        foreach ($touteSorties as $sorty){
            $participant->removeSorty($sorty);
        }

        $entityManager->remove($participant);

        $entityManager->flush();
        $this->addFlash('success', "L'utilisateur a bien été supprimé");
        return $this->redirectToRoute('admin_admin_listParticipant')
            ;
    }

}
