<?php
require_once '../Database.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM GetInTouch");
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($inquiries);
}
?>
