<?php
require_once '../Database.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM Categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'];
    $stmt = $pdo->prepare("INSERT INTO Categories (name) VALUES (:name)");
    $stmt->execute(['name' => $name]);
    echo json_encode(['message' => 'Category created successfully']);
} elseif ($method === 'DELETE') {
    $category_id = $_GET['category_id'];
    $stmt = $pdo->prepare("DELETE FROM Categories WHERE category_id = :category_id");
    $stmt->execute(['category_id' => $category_id]);
    echo json_encode(['message' => 'Category deleted successfully']);
}
?>
