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

// Fetch user details
$user_stmt = $conn->prepare("SELECT id, username, email, created_at FROM users");
if (!$user_stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$user_stmt->execute();
$user_stmt->store_result();
$user_stmt->bind_result($user_id, $username, $email, $created_at);

// Store user details in an array
$users = [];
while ($user_stmt->fetch()) {
    $users[] = [
        'id' => $user_id,
        'username' => $username,
        'email' => $email,
        'created_at' => $created_at,
    ];
}
$user_stmt->close();

// Fetch reservations with total price
$reservation_stmt = $conn->prepare("SELECT reservations.id, users.username, exhibits.name, reservations.num_tickets, reservations.reservation_date, (reservations.num_tickets * exhibits.price) AS total_price FROM reservations 
                                    JOIN users ON reservations.user_id = users.id 
                                    JOIN exhibits ON reservations.exhibit_id = exhibits.id");
if (!$reservation_stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$reservation_stmt->execute();
$reservation_stmt->store_result();
$reservation_stmt->bind_result($reservation_id, $user_name, $exhibit_name, $num_tickets, $reservation_date, $total_price);

// Store reservation details in an array
$reservations = [];
while ($reservation_stmt->fetch()) {
    $reservations[] = [
        'id' => $reservation_id,
        'username' => $user_name,
        'exhibit_name' => $exhibit_name,
        'num_tickets' => $num_tickets,
        'reservation_date' => $reservation_date,
        'total_price' => $total_price,
    ];
}
$reservation_stmt->close();

// Fetch payments
$payment_stmt = $conn->prepare("SELECT payments.id, users.username, exhibits.name, payments.cardholder_name, payments.card_number, payments.expiry_date, payments.cvv_number, payments.payment_date FROM payments 
                                JOIN reservations ON payments.reservation_id = reservations.id 
                                JOIN users ON reservations.user_id = users.id 
                                JOIN exhibits ON reservations.exhibit_id = exhibits.id");
if (!$payment_stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$payment_stmt->execute();
$payment_stmt->store_result();
$payment_stmt->bind_result($payment_id, $payer_username, $paid_exhibit_name, $cardholder_name, $card_number, $expiry_date, $cvv_number, $payment_date);

// Store payment details in an array
$payments = [];
while ($payment_stmt->fetch()) {
    $payments[] = [
        'id' => $payment_id,
        'username' => $payer_username,
        'exhibit_name' => $paid_exhibit_name,
        'cardholder_name' => $cardholder_name,
        'card_number' => $card_number,
        'expiry_date' => $expiry_date,
        'cvv_number' => $cvv_number,
        'payment_date' => $payment_date,
    ];
}
$payment_stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    
</head>
<body>
    <h1>Admin Dashboard</h1>
    
    <nav>
        <ul>
            <a href="admin_activity_logs.php">View User Activity Logs</a>
            <a href="admin_monitor_traffic.php">View User Monitor Traffic</a>
            <a href="backup_system.php">Backup & Recovery Database/Systems</a>
            <!-- Add other admin links here -->
        </ul>
    </nav>
    
    <h2>User Details</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Created At</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Reservations</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Exhibit</th>
            <th>Tickets</th>
            <th>Reservation Date</th>
            <th>Total Price</th>
        </tr>
        <?php foreach ($reservations as $reservation): ?>
        <tr>
            <td><?php echo htmlspecialchars($reservation['id']); ?></td>
            <td><?php echo htmlspecialchars($reservation['username']); ?></td>
            <td><?php echo htmlspecialchars($reservation['exhibit_name']); ?></td>
            <td><?php echo htmlspecialchars($reservation['num_tickets']); ?></td>
            <td><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
            <td>RM<?php echo htmlspecialchars($reservation['total_price']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Payments</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Exhibit</th>
            <th>Cardholder Name</th>
            <th>Card Number</th>
            <th>Expiry Date</th>
            <th>CVV Number</th>
            <th>Payment Date</th>
        </tr>
        <?php foreach ($payments as $payment): ?>
        <tr>
            <td><?php echo htmlspecialchars($payment['id']); ?></td>
            <td><?php echo htmlspecialchars($payment['username']); ?></td>
            <td><?php echo htmlspecialchars($payment['exhibit_name']); ?></td>
            <td><?php echo htmlspecialchars($payment['cardholder_name']); ?></td>
            <td><?php echo htmlspecialchars($payment['card_number']); ?></td>
            <td><?php echo htmlspecialchars($payment['expiry_date']); ?></td>
            <td><?php echo htmlspecialchars($payment['cvv_number']); ?></td>
            <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <a href="exhibits.php">Manage Exhibits</a>
    <a href="admin_logout.php">Logout</a>
</body>
</html>
