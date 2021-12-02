<?php

namespace App\Service\ImportService;

use App\Service\EntityService\BaseConfigInterface;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GeneralImportService {
    /**
     * @var BaseConfigInterface
     */
    public BaseConfigInterface $baseConfigInterface;

    /**
     * @param BaseConfigInterface $baseConfigInterface
     */
    public function __construct(BaseConfigInterface $baseConfigInterface) {
        $this->baseConfigInterface = $baseConfigInterface;
    }

    /**
     * @param $inputFile
     *
     * @return mixed
     */
    public function getCsvRowsAsArrays($inputFile) {
        //
        if (!file_exists($inputFile)) {
            throw new FileNotFoundException("File $inputFile not exists");
        }
        //use serializer for transfer csv to array
        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        //get array of objects
        return $decoder->decode(file_get_contents($inputFile), 'csv');
    }

    /**
     * @param $fileName
     * @param false $isTest
     *
     * @return array
     */
    public function importByRules($fileName, bool $isTest = false): array {
        $itemsArray = $this->getCsvRowsAsArrays($fileName);
        $countMissingItems = 0;
        $countSuccessItems = 0;
        $arrayIncorrectItems = [];
        $arrayHeaders = $this->baseConfigInterface->getFileRules();

        foreach ($arrayHeaders as $header) {
            if (!array_key_exists($header, $itemsArray[0])) {
                throw new InvalidArgumentException('File headers do not match expected');
            }
        }
        //style for console
        foreach ($itemsArray as $item) {
            if (!$this->baseConfigInterface->getItemValid($item)) {
                array_push($arrayIncorrectItems, $item);

                continue;
            }

            if (!$this->baseConfigInterface->getItemRules($item)) {
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
