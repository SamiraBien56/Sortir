<?php

namespace App\utils;

use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    private $participantRepository;

    public function __construct(ParticipantRepository $participantRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->participantRepository = $participantRepository;
        $this->entityManager = $entityManager;
    }

    function getUserInscriptions($idUser) {
        $inscriptions = $this->entityManager->createQuery(
            'SELECT participant FROM App\Entity\Participant participant
            LEFT JOIN participant.inscriptions inscription
            WHERE participant.id LIKE :idUser'
        )
            ->setParameter('idUser', $idUser);
        $res = $inscriptions->getResult();
        return $res;
    }

}