<?php
// Include your database connection file
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phoneno = $_POST['phoneno'];
    $table_no = $_POST['tableno']; // Ensure this matches the form's select name

    // Check if customer already exists
    $checkQuery = "SELECT id FROM customer WHERE email = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, 's', $email);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    
    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        // Customer exists, update their details
        $updateQuery = "UPDATE customer SET name = ?, phoneno = ? WHERE email = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, 'sss', $name, $phoneno, $email);

        if (mysqli_stmt_execute($updateStmt)) {
            // Fetch the existing customer ID
            mysqli_stmt_bind_result($checkStmt, $customer_id);
            mysqli_stmt_fetch($checkStmt);
            
            // Allocate table to the customer
            $allocateQuery = "INSERT INTO table_allocations (customer_id, table_no) VALUES (?, ?)";
            $allocateStmt = mysqli_prepare($conn, $allocateQuery);
            mysqli_stmt_bind_param($allocateStmt, 'ii', $customer_id, $table_no); // Change 'is' to 'ii' for INT
            mysqli_stmt_execute($allocateStmt);
            mysqli_stmt_close($allocateStmt);
            
            // Update table status to occupied and set customer_id
            $updateTableQuery = "UPDATE tables SET status = 'occupied', customer_id = ? WHERE tableno = ? AND (status = 'available' OR status = 'reserved')";
            $updateTableStmt = mysqli_prepare($conn, $updateTableQuery);
            mysqli_stmt_bind_param($updateTableStmt, 'ii', $customer_id, $table_no);
            mysqli_stmt_execute($updateTableStmt);
            mysqli_stmt_close($updateTableStmt);
            
            // Start a session and log in the user
            session_start();
            $_SESSION['customer_id'] = $customer_id; 
            $_SESSION['customer_name'] = $name;
            $_SESSION['customer_email'] = $email;
            $_SESSION['customer_phone'] = $phoneno;

            // Redirect to order management page
            header("Location: manage_orders.php");
            exit();
        } else {
            echo "Error during update: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($updateStmt);
    } else {
        // Customer does not exist, insert new record
        $insertQuery = "INSERT INTO customer (name, email, phoneno, registration_date) VALUES (?, ?, ?, NOW())";
        $insertStmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, 'sss', $name, $email, $phoneno);

        if (mysqli_stmt_execute($insertStmt)) {
            // Get the last inserted customer ID
            $customer_id = mysqli_insert_id($conn); 
            
            // Allocate table to the customer
            $allocateQuery = "INSERT INTO table_allocations (customer_id, table_no) VALUES (?, ?)";
            $allocateStmt = mysqli_prepare($conn, $allocateQuery);
            mysqli_stmt_bind_param($allocateStmt, 'ii', $customer_id, $table_no); // Change 'is' to 'ii' for INT
            mysqli_stmt_execute($allocateStmt);
            mysqli_stmt_close($allocateStmt);
            
            // Update table status to occupied and set customer_id
            $updateTableQuery = "UPDATE tables SET status = 'occupied', customer_id = ? WHERE tableno = ? AND (status = 'available' OR status = 'reserved')";
            $updateTableStmt = mysqli_prepare($conn, $updateTableQuery);
            mysqli_stmt_bind_param($updateTableStmt, 'ii', $customer_id, $table_no);
            mysqli_stmt_execute($updateTableStmt);
            mysqli_stmt_close($updateTableStmt);
            
            // Start a session and log in the user
            session_start();
            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['customer_name'] = $name;
            $_SESSION['customer_email'] = $email;
            $_SESSION['customer_phone'] = $phoneno;

            // Redirect to order management page
            header("Location: manage_orders.php");
            exit();
        } else {
            echo "Error during registration: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($insertStmt);
    }

    mysqli_stmt_close($checkStmt);
}

mysqli_close($conn);
?>
