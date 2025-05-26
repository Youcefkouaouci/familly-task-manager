<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    
    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find tasks by parent for the current week
     */
    public function findByParentForCurrentWeek(User $parent): array
    {
        $startOfWeek = new \DateTimeImmutable('monday this week');
        $endOfWeek = new \DateTimeImmutable('sunday this week 23:59:59');
        
        return $this->createQueryBuilder('t')
            ->andWhere('t.parent = :parent')
            ->andWhere('t.createdAt BETWEEN :start AND :end')
            ->setParameter('parent', $parent)
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tasks by parent for the current month
     */
    public function findByParentForCurrentMonth(User $parent): array
    {
        $startOfMonth = new \DateTimeImmutable('first day of this month midnight');
        $endOfMonth = new \DateTimeImmutable('last day of this month 23:59:59');
        
        return $this->createQueryBuilder('t')
            ->andWhere('t.parent = :parent')
            ->andWhere('t.createdAt BETWEEN :start AND :end')
            ->setParameter('parent', $parent)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find urgent tasks
     */
    public function findUrgentTasks(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isUrgent = :isUrgent')
            ->setParameter('isUrgent', true)
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Task[] Returns an array of Task objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Task
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
