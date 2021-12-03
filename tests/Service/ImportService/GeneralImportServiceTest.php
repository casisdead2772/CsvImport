<?php

namespace App\Tests\Service\ImportService;

use App\Service\ImportService\GeneralImportService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeneralImportServiceTest extends KernelTestCase {
    /**
     * @return void
     */
    public function testImportFile(): void {
        $projectDirectory = getcwd();
        $filePath = '/storage/test/csvfiles/stock.csv';
        $container = static::getContainer();
        $productImportService = $container->get(GeneralImportService::class);
        $this->assertArrayHasKey('countSuccessItems', $productImportService->importByRules($projectDirectory.$filePath, true));
    }
}
