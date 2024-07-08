
<?php
// the php code for the featured products, javascript will fetech this information (in the index.html) 
// Include the database connection file
session_start();
require 'Database.php';

// Query to fetch featured products
$sql = 'SELECT p.product_id, p.name, p.description, p.price, p.image_url 
        FROM FeaturedProducts fp
        JOIN Products p ON fp.product_id = p.product_id
        WHERE fp.featured_from <= NOW() AND (fp.featured_to IS NULL OR fp.featured_to >= NOW())';

try {
    $stmt = $pdo->query($sql);
    $featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set the header to indicate JSON response
    header('Content-Type: application/json');
    echo json_encode($featuredProducts);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch featured products']);
}
?>
