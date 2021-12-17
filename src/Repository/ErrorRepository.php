<?php

namespace App\Repository;

use App\Entity\Error;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Error|null find($id, $lockMode = null, $lockVersion = null)
 * @method Error|null findOneBy(array $criteria, array $orderBy = null)
 * @method Error[]    findAll()
 * @method Error[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ErrorRepository extends ServiceEntityRepository {
    public const CODE_FAILED = 0;

    public const CODE_INCORRECT_ITEM = 1;

    public const CODE_UNSUITED_ITEM = 2;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Error::class);
    }

    /**
     * @param $message
     *
     * @return Error
     */
    public function getLastErrorByMessage($message): Error {
        $lastError = $this->findOneBy(['message' => $message, 'code' => self::CODE_FAILED], ['id'=> 'DESC']);

        if (!$lastError) {
            throw new NotFoundHttpException('Errors for this message not founded');
        }

        return $lastError;
    }

    /**
     * @param $message
     *
     * @return Error
     */
    public function getFailureByMessage($message): Error {
        $failures = $this->findOneBy(['message' => $message, 'code' => self::CODE_INCORRECT_ITEM]);

        if (!$failures) {
            throw new NotFoundHttpException('Failures for this message not founded');
        }

        return $failures;
    }

    /**
     * @param $message
     *
     * @return Error
     */
    public function getUnsuitedByMessage($message): Error {
        $unsuited = $this->findOneBy(['message' => $message, 'code' => self::CODE_UNSUITED_ITEM]);

        if (!$unsuited) {
            throw new NotFoundHttpException('Unsuited for this message not founded');
        }

        return $unsuited;
    }
}
