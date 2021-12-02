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

    /**
     * @return array
     */
    public function getFileRules(): array {
        return ['Product Code', 'Product Name', 'Product Description', 'Stock', 'Cost in GBP', 'Discontinued'];
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    public function getItemValid(array $item): bool {
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
    public function getItemRules(array $item): bool {
        $costProduct = (int)((float)$item['Cost in GBP'] * 100);
        $itemImportRules = !(((int)$item['Stock'] < 10 && $costProduct < 5 * 100) || $costProduct > 1000 * 100);

        return $itemImportRules;
    }
}
