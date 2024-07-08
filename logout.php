<?php
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
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
    <!-- Popup for successful logout -->
    <div id="logout-popup" class="popup">
        <p>Logout was successful!</p>
        <button id="ok-btn">OK</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutPopup = document.getElementById('logout-popup');
            const okBtn = document.getElementById('ok-btn');
            
            // Show the popup
            logoutPopup.style.display = 'block';
            
            // Redirect to the login page after clicking OK
            okBtn.addEventListener('click', function() {
                window.location.href = 'login.html';
            });
            
            // Auto redirect after 3 seconds if the user does not click OK
            setTimeout(function() {
                window.location.href = 'login.html';
            }, 3000);
        });
    </script>
</body>
</html>
