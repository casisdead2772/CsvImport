<?php

namespace App\Command;

use App\Factory\ServiceImportFactory;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ImportFileCommand extends Command {
    /**
     * @var string The default command name, override in parent
     */
    protected static $defaultName = 'app:import';

    private ServiceImportFactory $factory;


    public function __construct(ServiceImportFactory $factory) {
        $this->factory = $factory;
        parent::__construct();
    }

    protected function configure() {
        $this->setDescription('Read CSV file')
            ->addArgument('filename', InputArgument::REQUIRED, 'File name')
            ->addArgument('importType', InputArgument::REQUIRED, 'Type import file')
            ->addArgument('test', InputArgument::OPTIONAL, 'Test execute', false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int {
        //Get argument
        $filename = $input->getArgument('filename');
        $importType = $input->getArgument('importType');
        $isTest = !empty($input->getArgument('test'));
        $io = new SymfonyStyle($input, $output);

        try {
            $importService = $this->factory->getImportService($importType);
        } catch (InvalidArgumentException $e) {
            $io->getErrorStyle()->error($e->getMessage());

            return Command::FAILURE;
        }

        try {
            $results = $importService->importByRules($filename, $isTest);
        } catch (\InvalidArgumentException | FileNotFoundException $e) {
            $io->getErrorStyle()->error($e->getMessage());

            return Command::FAILURE;
        }

        //command ui style for console
        $io->success($results['countSuccessItems']. ' products was imported');
        $io->warning($results['countMissingItems']. ' products was missing');
        $io->getErrorStyle()->error("Incorrect products:");

        foreach ($results['arrayIncorrectItems'] as $item) {
            $io->listing($item);
        }

        return Command::SUCCESS;
    }
}
