<?php

namespace App\Service\ImportService;

use App\Repository\ErrorRepository;
use App\Service\EntityService\BaseImportInterface;
use App\Service\EntityService\Error\ErrorService;
use App\Service\EntityService\Message\MessageImport\MessageImportService;
use Doctrine\Migrations\Tools\Console\Exception\FileTypeNotSupported;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GeneralImportService {
    /**
     * @var BaseImportInterface
     */
    public BaseImportInterface $baseConfigInterface;

    /**
     * @var ErrorService
     */
    private ErrorService $errorService;

    /**
     * @var MessageImportService
     */
    private MessageImportService $messageImportService;

    /**
     * @param BaseImportInterface $baseConfigInterface
     * @param ErrorService $errorService
     * @param MessageImportService $messageImportService
     */
    public function __construct(BaseImportInterface $baseConfigInterface, ErrorService $errorService, MessageImportService $messageImportService) {
        $this->baseConfigInterface = $baseConfigInterface;
        $this->errorService = $errorService;
        $this->messageImportService = $messageImportService;
    }

    /**
     * @param $inputFile
     *
     * @return mixed
     */
    private function getCsvRowsAsArrays($inputFile): mixed {
        if (!file_exists($inputFile)) {
            throw new FileNotFoundException(sprintf('File %s not exists', $inputFile));
        }

        if (pathinfo($inputFile)['extension'] !== 'csv') {
            throw new FileTypeNotSupported(sprintf('File %s not supported', $inputFile));
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
        $arrayIncorrectItems = [];
        $notExistingHeaders = [];
        $countMissingItems = 0;
        $countSuccessItems = 0;
        $countIncorrectItems = 0;
        $arrayHeaders = $this->baseConfigInterface->getItemHeaders();

        if (empty($arrayHeaders)) {
            throw new InvalidArgumentException('Excepted file headers not founded');
        }

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
                $arrayIncorrectItems[$countIncorrectItems] = $this->resultBuilder($violationsValid, 'errors', $row);

                $countIncorrectItems++;

                continue;
            }

            $violationsRules = $this->baseConfigInterface->getItemRulesIsValid($item);

            if ($violationsRules->count() > 0) {
                $arrayMissingItems[$countMissingItems] = $this->resultBuilder($violationsRules, 'rules', $row);

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

    /**
     * @param string $fileName
     * @param string $messageId
     * @param int $importType
     */
    public function importWithLog(string $fileName, string $messageId, int $importType): void {
        $this->messageImportService->createIfNotExists($messageId, $importType);
        $results = $this->importByRules($fileName);

        $error = serialize($results['arrayIncorrectItems']);
        $this->errorService->create([
            'message_id' => $messageId,
            'code' => ErrorRepository::CODE_INCORRECT_ITEM,
            'message' => $error
        ]);

        $unsuited = serialize($results['arrayMissingItems']);
        $this->errorService->create([
            'message_id' => $messageId,
            'code' => ErrorRepository::CODE_UNSUITED_ITEM,
            'message' => $unsuited
        ]);
    }

    /**
     * @param ConstraintViolationListInterface $violations
     * @param string $validType
     * @param int $row
     *
     * @return array
     */
    private function resultBuilder(ConstraintViolationListInterface $violations, string $validType, int $row): array {
        $resultArray['row'] = $row + 2;
        /** @var ConstraintViolation $error */

        foreach ($violations as $key => $error) {
            $resultArray[$validType][$key]['column'] = preg_replace('/\[|]/', '', $error->getPropertyPath());
            $resultArray[$validType][$key]['message'] = $error->getMessage();
        }

        return $resultArray;
    }
}
