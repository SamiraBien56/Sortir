<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\EditProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/user/profil/modifier", name="profil_editProfil")
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfilType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('profil_profil');
        }

        return $this->render('profil/editProfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/profil/{id}", name = "profil_detail")
     */
    public function detailProfil(int $id, ParticipantRepository $participantRepository): Response
    {
        $profil = $participantRepository->find($id);
        return $this->render('profil/detail.html.twig', [
            "profil"=> $profil
        ]);

    }

}
