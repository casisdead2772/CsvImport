<?php

namespace App\Service\EntityService\Error;

use App\Entity\Error;
use App\Entity\Message;
use App\Repository\ErrorRepository;
use App\Repository\MessageRepository;
use App\Traits\EntityManagerTrait;
use Doctrine\ORM\EntityNotFoundException;

class ErrorService {
    use EntityManagerTrait;

    /**
     * @param array $error
     *
     * @return Error
     *
     * @throws EntityNotFoundException
     */
    public function create(array $error): Error {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($error['message_id']);

        $newError = new Error();
        $newError->setCode($error['code']);
        $newError->setErrorMessage($error['message']);
        $newError->setMessage($message);

        $this->getEntityManager()->persist($newError);
        $this->getEntityManager()->flush();

        return $newError;
    }

    /**
     * @param string $messageId
     *
     * @return mixed|string
     *
     * @throws EntityNotFoundException
     */
    public function getLastMessageError(string $messageId) {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($messageId);

        /** @var ErrorRepository $errorRepository */
        $errorRepository = $this->getRepository(Error::class);
        $lastError = $errorRepository->getLastErrorByMessage($message);

        return explode('failed: ', $lastError->getErrorMessage())[1];
    }

    /**
     * @param string $messageId
     *
     * @return string|null
     *
     * @throws EntityNotFoundException
     */
    public function getFailureMessage(string $messageId): string {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($messageId);

        /** @var ErrorRepository $errorRepository */
        $errorRepository = $this->getRepository(Error::class);

        return $errorRepository->getFailureByMessage($message)->getErrorMessage();
    }

    /**
     * @param string $messageId
     *
     * @return string
     *
     * @throws EntityNotFoundException
     */
    public function getUnsuitedMessage(string $messageId): string {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($messageId);

        /** @var ErrorRepository $errorRepository */
        $errorRepository = $this->getRepository(Error::class);

        return $errorRepository->getUnsuitedByMessage($message)->getErrorMessage();
    }
}
