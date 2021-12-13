<?php

namespace App\Repository;

use App\Entity\ImportResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImportResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportResult[]    findAll()
 * @method ImportResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportResultRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ImportResult::class);
    }
}
