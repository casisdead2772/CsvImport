<?php

namespace App\MessageHandler;

use App\Message\UploadNotification;
use App\Service\ImportService\GeneralImportService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UploadNotificationHandler implements MessageHandlerInterface {
    /**
     * @var GeneralImportService
     */
    private GeneralImportService $productImportService;

    public function __construct(GeneralImportService $productImportService) {
        $this->productImportService = $productImportService;
    }

    /**
     * @param UploadNotification $content
     *
     * @return void
     */
    public function __invoke(UploadNotification $content) {
        $fileName = $content->getFile();
        $this->productImportService->importByRules($fileName);
    }
}
