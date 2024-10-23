<?php
// db.php - MySQL Database Connection
$servername = "localhost"; // Database server address
$username = "root"; // Database username
$password = ""; // Database password (empty for root in XAMPP)
$dbname = "cafe_db"; // Name of the database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form data, ensuring proper validation and sanitation
$tableno = isset($_POST['tableno']) ? (int)$_POST['tableno'] : 0; // Cast to integer
$tablesize = isset($_POST['tablesize']) ? (int)$_POST['tablesize'] : 0; // Cast to integer

// Check if the table number already exists
$checkQuery = "SELECT * FROM tables WHERE tableno = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("i", $tableno);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    // Table number already exists
    echo "<script>alert('Error: A table with this number already exists!'); window.location.href='admin_add_table.php';</script>";
} else {
    // Prepare and bind the insert statement
    $query = "INSERT INTO tables (tableno, tablesize) VALUES (?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ii", $tableno, $tablesize); // Corrected to match the number of columns

    // Execute the insert statement
    if ($stmt->execute()) {
        echo "<script>alert('Table added successfully!'); window.location.href='admin_dashboard.php';</script>";
        exit(); // Ensure no further code is executed after the redirect
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='admin_add_table.php';</script>"; // Display error if execution fails
    }

    // Close the insert statement
    $stmt->close();
}

// Close the check statement and connection
$checkStmt->close();
$conn->close();
?>
