<?php

namespace App\Service\EntityService;

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
     * @return bool
     */
    public function getItemIsValid(array $item): bool;

    /**
     * @param array $item
     *
     * @return bool
     */
    public function getItemRulesIsValid(array $item): bool;
}
