<?php

namespace App\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

class UniqueIdStamp implements StampInterface {
    /**
     * @var string
     */
    private string $uniqueId;

    public function __construct() {
        $this->uniqueId = uniqid('', true);
    }

    /**
     * @return string
     */
    public function getUniqueId(): string {
        return $this->uniqueId;
    }
}
