<?php

namespace App\Repository;

use App\Entity\RecordingLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecordingLog>
 *
 * @method RecordingLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecordingLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecordingLog[]    findAll()
 * @method RecordingLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordingLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecordingLog::class);
    }

    public function add(RecordingLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RecordingLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByLead($lead_ids)
        {
           
            $qb = $this->createQueryBuilder("recording_log");
            $qb->andWhere('recording_log.leadId IN (:lead_ids)')
             ->setParameter('lead_ids',$lead_ids[0]);
            $result = $qb->getQuery()->getResult();
           
            return $result;
        }

//    /**
//     * @return RecordingLog[] Returns an array of RecordingLog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RecordingLog
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
