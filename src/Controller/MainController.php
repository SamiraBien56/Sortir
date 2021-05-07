<?php


namespace App\Controller;


use App\Form\FilterListType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\utils\SortieManager;
use App\utils\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{

    /**
     * @Route ("/", name="main_home")
     *
     */
    public function home(SortieManager $sortieManager, SortieRepository $sortieRepository, UserManager $userManager, Request $request,
                         ParticipantRepository $participantRepository, EntityManagerInterface $entityManager)
    {
        if ($this->getUser() != null) {

            $userId = $this->getUser()->getId();
            $insc = $participantRepository->find($userId);

            $inscriptions= $insc->getInscriptions();

            $entityManager->flush();
            $sortieManager->majEtatSorties();

            $filterForm = $this->createForm(FilterListType::class);
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {

                $req = $request->query->all();

                if ($request->query->has('organisateur')){
                    $organisateur = $req["organisateur"];
                } else {
                    $organisateur = null;
                }
                if ($request->query->has('dateHeureDebut')){
                    $dateHeureDebut = $req["dateHeureDebut"];
                } else {
                    $dateHeureDebut = null;
                }
                if ($request->query->has('inscrit')){
                    $inscrit = $req["inscrit"];
                } else {
                    $inscrit = null;
                }
                if ($request->query->has('nonInscrit')){
                    $nonInscrit = $req["nonInscrit"];
                } else {
                    $nonInscrit = null;
                }
                //dd($req);
                //$listAllSorties = $sortieManager->getSortiesByFilter($req["campus"], $req["nom"], $req["dateMin"], $req["dateMax"]);
                //$listAllSorties = $sortieRepository->findSearch($request);
                //$listAllSorties = $sortieManager->search($req["campus"], $req["nom"], $req["dateMin"], $req["dateMax"], $req["organisateur"], $req["dateHeureDebut"]);
                $listAllSorties = $sortieRepository->search($userId, $req["campus"], $req["nom"], $req["dateMin"], $req["dateMax"], $organisateur, $dateHeureDebut, $inscrit, $nonInscrit);
            } else {
                if ($this->getUser() != null) {

                    $user = $participantRepository->find($this->getUser()->getId());
                    $listAllSorties = $sortieManager->getSortiesByCampus($user->getCampus()->getId(), $user->getId());

                } else {
                    $listAllSorties = $sortieManager->getAllSorties();
                }
            }
            return $this->render('main/home.html.twig', [
                'listAllSorties' => $listAllSorties,
                'filterForm' => $filterForm->createView(),
                'inscriptions' => $inscriptions,

                //'maj' => $maj
            ]);
        }else{
            return $this->redirectToRoute('app_login');
        }


}}