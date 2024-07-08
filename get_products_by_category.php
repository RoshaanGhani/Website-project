<?php
// Include the database connection file
require 'Database.php';

// Get the category_id from the query string
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

if ($category_id > 0) {
    // Query to fetch products for the given category
    $sql = 'SELECT product_id, name, description, price, image_url 
            FROM Products 
            WHERE category_id = :category_id';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Set the header to indicate JSON response
        header('Content-Type: application/json');
        echo json_encode($products);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch products']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid category ID']);
}
?>
