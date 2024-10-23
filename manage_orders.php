<?php
// Include your database connection file here
include 'db.php';

// Start the session to access customer or admin information
session_start();

$customer_id = null; // Initialize customer_id

// Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    // Admin can provide customer_id through URL
    $customer_id = $_GET['customer_id'] ?? null; // Admin will provide customer_id via GET parameter
} elseif (isset($_SESSION['customer_id'])) {
    // Customer is managing their own orders
    $customer_id = $_SESSION['customer_id'];
} else {
    // Redirect to the login page if neither admin nor customer is logged in
    header("Location: login.php");
    exit(); // Stop further execution
}

// Ensure that the customer_id is provided
if (!$customer_id) {
    $_SESSION['message'] = 'No customer selected or logged in.';
    header("Location: login.php");
    exit();
}

// Handle form submissions for updating, deleting, and adding orders
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare statements for reusability
    $insert_stmt = $conn->prepare("INSERT INTO orders (customer_id, menu_id, amount, quantity, tableno) VALUES (?, ?, ?, ?, ?)");

    if (isset($_POST['add_order'])) {
        $tableno = $_POST['tableno'];
        $menu_id = $_POST['menu_id'];
        $quantity = $_POST['quantity'];

        // Check if the customer is sitting at the allocated table
        $check_allocation_stmt = $conn->prepare("SELECT COUNT(*) FROM table_allocations WHERE customer_id = ? AND table_no = ?");
        $check_allocation_stmt->bind_param('ii', $customer_id, $tableno);
        $check_allocation_stmt->execute();
        $check_allocation_stmt->bind_result($is_allocated);
        $check_allocation_stmt->fetch();
        $check_allocation_stmt->close();

        if ($is_allocated > 0) {
            // Fetch the menu item price
            $stmt = $conn->prepare("SELECT amount FROM menu WHERE id = ?");
            $stmt->bind_param('i', $menu_id);
            $stmt->execute();
            $stmt->bind_result($amount);
            $stmt->fetch();
            $stmt->close();

            // Calculate the total amount
            $total_amount = $amount * $quantity;

            // Insert the new order into the database
            $insert_stmt->bind_param('iidii', $customer_id, $menu_id, $total_amount, $quantity, $tableno);
            if ($insert_stmt->execute()) {
                $_SESSION['message'] = 'Order placed successfully!';
                header("Location: manage_orders.php");
                exit();
            } else {
                $_SESSION['message'] = 'Error placing order: ' . addslashes($insert_stmt->error);
                header("Location: manage_orders.php");
                exit();
            }
        } else {
            $_SESSION['message'] = 'You are not allocated to this table. Please select your allocated table or contact the admin.';
            header("Location: manage_orders.php");
            exit();
        }
    }

    // Check if it's an update request
    if (isset($_POST['update'])) {
        $order_id = $_POST['order_id'];
        $quantity = $_POST['quantity'];

        // Fetch the menu item price to recalculate the amount
        $stmt = $conn->prepare("SELECT amount FROM menu WHERE id = (SELECT menu_id FROM orders WHERE id = ?)");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $stmt->bind_result($amount);
        $stmt->fetch();
        $stmt->close();

        // Calculate the new total amount
        $new_amount = $amount * $quantity;

        // Update the order in the database
        $update_stmt = $conn->prepare("UPDATE orders SET quantity = ?, amount = ? WHERE id = ?");
        $update_stmt->bind_param('idi', $quantity, $new_amount, $order_id);
        if ($update_stmt->execute()) {
            $_SESSION['message'] = 'Order updated successfully!';
            header("Location: manage_orders.php");
            exit();
        } else {
            $_SESSION['message'] = 'Error updating order: ' . addslashes($update_stmt->error);
            header("Location: manage_orders.php");
            exit();
        }
        $update_stmt->close();
    }

    // Check if it's a delete request
    if (isset($_POST['delete'])) {
        $order_id = $_POST['order_id'];

        // Delete the order from the database
        $delete_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $delete_stmt->bind_param('i', $order_id);
        if ($delete_stmt->execute()) {
            $_SESSION['message'] = 'Order deleted successfully!';
            header("Location: manage_orders.php");
            exit();
        } else {
            $_SESSION['message'] = 'Error deleting order: ' . addslashes($delete_stmt->error);
            header("Location: manage_orders.php");
            exit();
        }
        $delete_stmt->close();
    }

    // Close prepared statements
    $insert_stmt->close();
}

// Fetch orders for the logged-in customer
$query = "SELECT o.id, m.name, o.amount, o.quantity, o.tableno 
          FROM orders o 
          JOIN menu m ON o.menu_id = m.id 
          WHERE o.customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Fetch menu items for the dropdown
$menu_query = "SELECT id, name FROM menu WHERE status = 'in_stock'";
$menu_stmt = $conn->prepare($menu_query);
$menu_stmt->execute();
$menu_result = $menu_stmt->get_result();
$menu_stmt->close();

// Handle form submissions for creating or generating bills
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Determine which button was clicked
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create_bill') {
            // Logic to create a bill
            header("Location: bill_generator.php");
            exit(); // Stop further execution
        } elseif ($action === 'generate_bill') {
            // Logic to generate a bill
            header("Location: generate_bill.php");
            exit(); // Stop further execution
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Order</title>
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

    <style>

            /* Adjust for header height */
            body {
                padding-top: 80px; /* Adjust this value to match the actual height of the header */
            }

            /* Style for the fixed background image */
            body {
                background-image: url('assets/img/bg_dashboard.jpg'); /* Ensure the image path is correct */
                background-size: cover; /* Cover the entire viewport */
                background-position: center; /* Center the image */
                background-attachment: fixed; /* Fixed position */
                background-repeat: no-repeat; /* Prevent repeat */
                opacity: 2; /* Set opacity */
                height: 100vh; /* Full height for the body */
            }

            .header {
                background-color: #0a0a0a;
                z-index: 1000; /* Ensure header stays on top */
                padding: 10px 0;
            }

            .fixed-top {
                position: fixed;
                top: 0;
                width: 100%;
            }

            .header {
                z-index: 1000; /* Ensure the header is on top */
                background-color: #0a0a0a; /* Set a background color */
            }

            body {
                font-family: 'Playlist Script', cursive;
                background-color: #f9f9f9; /* Light background for contrast */
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh; /* Full height for centering */
            }

            .container {
                text-align: center;
                display: flex; /* Use Flexbox to center content */
                justify-content: center; /* Center horizontally */
            }

            body {
                margin: 0; /* Remove default body margin */
                background-color: #f2f2f2; /* Optional: Background color for contrast */
            }

            .form {
                background-color: white; /* White background for the form */
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
                margin: 20px; /* Space around the form */
                width: 500px; /* Fixed width for uniformity */
                position: relative; /* To allow scrolling */
                margin-bottom: 20px;
                margin-top: 300px;
            }

            .form h1 {
                font-size: 24px; /* Heading size */
                margin-bottom: 15px; /* Space below the heading */
            }

            .form label {
                display: block; /* Labels as block elements */
                margin: 10px 0 5px; /* Space around labels */
            }

            .form input {
                width: 100%; /* Full width of input field */
                padding: 10px; /* Padding for uniform input size */
                border: 1px solid #ccc; /* Border color */
                border-radius: 4px; /* Rounded corners */
                margin-bottom: 15px; /* Space below input */
                box-sizing: border-box; /* Include padding in width */
                font-family: 'Playlist Script', cursive; /* Consistent font */
            }
            .submit-button {
                background-color: #007bff; /* Blue background */
                color: white; /* White text */
                border: none; /* No border */
                padding: 10px 20px; /* Button padding */
                border-radius: 4px; /* Rounded corners */
                cursor: pointer; /* Pointer cursor on hover */
                font-size: 16px; /* Font size */
                transition: background-color 0.3s; /* Smooth transition for hover */
                width: 100%; /* Full width for button */
                margin-bottom: 20px; /* Space below the button */
            }

            .submit-button:hover {
                background-color: #0056b3; /* Darker blue on hover */
            }

            .styled-table {
                width: 100%; /* Full width of table */
                margin: 0 auto; /* Center the table */
                border-collapse: collapse; /* Collapse borders */
                margin-top: 0px;
                margin-bottom: 100px; /* Space above table */
                background-color: white; /* White background for the entire table */
            }

            .styled-table th,
            .styled-table td {
                padding: 10px; /* Padding for table cells */
                border: 1px solid #ddd; /* Light border */
                text-align: center; /* Center align text */
                color: black; /* Black font color */
            }

            .styled-table th {
                background-color: #f2f2f2; /* Light gray for header */
            }

            .action{
                margin-bottom: 20px;
                margin-top: 250px;

            }

            .action-button {
                background-color: #28a745; /* Green background */
                color: clack; /* White text */
                border: none; /* No border */
                padding: 5px 10px; /* Button padding */
                border-radius: 4px; /* Rounded corners */
                cursor: pointer; /* Pointer cursor on hover */
                transition: background-color 0.3s; /* Smooth transition for hover */
                margin-bottom: 50px; /* Increased space below action buttons */
            }

            .action-button:hover {
                background-color: #218838; /* Darker green on hover */
                
            }


            /* Ensure header background is fully opaque */
            .header {
                    background-color: rgba(10, 10, 10, 1); /* Fully opaque header */
                    z-index: 1000;
                    padding: 10px 0;
            }

            .alert {
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #d4edda;
                border-radius: 5px;
                background-color: #f8f9fa;
                color: #155724;
                position: relative; /* Optional: for positioning */
                top: 0; /* Adjust as needed */
                width: 100%; /* Full width */
                z-index: 1000; /* Make sure it appears above other content */
            }

            /* Footer or scroll-to-top */
            #scroll-top {
                background-color: rgba(0, 0, 0, 1); /* Darker but still visible button */
            }
    </style>
</head>
<body>
<header id="header" class="header fixed-top">
  <div class="topbar d-flex align-items-center">
    <div class="container d-flex justify-content-end justify-content-md-between">
      <div class="contact-info d-flex align-items-center">
        <i class="bi bi-phone d-flex align-items-center d-none d-lg-block"><span>+91 9607990069</span></i>
        <i class="bi bi-clock ms-4 d-none d-lg-flex align-items-center"><span>Mon-Sat: 11:00 AM - 23:00 PM</span></i>
    </div>
  <div class="branding d-flex align-items-center">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      <a href="admin_dashboard.php" class="logo d-flex align-items-center">
        <h1 class="sitename">Cafe</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="admin_dashboard.php" class="active">Home</a></li>
          <li><a href="#book-a-table">Reservation</a></li>
          <li><a href="register.php">Order Management</a></li>
        </ul>
      </nav>
    </div>
  </div>
</header>

<main>
  <div class="container">
    <!-- Order Form -->
    <form method="POST"  class="form">
      <div class="mb-3">
        <label for="menu_id" class="form-label">Menu Item</label>
        <select name="menu_id" class="form-select" required>
          <option value="">Select a menu item</option>
          <?php while ($menu_item = mysqli_fetch_assoc($menu_result)): ?>
            <option value="<?php echo $menu_item['id']; ?>"><?php echo $menu_item['name']; ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" min="1" required>
      </div>
      <div class="mb-3">
        <label for="tableno" class="form-label">Table Number</label>
        <input type="number" name="tableno" class="form-control" required>
      </div>
      <button type="submit" name="add_order" class="btn btn-success">Add Order</button>
    </form>
</div>
  <!-- Create Bill Button -->
  <div class="container">
      <form method="POST" action="">
          <button type="submit" name="action" value="create_bill">Create Bill</button>
          <button type="submit" name="action" value="generate_bill">Generate Bill</button>
      </form>
  </div>

<div id="message" class="alert" style="<?php echo isset($_SESSION['message']) ? '' : 'display:none;'; ?>">
    <?php 
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']); // Clear message after displaying
        }
    ?>
</div>

<div class="container">
    <!-- Orders Table -->
    <div class="table-container">
    <h2 style="color: white;">Your Orders:</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Menu Item</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Table No</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['tableno']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1">
                            <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
  </div>
</div>

</main>



<script>
    // Automatically hide the message after 2 seconds
    setTimeout(function() {
        var messageDiv = document.getElementById('message');
        if (messageDiv) {
            messageDiv.style.display = 'none';
        }
    }, 2000);
</script>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
