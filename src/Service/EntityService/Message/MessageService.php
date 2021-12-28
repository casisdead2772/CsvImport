<?php

namespace App\Service\EntityService\Message;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Traits\EntityManagerTrait;
use Doctrine\ORM\EntityNotFoundException;

class MessageService {
    use EntityManagerTrait;

    /**
     * @param $messageId
     *
     * @return void
     */
    public function create($messageId): void {
        $result = new Message();
        $result->setMessageId($messageId);
        $result->setStatus(MessageRepository::SENT);

        $this->getEntityManager()->persist($result);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $messageId
     * @param $status
     *
     * @return void
     *
     * @throws EntityNotFoundException
     */
    public function update($messageId, $status): void {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($messageId);
        $message->setStatus($status);

        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $messageId
     *
     * @return int
     *
     * @throws EntityNotFoundException
     */
    public function getStatusMessage($messageId): int {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);

        return $messageRepository->getMessageById($messageId)->getStatus();
    }
}
