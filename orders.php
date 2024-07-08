<?php
require_once '../Database.php';
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM Orders");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($orders);
    } elseif ($method === 'DELETE') {
        $order_id = $_GET['order_id'];
        $stmt = $pdo->prepare("DELETE FROM Orders WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $order_id]);
        echo json_encode(['message' => 'Order deleted successfully']);
    } elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        $order_id = $_GET['order_id'];
        $status = $data['status'];
        $stmt = $pdo->prepare("UPDATE Orders SET status = :status WHERE order_id = :order_id");
        $stmt->execute([
            'status' => $status,
            'order_id' => $order_id
        ]);
        echo json_encode(['message' => 'Order status updated successfully']);
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server Error', 'error' => $e->getMessage()]);
}
?>
