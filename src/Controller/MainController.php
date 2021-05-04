<?php


namespace App\Controller;


use App\utils\SortieManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{

    /**
     * @Route ("/", name="main_home")
     *
     */
    public function home(SortieManager $sortieManager)
    {
        if($this->getUser() != null) {
            $listAllSorties = $sortieManager->getAllSorties();
        } else {
            $listAllSorties = $sortieManager->getAllSorties();
        }
        return $this->render('main/home.html.twig', [
            'listAllSorties' => $listAllSorties
        ]);
    }

}