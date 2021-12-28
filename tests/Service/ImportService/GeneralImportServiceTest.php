<?php

namespace App\Tests\Service\ImportService;

use App\Service\EntityService\BaseImportInterface;
use App\Service\ImportService\GeneralImportService;
use Doctrine\Migrations\Tools\Console\Exception\FileTypeNotSupported;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GeneralImportServiceTest extends KernelTestCase {
    /**
     * @var MockObject
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
    private string $testFileName;

    /**
     * @var string
     */
    private string $badFileName;

    protected function setUp(): void {
        parent::setUp();

        $currentDirectory = sprintf('%s/tests/storage/', getcwd());
        $this->testFileName = sprintf('s%general_import_service_stock.csv', $currentDirectory);

        $file = fopen($this->testFileName, 'wb+');

        $testData = [
            ['header1', 'header2', 'header3', 'header4'],
            ['P0001', 'TV', '32”', 'Tv'],
            ['P0002', 'Test', '32”', 'Tv'],
            ['P0003', 'TV', '32”', 'Tv'],
        ];

        foreach ($testData as $fields) {
            fputcsv($file, $fields);
        }

        $this->badFileName = sprintf('%sbadfile.notcsv', $currentDirectory);
        file_put_contents($this->badFileName, 'bad test file');

        $this->validatorMock = $this->createMock(ConstraintViolationListInterface::class);
        $this->importInterface = $this->createMock(BaseImportInterface::class);
        $this->importService = new GeneralImportService($this->importInterface);
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
        $this->expectException(FileTypeNotSupported::class);
        $this->importService->importByRules($this->badFileName);
    }

    public function testSuccessImportByRules(): void {
        $this->importInterface->expects($this->once())
            ->method('getItemHeaders')
            ->willReturn(['header1', 'header2', 'header3', 'header4']);

        $result = $this->importService->importByRules($this->testFileName);
        $this->assertArrayHasKey('countSuccessItems', $result);
        $this->assertArrayHasKey('arrayIncorrectItems', $result);
        $this->assertArrayHasKey('countMissingItems', $result);
    }

    public function tearDown(): void {
        parent::tearDown();
        unlink($this->testFileName);
        unlink($this->badFileName);
    }
}
