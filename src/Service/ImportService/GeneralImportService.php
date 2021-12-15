<?php

namespace App\Service\ImportService;

use App\Service\EntityService\BaseImportInterface;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;

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
        $notExistingHeaders = [];
        $arrayHeaders = $this->baseConfigInterface->getItemHeaders();

        foreach ($arrayHeaders as $header) {
            if (!array_key_exists($header, $itemsArray[0])) {
                $notExistingHeaders[] = $header;
            }
        }

        if (!empty($notExistingHeaders)) {
            throw new InvalidArgumentException(sprintf('Excepted file headers: %s not founded', implode(', ', $notExistingHeaders)));
        }

        $count = 0;
        foreach ($itemsArray as $row => $item) {
            $violations = $this->baseConfigInterface->getItemIsValid($item);

            if ($violations->count() > 0) {
                $arrayIncorrectItems[$count]['item'] = $item;
                $arrayIncorrectItems[$count]['row'] = $row + 2;
                /** @var ConstraintViolation $error */
                foreach ($this->baseConfigInterface->getItemIsValid($item) as $key => $error) {
                    $arrayIncorrectItems[$count]['errors'][$key]['column'] = $error->getPropertyPath();
                    $arrayIncorrectItems[$count]['errors'][$key]['message'] = $error->getMessage();
                }
                $count++;

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
