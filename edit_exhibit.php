<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $tickets_available = $_POST['tickets_available'];
    $price = $_POST['price'];
} else {
    header("Location: exhibits.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Exhibit</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Edit Exhibit</h1>
    <form action="exhibits.php" method="post">
        <input type="hidden" name="edit_exhibit" value="1">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
        <label for="tickets_available">Tickets Available:</label>
        <input type="number" id="tickets_available" name="tickets_available" value="<?php echo htmlspecialchars($tickets_available); ?>" required>
        <label for="price">Price per Ticket:</label>
        <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
        <button type="submit">Update Exhibit</button>
    </form>
    <a href="exhibits.php">Back to Exhibits</a>
</body>
</html>
