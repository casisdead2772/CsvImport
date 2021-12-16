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
     *
     * @return ConstraintViolationListInterface
     */
    public function getItemIsValid(array $item): ConstraintViolationListInterface;

    /**
     * @param array $item
     *
     * @return ConstraintViolationListInterface
     */
    public function getItemRulesIsValid(array $item): ConstraintViolationListInterface;
}
