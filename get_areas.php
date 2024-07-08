<?php
require 'Database.php';

try {
    $sql = 'SELECT area_name FROM DeliveryAreas';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['areas' => $areas]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch areas']);
}
?>
