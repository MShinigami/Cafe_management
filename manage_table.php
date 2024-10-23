<?php
include 'db.php';  // Include the database connection

// Fetch all bookings (i.e., tables that are reserved but not yet released)
$sql = "
    SELECT 
        t.id AS table_id, 
        t.tableno, 
        t.tablesize, 
        t.status, 
        c.name AS customer_name, 
        c.phoneno, 
        o.id AS order_id, 
        o.order_time
    FROM tables t
    LEFT JOIN customer c ON t.customer_id = c.id
    LEFT JOIN orders o ON c.id = o.customer_id
    WHERE t.status IN ('reserved', 'occupied')
    ORDER BY o.order_time";

$result = $conn->query($sql);

// Check if there are any bookings to display
if ($result->num_rows > 0) {
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
} else {
    $bookings = [];
}

// Handle releasing a table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['release_table'])) {
    $table_id = $_POST['table_id'];  // Get the table ID to release
    $customer_id = $_POST['customer_id'];  // Get the customer ID to delete from allocations

    // Step 1: Update the table status to 'available'
    $update_table_stmt = $conn->prepare("UPDATE tables SET status = 'available', customer_id = NULL WHERE id = ?");
    $update_table_stmt->bind_param("i", $table_id);

    if ($update_table_stmt->execute()) {
        // Table status updated successfully
        echo "Table released and status updated successfully!";

        // Step 2: Optionally, update related orders and bills if needed
        $order_id = $_POST['order_id'];
        $update_order_stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
        $update_order_stmt->bind_param("i", $order_id);
        $update_order_stmt->execute();

        // Step 3: Redirect back to the same page to refresh the booking list
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error updating table status: " . $update_table_stmt->error;
    }

    $update_table_stmt->close();

    $delete_allocation_stmt = $conn->prepare("DELETE FROM table_allocations WHERE customer_id = ? AND table_no = ?");
    $delete_allocation_stmt->bind_param("ii", $customer_id, $table_id); // Assuming `table_no` is the same as `id` for allocation

    if ($delete_allocation_stmt->execute()) {
        echo "Table allocation record deleted successfully!";
    } else {
        echo "Error deleting table allocation record: " . $delete_allocation_stmt->error;
    }

    $delete_allocation_stmt->close();
}
$conn->close();  // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Basic meta tags -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  
  <!-- Site title and meta tags -->
  <meta name="keywords" content="" />
  <meta name="description" content="Cafe Management System - Manage Bookings" />
  <meta name="author" content="" />

  <title>Cafe Management - Manage Bookings</title>

  <!-- External CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- Responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

  <!-- Additional Styling -->
  <style>
      body {
          padding: 0 20px; /* Add some space to the left and right sides of the page */
      }

      body::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-image: url('images/bg_dashboard.jpg');
          background-size: cover;
          background-position: center;
          background-attachment: fixed;
          opacity: 0.5; /* Adjust the opacity here (0.1 to 1.0) */
          z-index: -1; /* Keeps the image behind all other content */
      }

      .container {
          max-width: 1200px; /* Limit the maximum width of the content */
          margin: 0 auto; /* Center the container */
      }

      table {
          width: 100%;
          border-collapse: collapse;
          margin: 20px 0;
          font-size: 18px;
      }

      table, th, td {
          border: 1px solid black;
      }

      th, td {
          padding: 12px;
          text-align: left;
      }

      th {
          background-color: #f2f2f2;
      }

      td {
          background-color: #fff;
      }

      tr:nth-child(even) {
          background-color: #f9f9f9;
      }

      tr:hover {
          background-color: #f1f1f1;
      }

      h2 {
          text-align: center;
          margin-top: 20px;
      }

      .button {
          display: inline-block;
          padding: 10px 15px;
          font-size: 16px;
          color: white;
          background-color: #007bff;
          border: none;
          border-radius: 5px;
          cursor: pointer;
          text-align: center;
      }

      .button:hover {
          background-color: #0056b3;
      }
  </style>
</head>

<body>
<div class="container">
    <h2>Manage Bookings</h2>

    <!-- Button to go back to the Dashboard -->
    <form action="admin_dashboard.php" method="POST" style="text-align: right; margin-bottom: 20px;">
          <button type="submit" class="button">Go to Dashboard</button>
    </form>

    <!-- Display all bookings -->
    <table>
        <thead>
            <tr>
                <th>Table No</th>
                <th>Customer Name</th>
                <th>Phone No</th>
                <th>Release Table</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['tableno']); ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['phoneno']); ?></td>
                        <td>
                            <!-- Form to release the table -->
                            <form method="POST" action="">
                                <input type="hidden" name="table_id" value="<?php echo $booking['table_id']; ?>">
                                <input type="hidden" name="order_id" value="<?php echo $booking['order_id']; ?>">
                                <input type="submit" name="release_table" value="Release Table">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="back-link">
        <a href="customer_dashboard.php">Redirect to Dashboard</a>
</div>
<!-- Scripts -->
<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="js/custom.js"></script>

</body>

</html>
