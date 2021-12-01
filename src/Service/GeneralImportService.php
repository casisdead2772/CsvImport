<?php

namespace App\Service;

use App\ServiceInterface\ImportInterface;
use InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GeneralImportService implements ImportInterface {
    /**
     * @var ProductService
     */
    public ProductService $productService;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService) {
        $this->productService = $productService;
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
            throw new InvalidArgumentException('File headers do not match expected');
        } else {
            return $rows;
        }
    }

    /**
     * @param $itemsArray
     * @param false $isTest
     *
     * @return array
     */
    public function importByRules($itemsArray, bool $isTest = false): array {
        $countMissingItems = 0;
        $countSuccessItems = 0;
        $arrayIncorrectItems = [];

        //style for console
        foreach ($itemsArray as $product) {
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

            //if arg exists, no import to the db
            if (!$isTest) {
                $this->productService->checkExistingProduct($product);
            }

            $countSuccessItems++;
        }

        $results['countMissingItems'] = $countMissingItems;
        $results['countSuccessItems'] = $countSuccessItems;
        $results['arrayIncorrectItems'] = $arrayIncorrectItems;

        return $results;
    }
}
