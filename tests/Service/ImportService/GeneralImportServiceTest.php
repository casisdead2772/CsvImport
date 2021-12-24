<?php

namespace App\Tests\Service\ImportService;

use App\Service\EntityService\BaseImportInterface;
use App\Service\EntityService\Product\ProductService;
use App\Service\ImportService\GeneralImportService;
use Doctrine\Migrations\Tools\Console\Exception\FileTypeNotSupported;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GeneralImportServiceTest extends KernelTestCase {
    /**
     * @var ProductService|MockObject
     */
    private $importService;

    /**
     * @var BaseImportInterface|MockObject
     */
    private $importInterface;

    /**
     * @var MockObject|ConstraintViolationListInterface
     */
    private $validatorMock;

    /**
     * @var string
     */
    private string $currentDirectory;

    /**
     * @var string
     */
    private string $testFileName;

    /**
     * @var string
     */
    private string $badFileName;

    protected function setUp(): void {
        parent::setUp();

        $this->currentDirectory = getcwd().'/tests/storage/';
        $this->testFileName = $this->currentDirectory.'general_import_service_stock.csv';

        $file = fopen($this->testFileName, 'wb+');

        $testData = [
            ProductService::PRODUCT_HEADERS,
            ['P0001', 'TV', '32”', 'Tv', '10', '399.99'],
            ['P0001', 'Test', '32”', 'Tv', '10', '399.99'],
            ['P0001', 'TV', '32”', 'Tv', '10', '399.99'],
        ];

        foreach ($testData as $fields) {
            fputcsv($file, $fields);
        }

        $this->badFileName = $this->currentDirectory.'badfile.notcsv';
        file_put_contents($this->badFileName, 'bad test file');

        $this->validatorMock = $this->createMock(ConstraintViolationListInterface::class);
        $this->importInterface = $this->createMock(BaseImportInterface::class);
        $this->importService = new GeneralImportService($this->importInterface);
    }

    /**
     * @return void
     */
    public function testImportProduct(): void {
        $container = static::getContainer();
        $productImportService = $container->get('product_import_service');
        $result = $productImportService->importByRules($this->testFileName, true);
        $this->assertArrayHasKey('countSuccessItems', $result);
        $this->assertArrayHasKey('arrayIncorrectItems', $result);
        $this->assertArrayHasKey('countMissingItems', $result);
    }

    public function testBadHeaders(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->importInterface
            ->method('getItemHeaders')
            ->willReturn(['item1', 'item2', 'item3']);

        $this->importService->importByRules($this->testFileName);
    }

    public function testNoValidate(): void {
        $this->validatorMock
            ->method('count')
            ->willReturn(1);

        $this->importInterface
            ->method('getItemIsValid')
            ->willReturn($this->validatorMock);

        $this->expectException(InvalidArgumentException::class);
        $this->importService->importByRules($this->testFileName);
    }

    public function testBadFileExtension(): void {
        $filename = $this->currentDirectory.'badfile.notcsv';
        file_put_contents($filename, 'bad test file');
        $this->expectException(FileTypeNotSupported::class);
        $this->importService->importByRules($filename);
    }

    public function tearDown(): void {
        parent::tearDown();
        unlink($this->testFileName);
        unlink($this->badFileName);
    }
}
