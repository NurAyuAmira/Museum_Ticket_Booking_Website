<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch available exhibits
$stmt = $conn->prepare("SELECT id, name, description, date, tickets_available, price FROM exhibits");
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $description, $date, $tickets_available, $price);

// Store exhibit details in an array
$exhibits = [];
while ($stmt->fetch()) {
    $exhibits[] = [
        'id' => $id,
        'name' => $name,
        'description' => $description,
        'date' => $date,
        'tickets_available' => $tickets_available,
        'price' => $price,
    ];
}
$stmt->close();

// Fetch user's reservation history
$reservation_stmt = $conn->prepare("SELECT reservations.id, exhibits.name, reservations.num_tickets, reservations.reservation_date, (reservations.num_tickets * exhibits.price) AS total_price FROM reservations 
                                    JOIN exhibits ON reservations.exhibit_id = exhibits.id 
                                    WHERE reservations.user_id = ?");
$reservation_stmt->bind_param("i", $user_id);
$reservation_stmt->execute();
$reservation_stmt->store_result();
$reservation_stmt->bind_result($reservation_id, $exhibit_name, $num_tickets, $reservation_date, $total_price);

// Store reservation details in an array
$reservations = [];
while ($reservation_stmt->fetch()) {
    $reservations[] = [
        'id' => $reservation_id,
        'exhibit_name' => $exhibit_name,
        'num_tickets' => $num_tickets,
        'reservation_date' => $reservation_date,
        'total_price' => $total_price,
    ];
}
$reservation_stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museum Reservation System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Welcome to the Museum</h1>
    
    <a href="profile.php">User Profile</a>
    
    <h2>Available Exhibits</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Date</th>
            <th>Tickets Available</th>
            <th>Price per Ticket</th>
            <th>Reserve</th>
        </tr>
        <?php foreach ($exhibits as $exhibit): ?>
        <tr>
            <td><?php echo htmlspecialchars($exhibit['name']); ?></td>
            <td><?php echo htmlspecialchars($exhibit['description']); ?></td>
            <td><?php echo htmlspecialchars($exhibit['date']); ?></td>
            <td><?php echo htmlspecialchars($exhibit['tickets_available']); ?></td>
            <td><?php echo htmlspecialchars($exhibit['price']); ?></td>
            <td>
                <form action="reserve.php" method="post">
                    <input type="hidden" name="exhibit_id" value="<?php echo htmlspecialchars($exhibit['id']); ?>">
                    <input type="number" name="num_tickets" min="1" max="<?php echo htmlspecialchars($exhibit['tickets_available']); ?>" required>
                    <button type="submit">Reserve</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Your Order History</h2>
    <table>
        <tr>
            <th>Reservation ID</th>
            <th>Exhibit</th>
            <th>Number of Tickets</th>
            <th>Reservation Date</th>
            <th>Total Price</th>
        </tr>
        <?php foreach ($reservations as $reservation): ?>
        <tr>
            <td><?php echo htmlspecialchars($reservation['id']); ?></td>
            <td><?php echo htmlspecialchars($reservation['exhibit_name']); ?></td>
            <td><?php echo htmlspecialchars($reservation['num_tickets']); ?></td>
            <td><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
            <td>RM<?php echo htmlspecialchars($reservation['total_price']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a href="logout.php">Logout</a>
</body>
</html>
