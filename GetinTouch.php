<?php
// Include the database connection script
include 'database.php';

$errors = []; // Initialize an empty array for errors

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $cell = $_POST['cell'];
    $subject = $_POST['subject'];
    $description = $_POST['message'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate phone number (must be exactly 10 digits)
    if (!preg_match('/^\d{10}$/', $cell)) {
        $errors[] = "Phone number must be 10 digits";
    }

    // If there are errors, display them as pop-ups
    if (!empty($errors)) {
        echo "<script>";
        foreach ($errors as $error) {
            echo "alert('" . addslashes($error) . "');";
        }
        echo "</script>";
    } else {
        // Prepare SQL statement to insert data into the GetInTouch table
        $sql = "INSERT INTO GetInTouch (name, email, cell, subject, description) VALUES (?, ?, ?, ?, ?)";

        // Use prepared statement to prevent SQL injection
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $cell, $subject, $description]);

        // Redirect to a success page or do any other processing
        header("Location: success.html");
        exit(); // Terminate script execution
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talk To Us!</title>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Getintouch.css"> <!-- Update the path to your CSS file -->
</head>
<body>
    <section class="py-3 py-md-5 py-xl-8">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-10 col-lg-8">
                    <h3 class="fs-5 mb-2 text-secondary text-uppercase">Contact</h3>
                    <h2 class="display-5 mb-4 mb-md-5 mb-xl-8">We're always on the lookout to answer any questions for our new clients. Please get in touch in one of the following ways.</h2>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row gy-4 gy-md-5 gy-lg-0 align-items-md-center">
                <div class="col-12 col-lg-6">
                    <div class="border overflow-hidden">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="row gy-4 gy-xl-5 p-4 p-xl-5">
                                <div class="col-12">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="cell" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="cell" name="cell" required>
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="subject" name="subject" required>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button class="btn btn-dark btn-lg" type="submit">Send Message</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
  
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
