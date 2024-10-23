<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get positive feedback (rating 4 or higher)
$sql = "SELECT c.name, f.rating, f.suggestions, f.date FROM feedback f
        JOIN customer c ON f.customer_id = c.id";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Dashboard</title>
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

    /* Ensure that the fixed header has some spacing */
    .fixed-top {
        position: fixed;
        top: 0;
        width: 100%;
    }

     /* Style for the fixed background image */
     body {
        background-image: url('assets/img/bg_dashboard.jpg'); /* Ensure the image path is correct */
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the image */
        background-attachment: fixed; /* Fixed position */
        background-repeat: no-repeat; /* Prevent repeat */
        opacity: 0.7; /* Set opacity */
        height: 100vh; /* Full height for the body */
      }
  
      /* To ensure content remains fully visible on top of the background */
      .content {
        position: relative;
        z-index: 1; /* Ensures content is above the background */
      }

      /* General section spacing */
        .section {
            padding: 60px 0;
        }

        .section-title {
            margin-bottom: 50px;
        }

        /* Style for the Why-Us section */
        .why-us .card-item {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .why-us .card-item:hover {
            transform: scale(1.05);
        }

        .why-us .card-item h4 {
            margin-top: 15px;
            font-weight: 700;
        }

        .why-us .card-item p {
            font-size: 14px;
            color: #6c757d;
        }

        .why-us .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .why-us .section-title h2 {
            font-size: 32px;
            font-weight: bold;
        }
        /* Global Styles */
        body {
            background-image: url('assets/img/bg_admin_login.jpg'); /* Adjust path as needed */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            opacity: 1; /* Increase body opacity */
        }

        /* Section Visibility */
        .section {
            background-color: rgba(255, 255, 255, 0.85); /* White background with slight transparency */
            padding: 60px 0; /* Ensure padding for visibility */
            border-radius: 8px; /* Add subtle rounding to sections */
        }

        /* Why-us section */
        .why-us .card-item {
            background-color: rgba(255, 255, 255, 0.95); /* Increase opacity for better visibility */
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        /* Ensure header background is fully opaque */
        .header {
            background-color: rgba(10, 10, 10, 1); /* Fully opaque header */
            z-index: 1000;
            padding: 10px 0;
        }

        .fixed-top {
            position: fixed;
            top: 0;
            width: 100%;
        }

        /* Book-a-table section */
        .book-a-table {
            background-color: rgba(255, 255, 255, 0.9); /* Make the form background more opaque */
            padding: 40px 20px;
            border-radius: 8px;
        }

        .reservation-form-bg {
            background-color: rgba(255, 255, 255, 0.95); /* Ensure reservation form is highly visible */
        }

        /* Footer or scroll-to-top */
        #scroll-top {
            background-color: rgba(0, 0, 0, 0.7); /* Darker but still visible button */
        }

  </style>
</head>

<body class="index-page">

  <header id="header" class="header fixed-top">

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-end justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <i class="bi bi-phone d-flex align-items-center d-none d-lg-block"><span>+91 9607990069</span></i>
          <i class="bi bi-clock ms-4 d-none d-lg-flex align-items-center"><span>Mon-Sat: 11:00 AM - 23:00 PM</span></i>
        </div>
        <a href="logout.php" class="cta-btn">Logout</a>
      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-cente">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="admin_dashboard.html" class="logo d-flex align-items-center">
          <h1 class="sitename">Cafe</h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#why-uas" class="active">Home</a></li>
            <li><a href="#book-a-table">Reservation</a></li>
            <li><a href="register.php">Order Management</a></li>
            <li><a href="admin_menu.php">Menu Management</a></li>
            <li><a href="admin_add_table.php">Add Table</a></li>
            <li><a href="show_feedback.php">Feedbacks</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

      </div>

    </div>

  </header>

  <main class="main">

    <!-- Admin Functiom Section -->
    <section id="why-us" class="why-us section">
      <div class="container section-title" data-aos="fade-up">
        <div><span class="description-title">Welcome</span></div>
      </div><!-- End Section Title -->

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-item">
              <h4><a href="admin_menu.php" class="stretched-link">Menu Management</a></h4>
              <p>Add or Delete Menu</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card-item">
              <h4><a href="register.php" class="stretched-link">Order Management</a></h4>
              <p>Add,delete and bill generation to customer</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card-item">
              <h4><a href="manage_table.php" class="stretched-link">Table Management</a></h4>
              <p>Release the after the customer have left</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card-item">
              <h4><a href="admin_reserve_table.php" class="stretched-link">Table Reservation</a></h4>
              <p>Reserve tables for customer</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card-item">
              <h4><a href="menu_card.php" class="stretched-link">Menu Card</a></h4>
              <p>List of Items available in the cafe.</p>
            </div>
          </div><!-- Card Item -->

        </div>
      </div>
    </section><!-- /Admin Functions Section -->
    
    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

  </main>
</body>

</html>
