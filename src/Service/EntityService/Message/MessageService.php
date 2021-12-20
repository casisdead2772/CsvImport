<?php

namespace App\Service\EntityService\Message;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Traits\EntityManagerTrait;

class MessageService {
    use EntityManagerTrait;

    /**
     * @var MessageRepository
     */
    private MessageRepository $messageRepository;

    public function __construct(MessageRepository $messageRepository) {
        $this->messageRepository = $messageRepository;
    }

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
     */
    public function update($messageId, $status): void {
        $message = $this->messageRepository->getMessageById($messageId);
        $message->setStatus($status);

        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $messageId
     *
     * @return int
     */
    public function getStatusMessage($messageId): int {
        return $this->messageRepository->getMessageById($messageId)->getStatus();
    }
}
