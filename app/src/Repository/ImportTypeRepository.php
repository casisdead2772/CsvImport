<?php

namespace App\Repository;

use App\Entity\ImportType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method ImportType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportType[]    findAll()
 * @method ImportType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportTypeRepository extends ServiceEntityRepository {
    public const PRODUCT = 1;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ImportType::class);
    }

    /**
     * @param string $id
     *
     * @return ImportType
     *
     * @throws NotFoundHttpException
     */
    public function getImportTypeById(string $id): ImportType {
        $importType = $this->findOneBy(['id' => $id]);

        if (!$importType) {
            throw new NotFoundHttpException(sprintf('Import type with id:%s not founded', $id));
        }

        return $importType;
    }
}
