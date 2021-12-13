<?php

namespace App\Entity;

use App\Repository\ErrorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ErrorRepository::class)
 */
class Error {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ImportResult", inversedBy="errors")
     */
    private $importResult;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    public function getId(): ?int {
        return $this->id;
    }

    public function getUserId(): ?int {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function setCode(?string $code): self {
        $this->code = $code;

        return $this;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(string $message): self {
        $this->message = $message;

        return $this;
    }

    public function getImportResult(): ?ImportResult {
        return $this->importResult;
    }

    public function setImportResult(?ImportResult $importResult): self {
        $this->importResult = $importResult;

        return $this;
    }
}
