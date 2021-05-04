<?php


namespace App\Controller;


use App\Form\FilterListType;
use App\Repository\ParticipantRepository;
use App\utils\SortieManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{

    /**
     * @Route ("/", name="main_home")
     *
     */
    public function home(SortieManager $sortieManager, Request $request, ParticipantRepository $participantRepository)
    {
        $inscriptions=null;
        $filterForm = $this->createForm(FilterListType::class);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $req = $request->request->all("filter_list");
            $listAllSorties = $sortieManager->getSortiesByFilter($req["campus"], $req["dateMin"], $req["dateMax"]);
        } else {
            if($this->getUser() != null) {
                $inscriptions = $this->getUser()->getInscriptions();
                $user = $participantRepository->find($this->getUser()->getId());
                $listAllSorties = $sortieManager->getSortiesByCampus($user->getCampus()->getId());
            } else {
                $listAllSorties = $sortieManager->getAllSorties();
            }
        }
        return $this->render('main/home.html.twig', [
            'listAllSorties' => $listAllSorties,
            'filterForm'=> $filterForm->createView(),
            'inscription'=>$inscriptions
        ]);
    }

}