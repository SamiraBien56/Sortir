<?php

namespace App\utils;

use Doctrine\ORM\EntityManagerInterface;

class UserManager
{

    public function __construct(EntityManagerInterface $entityManager)
    {
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