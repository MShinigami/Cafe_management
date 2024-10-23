<?php
// Include your database connection file here
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];

    // Delete the order from the database
    $query = "DELETE FROM orders WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Order deleted successfully!";
    } else {
        echo "Error deleting order: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Close the database connection
mysqli_close($conn);
?>
