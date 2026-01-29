<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findPaginatedTasks(int $page, int $limit, ?string $search=null): array
    {
        $qb = $this->createQueryBuilder('t')->orderBy('t.createdAt', 'DESC');
        if($search)
        {
            $qb->andWhere('t.title LIKE :search')->setParameter('search','%'.$search.'%');
        }
        return $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countTasks(?string $search=null): int
    {
        $qb = $this->createQueryBuilder('t')->select('COUNT(t.id)');
        if ($search) 
        {
            $qb->andWhere('LOWER(t.title) LIKE LOWER(:search)')->setParameter('search', '%' . strtolower($search) . '%');
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Search tasks by title
     */
    public function searchByTitle(?string $search): array
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC');

        if ($search) {
            $qb->andWhere('LOWER(t.title) LIKE LOWER(:search)')
               ->setParameter('search', '%' . $search . '%');
        }
        return $qb->getQuery()->getResult();
    }

    public function countCompleted(): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.completed = true')
            ->getQuery()
            ->getSingleScalarResult();
    }
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
