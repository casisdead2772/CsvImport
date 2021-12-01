<?php

namespace App\Service\ImportService\Product;

use App\Service\EntityService\Product\ProductService;
use App\Service\ImportService\GeneralImportService;

class ProductImportService extends GeneralImportService {
    /**
     * @param ProductService $entityInterface
     */
    public function __construct(ProductService $entityInterface) {
        parent::__construct($entityInterface);
    }

    /**
     * @param $itemArray
     *
     * @return bool
     */
    public function getFileRules($itemArray): bool {
        $rules = array_key_exists('Product Code', $itemArray[0])
            && array_key_exists('Product Name', $itemArray[0])
            && array_key_exists('Product Description', $itemArray[0])
            && array_key_exists('Stock', $itemArray[0])
            && array_key_exists('Cost in GBP', $itemArray[0])
            && array_key_exists('Discontinued', $itemArray[0]);

        return $rules;
    }

    /**
     * @param $product
     * @param mixed $item
     *
     * @return bool
     */
    public function getItemValid($item): bool {
        $itemValid = isset($item['Stock']) && is_numeric($item['Stock'])
            && isset($item['Cost in GBP']) && is_numeric($item['Cost in GBP'])
            && !empty($item['Product Code'])
            && !empty($item['Product Name'])
            && !empty($item['Product Description']);

        return $itemValid;
    }

    /**
     * @param $product
     * @param mixed $item
     *
     * @return bool
     */
    public function getItemRules($item): bool {
        $costProduct = (int)((float)$item['Cost in GBP'] * 100);
        $itemImportRules = !(((int)$item['Stock'] < 10 && $costProduct < 5 * 100) || $costProduct > 1000 * 100);

        return $itemImportRules;
    }
}
