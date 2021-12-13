<?php

namespace App\Service\EntityService\ErrorService;

use App\Entity\Error;
use App\Entity\ImportResult;
use App\Traits\EntityManagerTrait;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ErrorService {
    use EntityManagerTrait;

    /**
     * @param mixed $error
     *
     * @return void
     */
    public function create($error): void {
        $importResultRepository = $this->getRepository(ImportResult::class);
        /** @var ImportResult $result */
        $result = $importResultRepository->findOneBy(['messageId' => $error['import_result_id']]);

        $newError = new Error();
        $newError->setCode($error['code']);
        $newError->setMessage($error['message']);
        $newError->setImportResult($result);

        $this->getEntityManager()->persist($newError);
        $this->getEntityManager()->flush();
    }

    public function getLastErrorMessage($messageId) {
        $importResultRepository = $this->getRepository(ImportResult::class);
        $errorRepository = $this->getRepository(Error::class);
        $importResultId = $importResultRepository->findOneBy(['messageId' => $messageId]);

        if (!$importResultId) {
            throw new BadRequestException('Message not founded');
        }

        /** @var Error $lastError */
        $lastError = $errorRepository->findOneBy(['importResult' => $importResultId], ['id'=> 'DESC']);

        if (!$lastError) {
            throw new BadRequestException('Errors for this message not founded');
        }

        return explode('failed: ', $lastError->getMessage())[1];
    }
}
