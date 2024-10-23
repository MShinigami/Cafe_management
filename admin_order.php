<?php
// Connect to the database
include 'db.php'; // Assuming this file has your database connection logic

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phoneno = $_POST['phoneno'];
    $tableno = $_POST['tableno'];

    // Check if customer already exists
    $sql = "SELECT id FROM customer WHERE email = ? OR phoneno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $phoneno);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Customer exists, fetch customer ID
        $stmt->bind_result($customer_id);
        $stmt->fetch();
    } else {
        // Customer doesn't exist, insert into customer table
        $sql = "INSERT INTO customer (name, email, phoneno) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $phoneno);
        $stmt->execute();
        $customer_id = $stmt->insert_id; // Get the inserted customer ID
    }
    $stmt->close();

    // Allocate table by updating the `tables` table
    $sql = "UPDATE tables SET customer_id = ?, status = 'occupied' WHERE tableno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customer_id, $tableno);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Table allocation successful, redirect to customer_order.php
        header("Location: process_order.php?customer_id=$customer_id&tableno=$tableno");
        exit;
    } else {
        echo "Error in table allocation.";
    }
    $stmt->close();
}
?>

<!-- HTML form to take customer details -->
<form method="post" action="admin_order.php">
    <label for="name">Customer Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Customer Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="phoneno">Phone Number:</label>
    <input type="text" id="phoneno" name="phoneno" required>

    <label for="tableno">Table Number:</label>
    <input type="number" id="tableno" name="tableno" required>

    <button type="submit">Allocate Table and Take Order</button>
</form>
