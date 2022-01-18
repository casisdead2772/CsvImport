<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository {
    public const SENT = 0;

    public const FAILED = 1;

    public const SUCCEED = 2;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param string $id
     *
     * @return Message
     *
     * @throws NotFoundHttpException
     */
    public function getMessageById(string $id): Message {
        $message = $this->findOneBy(['id' => $id]);

        if (!$message) {
            throw new NotFoundHttpException(sprintf('Message:%s not founded', $id));
        }

        return $message;
    }
}
