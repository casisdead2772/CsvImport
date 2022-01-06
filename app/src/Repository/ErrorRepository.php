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
        $messageError = $this->findOneBy(['message' => $message, 'code' => self::CODE_FAILED], ['id'=> 'DESC']);

        if (!$messageError) {
            throw new NotFoundHttpException(sprintf('Error for this message:%s not founded', $message->getId()));
        }

        return $messageError;
    }

    /**
     * @param Message $message
     *
     * @return Error
     */
    public function getFailureByMessage(Message $message): Error {
        $failedItems = $this->findOneBy(['message' => $message, 'code' => self::CODE_INCORRECT_ITEM]);

        if (!$failedItems) {
            throw new NotFoundHttpException(sprintf('Failed items for this message:%s not founded', $message->getId()));
        }

        return $failedItems;
    }

    /**
     * @param Message $message
     *
     * @return Error
     */
    public function getUnsuitedByMessage(Message $message): Error {
        $unsuitedItems = $this->findOneBy(['message' => $message, 'code' => self::CODE_UNSUITED_ITEM]);

        if (!$unsuitedItems) {
            throw new NotFoundHttpException(sprintf('Unsuited items for this message:%s not founded', $message->getId()));
        }

        return $unsuitedItems;
    }
}
