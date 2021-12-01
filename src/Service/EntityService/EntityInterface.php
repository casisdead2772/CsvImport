<?php

namespace App\Service\EntityService;

interface EntityInterface {
    public function createOrUpdate(array $object);
}
