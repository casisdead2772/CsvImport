<?php

namespace App\Repository;

use App\Entity\Error;
use App\Entity\Message;
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
     * @param Message $message
     *
     * @return Error
     */
    public function getLastErrorByMessage(Message $message): Error {
        $lastError = $this->findOneBy(['message' => $message, 'code' => self::CODE_FAILED], ['id'=> 'DESC']);

        if (!$lastError) {
            throw new NotFoundHttpException('Errors for this message: %s not founded', $message->getMessageId());
        }

        return $lastError;
    }

    /**
     * @param Message $message
     *
     * @return Error
     */
    public function getFailureByMessage(Message $message): Error {
        $failures = $this->findOneBy(['message' => $message, 'code' => self::CODE_INCORRECT_ITEM]);

        if (!$failures) {
            throw new NotFoundHttpException('Failures for this message: %s not founded', $message->getMessageId());
        }

        return $failures;
    }

    /**
     * @param Message $message
     *
     * @return Error
     */
    public function getUnsuitedByMessage(Message $message): Error {
        $unsuited = $this->findOneBy(['message' => $message, 'code' => self::CODE_UNSUITED_ITEM]);

        if (!$unsuited) {
            throw new NotFoundHttpException(sprintf('Unsuited for this message: %s not founded', $message->getMessageId()));
        }

        return $unsuited;
    }
}
