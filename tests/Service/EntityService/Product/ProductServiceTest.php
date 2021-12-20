<?php

namespace App\Tests\Service\EntityService\Product;

use App\Entity\Product;
use App\Service\EntityService\Product\ProductService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductServiceTest extends KernelTestCase {
    /**
     * @return void
     */
    public function testCreateOrUpdate(): void {
        $newProject = [
            'Product Name' => 'test',
            'Product Code' => '001test',
            'Product Description' => 'about test',
            'Stock' => '234',
            'Cost in GBP' => '123',
            'Discontinued' => ''
        ];
        $productService = static::getContainer()->get(ProductService::class);
        $productRepository = static::getContainer()->get('doctrine')->getRepository(Product::class);
        $productService->createOrUpdate($newProject);
        $product = $productRepository->findOneBy(['code' => $newProject['Product Code']]);

        self::assertIsObject($product);
        self::assertEquals('test', $product->getName());
        self::assertEquals('001test', $product->getCode());
        self::assertEquals(234, $product->getStock());
        self::assertEquals('about test', $product->getDescription());
    }
}
