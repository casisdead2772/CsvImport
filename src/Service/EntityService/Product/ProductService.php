<?php

namespace App\Service\EntityService\Product;

use App\Entity\Product;
use App\Service\EntityService\BaseImportInterface;
use App\Traits\EntityManagerTrait;

class ProductService implements BaseImportInterface {
    use EntityManagerTrait;

    private const HEADERS = ['Product Code', 'Product Name', 'Product Description', 'Stock', 'Cost in GBP', 'Discontinued'];
    /**
     * @param array $object
     */
    public function createOrUpdate(array $object): void {
        $productRepository = $this->getRepository(Product::class);
        $selectedProduct = $productRepository->findOneBy(['code' => $object['Product Code']]);

        if (!$selectedProduct) {
            $selectedProduct = new Product();
        }

        $selectedProduct->setName($object['Product Name']);
        $selectedProduct->setCode($object['Product Code']);
        $selectedProduct->setDescription($object['Product Description']);
        $selectedProduct->setStock((int)$object['Stock']);
        $selectedProduct->setCost((int)((float)$object['Cost in GBP'] * 100));

        if ($object['Discontinued'] === 'yes' && !$selectedProduct->getDiscontinued()) {
            $selectedProduct->setDiscontinued(new \DateTime('now'));
        }

        $this->getEntityManager()->persist($selectedProduct);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array
     */
    public function getItemHeaders(): array {
        return self::HEADERS;
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    public function getItemIsValid(array $item): bool {
        $itemValid = isset($item['Stock']) && is_numeric($item['Stock'])
            && isset($item['Cost in GBP']) && is_numeric($item['Cost in GBP'])
            && !empty($item['Product Code'])
            && !empty($item['Product Name'])
            && !empty($item['Product Description']);

        return $itemValid;
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    public function getItemRulesIsValid(array $item): bool {
        $costProduct = (int)((float)$item['Cost in GBP'] * 100);
        $itemImportRules = !(((int)$item['Stock'] < 10 && $costProduct < 5 * 100) || $costProduct > 1000 * 100);

        return $itemImportRules;
    }
}
