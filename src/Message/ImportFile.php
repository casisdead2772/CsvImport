<?php

namespace App\Message;

class ImportFile {
    /**
     * @var string
     */
    public string $fileName;

    /**
     * @var string
     */
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
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }
}
