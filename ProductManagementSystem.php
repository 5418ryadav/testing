<?php

// Base Product class
class Product
{
    protected $id;
    protected $name;
    protected $price;
    protected $category;

    public function __construct($id, $name, $price, $category)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }
}

// Inherited class for a specific type of product (e.g., electronics)
class Electronics extends Product
{
    private $warrantyPeriod;

    public function __construct($id, $name, $price, $category, $warrantyPeriod)
    {
        parent::__construct($id, $name, $price, $category);
        $this->warrantyPeriod = $warrantyPeriod;
    }

    public function getWarrantyPeriod()
    {
        return $this->warrantyPeriod;
    }

    public function setWarrantyPeriod($warrantyPeriod)
    {
        $this->warrantyPeriod = $warrantyPeriod;
    }
}

// Product Repository to handle product management
class ProductRepository
{
    private $products = [];

    public function addProduct(Product $product)
    {
        $this->products[] = $product;
    }

    public function getProductById($id)
    {
        foreach ($this->products as $product) {
            if ($product->getId() === $id) {
                return $product;
            }
        }
        return null;
    }

    public function removeProductById($id)
    {
        foreach ($this->products as $key => $product) {
            if ($product->getId() === $id) {
                unset($this->products[$key]);
                $this->products = array_values($this->products); // Reindex array
                return true;
            }
        }
        return false;
    }

    public function listProducts()
    {
        return $this->products;
    }

    public function sortProductsBy($property)
    {
        usort($this->products, function($a, $b) use ($property) {
            return $a->{$property}() <=> $b->{$property}();
        });
    }

    public function filterProductsByCategory($category)
    {
        return array_filter($this->products, function($product) use ($category) {
            return $product->getCategory() === $category;
        });
    }
}

// Product Manager for user interaction
class ProductManager
{
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createProduct($type, $id, $name, $price, $category, $additionalAttributes = [])
    {
        switch ($type) {
            case 'electronics':
                $product = new Electronics($id, $name, $price, $category, $additionalAttributes['warrantyPeriod']);
                break;
            default:
                $product = new Product($id, $name, $price, $category);
                break;
        }
        $this->repository->addProduct($product);
    }

    public function displayProducts()
    {
        $products = $this->repository->listProducts();
        foreach ($products as $product) {
            echo "ID: " . $product->getId() . "\n";
            echo "Name: " . $product->getName() . "\n";
            echo "Price: " . $product->getPrice() . "\n";
            echo "Category: " . $product->getCategory() . "\n";
            if ($product instanceof Electronics) {
                echo "Warranty: " . $product->getWarrantyPeriod() . " years\n";
            }
            echo "------------------------------------\n";
        }
    }

    public function displayProductsByCategory($category)
    {
        $products = $this->repository->filterProductsByCategory($category);
        foreach ($products as $product) {
            echo "ID: " . $product->getId() . "\n";
            echo "Name: " . $product->getName() . "\n";
            echo "Price: " . $product->getPrice() . "\n";
            echo "Category: " . $product->getCategory() . "\n";
            if ($product instanceof Electronics) {
                echo "Warranty: " . $product->getWarrantyPeriod() . " years\n";
            }
            echo "------------------------------------\n";
        }
    }

    public function updateProductPrice($id, $newPrice)
    {
        $product = $this->repository->getProductById($id);
        if ($product) {
            $product->setPrice($newPrice);
            echo "Price updated for product ID " . $id . "\n";
        } else {
            echo "Product not found.\n";
        }
    }

    public function deleteProduct($id)
    {
        if ($this->repository->removeProductById($id)) {
            echo "Product ID " . $id . " removed successfully.\n";
        } else {
            echo "Product not found.\n";
        }
    }
}

// Usage example
$repository = new ProductRepository();
$manager = new ProductManager($repository);

// Adding products
$manager->createProduct('default', 1, 'Laptop', 1000, 'electronics');
$manager->createProduct('electronics', 2, 'Smartphone', 800, 'electronics', ['warrantyPeriod' => 2]);
$manager->createProduct('default', 3, 'Desk', 200, 'furniture');

// Display all products
echo "All Products:\n";
$manager->displayProducts();

// Sort products by price
$repository->sortProductsBy('getPrice');
echo "\nProducts sorted by price:\n";
$manager->displayProducts();

// Filter products by category
echo "\nElectronics Products:\n";
$manager->displayProductsByCategory('electronics');

// Update product price
echo "\nUpdating price of product ID 1:\n";
$manager->updateProductPrice(1, 950);
$manager->displayProducts();

// Delete a product
echo "\nDeleting product ID 3:\n";
$manager->deleteProduct(3);
$manager->displayProducts();

?>