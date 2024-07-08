<?php
session_start();
require 'Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if ($productId !== false && $quantity !== false) {
            $dsn = 'mysql:host=localhost;dbname=g4gas';
            $username = 'root';
            $password = '';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                $pdo = new PDO($dsn, $username, $password, $options);

                if ($quantity > 0) {
                    $stmt = $pdo->prepare("UPDATE CartItems SET quantity = :quantity WHERE product_id = :product_id AND cart_id = (SELECT cart_id FROM ShoppingCart WHERE user_id = :user_id)");
                    $stmt->execute(['quantity' => $quantity, 'product_id' => $productId, 'user_id' => $_SESSION['user_id']]);
                } else {
                    $stmt = $pdo->prepare("DELETE FROM CartItems WHERE product_id = :product_id AND cart_id = (SELECT cart_id FROM ShoppingCart WHERE user_id = :user_id)");
                    $stmt->execute(['product_id' => $productId, 'user_id' => $_SESSION['user_id']]);
                }

                $response = ['success' => true];
                echo json_encode($response);
                exit();
            } catch (PDOException $e) {
                $response = ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
                echo json_encode($response);
                exit();
            }
        } else {
            $response = ['success' => false, 'error' => 'Invalid input'];
            echo json_encode($response);
            exit();
        }
    } else {
        $response = ['success' => false, 'error' => 'Missing parameters'];
        echo json_encode($response);
        exit();
    }
} else {
    http_response_code(405);
}
?>
