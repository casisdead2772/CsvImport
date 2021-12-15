<?php

namespace App\Service\EntityService;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface BaseImportInterface {
    /**
     * @param array $object
     */
    public function createOrUpdate(array $object);

    /**
     * @return array
     */
    public function getItemHeaders(): array;

    /**
     * @param array $item
     */
    public function getItemIsValid(array $item): ConstraintViolationListInterface;

    /**
     * @param array $item
     *
     * @return bool
     */
    public function getItemRulesIsValid(array $item): bool;
}
