<?php

namespace App\Message;

class UploadNotification {
    /**
     * @var string
     */
    public string $fileName;
    private string $id;


    public function __construct(string $fileName, string $id) {
        $this->fileName = $fileName;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFile(): string {

        return $this->fileName;
    }

    /**
     * @return mixed
     */
    public function getId() {

        return $this->id;
    }
}
