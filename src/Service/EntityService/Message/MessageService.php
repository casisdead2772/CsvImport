<?php

namespace App\Service\EntityService\Message;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Traits\EntityManagerTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessageService {
    use EntityManagerTrait;

    /**
     * @param string $messageId
     *
     * @return void
     */
    public function create(string $messageId): void {
        $result = new Message();
        $result->setId($messageId);
        $result->setStatus(MessageRepository::SENT);

        $this->getEntityManager()->persist($result);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $messageId
     * @param int $status
     *
     * @return void
     *
     * @throws NotFoundHttpException
     */
    public function update(string $messageId, int $status): void {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($messageId);
        $message->setStatus($status);

        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $messageId
     *
     * @return int
     *
     * @throws NotFoundHttpException
     */
    public function getStatusMessage(string $messageId): int {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);

        return $messageRepository->getMessageById($messageId)->getStatus();
    }

    /**
     * @param string $id
     *
     * @return Message
     *
     * @throws NotFoundHttpException
     */
    public function getMessage(string $id): Message {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);

        return $messageRepository->getMessageById($id);
    }
}
