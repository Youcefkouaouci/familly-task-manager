<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\TaskAssignment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskAssignment>
 */
class TaskAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskAssignment::class);
    }


     public function save(TaskAssignment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TaskAssignment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find tasks assigned to a child with a specific status
     */
    public function findByChildAndStatus(User $child, string $status): array
    {
        return $this->createQueryBuilder('ta')
            ->andWhere('ta.child = :child')
            ->andWhere('ta.status = :status')
            ->setParameter('child', $child)
            ->setParameter('status', $status)
            ->orderBy('ta.lastStatusChangedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all tasks assigned to a child
     */
    public function findByChild(User $child): array
    {
        return $this->createQueryBuilder('ta')
            ->andWhere('ta.child = :child')
            ->setParameter('child', $child)
            ->orderBy('ta.task.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tasks assigned to a child that are in progress (pending or accepted)
     */
    public function findInProgressTasksByChild(User $child): array
    {
        return $this->createQueryBuilder('ta')
            ->andWhere('ta.child = :child')
            ->andWhere('ta.status IN (:statuses)')
            ->setParameter('child', $child)
            ->setParameter('statuses', [
                TaskAssignment::STATUS_PENDING,
                TaskAssignment::STATUS_ACCEPTED
            ])
            ->join('ta.task', 't')
            ->orderBy('t.isUrgent', 'DESC')
            ->addOrderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tasks assigned to a child for a specific task
     */
    public function findByChildAndTask(User $child, Task $task): ?TaskAssignment
    {
        return $this->createQueryBuilder('ta')
            ->andWhere('ta.child = :child')
            ->andWhere('ta.task = :task')
            ->setParameter('child', $child)
            ->setParameter('task', $task)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find completed tasks by child
     */
    public function findCompletedByChild(User $child): array
    {
        return $this->createQueryBuilder('ta')
            ->andWhere('ta.child = :child')
            ->andWhere('ta.status = :status')
            ->setParameter('child', $child)
            ->setParameter('status', TaskAssignment::STATUS_COMPLETED)
            ->orderBy('ta.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return TaskAssignment[] Returns an array of TaskAssignment objects
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

    //    public function findOneBySomeField($value): ?TaskAssignment
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
