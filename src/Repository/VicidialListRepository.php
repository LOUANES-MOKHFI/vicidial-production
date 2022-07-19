<?php

namespace App\Repository;

use App\Entity\VicidialList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VicidialList>
 *
 * @method VicidialList|null find($id, $lockMode = null, $lockVersion = null)
 * @method VicidialList|null findOneBy(array $criteria, array $orderBy = null)
 * @method VicidialList[]    findAll()
 * @method VicidialList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VicidialListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VicidialList::class);
    }

    public function add(VicidialList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VicidialList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

        public function getByDate($from,$to)
        {
            $qb = $this->createQueryBuilder("vicidial_list");
            $qb->andWhere('vicidial_list.entry_date BETWEEN :from AND :to')
                ->setParameter('from', $from )
                ->setParameter('to', $to)
            ;
            $result = $qb->getQuery()->getResult();

            return $result;
        }
        public function getByStatus($status)
        {
            $qb = $this->createQueryBuilder("vicidial_list");
            $qb->andWhere('vicidial_list.status = :status')
                ->setParameter('status', $status )
            ;
            $result = $qb->getQuery()->getResult();

            return $result;
        }
        public function getByDateAndStatus($from,$to,$status)
        {
            $qb = $this->createQueryBuilder("vicidial_list");
            $qb->andWhere('vicidial_list.status = :status')
                ->andWhere('vicidial_list.last_local_call_time BETWEEN :from AND :to')
                //->orWhere('vicidial_list.modify_date BETWEEN :from AND :to')
                ->setParameter('status', $status)
                ->setParameter('from', $from)
                ->setParameter('to', $to);
            
            $result = $qb->getQuery()->getResult();

            return $result;
        }
//    /**
//     * @return VicidialList[] Returns an array of VicidialList objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VicidialList
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
