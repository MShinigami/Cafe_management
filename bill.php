<?php
// Database connection
include 'db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if order_id is set
if (!isset($_GET['order_id'])) {
    die("Order ID is not set.");
}

$order_id = $_GET['order_id'];

// Prepare and execute the query to retrieve the bill
$query = "SELECT * FROM bill WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if any results were returned
if (mysqli_num_rows($result) > 0) {
    $bill = mysqli_fetch_assoc($result);
} else {
    echo "No bill found for the specified order ID.";
    exit; // Exit if no bill found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Details</title>
    <style>
        /* CSS Variables */
        :root {
            --primary-color: #1779ba;
            --black: #322d28;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--black);
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }

        header {
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .inner-container {
            max-width: 800px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        table th {
            background-color: var(--primary-color);
            color: #fff;
        }

        .additional-info h5 {
            font-size: 1em;
            font-weight: 700;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <header>
        <h1>Invoice</h1>
    </header>
    <main>
        <div class="inner-container">
            <table>
                <tr>
                    <th>Bill ID</th>
                    <td><?php echo $bill['id']; ?></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><?php echo $bill['date']; ?></td>
                </tr>
                <tr>
                    <th>Customer Name</th>
                    <td><?php echo $bill['customer_name']; ?></td>
                </tr>
                <tr>
                    <th>Order Details</th>
                    <td><?php echo $bill['order_details']; ?></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td><?php echo $bill['total_amount']; ?></td>
                </tr>
            </table>
            <div class="additional-info">
                <h5>Thank you for your order!</h5>
            </div>
        </div>
    </main>
</body>
</html>
