<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\AddVilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VillesLieuxController extends AbstractController
{
    /**
     * @Route("/villes/add", name="villes_add")
     */
    public function villeAdd(Request $request, EntityManagerInterface $entityManager)
    {
        $ville = new Ville();
        $villeForm = $this->createForm(AddVilleType::class, $ville);
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'La ville a bien été enregistrée');
            return $this-> redirectToRoute('sortie_create');

        }
        return $this->render('villes_lieux/villeadd.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }
}
