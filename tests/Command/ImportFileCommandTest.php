<?php

namespace App\Tests\Command;

use App\Command\ImportFileCommand;
use App\Factory\ServiceImportFactory;
use App\Service\EntityService\Product\ProductService;
use App\Service\ImportService\GeneralImportService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ImportFileCommandTest extends TestCase {
    public string $projectDirectory;

    /**
     * @var CommandTester
     */
    private CommandTester $commandTester;

    protected function setUp(): void {
        $this->projectDirectory = getcwd();
        $productService = $this->getMockBuilder(ProductService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importFactory = $this->getMockBuilder(ServiceImportFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importFactory->method('getImportService')
            ->willReturn(new GeneralImportService($productService));

        $application = new Application();
        $application->add(new ImportFileCommand($importFactory));
        $command = $application->find('app:import');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @test
     */
    public function ExecuteCommand_WithTestArgh_SuccessReturned(): void {
        $this->assertEquals(0, $this->commandTester->execute(['filename' => $this->projectDirectory.'/storage/csvfiles/stock.csv',  'importType' => 'product']));
    }
}
