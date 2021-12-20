<?php

namespace App\Entity;

use App\Repository\ErrorRepository;
use App\Traits\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ErrorRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Error {
    use TimestampTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="errors")
     */
    private Message $message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $code;

    /**
     * @ORM\Column(type="text")
     */
    private string $errorMessage;

    public function getId(): int {
        return $this->id;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function setCode(?string $code): self {
        $this->code = $code;

        return $this;
    }

    public function getErrorMessage(): ?string {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $errorMessage): self {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function getMessage(): Message {
        return $this->message;
    }

    public function setMessage(Message $message): self {
        $this->message = $message;

        return $this;
    }
}
