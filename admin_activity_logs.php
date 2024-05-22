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
$sql = "SELECT activity_logs.id, activity_logs.user_id, activity_logs.activity_type, activity_logs.activity_details, activity_logs.timestamp 
        FROM activity_logs 
        JOIN users ON activity_logs.user_id = users.id
        ORDER BY activity_logs.timestamp DESC";
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
    <title>User Activity Logs</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>User Activity Logs</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Action</th>
                <th>Details</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['activity_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['activity_details']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No activity logs found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
