<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Enter Customer</title>
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
        body {
            font-family: 'Playlist Script', cursive; /* Apply Playlist Script font */
            background-image: url('assets/img/bg_dashboard.jpg'); /* Set the background image */
            background-size: cover; /* Cover the entire page */
            background-attachment: fixed; /* Fix the background so it doesn't scroll */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
            color: white; /* White text color for readability */
            height: 100vh; /* Full height */
            display: flex;
            flex-direction: column; /* Stack content vertically */
        }

        #header {
            background-color: rgba(51, 51, 51, 0.8); /* Dark background for header with transparency */
            color: white; /* White text color for header */
            padding: 10px 0; /* Padding for header */
            position: relative; /* Position for the header */
        }

        .branding {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            text-decoration: none;
            color: white; /* White logo text color */
        }

        nav {
            display: flex;
            align-items: center;
        }

        nav ul {
            list-style: none;
            display: flex;
            padding: 0;
            margin: 0;
        }

        nav li {
            margin-left: 20px; /* Space between menu items */
        }

        nav a {
            text-decoration: none;
            color: white; /* White link color */
            font-weight: bold; /* Bold links */
        }

        nav a.active {
            text-decoration: underline; /* Underline for active link */
        }

        h2 {
            color: #ffffff; /* White heading color */
            margin-bottom: 20px;
            margin-top: 20px; /* Add some margin at the top */
            text-align: left; /* Align text to the left */
        }

        .form-container {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: calc(100vh - 60px); /* Adjust height to allow for header */
            padding: 20px; /* Add padding */
        }

        form {
            background: rgba(255, 255, 255, 0.8); /* Slightly transparent white for contrast */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px; /* Max width for larger screens */
            text-align: left;
            color: black;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: none; /* Remove default border */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
            box-sizing: border-box;
        }

        input:focus,
        select:focus {
            outline: none; /* Remove outline on focus */
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.5); /* Glow effect on focus */
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745; /* Green button */
            color: white;
            border: none;
            border-radius: 8px; /* Rounded corners */
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s; /* Smooth transition */
        }

        button:hover {
            background-color: #218838; /* Darker green on hover */
            transform: translateY(-2px); /* Slight lift effect on hover */
        }
    </style>
</head>
<body>

<header id="header" class="header fixed-top">

    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="admin_dashboard.html" class="logo d-flex align-items-center">
                <h1 class="sitename">Cafe</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="admin_dashboard.php" class="active">Home</a></li>
                    <li><a href="admin_reserve_table.php">Reservation</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </div>
</header>

<div class="form-container">
    <div>
        <h2>Customer Registration</h2>
        <form action="register_customer.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            
            <label for="phoneno">Phone No:</label>
            <input type="tel" name="phoneno" id="phoneno" required>
            
            <label for="tableno">Table Number:</label>
            <select name="tableno" id="tableno" required>
                <?php
                // Include your database connection file
                include 'db.php';

                // Fetch available tables with customer names
                $sql = "
                    SELECT t.tableno, t.status, c.name AS customer_name 
                    FROM tables t 
                    LEFT JOIN customer c ON t.customer_id = c.id
                ";
                $result = $conn->query($sql);

                if ($result === FALSE) {
                    die("Error in query: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Check if the customer name exists; if not, display 'Available'
                        $customerName = !empty($row['customer_name']) ? $row['customer_name'] : 'Available'; 
                        echo '<option value="' . $row['tableno'] . '">' . $row['tableno'] . ' (' . $row['status'] . ' - ' . $customerName . ')</option>';
                    }
                } else {
                    echo '<option value="">No available tables</option>';
                }

                mysqli_close($conn);
                ?>
            </select>
            
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</div>

</body>
</html>
