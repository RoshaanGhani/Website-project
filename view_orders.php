<?php
session_start();
require 'Database.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;

if ($isLoggedIn) {
    try {
        // Prepare SQL statement to fetch order items
        $stmt = $pdo->prepare('
        SELECT oi.order_item_id, oi.order_id, oi.product_id, oi.quantity, oi.price, oi.created_at, p.name AS product_name, p.description
        FROM OrderItems oi
        INNER JOIN Products p ON oi.product_id = p.product_id
        WHERE oi.order_id IN (
            SELECT order_id
            FROM Orders
            WHERE user_id = ?
        )
        ORDER BY oi.created_at DESC
        ');
        $stmt->execute([$user_id]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    $orderItems = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="Previous orders.css">
    <style>
        /* Popup styling */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #ffe5b4;
            padding: 20px;
            border: 2px solid #006400;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .popup button {
            background-color: #006400;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup button:hover {
            background-color: #004d00;
        }
    </style>
</head>
<body>
    <header>
        <h1>Previous Orders</h1>
    </header>
    <main>
        <?php if ($isLoggedIn && !empty($orderItems)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Order Item ID</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item) : ?>
                        <tr>
                            <td><?php echo $item['order_item_id']; ?></td>
                            <td><?php echo $item['product_name']; ?></td>
                            <td><?php echo $item['description']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo $item['price']; ?></td>
                            <td><?php echo $item['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </main>

    <!-- Popup for not logged in -->
    <div id="login-popup" class="popup">
        <p>You must be logged in to view your orders.</p>
        <button id="login-btn">Log In</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
            
            if (!isLoggedIn) {
                const loginPopup = document.getElementById('login-popup');
                const loginBtn = document.getElementById('login-btn');
                
                loginPopup.style.display = 'block';
                
                loginBtn.addEventListener('click', function() {
                    window.location.href = 'login.html';
                });
            }
        });
    </script>
</body>
</html>
