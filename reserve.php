<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $exhibit_id = $_POST['exhibit_id'];
    $num_tickets = $_POST['num_tickets'];

    // Fetch tickets available and price
    $stmt = $conn->prepare("SELECT tickets_available, price FROM exhibits WHERE id = ?");
    $stmt->bind_param("i", $exhibit_id);
    $stmt->execute();
    $stmt->bind_result($tickets_available, $price);
    $stmt->fetch();
    $stmt->close();

    // Ensure enough tickets are available
    if ($tickets_available >= $num_tickets) {
        // Insert reservation
        $stmt = $conn->prepare("INSERT INTO reservations (user_id, exhibit_id, num_tickets) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $exhibit_id, $num_tickets);
        if ($stmt->execute()) {
            $reservation_id = $stmt->insert_id;
            $stmt->close();

            // Update tickets available
            $stmt = $conn->prepare("UPDATE exhibits SET tickets_available = tickets_available - ? WHERE id = ?");
            $stmt->bind_param("ii", $num_tickets, $exhibit_id);
            $stmt->execute();
            $stmt->close();

            // Calculate total price
            $total_price = $num_tickets * $price;

            // Redirect to payment page with reservation ID, total price, num tickets, and exhibit ID
            header("Location: payment.php?reservation_id=$reservation_id&total_price=$total_price&num_tickets=$num_tickets&exhibit_id=$exhibit_id");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Not enough tickets available";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Tickets</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <a href="index.php">Back to Exhibits</a>
</body>
</html>
