<?php

namespace App\EventListener;

use App\Messenger\UniqueIdStamp;
use App\Service\EntityService\ErrorService\ErrorService;
use App\Service\EntityService\ImportResult\ImportResultService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

class WorkerSubscriber implements EventSubscriberInterface {
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var ImportResultService
     */
    private ImportResultService $importResultService;
    /**
     * @var ErrorService
     */
    private ErrorService $errorService;

    /**
     * @param LoggerInterface $logger
     * @param ImportResultService $importResultService
     * @param ErrorService $errorService
     */
    public function __construct(LoggerInterface $logger, ImportResultService $importResultService, ErrorService $errorService) {
        $this->logger = $logger;
        $this->importResultService = $importResultService;
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
        /** @var UniqueIdStamp $uniqueIdStamp */
        $uniqueIdStamp = $event->getEnvelope()->last(UniqueIdStamp::class);
        $id = $uniqueIdStamp->getUniqueId();
        $this->logger->info($event->getReceiverName());

        $this->importResultService->update($id, 2);
    }

    public function onSendMessageToTransportsEvent(SendMessageToTransportsEvent $envelope): void {
        /** @var UniqueIdStamp $uniqueIdStamp */
        $uniqueIdStamp = $envelope->getEnvelope()->last(UniqueIdStamp::class);
        $id = $uniqueIdStamp->getUniqueId();
        $this->logger->info("SendMessage from event $id");

        $this->importResultService->create($id);
    }

    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $envelope): void {
        /** @var UniqueIdStamp $uniqueIdStamp */
        $uniqueIdStamp = $envelope->getEnvelope()->last(UniqueIdStamp::class);
        $id = $uniqueIdStamp->getUniqueId();
        $errors = $envelope->getThrowable();
        $errorInfo = [
            'code' => $errors->getCode(),
            'message' => $errors->getMessage(),
            'import_result_id' => $id
        ];

        $this->errorService->create($errorInfo);
        $this->importResultService->update($id, 1);
    }
}
