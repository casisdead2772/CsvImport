<?php

namespace App\Service\EntityService\Error;

use App\Entity\Error;
use App\Entity\Message;
use App\Repository\ErrorRepository;
use App\Repository\MessageRepository;
use App\Traits\EntityManagerTrait;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorService {
    use EntityManagerTrait;

    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator) {
        $this->paginator = $paginator;
    }

    /**
     * @param array $error
     *
     * @return Error
     *
     * @throws NotFoundHttpException
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
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public function getMessageError(string $messageId): string {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($messageId);

        /** @var ErrorRepository $errorRepository */
        $errorRepository = $this->getRepository(Error::class);

        if ($message->getStatus() === MessageRepository::FAILED) {
            $lastError = $errorRepository->getLastErrorByMessage($message)->getErrorMessage();
            $errorMessage = explode('failed: ', $lastError);

            if (array_key_exists(1, $errorMessage)) {
                return $errorMessage[1];
            }

            return 'Server error';
        }

        return 'Message has no errors';
    }

    /**
     * @param string $messageId
     *
     * @return array
     *
     * @throws NotFoundHttpException
     */
    public function getFailureMessage(string $messageId): array {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($messageId);

        /** @var ErrorRepository $errorRepository */
        $errorRepository = $this->getRepository(Error::class);
        $messageFailure = $errorRepository->getFailureByMessage($message)->getErrorMessage();

        return unserialize($messageFailure, ['allowed_classes' => false]);
    }

    /**
     * @param string $messageId
     *
     * @return array
     */
    public function getUnsuitedMessage(string $messageId): array {
        /** @var MessageRepository $messageRepository */
        $messageRepository = $this->getRepository(Message::class);
        $message = $messageRepository->getMessageById($messageId);

        /** @var ErrorRepository $errorRepository */
        $errorRepository = $this->getRepository(Error::class);
        $unsuitedItems = $errorRepository->getUnsuitedByMessage($message)->getErrorMessage();

        return unserialize($unsuitedItems, ['allowed_classes' => false]);
    }

    /**
     * @param string $messageId
     * @param Request $request
     *
     * @return PaginationInterface
     *
     * @throws NotFoundHttpException
     */
    public function getMessageUnsuitedWithPaginate(string $messageId, Request $request): PaginationInterface {
        $unsuited = $this->getUnsuitedMessage($messageId);

        return $this->paginator->paginate(
            $unsuited,
            $request->query->getInt('page', 1),
            10
        );
    }

    /**
     * @param string $messageId
     * @param Request $request
     *
     * @return PaginationInterface
     *
     * @throws NotFoundHttpException
     */
    public function getMessageFailuresWithPaginate(string $messageId, Request $request): PaginationInterface {
        $unsuited = $this->getFailureMessage($messageId);

        return $this->paginator->paginate(
            $unsuited,
            $request->query->getInt('page', 1),
            10
        );
    }
}
