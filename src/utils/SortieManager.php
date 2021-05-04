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

    public function getSortiesByCampus($idCampus)
    {
        $sortiesByCampus = $this->entityManager->createQuery(
            'SELECT sortie FROM App\Entity\Sortie sortie
            LEFT JOIN sortie.campus campus
            WHERE campus.id LIKE :idCampus'
        )
            ->setParameter('idCampus', $idCampus);
        $allSorties = $sortiesByCampus->getResult();
        return $allSorties;
    }

    public function getSortiesByFilter($idCampus, $dateMin, $dateMax)
    {
        $dateM = new \DateTime(implode('-', $dateMin));
        $dateMa = new \DateTime(implode('-', $dateMax));

        $sortiesByCampus = $this->entityManager->createQuery(
            'SELECT sortie FROM App\Entity\Sortie sortie
            LEFT JOIN sortie.campus campus
            WHERE campus.id LIKE :idCampus AND sortie.dateHeureDebut BETWEEN :dateMin  AND :dateMax'
        )
            ->setParameter('idCampus', $idCampus)
            ->setParameter('dateMin', $dateM->format('Y-d-m'))
            ->setParameter('dateMax', $dateMa->format('Y-d-m'))
        ;

        $allSorties = $sortiesByCampus->getResult();
        return $allSorties;
    }

    public function AllParticipants($idSortie)
    {
        $sortie = $this->sortieRepository->find($idSortie);
        return $sortie;
    }

}