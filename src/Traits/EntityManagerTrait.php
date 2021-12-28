<?php

namespace App\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

trait EntityManagerTrait {
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @required
     *
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em): void {
        $this->em = $em;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface {
        return $this->em;
    }

    /**
     * @param string $object
     *
     * @return EntityRepository|ObjectRepository
     */
    public function getRepository(string $object) {
        return $this->em->getRepository($object);
    }
}
