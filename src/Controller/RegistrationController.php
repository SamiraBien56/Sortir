<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $user = new Participant();
        $user->setRoles(['ROLE_USER']);
        $user->setAdministrateur(false);
        $user->setActif(true);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password

            $images = $form->get('image')->getData();
            $fichier = md5(uniqid()).'.'.$images->guessExtension();
            $user->setImage($fichier);

            $images->move(
                $this->getParameter('images_directory'),
                $fichier);
            /*foreach ($images as $image){
                // on génére un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                //dd($fichier);

                //on copie le fichier dans img
                $image->move(
                  $this->getParameter('images_directory'),
                    $fichier
                );
                // on stock en bdd
                $img = $this->getUser()->setImage('coucou');
                $user->setImage($img);
            } */

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
