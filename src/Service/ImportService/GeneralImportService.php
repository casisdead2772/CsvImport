<?php

namespace App\Service\ImportService;

use App\Service\EntityService\EntityInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class GeneralImportService implements ImportInterface {
    /**
     * @var EntityInterface
     */
    public EntityInterface $entityInterface;

    /**
     * @param EntityInterface $entityInterface
     */
    public function __construct(EntityInterface $entityInterface) {
        $this->entityInterface = $entityInterface;
    }

    /**
     * @param $inputFile
     *
     * @return mixed|void
     *
     * @throws InvalidArgumentException
     */
    public function getCsvRowsAsArrays($inputFile) {
        //
        if (!file_exists($inputFile)) {
            exit("File $inputFile not exists");
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

        if (!$this->getFileRules($itemsArray)) {
            throw new InvalidArgumentException('File headers do not match expected');
        }
        //style for console
        foreach ($itemsArray as $item) {
            if (!$this->getItemValid($item)) {
                array_push($arrayIncorrectItems, $item);

                continue;
            }

            if (!$this->getItemRules($item)) {
                $countMissingItems++;

                continue;
            }

            //if arg exists, no import to the db
            if (!$isTest) {
                $this->entityInterface->createOrUpdate($item);
            }

            $countSuccessItems++;
        }

        $results['countMissingItems'] = $countMissingItems;
        $results['countSuccessItems'] = $countSuccessItems;
        $results['arrayIncorrectItems'] = $arrayIncorrectItems;

        return $results;
    }
}
