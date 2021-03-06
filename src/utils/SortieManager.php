<?php

namespace App\utils;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class SortieManager
{
    private $sortieRepository;
    private $etatRepository;

    public function __construct(SortieRepository $sortieRepository,
                                EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllSorties()
    {
        $allSorties = $this->sortieRepository->findAll();
        return $allSorties;
    }

    public function getSortiesByCampus($idCampus, $idUser)
    {
        $sortiesByCampus = $this->entityManager->createQuery(
            'SELECT sortie FROM App\Entity\Sortie sortie
            LEFT JOIN sortie.campus campus
            LEFT JOIN sortie.etat etat
            LEFT JOIN sortie.organisateur organisateur
            WHERE campus.id LIKE :idCampus
            AND etat.id=2 OR etat.id=3 OR etat.id=4 OR etat.id=5
            OR organisateur.id LIKE :idUser
            '
        )
            ->setParameter('idCampus', $idCampus)
            ->setParameter('idUser', $idUser)

        ;
        $allSorties = $sortiesByCampus->getResult();
        return $allSorties;
    }

    public function AllParticipants($idSortie)
    {
        $sortie = $this->sortieRepository->find($idSortie);
        return $sortie;
    }

    public function majEtatSorties() {
        $jour = new \DateTime();
        $monthAgo = new \DateTime('1 month ago');
        $sorties = $this->entityManager->createQuery(
            'SELECT sortie FROM App\Entity\Sortie sortie
            LEFT JOIN sortie.etat etat
            WHERE etat.id=2 OR etat.id=3 or etat.id=4 or etat.id=5');
        $res = $sorties->getResult();
        foreach ($res as $sortie) {
            if($sortie->getDateLimiteInscription() < $jour){
                $etat = $this->etatRepository->find(3);
                $sortie->setEtat($etat);
                if ($sortie->getDateHeureDebut() < $jour->add(date_interval_create_from_date_string('1 day'))){
                    $etat = $this->etatRepository->find(5);
                    $sortie->setEtat($etat);
                    if ($sortie->getDateHeureDebut() < $monthAgo) {
                        $etat = $this->etatRepository->find(7);
                        $sortie->setEtat($etat);
                    }
                }
            }
            $this->entityManager->flush();
        }
    }

}