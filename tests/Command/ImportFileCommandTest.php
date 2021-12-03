<?php

namespace App\Tests\Command;

use App\Command\ImportFileCommand;
use App\Service\EntityService\Product\ProductService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ImportFileCommandTest extends TestCase {
    public string $projectDirectory;

    /**
     * @var CommandTester
     */
    private CommandTester $commandTester;

    /**
     * @var ProductService|MockObject
     */
    private ProductService $productServiceMock;

    protected function setUp(): void {
        $this->projectDirectory = getcwd();
        $this->productServiceMock = $this->getMockBuilder(ProductService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();

        $application->add(new ImportFileCommand($this->productServiceMock));
        $command = $application->find('app:import');
        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function ExecuteCommand_WithTestArgh_SuccessReturned(): void {
        $this->assertEquals(0, $this->commandTester->execute(['filename' => $this->projectDirectory.'/storage/csvfiles/stock.csv',  'importType' => 'product']));
    }
}
