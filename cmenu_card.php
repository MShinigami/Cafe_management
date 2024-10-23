<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Management - Order Management</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Custom Styles -->
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('assets/img/bg_dashboard.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            opacity: 0.9; 
        }
        .go-dashboard-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            z-index: 10;
        }

        .go-orders-section-btn {
            position: fixed;
            top: 70px;
            left: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            z-index: 10;
        }

        .go-add-section-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            z-index: 10;
        }

        .container {
            max-width: 800px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            border: 1px solid #ddd;
        }
        .form-container h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        input[type="number"], textarea {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s ease;
        }
        input[type="number"]:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        textarea {
            height: 100px;
            resize: none;
            font-family: Arial, sans-serif;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
        input[type="submit"]:active {
            background-color: #1e7e34;
            transform: scale(1);
        }
        label {
            font-size: 18px;
            color: #555;
            display: block;
            margin-bottom: 10px;
        }
        table {
            background-color: white;
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }
        th {
            background-color: #f2f2f2;
            color: black;
            padding: 20px;
            border: 1px solid #ddd;
            text-align: left;
        }
        td {
            background-color: white;
            color: black;
            padding: 10px;
            border: 1px solid #ddd;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        th, td {
            padding: 20px 15px;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Button to go to the Dashboard -->
    <form action="customer_dashboard.php" method="POST">
        <button type="submit" class="go-dashboard-btn">Go to Dashboard</button>
    </form>
    <!-- Menu Section -->
    <div id="menu-section" class="mt-4">
        <h3>Café Menu</h3>
        <div class="row">
            <?php
            $menuItems = [
                [
                    'image' => 'assets/img/hotcoffee.png',
                    'name' => 'Hot Coffee',
                    'description' => 'A rich and aromatic cup of hot coffee.',
                ],
                [
                    'image' => 'assets/img/coldcoffee.png',
                    'name' => 'Cold Coffee',
                    'description' => 'A refreshing glass of cold coffee served with ice.',
                ],
                [
                    'image' => 'assets/img/tea.png',
                    'name' => 'Tea',
                    'description' => 'A soothing cup of freshly brewed tea.',
                ],
                [
                    'image' => 'assets/img/icedtea.png',
                    'name' => 'Iced Tea',
                    'description' => 'Chilled tea served with lemon and ice.',
                ],
                [
                    'image' => 'assets/img/sandwich.png',
                    'name' => 'Sandwich',
                    'description' => 'A delicious sandwich with fresh ingredients.',
                ],
                [
                    'image' => 'assets/img/pizza.png',
                    'name' => 'Pizza',
                    'description' => 'A slice of cheesy pizza topped with fresh vegetables.',
                ],
                [
                    'image' => 'assets/img/burger.png',
                    'name' => 'Burger',
                    'description' => 'A juicy burger with all the fixings.',
                ],
                [
                    'image' => 'assets/img/fries.png',
                    'name' => 'Fries',
                    'description' => 'Crispy fries served with your choice of dipping sauce.',
                ]
            ];

            foreach ($menuItems as $item) {
                echo "<div class='col-md-3 text-center'>";
                echo "<img src='{$item['image']}' alt='{$item['name']}' class='card-img-top' style='width: 100%; height: auto;' onclick=\"document.getElementsByName('order_details')[0].value += '{$item['name']}, ';\">";
                echo "<h5>{$item['name']}</h5>";
                echo "<p>{$item['description']}</p>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
