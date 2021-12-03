<?php

namespace App\Service\ImportService;

use App\Service\EntityService\BaseImportInterface;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GeneralImportService {
    /**
     * @var BaseImportInterface
     */
    public BaseImportInterface $baseConfigInterface;

    /**
     * @param BaseImportInterface $baseConfigInterface
     */
    public function __construct(BaseImportInterface $baseConfigInterface) {
        $this->baseConfigInterface = $baseConfigInterface;
    }

    /**
     * @param $inputFile
     *
     * @return mixed
     */
    private function getCsvRowsAsArrays($inputFile) {
        //
        if (!file_exists($inputFile)) {

            throw new FileNotFoundException(sprintf('File %s not exists', $inputFile));
        }
        //use serializer for transfer csv to array
        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        //get array of objects
        return $decoder->decode(file_get_contents($inputFile), 'csv');
    }


    /**
     * @param $fileName
     * @param bool $isTest
     *
     * @return array
     */
    public function importByRules($fileName, bool $isTest = false): array {
        $itemsArray = $this->getCsvRowsAsArrays($fileName);
        $countMissingItems = 0;
        $countSuccessItems = 0;
        $arrayIncorrectItems = [];
        $arrayHeaders = $this->baseConfigInterface->getItemHeaders();

        foreach ($arrayHeaders as $header) {
            if (!array_key_exists($header, $itemsArray[0])) {

                throw new InvalidArgumentException('File headers do not match expected');
            }
        }
        //style for console
        foreach ($itemsArray as $item) {
            if (!$this->baseConfigInterface->getItemIsValid($item)) {
                $arrayIncorrectItems[] = $item;

                continue;
            }

            if (!$this->baseConfigInterface->getItemRulesIsValid($item)) {
                $countMissingItems++;

                continue;
            }
            //if arg exists, no import to the db
            if (!$isTest) {
                $this->baseConfigInterface->createOrUpdate($item);
            }

            $countSuccessItems++;
        }

        $results['countMissingItems'] = $countMissingItems;
        $results['countSuccessItems'] = $countSuccessItems;
        $results['arrayIncorrectItems'] = $arrayIncorrectItems;

        return $results;
    }
}
