<?php
// Include your database connection file here
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $quantity = $_POST['quantity'];
    $tableno = $_POST['tableno'];

    // Fetch the menu item price to recalculate the amount
    $query = "SELECT amount FROM menu WHERE id = (SELECT menu_name FROM orders WHERE id = ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $amount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Calculate the new total amount
    $new_amount = $amount * $quantity;

    // Update the order in the database
    $update_query = "UPDATE orders SET quantity = ?, amount = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, 'idi', $quantity, $new_amount, $order_id);

    if (mysqli_stmt_execute($update_stmt)) {
        echo "Order updated successfully!";
    } else {
        echo "Error updating order: " . mysqli_error($conn);
    }

    mysqli_stmt_close($update_stmt);
}

// Close the database connection
mysqli_close($conn);
?>
