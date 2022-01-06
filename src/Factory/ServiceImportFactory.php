<?php

namespace App\Factory;

use App\Service\EntityService\Error\ErrorService;
use App\Service\EntityService\Message\MessageImport\MessageImportService;
use App\Service\EntityService\Product\ProductService;
use App\Service\ImportService\GeneralImportService;
use InvalidArgumentException;

class ServiceImportFactory {
    private const PRODUCT = 'product';

    /**
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * @var ErrorService
     */
    private ErrorService $errorService;

    /**
     * @var MessageImportService
     */
    private MessageImportService $messageImportService;

    /**
     * @param ProductService $productService
     * @param ErrorService $errorService
     * @param MessageImportService $messageImportService
     */
    public function __construct(ProductService $productService, ErrorService $errorService, MessageImportService $messageImportService) {
        $this->productService = $productService;
        $this->errorService = $errorService;
        $this->messageImportService = $messageImportService;
    }

    /**
     * @return GeneralImportService
     */
    public function createProductService(): GeneralImportService {
        return new GeneralImportService($this->productService, $this->errorService, $this->messageImportService);
    }

    /**
     * @param $importType
     *
     * @return GeneralImportService
     */
    public function getImportService($importType): GeneralImportService {
        if ($importType === self::PRODUCT) {
            return $this->createProductService();
        }

        throw new InvalidArgumentException('This type not required');
    }
}
