<?php
// Include the database connection
include 'db.php'; // Ensure this file contains the database connection setup

// Start the session to access customer information
session_start();
$customer_id = $_SESSION['customer_id'] ?? null; // Get the logged-in customer ID
$_SESSION['customer_id'] = $customer_id;
try {
    // Display status message if present in the URL
    if (isset($_GET['status'])) {
        $status = htmlspecialchars($_GET['status']);
        if ($status === 'success') {
            echo "<p style='color: green;'>Bill has been canceled successfully.</p>";
        } elseif ($status === 'failure') {
            echo "<p style='color: red;'>Failed to cancel the bill or bill not found.</p>";
        }
    }

    // Check if customer_id is available
    if ($customer_id) {
        // Fetch orders and join with the menu table to get the menu name
        $order_stmt = $conn->prepare(
            "SELECT o.*, m.name as menu_name 
             FROM orders o 
             JOIN menu m ON o.menu_id = m.id 
             WHERE o.customer_id = ?"
        );
        $order_stmt->bind_param('i', $customer_id); // Assuming customer_id is an integer
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();

        // Display the bill summary in the center
        echo '<div class="bill-container">
                <div class="bill-header">
                    <h2>Your Bill Summary</h2>
                    <p>Customer ID: ' . htmlspecialchars($customer_id) . '</p>
                </div>';

        // Display the orders
        echo '<table class="bill-table">
                <thead>
                    <tr>
                        <th>Menu Item</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>';

        $total_amount = 0; // To calculate the total bill amount
        while ($row = $order_result->fetch_assoc()) {
            $total_amount += $row['amount'] * $row['quantity']; // Calculate total for each item
            echo '<tr>
                    <td>' . htmlspecialchars($row['menu_name']) . '</td>
                    <td>' . htmlspecialchars($row['quantity']) . '</td>
                    <td>₹' . htmlspecialchars($row['amount']) . '</td>
                </tr>';
        }

        echo '</tbody></table>';
        echo '<p><strong>Total Amount: ₹' . htmlspecialchars($total_amount) . '</strong></p>'; // Display total amount

        // Prepare the SQL statement to fetch the bill details for the specified customer
        $stmt = $conn->prepare("SELECT * FROM bill WHERE customer_id = ?");
        $stmt->bind_param('i', $customer_id); // Assuming customer_id is an integer
        $stmt->execute();

        // Fetch all bills for the customer
        $result = $stmt->get_result();
        $bills = $result->fetch_all(MYSQLI_ASSOC);

        if ($bills) {
            echo "<div class='bill-summary'>
                    <h2>Bill Details</h2>";
            echo "<table class='bill-table'>
                    <tr><th>Bill ID</th><th>Amount</th><th>Quantity</th><th>Table No</th><th>Date</th><th>Status</th></tr>";
            
            foreach ($bills as $bill) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($bill['id']) . "</td>";
                echo "<td>₹" . htmlspecialchars($bill['amount']) . "</td>";
                echo "<td>" . htmlspecialchars($bill['quantity']) . "</td>";
                echo "<td>" . htmlspecialchars($bill['tableno']) . "</td>";
                echo "<td>" . htmlspecialchars($bill['date']) . "</td>";
                echo "<td>" . htmlspecialchars($bill['status']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table></div>";
        } else {
            echo "<p>No bills found for the customer.</p>";
        }
    } else {
        echo "<p>You must be logged in to view your bills.</p>";
    }
} catch (mysqli_sql_exception $e) {
    // Consider logging the error instead of displaying it
    error_log("Database error: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
} finally {
    // Close the connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>


<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Pay Bill</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/cafe.png" rel="icon">
  <link href="assets/img/cafe.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Satisfy:wght@400&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
<!-- CSS Styling for Centralized Bill -->
<style>

    /* Adjust for header height */
    body {
        padding-top: 80px; /* Adjust this value to match the actual height of the header */
    }

    .header {
        background-color: #0a0a0a;
        z-index: 1000; /* Ensure header stays on top */
        padding: 10px 0;
    }

    /* Ensure that the fixed header has some spacing */
    .fixed-top {
        position: fixed;
        top: 0;
        width: 100%;
    }

    /* Header styling */
    h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 28px; /* Increased font size */
        color: black; /* Changed to black for better contrast against the background */
    }

    /* Ensure header background is fully opaque */
    .header {
        background-color: rgba(10, 10, 10, 1); /* Fully opaque header */
        z-index: 1000;
        padding: 10px 0;
    }   

    .bill-container {
        width: 50%; /* Half screen width */
        margin: 0 auto; /* Center horizontally */
        text-align: center; /* Center text */
        background-color: #f9f9f9; /* Light background for bill */
        padding: 20px; /* Add padding */
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Slight shadow for effect */
        margin-top: 50px;
    }

    .bill-header {
        margin-bottom: 20px; /* Space below the header */
    }

    .bill-header h2 {
        color: #333;
        font-size: 24px;
        margin-bottom: 5px;
    }

    .bill-header p {
        color: #777;
        font-size: 14px;
    }

    .bill-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    .bill-table th, .bill-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }

    .bill-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .bill-footer {
        margin-top: 20px;
        font-size: 18px;
        color: #333;
    }

    .bill-summary h2 {
        margin-top: 40px;
        color: #333;
    }

    /* Pay button transition */
    #pay-button {
        transition: background-color 0.3s ease;
    }

    #pay-button:hover {
        background-color: darkgreen; /* Change color on hover */
    }

    /* Button styling for top-left green button */
    .top-left-button {
        position: absolute;
        top: 100px; /* Adjust to position it below the header */
        left: 20px; /* Align to the left */
    }

    .dbutton {
        background-color: green; /* Green background */
        color: white; /* White text */
        padding: 10px 20px; /* Padding for better appearance */
        border: none; /* No border */
        cursor: pointer; /* Pointer cursor */
        border-radius: 5px; /* Rounded corners */
        transition: background-color 0.3s ease; /* Smooth hover effect */
    }

    .dbutton:hover {
        background-color: darkgreen; /* Darker green on hover */
    }

</style>
</head>

<header id="header" class="header fixed-top">
    <div class="branding d-flex align-items-cente">

    <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="customer_dashboard.php" class="logo d-flex align-items-center">
        <h1 class="sitename">Cafe</h1>
        </a>

        <nav id="navmenu" class="navmenu">
        <ul>
            <li><a href="customer_dashboard.php" class="active">Home</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

    </div>

    </div>

</header>
<tr>
    <td>
        <form action="cust_process_payment.php" method="post" style="display:inline;">
            <input type="hidden" name="bill_id" value="<?php echo htmlspecialchars($bill['id']); ?>">
            <button type="submit" id="pay-button" style="background-color: green; color: white;">Pay</button>
        </form>

        <form action="cust_cancel_bill.php" method="post" style="display:inline;">
            <input type="hidden" name="bill_id" value="<?php echo htmlspecialchars($bill['id']); ?>">
            <button type="submit" style="background-color: red; color: white;">Cancel</button>
        </form>
    </td>
</tr>



<!-- Button section -->
<div class="top-left-button">
    <a href="customer_add_order.php">
        <button type="button" class="dbutton">Go back to Orders</button>
    </a>
</div>

