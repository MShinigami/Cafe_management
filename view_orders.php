<?php
// Include your database connection file here
include 'db.php';

// Assume $customer_id is the logged-in user's ID
$customer_id = 1; // Replace with actual logged-in customer ID

// Fetch orders for the logged-in customer
$query = "SELECT o.id, m.name, o.amount, o.quantity, o.tableno 
          FROM orders o 
          JOIN menu m ON o.menu_name = m.id 
          WHERE o.customer_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
</head>
<body>

<h2>Your Orders</h2>
<table border="1">
    <tr>
        <th>Menu Item</th>
        <th>Quantity</th>
        <th>Amount</th>
        <th>Table No</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td>
                <form action="update_order.php" method="POST" style="display:inline;">
                    <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" required>
                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="tableno" value="<?php echo $row['tableno']; ?>">
                    <button type="submit">Update</button>
                </form>
            </td>
            <td>₹<?php echo $row['amount']; ?></td>
            <td><?php echo $row['tableno']; ?></td>
            <td>
                <form action="delete_order.php" method="POST" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this order?');">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
