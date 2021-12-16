<?php

namespace App\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks()
 */
trait TimestampTrait {
    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, columnDefinition="timestamp(6)")
     */
    protected DateTime $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, columnDefinition="timestamp(6)")
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
     * @ORM\PreUpdate()
     *
     * @return void
     */
    public function setUpdatedAt(): void {
        $this->updatedAt = new \DateTime('now');
    }
}
