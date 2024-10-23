<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Table</title>
    <style>
        /* General reset and styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Times New Roman', sans-serif;
        }

        /* Background styling */
        body {
            background-image: url('assets/img/bg_admin_login.jpg'); /* Background image */
            background-size: cover; /* Cover the whole page */
            background-position: center; /* Center the image */
            background-attachment: fixed; /* Ensure the background stays fixed */
            background-repeat: no-repeat;
            height: 100vh; /* Full viewport height */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
            color: #fff; /* Text color to contrast with the background */
        }

        /* Login form styling */
        .login-form {
            background-color: rgba(255, 255, 255, 0.7); /* White background with opacity */
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            padding: 40px 60px;
            width: 100%;
            max-width: 400px;
        }

        /* Header styling */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px; /* Increased font size */
            color: black; /* Changed to black for better contrast against the background */
        }

        /* Error message styling */
        .error {
            color: #d9534f;
            background-color: #f2dede;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }

        /* Input fields styling */
        input[type="text"],
        input[type="password"],
        input[type="number"],
        select {
            width: 100%;
            padding: 14px; /* Increased padding for better touch target */
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
            transition: 0.3s ease;
            background-color: #f9f9f9; /* Light background for input fields */
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Button styling */
        button,
        input[type="submit"] {
            width: 100%;
            padding: 14px; /* Increased padding for better touch target */
            background-color: #007bff; /* Primary color */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover,
        input[type="submit"]:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }

        button:active,
        input[type="submit"]:active {
            background-color: #00408a; /* Even darker shade on active */
        }

        /* Radio button styling */
        .role-selection {
            margin: 20px 0;
            text-align: center;
        }

        .role-selection input {
            margin-right: 10px;
        }

        .role-selection label {
            color: white; /* Fixed the typo "while" to "white" */
            font-size: 16px;
        }

        /* Registration link styling */
        .register-link {
            margin-top: 20px;
            text-align: center;
            color: white;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            .login-form {
                padding: 20px 30px; /* Reduced padding for smaller screens */
            }

            h2 {
                font-size: 24px; /* Adjusted font size */
            }
        }

        /* Section background color */
        .book-a-table {
            background-color: white; /* White background for the table section */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
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

<body class="index-page">
<!-- Button to go back to the Dashboard -->

  <main class="main">

    <!-- Add A Table Section -->
    <section id="book-a-table" class="book-a-table section">
        <div class="container">
            <div class="row g-0" data-aos="fade-up" data-aos-delay="100">
            <h2>Add A Table</h2>
                <div class="col-lg-4 reservation-img" style="background-image: url(assets/img/reservation.jpg);"></div>

                <div class="col-lg-8 d-flex align-items-center reservation-form-bg" data-aos="fade-up" data-aos-delay="200">
                    <form action="admin_add_table_process.php" method="post" role="form" class="php-email-form">
                        <div class="row gy-4">
                            <div class="col-lg-4 col-md-6">
                                <input type="number" name="tableno" class="form-control" id="tableno" placeholder="Table Number" required="">
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <input type="number" class="form-control" name="tablesize" id="tablesize" placeholder="Table Size" required="">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Add Table</button>
                    </form>
                </div><!-- End Add Table Form -->
            </div>
        </div>
    </section><!-- /Book A Table Section -->

<div style="text-align: right; margin-bottom: 20px;">
    <form action="admin_dashboard.php" method="POST" style="display: inline;">
        <button type="submit" class="dbutton">Go to Dashboard</button>
    </form>
</div>
</main>
</body>

</html>
