<?php
session_start();
require 'Database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Prepare SQL statement to fetch cart items
    $stmt = $pdo->prepare('
        SELECT p.product_id, p.name, p.description, p.price, p.image_url, ci.quantity
        FROM CartItems ci
        JOIN Products p ON ci.product_id = p.product_id
        JOIN ShoppingCart sc ON ci.cart_id = sc.cart_id
        WHERE sc.user_id = ?
    ');
    // Execute the statement with the user ID
    $stmt->execute([$user_id]);
    // Fetch all cart items
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return cart items as JSON response
    echo json_encode($cart_items);
} catch (PDOException $e) {
    // Handle any errors during the database query
    echo json_encode(['error' => 'Failed to fetch cart items']);
}
?>
