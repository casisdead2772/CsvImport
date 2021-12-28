<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

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
     * @param $id
     *
     * @return Message
     *
     * @throws EntityNotFoundException
     */
    public function getMessageById($id): Message {
        $message = $this->findOneBy(['messageId' => $id]);

        if (!$message) {
            throw new EntityNotFoundException(sprintf('Message:%s not founded', $id));
        }

        return $message;
    }
}
