<?php
include 'db.php';  // Ensure the connection to the database is established
session_start();  // Start the session for session management

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate if all expected keys exist in $_POST
    if (isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['password'])) {
        // Sanitize inputs
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $password = htmlspecialchars(trim($_POST['password']));

        // 1. Check if the customer already exists based on email
        $stmt = $conn->prepare("SELECT id FROM customer WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // If the customer already exists, set an error message
            $_SESSION['error_message'] = "Customer already exists with this email.";
        } else {
            // Hash the password using password_hash()
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new customer into the customer table
            $stmt = $conn->prepare("INSERT INTO customer (name, email, password, phoneno) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);

            if ($stmt->execute()) {
                // Successful registration
                $_SESSION['success_message'] = "Customer registered successfully!";
                // Optionally store the new customer ID in session
                $_SESSION['customer_id'] = $stmt->insert_id;  // Get the ID of the newly inserted customer
                // Redirect to login.php after successful registration
                header("Location: login.php");
                exit();  // Make sure to call exit() after header redirection
            } else {
                // Error while registering
                $_SESSION['error_message'] = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <style>
        /* General reset and styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        /* Background styling */
        body {
            background-image: url('assets/img/bg_dashboard.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 40px 60px;
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        h2, label, input {
            color: black;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
            color: black;
            transition: 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .error-message {
            color: #d9534f;
            background-color: #f2dede;
            padding: 10px;
            border-radius: 5px;
        }

        .success-message {
            color: #28a745;
            background-color: #dff0d8;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Customer Registration</h2>

        <!-- Display success or error message -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php elseif (isset($_SESSION['success_message'])): ?>
            <div class="message success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" placeholder="Name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Email" required>

            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" placeholder="Phone" required pattern="\d{10}" title="Phone number should be 10 digits">

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Password" required>

            <input type="submit" value="Register">
        </form>
    </div>

</body>
</html>
