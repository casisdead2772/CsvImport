<?php

namespace App\Service\EntityService\Product;

use App\Entity\Product;
use App\Service\EntityService\BaseImportInterface;
use App\Traits\EntityManagerTrait;
use DateTime;
use InvalidArgumentException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductService implements BaseImportInterface {
    use EntityManagerTrait;

    private const PRODUCT_HEADERS = [self::PRODUCT_NAME, self::PRODUCT_CODE, self::PRODUCT_DESCRIPTION, self::STOCK, self::COST_IN_GBP, self::DISCONTINUED];

    private const PRODUCT_CODE = 'Product Code';

    private const PRODUCT_NAME = 'Product Name';

    private const PRODUCT_DESCRIPTION = 'Product Description';

    private const STOCK = 'Stock';

    private const COST_IN_GBP = 'Cost in GBP';

    private const DISCONTINUED = 'Discontinued';

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    /**
     * @param array $object
     */
    public function createOrUpdate(array $object): void {
        $this->checkItemHeaders($object);
        $productRepository = $this->getRepository(Product::class);
        $selectedProduct = $productRepository->findOneBy(['code' => $object[self::PRODUCT_CODE]]);

        if (!$selectedProduct) {
            $selectedProduct = new Product();
        }

        $selectedProduct->setName($object[self::PRODUCT_NAME]);
        $selectedProduct->setCode($object[self::PRODUCT_CODE]);
        $selectedProduct->setDescription($object[self::PRODUCT_DESCRIPTION]);
        $selectedProduct->setStock((int)$object[self::STOCK]);
        $selectedProduct->setCost((int)((float)$object[self::COST_IN_GBP] * 100));

        if ($object[self::DISCONTINUED] === 'yes' && !$selectedProduct->getDiscontinued()) {
            $selectedProduct->setDiscontinued(new DateTime('now'));
        }

        $this->getEntityManager()->persist($selectedProduct);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array
     */
    public function getItemHeaders(): array {
        return self::PRODUCT_HEADERS;
    }

    /**
     * @param array $item
     *
     * @return ConstraintViolationListInterface
     */
    public function getItemIsValid(array $item): ConstraintViolationListInterface {
        $itemConstraint = new Assert\Collection([
            self::PRODUCT_NAME => [
                new Assert\NotNull(),
                new Assert\Type(['type' => 'string']),
            ],
            self::PRODUCT_CODE => [
                new Assert\NotNull(),
                new Assert\Type(['type' => 'string']),
            ],
            self::PRODUCT_DESCRIPTION => [
                new Assert\NotNull(),
                new Assert\Type(['type' => 'string']),
            ],
            self::COST_IN_GBP => [
                new Assert\Positive(),
            ],
            self::STOCK => [
                new Assert\NotBlank(),
                new Assert\PositiveOrZero(),
                new Assert\Type(['type' => 'numeric']),
            ],
            self::DISCONTINUED => new Assert\Required()
        ]);

        return $this->validator->validate($item, $itemConstraint);
    }

    /**
     * @param array $item
     *
     * @return ConstraintViolationListInterface
     */
    public function getItemRulesIsValid(array $item): ConstraintViolationListInterface {
        $costProduct = (int)((float)$item[self::COST_IN_GBP] * 100);
        $rules = [
            'lowStockAndCost' => (int)$item[self::STOCK] < 10 && $costProduct < 5 * 100,
            'highCost' => $costProduct > 1000 * 100
        ];

        $itemConstraint = new Assert\Collection([
            'lowStockAndCost' => new Assert\IsFalse([
                'message' => 'Unsuitable leftovers and price for this product'
            ]),
            'highCost' => new Assert\IsFalse([
                'message' => 'Too high a price'
            ])
            ]);

        return $this->validator->validate($rules, $itemConstraint);
    }

    /**
     * @param $item
     */
    private function checkItemHeaders($item): void {
        $notExistingHeaders = [];

        foreach (self::PRODUCT_HEADERS as $header) {
            if (!array_key_exists($header, $item)) {
                $notExistingHeaders[] = $header;
            }
        }

        if (!empty($notExistingHeaders)) {
            throw new InvalidArgumentException('Required fields are missing');
        }
    }
}
