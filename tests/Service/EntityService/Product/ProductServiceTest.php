<?php

namespace App\Tests\Service\EntityService\Product;

use App\Service\EntityService\Product\ProductService;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductServiceTest extends KernelTestCase {
    /**
     * @var array
     */
    private array $newCorrectProduct;

    /**
     * @var array
     */
    private array $newIncorrectProject;

    /**
     * @var ProductService|MockObject
     */
    private MockObject|ProductService $productServiceMock;

    /**
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * @var MockObject|ConstraintViolationListInterface
     */
    private MockObject|ConstraintViolationListInterface $violationsMock;

    protected function setUp(): void {
        $this->productServiceMock = $this->createPartialMock(ProductService::class, ['getRepository', 'getEntityManager']);
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $this->productService = new ProductService($validatorMock);
        $this->violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $validatorMock
            ->method('validate')
            ->willReturn($this->violationsMock);

        $this->newCorrectProduct = [
            'Product Name' => 'test',
            'Product Code' => '001test',
            'Product Description' => 'about test',
            'Stock' => '234',
            'Cost in GBP' => '123',
            'Discontinued' => ''
        ];

        $this->newIncorrectProject = [
            'Incorrect field' => 'test',
            'Product Code' => '001test',
            'Product Description' => 'about test',
            'Stock' => '234',
            'Cost in GBP' => '123',
            'Discontinued' => ''
        ];
    }

    public function testCreateIncorrect(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->productServiceMock->createOrUpdate($this->newIncorrectProject);
    }

    public function testGetIncorrectItemIsValid(): void {
        $this->violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(3);
        $violations = $this->productService->getItemIsValid($this->newIncorrectProject);
        self::assertNotCount(0, $violations);
    }

    public function testCorrectItemIsValid(): void {
        $this->violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(0);
        $violations = $this->productService->getItemIsValid($this->newCorrectProduct);
        self::assertCount(0, $violations);
    }

    public function testGetItemRulesIsValid(): void {
        $this->violationsMock->expects($this->once())
            ->method('count')
            ->willReturn(0);
        $violations = $this->productService->getItemRulesIsValid($this->newCorrectProduct);
        self::assertCount(0, $violations);
    }
}
