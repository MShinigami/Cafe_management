<?php
// Include database connection
include 'db.php'; // Adjust the path as necessary

session_start();
$bill_id = $_POST['bill_id'] ?? null; // Assuming the bill ID is posted from a form

try {
    if ($bill_id) {
        // Step 1: Fetch the customer_id based on the bill
        $stmt = $conn->prepare("SELECT customer_id FROM bill WHERE id = ?");
        $stmt->bind_param("i", $bill_id);
        $stmt->execute();
        $stmt->bind_result($customer_id);
        $stmt->fetch();
        $stmt->close();

        if ($customer_id) {
            // Step 2: Update the bill status to 'discarded'
            $stmt = $conn->prepare("UPDATE bill SET status = 'discarded' WHERE id = ?");
            $stmt->bind_param("i", $bill_id);
            $stmt->execute();

            // Step 3: Update the status of related orders to 'canceled'
            $stmt = $conn->prepare("UPDATE orders SET status = 'canceled' WHERE customer_id = ?");
            $stmt->bind_param("i", $customer_id); // Updating orders for this customer
            $stmt->execute();

            // Step 4: Delete the canceled orders related to the customer
            $stmt = $conn->prepare("DELETE FROM orders WHERE customer_id = ?");
            $stmt->bind_param("i", $customer_id);  // Delete orders based on customer_id
            $stmt->execute();

            // Step 5: Delete the canceled bill for this customer
            $stmt = $conn->prepare("DELETE FROM bill WHERE id = ?");
            $stmt->bind_param("i", $bill_id);  // Deleting the specific bill
            $stmt->execute();
        }

        // Step 6: Redirect back to the generate bill page after successful cancellation
        header("Location: cust_topup_bill.php");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    // Step 7: Handle errors
    echo "Error: " . $e->getMessage();
}
?>
