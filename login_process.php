<?php
session_start();
include 'db.php';  // Connect to your database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];  // Determine if it's admin or customer login

    // Check for empty fields
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "All fields are required.";
        header("Location: login.php");
        exit();
    }

    // SQL query and error message initialization
    $sql = "";
    $error_message = "";

    if ($role == 'admin') {
        $sql = "SELECT * FROM admin WHERE email = ?";
        $error_message = "No admin found with that email.";
    } else if ($role == 'customer') {
        $sql = "SELECT * FROM customer WHERE email = ?";
        $error_message = "No customer found with that email.";
    } else {
        $_SESSION['login_error'] = "Invalid user role.";
        header("Location: login.php");
        exit();
    }

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Set session variables based on user role
            if ($role == 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_email'] = $row['email'];
                header("Location: admin_dashboard.php");  // Redirect to admin dashboard
            } else if ($role == 'customer') {
                $_SESSION['customer_logged_in'] = true;
                $_SESSION['customer_id'] = $row['id']; // Store customer ID in session
                $_SESSION['customer_email'] = $row['email'];
                header("Location: customer_dashboard.php");  // Redirect to customer dashboard
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Incorrect password.";
        }
    } else {
        $_SESSION['login_error'] = $error_message;
    }

    // Clean up
    $stmt->close();
    $conn->close();

    // Redirect back to the login page if login failed
    header("Location: login.php");
    exit();
}
?>
