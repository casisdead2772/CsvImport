<?php

namespace App\EventSubscriber;

use App\Messenger\UniqueIdStamp;
use App\Repository\MessageRepository;
use App\Service\EntityService\Error\ErrorService;
use App\Service\EntityService\Message\MessageService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

class WorkerSubscriber implements EventSubscriberInterface {
    /**
     * @var MessageService
     */
    private MessageService $messageService;
    /**
     * @var ErrorService
     */
    private ErrorService $errorService;

    /**
     * @param MessageService $messageService
     * @param ErrorService $errorService
     */
    public function __construct(MessageService $messageService, ErrorService $errorService) {
        $this->messageService = $messageService;
        $this->errorService = $errorService;
    }

    public static function getSubscribedEvents(): array {
        return [
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandledEvent',
            SendMessageToTransportsEvent::class => 'onSendMessageToTransportsEvent',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent'
        ];
    }

    /**
     * @param WorkerMessageHandledEvent $event
     *
     * @return void
     */
    public function onWorkerMessageHandledEvent(WorkerMessageHandledEvent $event): void {
        $id = $this->getMessageId($event);

        $this->messageService->update($id, MessageRepository::SUCCEED);
    }

    public function onSendMessageToTransportsEvent(SendMessageToTransportsEvent $event): void {
        $id = $this->getMessageId($event);

        $this->messageService->create($id);
    }

    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $event): void {
        if (!$event->willRetry()) {
            $id = $this->getMessageId($event);
            $errors = $event->getThrowable();
            $errorInfo = [
                'code' => $errors->getCode(),
                'message' => $errors->getMessage(),
                'message_id' => $id
            ];

            $this->errorService->create($errorInfo);
            $this->messageService->update($id, MessageRepository::FAILED);
        }
    }

    private function getMessageId($event): string {
        /** @var UniqueIdStamp $uniqueIdStamp */
        $uniqueIdStamp = $event->getEnvelope()->last(UniqueIdStamp::class);

        return $uniqueIdStamp->getUniqueId();
    }
}
