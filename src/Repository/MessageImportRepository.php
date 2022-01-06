<?php

namespace App\Repository;

use App\Entity\MessageImport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MessageImport|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageImport|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageImport[]    findAll()
 * @method MessageImport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageImportRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, MessageImport::class);
    }

    public function getAllImportsWithFilterByCreated($type): QueryBuilder {
        $query = $this->createQueryBuilder('i')
            ->select(['i', 'm'])
            ->innerJoin('i.message', 'm')
            ->where('m.status != 0')
            ->orderBy('m.createdAt', 'DESC');

        if (!empty($type)) {
            $query
                ->andWhere('i.type = :type')
                ->setParameter('type', $type);
        }

        return $query;
    }
}
