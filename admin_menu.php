<?php
// Include database connection file
include 'db.php';

// Initialize variables
$name = $description = $amount = $status = $category = $picture_url = "";
$success_message = $error_message = "";

// Handle form submission for adding a menu item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    // Get form input values
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $category = $_POST['category'];
    $picture_url = trim($_POST['picture_url']);

    // Validate amount
    if ($amount < 0) {
        $error_message = "Amount must be a positive number.";
    } else {
        // Insert the new menu item into the database
        $sql = "INSERT INTO menu (name, description, amount, status, category, picture_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsis", $name, $description, $amount, $status, $category, $picture_url);
        
        if ($stmt->execute()) {
            $success_message = "Menu item added successfully!";
        } else {
            $error_message = "Error adding menu item: " . $stmt->error;
        }
    }
}

// Handle deletion of a menu item
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete the menu item from the database
    $sql = "DELETE FROM menu WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success_message = "Menu item deleted successfully!";
    } else {
        $error_message = "Error deleting menu item: " . $stmt->error;
    }
}

// Fetch all menu items
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);

$menu_items = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $menu_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Menu - Admin</title>

  <!-- Favicons -->
  <link href="assets/img/cafe.png" rel="icon">
  <link href="assets/img/cafe.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    /* General Body Styles */
    body {
        background-image: url('assets/img/woodpattern.jpg'); /* Adjust path as needed */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        padding-top: 80px; /* Adjust this value to match the actual height of the header */
    }

    .container {
        background-color: rgba(255, 255, 255, 0.8); /* White background with slight transparency */
        padding: 40px;
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); /* Add shadow for depth */
    }

    h2, h3 {
        text-align: center;
    }

    form {
        display: flex;
        flex-direction: column; /* Make form vertical */
        gap: 15px; /* Space between form elements */
        margin-bottom: 30px; /* Space below the form */
    }

    input, textarea, select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    button {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px;
        font-size: 16px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .dbbutton {
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

      .dbbutton:hover {
          background-color: #0056b3;
      }
  </style>
</head>
<body>
<!-- Button to go back to the Dashboard -->
<div style="text-align: right; margin-bottom: 20px;">
    <form action="admin_dashboard.php" method="POST" style="display: inline;">
        <button type="submit" class="dbutton">Go to Dashboard</button>
    </form>
</div>

<div class="container">
    <h2>Manage Menu</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form action="admin_menu.php" method="post">
        <input type="text" name="name" placeholder="Menu Item Name" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" name="amount" placeholder="Amount" step="0.01" required>
        <select name="status" required>
            <option value="in_stock">In Stock</option>
            <option value="out_of_stock">Out of Stock</option>
        </select>
    </form>

    <h3>Current Menu Items</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menu_items as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td><?= htmlspecialchars($item['amount']) ?></td>
                    <td><?= htmlspecialchars($item['status']) ?></td>
                    <td>
                        <a href="admin_menu.php?delete=<?= $item['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
