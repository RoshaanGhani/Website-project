<!-- for admin panel -->

<?php
require 'Database.php';

header('Content-Type: application/json');

$orderId = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$stmt = $pdo->prepare("SELECT * FROM OrderItems WHERE order_id = :order_id");
$stmt->execute(['order_id' => $orderId]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
