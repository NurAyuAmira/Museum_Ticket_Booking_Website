<?php
session_start();
require_once 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch activity logs
$sql = "SELECT traffic_monitor.id, traffic_monitor.user_id, traffic_monitor.ip_address, traffic_monitor.request_count, traffic_monitor.last_request
        FROM traffic_monitor 
        JOIN users ON traffic_monitor.user_id = users.id
        ORDER BY traffic_monitor.last_request DESC";
$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error;
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Monitor Traffic</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>User Monitor Traffic</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Ip Address</th>
                <th>Request Count</th>
                <th>Last Request</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ip_address']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['request_count']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['last_request']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No monitor traffic found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
