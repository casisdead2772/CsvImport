<?php

namespace App\Message;

class UploadNotification {
    /**
     * @var string
     */
    public string $fileName;

    public function __construct(string $fileName) {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFile(): string {
        return $this->fileName;
    }
}
