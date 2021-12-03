<?php

namespace App\Factory;

use App\Service\EntityService\Product\ProductService;
use App\Service\EntityService\Product\TestService;
use App\Service\ImportService\GeneralImportService;
use InvalidArgumentException;

class ServiceImportFactory {
    private ProductService $productService;
    private TestService $testService;


    public function __construct(ProductService $productService, TestService $testService) {
        $this->productService = $productService;
        $this->testService = $testService;
    }

    /**
     * @required
     *
     * @return GeneralImportService
     */
    public function createProductService(): GeneralImportService {
        return new GeneralImportService($this->productService);
    }

    public function createTestService(): GeneralImportService {
        return new GeneralImportService($this->testService);
    }

    public function getImportService($importType): GeneralImportService {
        if ($importType === 'product') {
            return $this->createProductService();
        }

        throw new InvalidArgumentException('This type not required');
    }
}
