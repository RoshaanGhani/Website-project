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

    if (empty($cart_items)) {
        echo json_encode(['error' => 'Cart is empty']);
        exit();
    }

    // Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Fetch the user details
    $user_stmt = $pdo->prepare('SELECT area_id, full_name, email, street_address, number FROM Users WHERE user_id = ?');
    $user_stmt->execute([$user_id]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !isset($user['area_id'])) {
        echo json_encode(['error' => 'User area information not found']);
        exit();
    }

    $area_id = $user['area_id'];

    // Start a transaction
    $pdo->beginTransaction();

    // Insert order into Orders table
    $order_stmt = $pdo->prepare('
        INSERT INTO Orders (user_id, total_amount, status, area_id) 
        VALUES (?, ?, ?, ?)
    ');
    $order_status = 'pending';
    $order_stmt->execute([$user_id, $total_amount, $order_status, $area_id]);
    $order_id = $pdo->lastInsertId();

    // Insert order items into OrderItems table
    $order_item_stmt = $pdo->prepare('
        INSERT INTO OrderItems (order_id, product_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ');

     // Prepare statement to update stock quantity
     $update_stock_stmt = $pdo->prepare('
     UPDATE Products 
     SET stock_quantity = stock_quantity - ? 
     WHERE product_id = ? AND stock_quantity >= ?
 ');

 foreach ($cart_items as $item) {
     // Insert order item
     $order_item_stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
     
     // Update product stock quantity
     $update_stock_stmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);

     // Check if stock update was successful
     if ($update_stock_stmt->rowCount() == 0) {
         // Rollback the transaction and return an error if stock update failed
         $pdo->rollBack();
         echo json_encode(['error' => 'Insufficient stock for product: ' . $item['name']]);
         exit();
     }
 }

    // Commit the transaction
    $pdo->commit();

    // Hard-code the admin email
    $admin_email = 'ghaniroshaan@gmail.com';

    // Prepare email data
    $email_data = [
        'order_id' => $order_id,
        'user_name' => $user['full_name'],
        'user_email' => $user['email'],
        'user_address' => $user['street_address'],
        'user_phone' => $user['number'],
        'total_amount' => $total_amount,
        'area_id' => $area_id,
        'admin_email' => $admin_email,
        'cart_items' => json_encode($cart_items) // Include cart items in the email data
    ];

    // Return success response with email data
    echo json_encode(['success' => 'Order processed successfully', 'email_data' => $email_data]);
} catch (PDOException $e) {
    // Rollback the transaction in case of error
    $pdo->rollBack();
    echo json_encode(['error' => 'Failed to process order: ' . $e->getMessage()]);
}
?>
