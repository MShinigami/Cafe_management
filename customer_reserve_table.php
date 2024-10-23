<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Reserve Table for Customer</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/cafe.png" rel="icon">
    <link href="assets/img/cafe.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playlist+Script&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        body {
            padding-top: 50px;
            font-family: 'Roboto', sans-serif;
            background: url('assets/img/bg_dashboard.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: calc(100vh - 50px);
        }

        h2 {
            text-align: left;
            margin: 20px;
            color: #333;
            font-size: 24px;
            font-family: 'Playlist Script', cursive;
            font-style: italic;
        }

        .container {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: black;
            font-family: 'Playlist Script', cursive;
        }

        input[type="text"],
        input[type="email"],
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            background-color: #f9f9f9;
            color: black;
            font-family: 'Playlist Script', cursive;
        }

        button {
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Playlist Script', cursive;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 14px;
            font-family: 'Playlist Script', cursive;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Reserve Table</h2>
        
        <form method="POST" action="">
            <label for="customer_name">Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>
            
            <label for="customer_email">Email:</label>
            <input type="email" id="customer_email" name="customer_email" required>
            
            <label for="customer_phoneno">Phone:</label>
            <input type="text" id="customer_phoneno" name="customer_phoneno" required>
            
            <label for="table_no">Select Table Number:</label>
            <select id="table_no" name="table_no" required>
                <option value="">-- Select a Table --</option>
                <?php
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "cafe_db";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch available tables
                $sql = "SELECT tableno, status FROM tables";
                $result = $conn->query($sql);
                if ($result === FALSE) {
                    die("Error in query: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row['tableno'] . '">' . $row['tableno'] . ' (' . $row['status'] . ')</option>';
                    }
                } else {
                    echo '<option value="">No available tables</option>';
                }

                // Close the connection
                $conn->close();
                ?>
            </select>

            <label for="minimum_wage">Minimum Wage (₹):</label>
            <input type="text" id="minimum_wage" name="minimum_wage" value="2000" readonly>

            <button type="submit">Pay ₹2000 to Reserve</button>
        </form>

        <div class="back-link">
            <a href="customer_dashboard.php">Redirect to Dashboard</a>
        </div>
    </div>

    <?php
    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Database connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get form data
        $customer_name = $_POST['customer_name'];
        $customer_email = $_POST['customer_email'];
        $customer_phone = $_POST['customer_phoneno']; // Corrected field name
        $table_no = $_POST['table_no'];
        $minimum_wage = $_POST['minimum_wage']; // Get the minimum wage

        // Check if the customer already exists
        $sql = "SELECT id FROM customer WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $customer_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Customer exists, reserve the table for them
            $customer_id = $result->fetch_assoc()['id'];

            // Reserve the table (update status in tables)
            $sql = "UPDATE tables SET status = 'reserved', customer_id = ? WHERE tableno = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $customer_id, $table_no);
            $stmt->execute();
        } else {
            // New customer, insert into customer table
            $sql = "INSERT INTO customer (name, email, phoneno) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $customer_name, $customer_email, $customer_phone);
            $stmt->execute();

            // Get the new customer's ID
            $customer_id = $stmt->insert_id;

            // Reserve the table (update status in tables)
            $sql = "UPDATE tables SET status = 'reserved', customer_id = ? WHERE tableno = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $customer_id, $table_no);
            $stmt->execute();
        }

        // Close the connection
        $stmt->close();
        $conn->close();
    }
    ?>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>
</body>
</html>
