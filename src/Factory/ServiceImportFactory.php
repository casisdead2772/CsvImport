<?php

namespace App\Factory;

use App\Service\EntityService\Product\ProductService;
use App\Service\ImportService\GeneralImportService;
use InvalidArgumentException;

class ServiceImportFactory {
    private const PRODUCT = 'product';
    
    /**
     * @var ProductService
     */
    private ProductService $productService;

    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }

    /**
     * @return GeneralImportService
     */
    public function createProductService(): GeneralImportService {
        return new GeneralImportService($this->productService);
    }

    public function getImportService($importType): GeneralImportService {
        if ($importType === self::PRODUCT) {
            return $this->createProductService();
        }

        throw new InvalidArgumentException('This type not required');
    }
}
