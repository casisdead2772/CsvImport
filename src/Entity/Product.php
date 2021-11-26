<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product.
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
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     */
    private string $code;

    /**
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private int $stock;

    /**
     * @ORM\Column(name="costInGbp", type="integer", nullable=false)
     */
    private int $cost;

    /**
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private ?\DateTime $added = null;

    /**
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private ?\DateTime $discontinued = null;

    /**
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private \DateTime $timestamp;

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function setCode(string $code): void {
        $this->code = $code;
    }

    public function getAdded(): ?\DateTime {
        return $this->added;
    }

    /**
     * @ORM\PrePersist
     */
    public function setAdded(): void {
        $this->added = new \DateTime('now');
    }

    public function getDiscontinued(): ?\DateTime {
        return $this->discontinued;
    }

    public function setDiscontinued(?\DateTime $discontinued): void {
        $this->discontinued = $discontinued;
    }

    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setTimestamp(): void {
        $this->timestamp = new \DateTime('now');
    }

    public function getStock(): int {
        return $this->stock;
    }

    public function setStock(int $stock): void {
        $this->stock = $stock;
    }

    public function getCost(): int {
        return $this->cost;
    }

    public function setCost(int $cost): void {
        $this->cost = $cost;
    }
}
