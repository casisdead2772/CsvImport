<?php

namespace App\Command;

use App\Service\CommandCallService;
use App\Service\FileUploadService;
use App\Service\ProductService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function PHPUnit\Framework\throwException;

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
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * @var FileUploadService
     */
    private FileUploadService $fileUploadService;


    /**
     * @param $targetDirectory
     * @param ProductService $productService
     * @param FileUploadService $fileUploadService
     */
    public function __construct($targetDirectory, ProductService $productService, FileUploadService $fileUploadService) {
        $this->targetDirectory = $targetDirectory;
        $this->productService = $productService;
        $this->fileUploadService = $fileUploadService;
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

        try {
            $productsArray = $this->getCsvRowsAsArrays($filename);
        } catch (Exception $e) {
            return Command::INVALID;
        }
        $countMissingItems = 0;
        $countSuccessItems = 0;

        $arrayIncorrectItems = [];

        //style for console
        $io = new SymfonyStyle($input, $output);

        foreach ($productsArray as $product) {
            //validate fields
            $productValid = isset($product['Stock']) && is_numeric($product['Stock'])
                && isset($product['Cost in GBP']) && is_numeric($product['Cost in GBP'])
                && !empty($product['Product Code'])
                && !empty($product['Product Name'])
                && !empty($product['Product Description']);

            if (!$productValid) {
                array_push($arrayIncorrectItems, $product);

                continue;
            }

            $costProduct = (int)((float)$product['Cost in GBP'] * 100);
            // money *100 for int
            $productImportRules = (
                (int)$product['Stock'] < 10 && $costProduct < 5 * 100
            )
                || $costProduct > 1000 * 100;

            if ($productImportRules) {
                $countMissingItems++;

                continue;
            }

            try {
                //if arg exists, no import to the db
                if (!$processPermission) {
                    $this->productService->checkExistingProduct($product);
                }

                $countSuccessItems++;
            } catch (Exception $exception) {
                $io->warning($exception->getMessage());

                return Command::FAILURE;
            }
        }

        //command ui
        $io->success($countSuccessItems. ' products was imported');
        $io->warning($countMissingItems. ' products was missing');
        $io->getErrorStyle()->error("Incorrect products:");

        foreach ($arrayIncorrectItems as $item) {
            $io->listing($item);
        }

        return Command::SUCCESS;
    }

    /**
     * @param $inputFile
     *
     * @return mixed|void
     *
     * @throws Exception
     */
    public function getCsvRowsAsArrays($inputFile) {
        //
        if (!file_exists($inputFile)) {
            exit("File $inputFile not exists");
        }
        //use serializer for transfer csv to array
        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        //get array of objects
        $rows = $decoder->decode(file_get_contents($inputFile), 'csv');
        //check headers
        if (
            !array_key_exists('Product Code', $rows[0])
            || !array_key_exists('Product Name', $rows[0])
            || !array_key_exists('Product Description', $rows[0])
            || !array_key_exists('Stock', $rows[0])
            || !array_key_exists('Cost in GBP', $rows[0])
            || !array_key_exists('Discontinued', $rows[0])
        ) {
            throw new Exception('File headers do not match expected');
        } else {
            return $rows;
        }
    }
}
