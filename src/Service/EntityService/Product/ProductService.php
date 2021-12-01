<?php

namespace App\Service\EntityService\Product;

use App\Entity\Product;
use App\Service\EntityService\AbstractEntityService;
use Doctrine\ORM\EntityManagerInterface;

class ProductService extends AbstractEntityService {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, Product::class);
    }

    /**
     * @param array $object
     */
    public function createOrUpdate(array $object) {
        $selectedProduct = $this->model->findOneBy(['code' => $object['Product Code']]);

        if (!$selectedProduct) {
            $selectedProduct = new Product();
        }

        $selectedProduct->setName($object['Product Name']);
        $selectedProduct->setCode($object['Product Code']);
        $selectedProduct->setDescription($object['Product Description']);
        $selectedProduct->setStock((int)$object['Stock']);
        $selectedProduct->setCost((int)((float)$object['Cost in GBP'] * 100));

        if ($object['Discontinued'] == 'yes' && !$selectedProduct->getDiscontinued()) {
            $selectedProduct->setDiscontinued(new \DateTime('now'));
        }

        $this->save($selectedProduct);
    }
}
