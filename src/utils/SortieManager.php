<?php

namespace App\utils;

use App\Entity\Sortie;
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
        $monthAfter = new \DateTime('+5 weeks');
        $monthAgo = new \DateTime('1 month ago');
        $sortiesByCampus = $this->entityManager->createQuery(
            'SELECT sortie FROM App\Entity\Sortie sortie
            LEFT JOIN sortie.campus campus
            LEFT JOIN sortie.etat etat
            LEFT JOIN sortie.organisateur organisateur
            WHERE sortie.dateHeureDebut BETWEEN :monthAgo AND :monthAfter
            AND campus.id LIKE :idCampus
            AND etat.id=2 OR etat.id=3 OR etat.id=4 OR etat.id=5
            OR organisateur.id LIKE :idUser
            '
        )
            ->setParameter('monthAgo', $monthAgo)
            ->setParameter('monthAfter', $monthAfter)
            ->setParameter('idCampus', $idCampus)
            ->setParameter('idUser', $idUser)

        ;
        $allSorties = $sortiesByCampus->getResult();
        return $allSorties;
    }

    public function getSortiesByFilter($idCampus, $nom, $dateMin, $dateMax)
    {
            $dateM = new \DateTime(implode('-', $dateMin));
            $dateMa = new \DateTime(implode('-', $dateMax));

        $sortiesFilter = $this->entityManager->createQuery(
            'SELECT sortie FROM App\Entity\Sortie sortie
            LEFT JOIN sortie.campus campus
            WHERE campus.id LIKE :idCampus 
            AND sortie.nom LIKE :nom 
            AND sortie.dateHeureDebut BETWEEN :dateMin  AND :dateMax'
        )
            ->setParameter('idCampus', $idCampus)
            ->setParameter('nom', "%{$nom}%")
            ->setParameter('dateMin', $dateM->format('Y-d-m'))
            ->setParameter('dateMax', $dateMa->format('Y-d-m'))
        ;

        $allSorties = $sortiesFilter->getResult();
        return $allSorties;
    }

    public function AllParticipants($idSortie)
    {
        $sortie = $this->sortieRepository->find($idSortie);
        return $sortie;
    }

    public function majEtatSorties() {
        $jour = new \DateTime();
        $sorties = $this->entityManager->createQuery(
            'SELECT sortie FROM App\Entity\Sortie sortie
            LEFT JOIN sortie.etat etat
            WHERE etat.id=2 OR etat.id=3 or etat.id=4');
        $res = $sorties->getResult();
        foreach ($res as $sortie) {
            if($sortie->getDateLimiteInscription() < $jour){
                $etat = $this->etatRepository->find(3);
                $sortie->setEtat($etat);
                if ($sortie->getDateHeureDebut() < $jour->add(date_interval_create_from_date_string('1 day'))){
                    $etat = $this->etatRepository->find(5);
                    $sortie->setEtat($etat);
                }
            }
            $this->entityManager->flush();
        }
    }



    /*public function sinscrire($idSortie, $idParticipant){
        $inscription =$this->entityManager->createQuery(
            'INSERT INTO `sortie_participant`(`sortie_id`, `participant_id`)
                '
        )
            ->setParameter('idSortie', $idSortie)
            ->setParameter('idParticipant', $idParticipant);*/

}