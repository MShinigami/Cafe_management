<?php
include 'db.php';  // Ensure the connection to the database is established

// The admin details (can be hardcoded or dynamically passed)
$name = 'Manish';  // Admin Name
$email = 'manishgawas1005@gmail.com';  // Admin Email
$phoneno = '9607990069';  // Admin Phone
$password = 'manish';  // The password provided by admin

// Hash the password using password_hash()
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert the admin details into the `admin` table
$sql = "INSERT INTO `admin` (`name`, `email`, `phoneno`, `password`) VALUES (?, ?, ?, ?)";

// Prepare and bind the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $phoneno, $hashed_password);

// Execute the query
if ($stmt->execute()) {
    echo "Admin inserted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the prepared statement and the database connection
$stmt->close();
$conn->close();
?>
    