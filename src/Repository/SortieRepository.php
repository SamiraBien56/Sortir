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
    public function findSearch(Sortie $search):array
    { $jour = new \DateTime();

        $query = $this
            ->createQueryBuilder('s')
            ->select( 's', 's.participants');



        if(!empty($search->nom)){
            $query=$query
                ->andWhere('s.nom LIKE :nom' )
                ->setParameter('nom',"%{$search->nom}%");

        }
        if(!empty($search->dateMin)){
            $query=$query
                ->andWhere('s.dateHeureDebut >= dateMin' )
                ->setParameter('dateMin',$search->dateMin);

        }
        if(!empty($search->dateMax)){
            $query=$query
                ->andWhere('s.dateHeureDebut >= dateMax' )
                ->setParameter('dateMax',$search->dateMax);

        }
        if(!empty($search->organisateur)){
            $query=$query
                ->andWhere('s.organisateur = userId' )
                ->setParameter('userId',$search->getParticipants());;


        }
        if(!empty($search->dateHeureDebut)){
            $query=$query
                ->andWhere('s.dateHeureDebut > jour' )
                ->setParameter('jour',$jour->format('Y-d-m'));;


        }
        if(!empty($search->campus)){
            $query=$query
                ->andWhere('s.campus LIKE campus' )
                ->setParameter('campus',$search->campus);;


        }

        return $query->getQuery()->getResult();
    }
}
