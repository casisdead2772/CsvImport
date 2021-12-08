<?php

namespace App\Tests\Service\ImportService;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeneralImportServiceTest extends KernelTestCase {
    /**
     * @return void
     */
    public function testImportFile(): void {
        $projectDirectory = getcwd();
        $filePath = '/storage/test/csvfiles/stock1.csv';
        $container = static::getContainer();
        $productImportService = $container->get('product_import_service');
        $this->assertArrayHasKey('countSuccessItems', $productImportService->importByRules($projectDirectory.$filePath, true));
    }
}
