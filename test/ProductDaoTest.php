<?php
/**
 * Created by PhpStorm.
 * User: Jay
 * Date: 2014.11.12.
 * Time: 22:26
 */

use Src\Product;
use Src\NullProduct;
use Src\ProductDao;

class ProductDaoTest extends PHPUnit_Framework_TestCase {
    /**
     * @var ProductDao
     */
    protected $productDao;

    protected function setUp()
    {
        $pdo = $this->getTestDataBase();


        $testProductData = $this->getTestProductData();
        $this->productDao = new ProductDao($pdo);

        $this->productDao->create($testProductData);
    }

    public function getTestDataBase()
    {
        $dsn = sprintf("sqlite:%s", '././test_product.db');
        $pdo = new \PDO($dsn);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


        $sth =$pdo->prepare("DROP TABLE IF EXISTS product");
        $sth->execute();
        $sth =$pdo->prepare("CREATE TABLE IF NOT EXISTS product ( id INTEGER PRIMARY KEY, ean varchar(64) default '', name text default '' );");
        $sth->execute();

        return $pdo;
    }

    public function getTestProductData()
    {
        $product = new Product();
        $product->id = 1;
        $product->ean = 'abc';
        $product->name = 'John';

        return $product;
    }

    public function testCreate()
    {
        $product = new Product();
        $product->id = 2;
        $product->ean = 'ebx';
        $product->name = 'Havas';

        $this->productDao->create($product);
        $productFromQuery = $this->productDao->getById($product->id);

        $this->assertEquals($product->id, $productFromQuery->id);
    }

    public function testGetByEan()
    {
        $product = $this->getTestProductData();
        $productFromQuery = $this->productDao->getByEan($product->ean);

        $this->assertEquals($product->name, $productFromQuery->name);
    }

    public function testGetById()
    {
        $product = $this->getTestProductData();
        $productFromQuery = $this->productDao->getByEan($product->ean);

        $this->assertEquals($product->name, $productFromQuery->name);
    }
    public function testModify()
    {
        $product = $this->getTestProductData();
        $product->name = 'Alfonz';
        $product->ean  = 'Szipuaaaaa';
        $this->productDao->modify($product);

        $productFromQuery = $this->productDao->getById($product->id);

        $this->assertEquals($product->name, $productFromQuery->name);
        $this->assertEquals($product->ean, $productFromQuery->ean);
    }
    public function testDelete()
    {
        $product = $this->getTestProductData();
        $this->productDao->delete($product);
        $productFromQuery = $this->productDao->getById($product->id);
        $nullProduct      = new NullProduct();

        $this->assertEquals(get_class($nullProduct), get_class($productFromQuery));
    }
}
