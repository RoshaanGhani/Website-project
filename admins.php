<?php
require_once '../Database.php';
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['email'], $data['password'], $data['full_name'])) {
            throw new Exception("Invalid input");
        }
        $email = $data['email'];
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $full_name = $data['full_name'];
        $stmt = $pdo->prepare("INSERT INTO AdminUsers (email, password_hash, full_name) VALUES (:email, :password_hash, :full_name)");
        $stmt->execute([
            'email' => $email,
            'password_hash' => $password_hash,
            'full_name' => $full_name
        ]);
        echo json_encode(['message' => 'Admin created successfully']);
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server Error', 'error' => $e->getMessage()]);
}
?>
