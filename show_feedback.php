<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Feedbacks</title>
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
    <title>Customer Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light background for the whole page */
            margin: 0;
            padding: 20px;
        }
        .feedback-container {
            display: flex; /* Use flexbox for layout */
            flex-wrap: wrap; /* Allow wrapping to the next line */
            gap: 20px; /* Space between feedback blocks */
            margin-top:100px;
        }
        .feedback-block {
            flex: 1 1 calc(25% - 20px); /* Base width to fit 4 blocks in a row */
            height: 250px; /* Fixed height to make it square */
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background-color: #fff; /* White background for feedback blocks */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Subtle shadow for depth */
            display: flex; /* Use flexbox for content alignment */
            flex-direction: column; /* Stack content vertically */
            justify-content: space-between; /* Space content evenly */
            min-width: 200px; /* Minimum width for feedback blocks */
            box-sizing: border-box; /* Include padding and border in width calculations */
        }
        .feedback-block h3 {
            margin: 0; /* No margin for the heading */
        }
        strong {
            color: #333; /* Darker color for labels */
        }
        .header {
            background-color: rgba(10, 10, 10, 1); /* Fully opaque header */
            z-index: 1000;
            padding: 10px 0;
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
    </style>
</head>
<body>
<header id="header" class="header fixed-top">
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
<?php
// Include the database connection
include 'db.php'; // Adjust the path if necessary

// SQL query to select all feedback
$sql = "SELECT id, customer_name, rating, suggestions, date FROM feedback";
$result = $conn->query($sql);

// Function to generate star ratings
function displayStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '⭐'; // Filled star
        } else {
            $stars .= '☆'; // Empty star
        }
    }
    return $stars;
}

echo "<div class='feedback-container'>"; // Start feedback container

// Check if there are results
if ($result->num_rows > 0) {
    // Output data for each feedback in separate blocks
    while($row = $result->fetch_assoc()) {
        echo "<div class='feedback-block'>";
        echo "<h3>Feedback ID: " . htmlspecialchars($row["id"]) . "</h3>";
        echo "<p><strong>Customer Name:</strong> " . htmlspecialchars($row["customer_name"]) . "</p>";
        echo "<p><strong>Rating:</strong> " . displayStars($row["rating"]) . "</p>";
        echo "<p><strong>Suggestions:</strong> " . htmlspecialchars($row["suggestions"]) . "</p>";
        echo "<p><strong>Date:</strong> " . htmlspecialchars($row["date"]) . "</p>";
        echo "</div>";
    }
} else {
    echo "<div class='feedback-block'><p>No feedback found.</p></div>";
}

echo "</div>"; // End feedback container

// Close the connection
$conn->close();
?>

</body>
</html>
