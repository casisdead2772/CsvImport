<?php

namespace App\Tests\Command;

use App\Command\ImportFileCommand;
use App\Factory\ServiceImportFactory;
use App\Service\EntityService\Product\ProductService;
use App\Service\ImportService\GeneralImportService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Exception\RuntimeException;

class ImportFileCommandTest extends TestCase {
    public string $projectDirectory;

    /**
     * @var CommandTester
     */
    private CommandTester $commandTester;

    /**
     * @var string
     */
    private string $filePath;
    /**
     * @var ProductService|MockObject
     */
    private $productService;
    /**
     * @var ServiceImportFactory|MockObject
     */
    private $importFactory;

    /**
     * @var string
     */
    private string $badFilePath;

    protected function setUp(): void {
        $this->projectDirectory = getcwd();
        $this->productService = $this->getMockBuilder(ProductService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->importFactory = $this->getMockBuilder(ServiceImportFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filePath = $this->projectDirectory . '/tests/storage/stock.csv';

        $file = fopen($this->filePath, 'wb+');
        $data = [
            ProductService::PRODUCT_HEADERS,
            ['P0001', 'TV', '32”', 'Tv', '10', '399.99'],
            ['P0001', 'Test', '32”', 'Tv', '10', '399.99'],
            ['P0001', 'TV', '32”', 'Tv', '10', '399.99'],

        ];
        $this->badFilePath = $this->projectDirectory . '/tests/storage/badfile.txt';
        file_put_contents($this->badFilePath, 'bad test file');

        foreach ($data as $fields) {
            fputcsv($file, $fields);
        }

        $application = new Application();
        $application->add(new ImportFileCommand($this->importFactory));
        $command = $application->find('app:import');
        $this->commandTester = new CommandTester($command);
    }

    public function testProductServiceMethodIsBad(): void {
        $this->productService->method('getItemHeaders')
            ->willReturn(['BadHeader', 'BadHeader']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->execute([
            'filename' => $this->filePath,
            'importType' => 'product',
        ]));
    }

    public function testSuccessExec(): void {
        $this->productService
            ->method('getItemHeaders')
            ->willReturn(ProductService::PRODUCT_HEADERS);
        $this->importFactory->method('getImportService')
            ->willReturn(new GeneralImportService($this->productService));
        $this->assertEquals(Command::SUCCESS, $this->commandTester->execute([
            'filename' => $this->filePath,
            'importType' => 'product',
        ]));
    }

    public function testExecuteWithoutArgs(): void {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }

    public function testBadImportService(): void {
        $importService = $this->createMock(GeneralImportService::class);
        $importService->expects($this->once())
            ->method('importByRules')
            ->willReturn([]);
        $this->importFactory->method('getImportService')
            ->willReturn($importService);

        $this->assertEquals(Command::FAILURE, $this->commandTester->execute([
            'filename' => $this->filePath,
            'importType' => 'product',
        ]));
    }

    public function testBadFile(): void {
        $this->assertEquals(Command::FAILURE, $this->commandTester->execute([
            'filename' => $this->badFilePath,
            'importType' => 'product',
        ]));
    }

    public function testBadTypeArgument(): void {
        $this->assertEquals(Command::FAILURE, $this->commandTester->execute([
            'filename' => $this->filePath,
            'importType' => 'Bad import type',
        ]));
    }

    public function tearDown(): void {
        parent::tearDown();
        unlink($this->filePath);
        unlink($this->badFilePath);
    }
}
