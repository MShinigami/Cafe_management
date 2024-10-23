<?php
// Include your database connection file
include 'db.php';

// Start the session to access customer information
session_start();

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get customer information from session
$customer_id = $_SESSION['customer_id'];

// Fetch the customer name from the database
$customer_query = $conn->prepare("SELECT name FROM customer WHERE id = ?");
$customer_query->bind_param('i', $customer_id);
$customer_query->execute();
$customer_query->bind_result($customer_name);
$customer_query->fetch();
$customer_query->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $suggestions = htmlspecialchars($_POST['suggestions'], ENT_QUOTES, 'UTF-8'); // Sanitize suggestions

    // Prepare and execute the insert statement
    $insert_stmt = $conn->prepare("INSERT INTO feedback (customer_name, rating, suggestions) VALUES (?, ?, ?)");
    $insert_stmt->bind_param('sis', $customer_name, $rating, $suggestions); // Bind parameters

    if ($insert_stmt->execute()) {
        header('Location: customer_dashboard.php');
        exit();
    } else {
        $_SESSION['message'] = 'Error submitting feedback: ' . addslashes($insert_stmt->error); // Store error message
        header('Location: feedback.php');
        exit();
    }
    $insert_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ff7e5f, #feb47b); /* Gradient background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .alert {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d4edda;
            border-radius: 5px;
            background-color: #f8f9fa;
            color: #155724;
            position: relative;
            width: 100%;
            z-index: 1000;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .rating {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .star {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star:hover,
        .star.selected {
            color: #ffcc00; /* Color for selected stars */
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            resize: none;
            height: 100px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div id="message" class="alert" style="<?php echo isset($_SESSION['message']) ? '' : 'display:none;'; ?>">
        <?php 
            if (isset($_SESSION['message'])) {
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Clear message after displaying
            }
        ?>
    </div>

    <!-- Feedback Form -->
    <form method="POST" action="">
        <h2>Feedback Form</h2>
        
        <div class="rating">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>

        <input type="hidden" name="rating" id="rating" required>

        <label for="suggestions">Suggestions:</label>
        <textarea name="suggestions" id="suggestions" placeholder="Your feedback or suggestions here..." required></textarea>

        <button type="submit">Submit Feedback</button>
    </form>
</div>

<script>
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.getAttribute('data-value');
            ratingInput.value = rating; // Set the rating input
            stars.forEach(s => {
                s.classList.remove('selected'); // Remove selected class from all stars
            });
            for (let i = 0; i < rating; i++) {
                stars[i].classList.add('selected'); // Highlight selected stars
            }
        });
    });
</script>

</body>
</html>
