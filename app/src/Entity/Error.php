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
     * @ORM\ManyToOne(targetEntity="Message")
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

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): self {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     *
     * @return $this
     */
    public function setErrorMessage(string $errorMessage): self {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message {
        return $this->message;
    }

    /**
     * @param Message $message
     *
     * @return $this
     */
    public function setMessage(Message $message): self {
        $this->message = $message;

        return $this;
    }
}
