<?php
require_once '../Database.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM Products");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Server Error', 'error' => $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];
    $stock_quantity = $data['stock_quantity'];
    $category_id = $data['category_id'];
    $image_url = $data['image_url'];
    $is_featured = $data['is_featured'];

    try {
        $pdo->beginTransaction();

        // Insert into Products table
        $stmt = $pdo->prepare("INSERT INTO Products (name, description, price, stock_quantity, category_id, image_url, is_featured) VALUES (:name, :description, :price, :stock_quantity, :category_id, :image_url, :is_featured)");
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock_quantity' => $stock_quantity,
            'category_id' => $category_id,
            'image_url' => $image_url,
            'is_featured' => $is_featured,
        ]);

        $product_id = $pdo->lastInsertId();

        // If the product is marked as featured, insert into FeaturedProducts table
        if ($is_featured) {
            $stmt = $pdo->prepare("INSERT INTO FeaturedProducts (product_id, featured_from) VALUES (:product_id, NOW())");
            $stmt->execute([
                'product_id' => $product_id,
            ]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Product created successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to create product', 'error' => $e->getMessage()]);
    }
} elseif ($method === 'DELETE') {
    // Handle DELETE request for products
    $product_id = $_GET['product_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Products WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product', 'error' => $e->getMessage()]);
    }
} elseif ($method === 'PUT') {
    // Handle PUT request for updating products
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'];
    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];
    $stock_quantity = $data['stock_quantity'];
    $category_id = $data['category_id'];
    $image_url = $data['image_url'];
    $is_featured = $data['is_featured'];

    try {
        $pdo->beginTransaction();

        // Update Products table
        $stmt = $pdo->prepare("UPDATE Products SET name = :name, description = :description, price = :price, stock_quantity = :stock_quantity, category_id = :category_id, image_url = :image_url, is_featured = :is_featured WHERE product_id = :product_id");
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock_quantity' => $stock_quantity,
            'category_id' => $category_id,
            'image_url' => $image_url,
            'is_featured' => $is_featured,
            'product_id' => $product_id,
        ]);

        // If the product is marked as featured, update or insert into FeaturedProducts table
        if ($is_featured) {
            $stmt = $pdo->prepare("INSERT INTO FeaturedProducts (product_id, featured_from) VALUES (:product_id, NOW()) ON DUPLICATE KEY UPDATE featured_from = NOW()");
            $stmt->execute([
                'product_id' => $product_id,
            ]);
        } else {
            // If the product is not featured, remove it from FeaturedProducts table
            $stmt = $pdo->prepare("DELETE FROM FeaturedProducts WHERE product_id = :product_id");
            $stmt->execute(['product_id' => $product_id]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to update product', 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
