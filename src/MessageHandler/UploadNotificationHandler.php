<?php

namespace App\MessageHandler;

use App\Message\UploadNotification;
use App\Repository\ErrorRepository;
use App\Service\EntityService\Error\ErrorService;
use App\Service\ImportService\GeneralImportService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UploadNotificationHandler implements MessageHandlerInterface {
    /**
     * @var GeneralImportService
     */
    private GeneralImportService $productImportService;

    /**
     * @var ErrorService
     */
    private ErrorService $errorService;

    public function __construct(GeneralImportService $productImportService, ErrorService $errorService) {
        $this->productImportService = $productImportService;
        $this->errorService = $errorService;
    }

    /**
     * @param UploadNotification $content
     */
    public function __invoke(UploadNotification $content) {
        $fileName = $content->getFile();
        $messageId = $content->getId();
        $results = $this->productImportService->importByRules($fileName);

        $error = serialize($results['arrayIncorrectItems']);
        $this->errorService->create([
            'message_id' => $messageId,
            'code' => ErrorRepository::CODE_INCORRECT_ITEM,
            'message' => $error
        ]);

        $unsuited = serialize($results['arrayMissingItems']);
        $this->errorService->create([
            'message_id' => $messageId,
            'code' => ErrorRepository::CODE_UNSUITED_ITEM,
            'message' => $unsuited
        ]);
    }
}
