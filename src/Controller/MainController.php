<?php


namespace App\Controller;



use App\Repository\SortieRepository;
use App\utils\SortieManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{

    /**
     * @Route ("/", name="main_home")
     *
     */
    public function home(SortieManager $sortieManager,SortieRepository $sortieRepository)
    {
    $inscriptions=null;


        if($this->getUser() != null) {
            $inscriptions = $this->getUser()->getInscriptions();
            $listAllSorties = $sortieManager->getAllSorties();

        } else {
            $listAllSorties = $sortieManager->getAllSorties();
        }
        return $this->render('main/home.html.twig', [
            'listAllSorties' => $listAllSorties,
            'inscription'=>$inscriptions


        ]);
    }

}