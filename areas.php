<?php
require_once '../Database.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM DeliveryAreas");
        $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($areas);
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $area_name = $data['area_name'];
        $stmt = $pdo->prepare("INSERT INTO DeliveryAreas (area_name) VALUES (:area_name)");
        $stmt->execute(['area_name' => $area_name]);
        echo json_encode(['message' => 'Area created successfully']);
    } elseif ($method === 'DELETE') {
        $area_id = $_GET['area_id'];
        $stmt = $pdo->prepare("DELETE FROM DeliveryAreas WHERE area_id = :area_id");
        $stmt->execute(['area_id' => $area_id]);
        echo json_encode(['message' => 'Area deleted successfully']);
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server Error', 'error' => $e->getMessage()]);
}
?>
