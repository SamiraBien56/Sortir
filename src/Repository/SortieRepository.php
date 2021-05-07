<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[]
     */
    public function search($idUser, $idCampus, $nom, $dateMin, $dateMax, $organisateur, $dateHeureDebut, $inscrit, $nonInscrit)
    {
        //$dateM = new \DateTime(implode('-', $dateMin));
        //$dateMa = new \DateTime(implode('-', $dateMax));

        $jour = new \DateTime();
        $query = $this
            ->createQueryBuilder('sortie')
            ->select('sortie')
            ->leftJoin('sortie.campus', 'campus')
            ->leftJoin('sortie.organisateur', 'organisateur')
            ->leftJoin('sortie.participants', 'participant');
        if ($nom != null) {
            $query = $query
                ->andWhere('sortie.nom LIKE :nom')
                ->setParameter('nom', "%{$nom}%");
        }
        if ($dateMin != null) {
            $query = $query
                ->andWhere('sortie.dateHeureDebut >= :dateMin')
                ->setParameter('dateMin', $dateMin);
        }
        if ($dateMax != null) {
            $query = $query
                ->andWhere('sortie.dateHeureDebut <= :dateMax')
                ->setParameter('dateMax', $dateMax);
        }
        if ($organisateur != null) {
            $query = $query
                ->andWhere('organisateur.id = :idUser')
                ->setParameter('idUser', $idUser);
        }
        if ($dateHeureDebut != null) {
            $query = $query
                ->andWhere('sortie.dateHeureDebut < :jour')
                ->setParameter('jour', $jour);
        }
        if ($idCampus != null) {
            $query = $query
                ->andWhere('campus.id LIKE :campus')
                ->setParameter('campus', $idCampus);
        }
        if ($inscrit != null) {
            $query = $query
                ->andWhere('participant.id LIKE :idUser')
                ->setParameter('idUser', $idUser);
        }
        /*if ($nonInscrit != null) {
            $query = $query
                ->andWhere(':idUser NOT IN participant.id')
                ->setParameter('idUser', $idUser)
            ;
        }*/
        return $query->getQuery()->getResult();
    }
}
