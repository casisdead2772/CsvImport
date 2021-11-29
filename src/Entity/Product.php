<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="tblProductData",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="strProductCode", columns={"strProductCode"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Product {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(name="intProductDataId", type="integer", nullable=false, options={"unsigned"=true})
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     */
    private string $code;

    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private int $stock;

    /**
     * @var int
     *
     * @ORM\Column(name="costInGbp", type="integer", nullable=false)
     */
    private int $cost;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private ?\DateTime $added = null;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private ?\DateTime $discontinued = null;

    /**
     * @var \DateTime
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private \DateTime $timestamp;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void {
        $this->code = $code;
    }

    /**
     * @return \DateTime|null
     */
    public function getAdded(): ?\DateTime {
        return $this->added;
    }

    /**
     * @ORM\PrePersist
     */
    public function setAdded(): void {
        $this->added = new \DateTime('now');
    }

    /**
     * @return \DateTime|null
     */
    public function getDiscontinued(): ?\DateTime {
        return $this->discontinued;
    }

    /**
     * @param \DateTime|null $discontinued
     */
    public function setDiscontinued(?\DateTime $discontinued): void {
        $this->discontinued = $discontinued;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setTimestamp(): void {
        $this->timestamp = new \DateTime('now');
    }

    /**
     * @return int
     */
    public function getStock(): int {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock(int $stock): void {
        $this->stock = $stock;
    }

    /**
     * @return int
     */
    public function getCost(): int {
        return $this->cost;
    }

    /**
     * @param int $cost
     */
    public function setCost(int $cost): void {
        $this->cost = $cost;
    }
}
