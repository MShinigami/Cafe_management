<?php
// Include database connection
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $people = $_POST['people'];
    $table_id = $_POST['table_id'];

    // Check if the selected table can accommodate the number of people
    $table_query = "SELECT tablesize FROM tables WHERE id = ?";
    $table_stmt = $conn->prepare($table_query);
    $table_stmt->bind_param("i", $table_id);
    $table_stmt->execute();
    $table_result = $table_stmt->get_result();

    if ($table_result->num_rows > 0) {
        $table_data = $table_result->fetch_assoc();
        if ($table_data['tablesize'] >= $people) {
            // Insert reservation into the database
            $sql = "INSERT INTO reservations (name, email, phone, people, table_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $name, $email, $phone, $people, $table_id);

            if ($stmt->execute()) {
                // Update the table status if the reservation is successful
                $update_sql = "UPDATE tables SET status = 'reserved' WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $table_id);
                $update_stmt->execute();

                echo "Table booked successfully!";
            } else {
                echo "Error booking table: " . $stmt->error;
            }
        } else {
            echo "Selected table cannot accommodate the number of people.";
        }
    } else {
        echo "Invalid table selection.";
    }

    $stmt->close();
}
$conn->close();
?>
