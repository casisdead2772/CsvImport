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
        $arrayMissingItems = [];
        $countMissingItems = 0;
        $countSuccessItems = 0;
        $countIncorrectItems = 0;
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

        foreach ($itemsArray as $row => $item) {
            $violationsValid = $this->baseConfigInterface->getItemIsValid($item);

            if ($violationsValid->count() > 0) {
                $arrayIncorrectItems[$countIncorrectItems]['item'] = $item;
                $arrayIncorrectItems[$countIncorrectItems]['row'] = $row + 2;
                /** @var ConstraintViolation $error */

                foreach ($this->baseConfigInterface->getItemIsValid($item) as $key => $error) {
                    $arrayIncorrectItems[$countIncorrectItems]['errors'][$key]['column'] = $error->getPropertyPath();
                    $arrayIncorrectItems[$countIncorrectItems]['errors'][$key]['message'] = $error->getMessage();
                }

                $countIncorrectItems++;

                continue;
            }

            $violationsRules = $this->baseConfigInterface->getItemRulesIsValid($item);

            if ($violationsRules->count() > 0) {
                $arrayMissingItems[$countMissingItems]['row'] = $row + 2;

                /** @var ConstraintViolation $error */

                foreach ($this->baseConfigInterface->getItemRulesIsValid($item) as $key => $rule) {
                    $arrayMissingItems[$countMissingItems]['rules'][$key]['column'] = $rule->getPropertyPath();
                    $arrayMissingItems[$countMissingItems]['rules'][$key]['message'] = $rule->getMessage();
                }

                $countMissingItems++;

                continue;
            }
            //if arg exists, no import to the db
            if (!$isTest) {
                $this->baseConfigInterface->createOrUpdate($item);
            }

            $countSuccessItems++;
        }

        $results['arrayMissingItems'] = $arrayMissingItems;
        $results['countSuccessItems'] = $countSuccessItems;
        $results['countMissingItems'] = $countMissingItems;
        $results['arrayIncorrectItems'] = $arrayIncorrectItems;

        return $results;
    }
}
