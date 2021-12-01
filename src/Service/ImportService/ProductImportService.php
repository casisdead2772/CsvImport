<?php

namespace App\Service\ImportService;

use App\Service\EntityService\EntityInterface;
use App\Service\EntityService\Product\ProductService;

class ProductImportService extends GeneralImportService {

    /**
     * @param ProductService $entityInterface
     */
    public function __construct(ProductService $entityInterface) {
        parent::__construct($entityInterface);
    }

    /**
     * @param $productArray
     *
     * @return bool
     */
    public function getFileRules($productArray): bool {
        $rules = array_key_exists('Product Code', $productArray[0])
            && array_key_exists('Product Name', $productArray[0])
            && array_key_exists('Product Description', $productArray[0])
            && array_key_exists('Stock', $productArray[0])
            && array_key_exists('Cost in GBP', $productArray[0])
            && array_key_exists('Discontinued', $productArray[0]);

        return $rules;
    }

    /**
     * @param $product
     *
     * @return bool
     */
    public function getProductValid($product): bool {
        $productValid = isset($product['Stock']) && is_numeric($product['Stock'])
            && isset($product['Cost in GBP']) && is_numeric($product['Cost in GBP'])
            && !empty($product['Product Code'])
            && !empty($product['Product Name'])
            && !empty($product['Product Description']);

        return $productValid;
    }

    /**
     * @param $product
     *
     * @return bool
     */
    public function getProductRules($product): bool {
        $costProduct = (int)((float)$product['Cost in GBP'] * 100);
        $productImportRules = !(((int)$product['Stock'] < 10 && $costProduct < 5 * 100) || $costProduct > 1000 * 100);

        return $productImportRules;
    }
}
