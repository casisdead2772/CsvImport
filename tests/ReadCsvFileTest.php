<?php

namespace App\Tests;

use App\Service\ImportService\ImportService\ProductService;
use PHPUnit\Framework\TestCase;
use App\Command\ReadCsvFile;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ReadCsvFileTest extends TestCase
{
    public string $projectDirectory;

    private CommandTester $commandTester;

    private ProductService $productServiceMock;

    protected function setUp():void
    {
        $this->projectDirectory = getcwd();
        $this->productServiceMock = $this->getMockBuilder(ProductService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();

        $application->add(new ReadCsvFile($this->projectDirectory, $this->productServiceMock));
        $command = $application->find('app:import');
        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function ExecuteCommand_WithTestArgh_SuccessReturned(): void
    {
        $this->assertEquals(0, $this->commandTester->execute([]));
    }

    /** @test */
    public function ImportData_ExistingFile_ArrayReturned():void
    {
        $filePath = '/storage/test/csvfiles/stock.csv';
        $app = new ReadCsvFile($this->projectDirectory, $this->productServiceMock);
        $this->assertArrayHasKey('Product Code', $app->getCsvRowsAsArrays($this->projectDirectory.$filePath)[0]);
    }
}
