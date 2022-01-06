<?php

namespace App\Entity;

use App\Repository\MessageImportRepository;
use App\Traits\TimestampTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageImportRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class MessageImport {
    use TimestampTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="ImportType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private ImportType $type;

    /**
     * @ORM\OneToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    private Message $message;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return ImportType
     */
    public function getType(): ImportType {
        return $this->type;
    }

    /**
     * @param ImportType $type
     *
     * @return $this
     */
    public function setType(ImportType $type): self {
        $this->type = $type;

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
     */
    public function setMessage(Message $message): void {
        $this->message = $message;
    }
}
