<?php
session_start();
require 'Database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$email = $_POST['email'] ?? '';
$number = $_POST['number'] ?? '';
$street_address = $_POST['street_address'] ?? '';
$area_id = $_POST['area_id'] ?? 0;

$errors = [];

// Validate email
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

// Validate phone number
if (!empty($number) && !preg_match('/^\d{10}$/', $number)) {
    $errors[] = 'Phone number must be exactly 10 digits.';
}

if (empty($errors)) {
    try {
        // Build the SQL query dynamically based on provided fields
        $sql = 'UPDATE Users SET ';
        $params = [];

        if (!empty($email)) {
            $sql .= 'email = :email, ';
            $params[':email'] = $email;
        }

        if (!empty($number)) {
            $sql .= 'number = :number, ';
            $params[':number'] = $number;
        }

        if (!empty($street_address)) {
            $sql .= 'street_address = :street_address, ';
            $params[':street_address'] = $street_address;
        }

        if (!empty($area_id)) {
            $sql .= 'area_id = :area_id, ';
            $params[':area_id'] = $area_id;
        }

        // Remove the trailing comma and space
        $sql = rtrim($sql, ', ');

        $sql .= ' WHERE user_id = :user_id';

        $params[':user_id'] = $user_id;

        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo '<script>alert("Profile updated successfully"); window.location.href = "edit_profile.php";</script>';

    } catch (PDOException $e) {
        echo '<script>alert("Error: ' . $e->getMessage() . '"); window.history.back();</script>';
    }
} else {
    $errorMsg = implode("\\n", $errors);
    echo '<script>alert("' . htmlspecialchars($errorMsg) . '"); window.history.back();</script>';
}
?>
