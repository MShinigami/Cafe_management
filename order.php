<?php
// Include your database connection file here
include 'db.php';

// Fetch menu items from the database
$query = "SELECT id, name, amount FROM menu WHERE status = 'in_stock'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <script>
        // Function to calculate amount based on quantity
        function calculateAmount() {
            var menuSelect = document.getElementById("menu_name");
            var selectedOption = menuSelect.options[menuSelect.selectedIndex];
            var price = parseFloat(selectedOption.getAttribute("data-amount"));
            var quantity = parseInt(document.getElementById("quantity").value);
            var amountField = document.getElementById("amount");

            if (!isNaN(quantity) && quantity > 0) {
                var totalAmount = price * quantity;
                amountField.value = totalAmount.toFixed(2); // Set amount to 2 decimal places
            } else {
                amountField.value = 0; // Reset amount if quantity is not valid
            }
        }
    </script>
</head>
<body>

<h2>Place Your Order</h2>
<form action="add_order.php" method="POST">
    <label for="menu_name">Menu Item:</label>
    <select id="menu_name" name="menu_name" onchange="calculateAmount()" required>
        <option value="" disabled selected>Select a menu item</option>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <option value="<?php echo $row['id']; ?>" data-amount="<?php echo $row['amount']; ?>">
                <?php echo $row['name']; ?> - ₹<?php echo $row['amount']; ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" min="1" oninput="calculateAmount()" required><br><br>

    <label for="amount">Amount:</label>
    <input type="text" id="amount" name="amount" readonly><br><br>

    <label for="tableno">Table Number:</label>
    <input type="number" id="tableno" name="tableno" required><br><br>

    <button type="submit">Submit Order</button>
</form>

</body>
</html>
