<?php
// Include the database connection file
require 'Database.php';

// Query to fetch all categories
$sql = 'SELECT category_id, name FROM Categories';

try {
    $stmt = $pdo->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set the header to indicate JSON response
    header('Content-Type: application/json');
    echo json_encode($categories);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch categories']);
}

?>
