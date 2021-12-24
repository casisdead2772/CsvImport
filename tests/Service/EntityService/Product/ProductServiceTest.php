<?php

namespace App\Tests\Service\EntityService\Product;

use App\Entity\Product;
use App\Service\EntityService\Product\ProductService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductServiceTest extends KernelTestCase {
    /**
     * @var ObjectRepository
     */
    private ObjectRepository $productRepository;
    /**
     * @var ProductService|object|null
     */
    private $productService;
    /**
     * @var string[]
     */
    private array $newCorrectProduct;
    /**
     * @var string[]
     */
    private array $newIncorrectProject;
    private ObjectManager $entityManager;

    protected function setUp(): void {
        $this->productService = static::getContainer()->get(ProductService::class);
        $this->productRepository = static::getContainer()
            ->get('doctrine')
            ->getRepository(Product::class);
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
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->entityManager->beginTransaction();
    }

    public function testCreateOrUpdate(): void {
        $this->productService->createOrUpdate($this->newCorrectProduct);
        $product = $this->productRepository->findOneBy(['code' => $this->newCorrectProduct['Product Code']]);

        self::assertIsObject($product);
        self::assertEquals('test', $product->getName());
        self::assertEquals('001test', $product->getCode());
        self::assertEquals(234, $product->getStock());
        self::assertEquals('about test', $product->getDescription());
    }

    public function testCreateIncorrect(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->productService->createOrUpdate($this->newIncorrectProject);
    }

    public function testGetIncorrectItemIsValid(): void {
        $violations = $this->productService->getItemIsValid($this->newIncorrectProject);
        self::assertNotCount(0, $violations);
    }

    public function testCorrectItemIsValid(): void {
        $violations = $this->productService->getItemIsValid($this->newCorrectProduct);
        self::assertCount(0, $violations);
    }

    protected function tearDown(): void {
        $this->entityManager->getConnection()->rollback();
        parent::tearDown();
    }
}
