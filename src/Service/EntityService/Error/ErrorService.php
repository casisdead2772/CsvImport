<?php

namespace App\Service\EntityService\Error;

use App\Entity\Error;
use App\Repository\ErrorRepository;
use App\Repository\MessageRepository;
use App\Traits\EntityManagerTrait;

class ErrorService {
    use EntityManagerTrait;

    /**
     * @var MessageRepository
     */
    private MessageRepository $messageRepository;
    /**
     * @var ErrorRepository
     */
    private ErrorRepository $errorRepository;


    public function __construct(MessageRepository $messageRepository, ErrorRepository $errorRepository) {
        $this->messageRepository = $messageRepository;
        $this->errorRepository = $errorRepository;
    }

    /**
     * @param mixed $error
     *
     * @return void
     */
    public function create($error): void {
        $message = $this->messageRepository->getMessageById($error['message_id']);

        $newError = new Error();
        $newError->setCode($error['code']);
        $newError->setErrorMessage($error['message']);
        $newError->setMessage($message);

        $this->getEntityManager()->persist($newError);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $messageId
     *
     * @return mixed|string
     */
    public function getLastMessageError($messageId) {
        $message = $this->messageRepository->getMessageById($messageId);
        $lastError = $this->errorRepository->getLastErrorByMessage($message);

        return explode('failed: ', $lastError->getErrorMessage())[1];
    }

    /**
     * @param $messageId
     *
     * @return string|null
     */
    public function getFailureMessage($messageId): ?string {
        $message = $this->messageRepository->getMessageById($messageId);

        return $this->errorRepository->getFailureByMessage($message)->getErrorMessage();
    }
}
