<?php
// Database configuration
$host = 'localhost'; // or your host
$db_name = 'cafe_db';
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare the stored procedure call
    $stmt = $conn->prepare("CALL AggregateOrders()");

    // Execute the stored procedure
    $stmt->execute();

    // Fetch the results if needed
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array

    // Optionally process the results
    foreach ($results as $row) {
        // Example of processing: Print or store in a session, etc.
        echo "Customer ID: " . $row['customer_id'] . " - Total Amount: " . $row['total_order_amount'] . " - Total Orders: " . $row['total_orders'] . "<br>";
    }
    
} catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
} finally {
    // Close the connection
    $conn = null;
}

// Redirect back to customer_add_order.php
header("Location: customer_add_order.php");
exit(); // Always call exit after header redirection to stop further execution
?>
