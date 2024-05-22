<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['reservation_id']) || !isset($_GET['num_tickets']) || !isset($_GET['exhibit_id'])) {
    die("Reservation ID, number of tickets, and exhibit ID are required.");
}

$reservation_id = $_GET['reservation_id'];
$num_tickets = $_GET['num_tickets'];
$exhibit_id = $_GET['exhibit_id'];

// Delete the reservation
$stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
$stmt->bind_param("i", $reservation_id);
if ($stmt->execute()) {
    // Update the ticket quantity
    $stmt = $conn->prepare("UPDATE exhibits SET tickets_available = tickets_available + ? WHERE id = ?");
    $stmt->bind_param("ii", $num_tickets, $exhibit_id);
    $stmt->execute();
    echo "Reservation canceled successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: index.php");
exit();
?>
