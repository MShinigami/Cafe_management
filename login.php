<?php
session_start();

// If admin or customer is already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}
if (isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in'] === true) {
    header("Location: customer_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            background-image: url('assets/img/bg_admin_login.jpg'); /* Background image */
            background-size: cover; /* Cover the whole page */
            background-position: center; /* Center the image */
            background-attachment: fixed; /* Ensure the background stays fixed */
            background-repeat: no-repeat;
            height: 100vh; /* Full viewport height */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
            color: #fff; /* Text color to contrast with the background */
        }

        /* Login form styling */
        .login-form {
            background-color: rgba(255, 255, 255, 0.7); /* White background with opacity */
            border-radius: 10px;
            opacity: 0.9;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            padding: 40px 60px;
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: black;
        }

        /* Error message styling */
        .error {
            color: #d9534f;
            background-color: #f2dede;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }

        /* Input fields styling */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
            transition: 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Button styling */
        button,
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

        button:hover,
        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        button:active,
        input[type="submit"]:active {
            background-color: #00408a;
        }

        /* Radio button styling */
        .role-selection {
            margin: 20px 0;
            text-align: center;
        }

        .role-selection input {
            margin-right: 10px;
        }

        .role-selection label {
            color: black; /* Set label text color to black */
            font-size: 16px;
        }

         /* Registration link styling */
         .register-link {
            margin-top: 20px;
            text-align: center;
            color: black;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-form">
    <h2>Login</h2>

    <!-- Display error message if any -->
    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="error"><?php echo htmlspecialchars($_SESSION['login_error']); ?></div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
    
    <!-- Login form -->
    <form action="login_process.php" method="POST">
        <input type="text" name="email" placeholder="Enter email" required>
        <input type="password" name="password" placeholder="Enter Password" required autocomplete="off">

        <div class="role-selection">
            <label>
                <input type="radio" name="role" value="admin" required> Admin
            </label>
            <label>
                <input type="radio" name="role" value="customer" required> Customer
            </label>
        </div>

        <input type="submit" value="Login">
    </form>

    <!-- Register link -->
    <div class="register-link">
        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </div>

</div>

</body>
</html>
