<?php

namespace App\Command;


use App\Service\ProductService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ReadCsvFile extends Command
{
    public static $defaultName = 'app:import';
    protected ProductService $productService;
    public string $projectDir;

    public function __construct($projectDir, ProductService $productService)
    {
        $this->projectDir = $projectDir;
        $this->productService = $productService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Read CSV file')
            ->addArgument('test', InputArgument::OPTIONAL, 'Test execute', false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        //Get argument
        $processPermission = $input->getArgument('test');

        $productsArray = $this->getCsvRowsAsArrays($this->projectDir.'/storage/csvfiles/stock.csv');

        $countMissingItems = 0;
        $countSuccessItems = 0;

        $arrayIncorrectItems = [];

        //style for console
        $io = new SymfonyStyle($input, $output);

        foreach($productsArray as $product) {
            //validate fields
            $productValid = is_numeric($product['Stock'])
                && is_numeric($product['Cost in GBP'])
                && !empty($product['Product Code'])
                && !empty($product['Product Name'])
                && !empty($product['Product Description']);
            if (!$productValid) {
                array_push($arrayIncorrectItems, $product);
                continue;
            }

            // money *100 for int
            $productImportRules = (
                    (int)$product['Stock'] < 10 && (int)((float)$product['Cost in GBP'] * 100) < 5 * 100)
                || (int)((float)$product['Cost in GBP'] * 100) > 1000 * 100;
            if ($productImportRules) {
                $countMissingItems++;
                continue;
            }

            try {
                //if arg exists, no import to the db
                if(!$processPermission){
                    $this->productService->checkExistingProduct($product);
                }
                $countSuccessItems++;
            } catch (\Exception $exception) {
                $io->warning($exception->getMessage());
                break;
            }
        }

        //command ui
        $io->success($countSuccessItems. ' products was imported');
        $io->warning($countMissingItems. ' products was missing');
        $io->getErrorStyle()->error("Incorrect products:");
        foreach($arrayIncorrectItems as $item){
            $io->listing($item);
        }
        return Command::SUCCESS;
    }

    public function getCsvRowsAsArrays($inputFile)
    {
        //
        if(!file_exists($inputFile)){
            exit("File $inputFile not exists");
        }
        //use serializer for transfer csv to array
        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        //get array of objects
        $rows = $decoder->decode(file_get_contents($inputFile), 'csv');

        //check headers
        $headers = array_keys($rows[0]);
        if(
            $headers[0] !== 'Product Code'
            || $headers[1] !== 'Product Name'
            || $headers[2] !== 'Product Description'
            || $headers[3] !== 'Stock'
            || $headers[4] !== 'Cost in GBP'
            || $headers[5] !== 'Discontinued'
        ){
            exit('File headers do not match expected');
        }
        return $rows;
    }

}