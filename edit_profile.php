<?php
session_start();
require 'Database.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;

if ($isLoggedIn) {
    try {
        // Fetch user information
        $sql = 'SELECT email, full_name, street_address, area_id, number FROM Users WHERE user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch all delivery areas
        $sql = 'SELECT area_id, area_name FROM DeliveryAreas';
        $areas_stmt = $pdo->query($sql);
        $areas = $areas_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    $user = [];
    $areas = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Edit Profile.css">
    <title>Edit Profile</title>
    <style>
        /* Popup styling */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #ffe5b4;
            padding: 20px;
            border: 2px solid #006400;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .popup button {
            background-color: #006400;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup button:hover {
            background-color: #004d00;
        }
    </style>
</head>
<body>
    <h1>Edit Profile</h1>
    <?php if ($isLoggedIn) : ?>
        <form action="update_profile.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br>

            <label for="number">Phone Number:</label>
            <input type="text" id="number" name="number" value="<?= htmlspecialchars($user['number']) ?>"><br>

            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" readonly><br>

            <label for="street_address">Street Address:</label>
            <input type="text" id="street_address" name="street_address" value="<?= htmlspecialchars($user['street_address']) ?>"><br>

            <label for="area_id">Area:</label>
            <select id="area_id" name="area_id">
                <?php foreach ($areas as $area): ?>
                    <option value="<?= $area['area_id'] ?>" <?= $area['area_id'] == $user['area_id'] ? 'selected' : '' ?>><?= htmlspecialchars($area['area_name']) ?></option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit">Update Profile</button>
        </form>
    <?php else : ?>
        <p>You must be logged in to update your profile.</p>
    <?php endif; ?>

    <!-- Popup for not logged in -->
    <div id="login-popup" class="popup">
        <p>You must be logged in to update your profile.</p>
        <button id="login-btn">Log In</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
            
            if (!isLoggedIn) {
                const loginPopup = document.getElementById('login-popup');
                const loginBtn = document.getElementById('login-btn');
                
                loginPopup.style.display = 'block';
                
                loginBtn.addEventListener('click', function() {
                    window.location.href = 'login.html';
                });
            }
        });
    </script>
</body>
</html>
