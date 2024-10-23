<?php
// Include your database connection file here
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $menu_name = $_POST['menu_name'];
    $quantity = $_POST['quantity'];
    $amount = $_POST['amount'];
    $tableno = $_POST['tableno'];
    $customer_id = 1; // Replace with the actual logged-in customer ID

    // Insert order into the database
    $query = "INSERT INTO orders (customer_id, menu_name, amount, quantity, tableno) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iisii', $customer_id, $menu_name, $amount, $quantity, $tableno);

    if (mysqli_stmt_execute($stmt)) {
        echo "Order placed successfully!";
    } else {
        echo "Error placing order: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Close the database connection
mysqli_close($conn);
?>
