<?php

namespace App\utils;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class SortieManager
{
    private $sortieRepository;


    public function __construct(SortieRepository $sortieRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->sortieRepository = $sortieRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllSorties()
    {
        $allSorties = $this->sortieRepository->findAll();
        return $allSorties;
    }


    public function AllParticipants($idSortie)
    {
        $sortie = $this->sortieRepository->find($idSortie);
        return $sortie;
    }
}

