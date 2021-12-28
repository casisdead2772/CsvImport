<?php

namespace App\Tests\Command;

use App\Command\ImportFileCommand;
use App\Factory\ServiceImportFactory;
use App\Service\ImportService\GeneralImportService;
use Doctrine\Migrations\Tools\Console\Exception\FileTypeNotSupported;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ImportFileCommandTest extends TestCase {
    public string $projectDirectory;

    /**
     * @var CommandTester
     */
    private CommandTester $commandTester;

    /**
     * @var GeneralImportService|MockObject
     */
    private $generalImportService;

    protected function setUp(): void {
        $this->projectDirectory = getcwd();

        $importFactory = $this->getMockBuilder(ServiceImportFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->generalImportService = $this->getMockBuilder(GeneralImportService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importFactory->method('getImportService')
            ->willReturn($this->generalImportService);

        $application = new Application();
        $application->add(new ImportFileCommand($importFactory));
        $command = $application->find('app:import');
        $this->commandTester = new CommandTester($command);
    }

    public function testSuccessExec(): void {
        $this->generalImportService
            ->expects($this->once())
            ->method('importByRules')
            ->willReturn([
                'countSuccessItems' => 3,
                'countMissingItems' => 2,
                'arrayIncorrectItems' => [],
            ]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->execute([
            'filename' => 'test name',
            'importType' => 'test type',
        ]));
    }

    public function testExecuteWithoutArgs(): void {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }

    public function testImportServiceReturnEmpty(): void {
        $this->generalImportService
            ->expects($this->once())
            ->method('importByRules')
            ->willReturn([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->execute([
            'filename' => 'test name',
            'importType' => 'test type',
        ]));
    }

    public function testBadFile(): void {
        $this->generalImportService
            ->expects($this->once())
            ->method('importByRules')
            ->willThrowException(new FileTypeNotSupported());
        $this->assertEquals(Command::FAILURE, $this->commandTester->execute([
            'filename' => 'test name',
            'importType' => 'test type',
        ]));
    }

    public function testBadFilePath(): void {
        $this->generalImportService
            ->expects($this->once())
            ->method('importByRules')
            ->willThrowException(new FileNotFoundException());
        $this->assertEquals(Command::FAILURE, $this->commandTester->execute([
            'filename' => '/bad/file',
            'importType' => 'product',
        ]));
    }
}
