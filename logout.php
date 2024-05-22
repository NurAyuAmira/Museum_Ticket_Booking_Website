<?php
session_start();
include 'connection.php'; // Include your database connection file

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    logActivity($user_id, 'Logout', 'User logged out');
    session_destroy();
    header('Location: login.php');
} else {
    header('Location: login.php');
}

function logActivity($user_id, $activity_type, $activity_details) {
    global $conn;
    $query = "INSERT INTO activity_logs (user_id, activity_type, activity_details) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iss', $user_id, $activity_type, $activity_details);
    $stmt->execute();
}

?>
