<?php
session_start();
require 'Database.php';

$data = json_decode(file_get_contents('php://input'), true);
$product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;

if ($product_id > 0) {
    try {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'User not logged in']);
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Check if cart exists for the user
        $sql = 'SELECT cart_id FROM ShoppingCart WHERE user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            // Create new cart for the user if it doesn't exist
            $sql = 'INSERT INTO ShoppingCart (user_id) VALUES (:user_id)';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $cart_id = $pdo->lastInsertId();
        } else {
            $cart_id = $cart['cart_id'];
        }

        // Check if product is already in the cart
        $sql = 'SELECT cart_item_id, quantity FROM CartItems WHERE cart_id = :cart_id AND product_id = :product_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart_item) {
            // Update quantity if product is already in the cart
            $new_quantity = $cart_item['quantity'] + 1;
            $sql = 'UPDATE CartItems SET quantity = :quantity WHERE cart_item_id = :cart_item_id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
            $stmt->bindParam(':cart_item_id', $cart_item['cart_item_id'], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Insert new product into the cart
            $sql = 'INSERT INTO CartItems (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, 1)';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add product to cart']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
}
?>
