<?php
session_start(); // Start the session

// Retrieve the logged-in customer ID from the session
$customer_id = $_SESSION['customer_id'] ?? null; // Use null coalescing operator to avoid warnings

if ($customer_id === null) {
    // If customer_id is not set, handle the error
    echo "Invalid customer ID.";
    exit(); // Stop execution if ID is invalid
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe_db";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement to call the stored procedure for bill payment
$stmt = $conn->prepare("CALL Bill_Pay(?)");
$stmt->bind_param("i", $customer_id); // Bind the customer_id as an integer

// Execute the statement
if ($stmt->execute()) {
    // Check how many rows were affected
    if ($stmt->affected_rows > 0) {
        // After successful bill payment, call the completedOB procedure
        if (!$conn->query("CALL completedOB()")) {
            // Handle any errors that occur during the procedure call
            echo "An error occurred while cleaning up completed orders and bills: " . $conn->error;
            $stmt->close();
            $conn->close();
            exit();
        }

        // Redirect to feedback.php after successful execution of both procedures
        header("Location: feedback.php");
        exit(); // Always call exit after a redirect
    } else {
        echo "No changes made. The status was already updated or the customer_id does not exist.";
    }
} else {
    // Handle error
    echo "An error occurred: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
