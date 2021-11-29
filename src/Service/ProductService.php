<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService {
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function updateOrCreateProduct(array $product, Product $selectedProduct) {
        $selectedProduct->setName($product['Product Name']);
        $selectedProduct->setCode($product['Product Code']);
        $selectedProduct->setDescription($product['Product Description']);
        $selectedProduct->setStock((int) $product['Stock']);
        $selectedProduct->setCost((int) ((float) $product['Cost in GBP'] * 100));
        if ('yes' == $product['Discontinued']) {
            $selectedProduct->setDiscontinued(new \DateTime('now'));
        }
        $selectedProduct->setTimestamp();
        $this->entityManager->persist($selectedProduct);
        $this->entityManager->flush();
    }

    /**
     * @param $product
     */
    public function checkExistingProduct($product) {
        $productRepository = $this->entityManager->getRepository(Product::class);
        $selectedProductObject = $productRepository->findOneBy(['code' => $product['Product Code']]);
        if (!$selectedProductObject) {
            $selectedProductObject = new Product();
        }
        $this->updateOrCreateProduct($product, $selectedProductObject);
    }
}
