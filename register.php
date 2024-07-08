<?php
session_start();
require 'Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $full_name = $input['full_name'];
    $email = $input['email'];
    $password = $input['password'];
    $confirm_password = $input['confirm_password'];
    $street_address = $input['street_address'];
    $number = $input['number'];
    $area_name = $input['area_name'];

    // Validate password
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long, contain at least one capital letter and one number']);
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Validate phone number
    if (!preg_match('/^\d{10}$/', $number)) {
        echo json_encode(['success' => false, 'message' => 'Phone number must be 10 digits long and contain only numbers']);
        exit;
    }

    // Check if the area exists in the DeliveryAreas table
    $sql = 'SELECT area_id FROM DeliveryAreas WHERE area_name = :area_name';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':area_name', $area_name, PDO::PARAM_STR);
    $stmt->execute();
    $area = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$area) {
        echo json_encode(['success' => false, 'message' => 'Entered area is not in the delivery areas']);
        exit;
    }

    $area_id = $area['area_id'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $sql = 'INSERT INTO Users (email, password_hash, full_name, street_address, number, area_id) VALUES (:email, :password_hash, :full_name, :street_address, :number, :area_id)';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
        $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
        $stmt->bindParam(':street_address', $street_address, PDO::PARAM_STR);
        $stmt->bindParam(':number', $number, PDO::PARAM_STR);
        $stmt->bindParam(':area_id', $area_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to register user']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
