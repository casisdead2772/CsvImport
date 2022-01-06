<?php

namespace App\Service\EntityService\ImportType;

use App\Entity\ImportType;
use App\Repository\ImportTypeRepository;
use App\Traits\EntityManagerTrait;

class ImportTypeService {
    use EntityManagerTrait;

    /**
     * @return array
     */
    public function getAllImportTypes(): array {
        /** @var ImportTypeRepository $importTypeRepository */
        $importTypeRepository = $this->getRepository(ImportType::class);

        return $importTypeRepository->findAll();
    }
}
