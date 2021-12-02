<?php

namespace App\Service\EntityService;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEntityService implements BaseConfigInterface {
    /**
     * @var $model
     */
    protected $model;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     * @param $entityName
     */
    protected function __construct(EntityManagerInterface $em, $entityName) {
        $this->em = $em;
        $this->model = $em->getRepository($entityName);
    }

    /**
     * @param $object
     */
    protected function save($object) {
        $this->em->persist($object);
        $this->em->flush();
    }
}
