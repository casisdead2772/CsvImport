<?php

namespace App\Service\EntityService\ImportResult;

use App\Entity\ImportResult;
use App\Traits\EntityManagerTrait;

class ImportResultService {
    use EntityManagerTrait;

    private const SEND = 0;

    private const FAIL = 1;

    private const SUCCESS = 2;

    /**
     * @param $messageId
     *
     * @return void
     */
    public function create($messageId): void {
        $result = new ImportResult();
        $result->setMessageId($messageId);
        $result->setStatus(self::SEND);

        $this->getEntityManager()->persist($result);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $messageId
     * @param $status
     *
     * @return void
     */
    public function update($messageId, $status): void {
        $productRepository = $this->getRepository(ImportResult::class);
        /** @var ImportResult $result */
        $result = $productRepository->findOneBy(['messageId' => $messageId]);
        $result->setStatus($status);

        $this->getEntityManager()->persist($result);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $messageId
     *
     * @return int|null
     */
    public function getStatusMessage($messageId): ?int {
        $productRepository = $this->getRepository(ImportResult::class);
        /** @var ImportResult $result */
        $result = $productRepository->findOneBy(['messageId' => $messageId]);

        return $result->getStatus();
    }
}
