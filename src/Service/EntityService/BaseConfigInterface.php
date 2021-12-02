<?php

namespace App\Service\EntityService;

interface BaseConfigInterface {
    public function createOrUpdate(array $object);

    public function getFileRules(): array;
    public function getItemValid(array $item): bool;
    public function getItemRules(array $item): bool;
}
