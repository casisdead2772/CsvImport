<?php

namespace App\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait TimestampTrait {
    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    protected DateTime $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false, columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    protected DateTime $updatedAt;

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setCreatedAt(): void {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     *
     * @return void
     */
    public function setUpdatedAt(): void {
        $this->updatedAt = new \DateTime('now');
    }
}
