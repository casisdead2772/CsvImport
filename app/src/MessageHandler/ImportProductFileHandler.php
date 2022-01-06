<?php

namespace App\MessageHandler;

use App\Message\ImportProductFile;
use App\Service\ImportService\GeneralImportService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Repository\ImportTypeRepository;

class ImportProductFileHandler implements MessageHandlerInterface {
    /**
     * @var GeneralImportService
     */
    private GeneralImportService $productImportService;

    public function __construct(GeneralImportService $productImportService) {
        $this->productImportService = $productImportService;
    }

    /**
     * @param ImportProductFile $content
     */
    public function __invoke(ImportProductFile $content) {
        $fileName = $content->getFile();
        $messageId = $content->getId();

        $this->productImportService->importWithLog($fileName, $messageId, ImportTypeRepository::PRODUCT);
    }
}
