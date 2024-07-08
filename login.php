<?php
session_start();
require 'Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email_or_phone = $input['email_or_phone'];
    $password = $input['password'];

    try {
        // Check if the email exists in the AdminUsers table
        $adminSql = 'SELECT admin_id, password_hash FROM AdminUsers WHERE email = :email';
        $adminStmt = $pdo->prepare($adminSql);
        $adminStmt->bindParam(':email', $email_or_phone, PDO::PARAM_STR);
        $adminStmt->execute();
        $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            echo json_encode(['success' => true, 'admin' => true, 'message' => 'Admin login successful']);
            exit;
        }

        // Check if the input is an email or phone number
        if (filter_var($email_or_phone, FILTER_VALIDATE_EMAIL)) {
            $sql = 'SELECT user_id, password_hash FROM Users WHERE email = :email_or_phone';
        } else {
            $sql = 'SELECT user_id, password_hash FROM Users WHERE number = :email_or_phone';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email_or_phone', $email_or_phone, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            echo json_encode(['success' => true, 'admin' => false, 'message' => 'Login successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email/phone or password']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to process login', 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
