<?php

namespace App\Command;

use App\Service\ImportService\GeneralImportService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ReadCsvFile extends Command {
    /**
     * @var string The default command name, override in parent
     */
    public static $defaultName = 'app:import';

    /**
     * @var string
     */
    public string $targetDirectory;

    /**
     * @var GeneralImportService
     */
    private GeneralImportService $generalImportService;

    /**
     * @param $targetDirectory
     * @param GeneralImportService $generalImportService
     */
    public function __construct($targetDirectory, GeneralImportService $generalImportService) {
        $this->targetDirectory = $targetDirectory;
        $this->generalImportService = $generalImportService;
        parent::__construct();
    }

    protected function configure() {
        $this->setDescription('Read CSV file')
            ->addArgument('filename', InputArgument::REQUIRED, 'File name')
            ->addArgument('test', InputArgument::OPTIONAL, 'Test execute', false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int {
        //Get argument
        $processPermission = $input->getArgument('test');
        $filename = $input->getArgument('filename');

        $isTest = !empty($processPermission);

        try {
            $results = $this->generalImportService->importByRules($filename, $isTest);
        } catch (\InvalidArgumentException | FileNotFoundException $e) {
            print($e->getMessage());

            return Command::FAILURE;
        }

        //command ui style for console
        $io = new SymfonyStyle($input, $output);
        $io->success($results['countSuccessItems']. ' products was imported');
        $io->warning($results['countMissingItems']. ' products was missing');
        $io->getErrorStyle()->error("Incorrect products:");

        foreach ($results['arrayIncorrectItems'] as $item) {
            $io->listing($item);
        }

        return Command::SUCCESS;
    }
}
