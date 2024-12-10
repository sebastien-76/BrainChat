<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }


        //    /**
    //     * @return Room[] Returns an array of Room objects
    //     */
    public function findAllPrivateRooms(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.isPrivate = :val')
            ->setParameter('val', true)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Room[] Returns an array of Room objects
    //     */
        public function findPrivateRooms($userId): array
        {
            return $this->createQueryBuilder('r')
                ->andWhere('r.isPrivate = :val')
                ->setParameter('val', true)
                ->innerJoin('r.participants', 'p')
                ->andWhere('p.user = :val2')
                ->setParameter('val2', $userId)
                ->orderBy('r.id', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }

    //    /**
    //     * @return Room[] Returns an array of Room objects
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

    //    public function findOneBySomeField($value): ?Room
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
