<?php

namespace App\Tests\Service\ImportService;

use App\Service\EntityService\BaseImportInterface;
use App\Service\EntityService\Error\ErrorService;
use App\Service\EntityService\Message\MessageImport\MessageImportService;
use App\Service\ImportService\GeneralImportService;
use Doctrine\Migrations\Tools\Console\Exception\FileTypeNotSupported;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GeneralImportServiceTest extends KernelTestCase {
    /**
     * @var GeneralImportService
     */
    private GeneralImportService $importService;

    /**
     * @var BaseImportInterface|MockObject
     */
    private MockObject|BaseImportInterface $importInterface;

    /**
     * @var MockObject|ConstraintViolationListInterface
     */
    private MockObject|ConstraintViolationListInterface $validatorMock;

    /**
     * @var string
     */
    private string $testFileName;

    /**
     * @var string
     */
    private string $currentDirectory;

    protected function setUp(): void {
        parent::setUp();

        $this->currentDirectory = sprintf('%s/tests/storage/', getcwd());
        $this->testFileName = sprintf('s%general_import_service_stock.csv', $this->currentDirectory);

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

        $this->validatorMock = $this->createMock(ConstraintViolationListInterface::class);
        $this->importInterface = $this->createMock(BaseImportInterface::class);
        $messageImportService = $this->createMock(MessageImportService::class);
        $errorService = $this->createMock(ErrorService::class);
        $this->importService = new GeneralImportService($this->importInterface, $errorService, $messageImportService);
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
        $badFileName = sprintf('%sbadfile.notcsv', $this->currentDirectory);
        file_put_contents($badFileName, 'bad test file');

        $this->expectException(FileTypeNotSupported::class);

        try {
            $this->importService->importByRules($badFileName);
        } finally {
            unlink($badFileName);
        }
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
    }
}
