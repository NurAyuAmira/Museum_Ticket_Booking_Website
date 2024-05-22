<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_exhibit'])) {
        $name = htmlspecialchars($_POST['name']);
        $description = htmlspecialchars($_POST['description']);
        $date = $_POST['date'];
        $tickets_available = $_POST['tickets_available'];
        $price = $_POST['price'];

        $stmt = $conn->prepare("INSERT INTO exhibits (name, description, date, tickets_available, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $name, $description, $date, $tickets_available, $price);
        
        if ($stmt->execute()) {
            echo "Exhibit added successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['edit_exhibit'])) {
        $id = $_POST['id'];
        $name = htmlspecialchars($_POST['name']);
        $description = htmlspecialchars($_POST['description']);
        $date = $_POST['date'];
        $tickets_available = $_POST['tickets_available'];
        $price = $_POST['price'];

        $stmt = $conn->prepare("UPDATE exhibits SET name = ?, description = ?, date = ?, tickets_available = ?, price = ? WHERE id = ?");
        $stmt->bind_param("sssiii", $name, $description, $date, $tickets_available, $price, $id);
        
        if ($stmt->execute()) {
            echo "Exhibit updated successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['delete_exhibit'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM exhibits WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "Exhibit deleted successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT id, name, description, date, tickets_available, price FROM exhibits");
$stmt->execute();
$stmt->bind_result($id, $name, $description, $date, $tickets_available, $price);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exhibits</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var notification = "<?php echo $notification; ?>";
            if (notification) {
                alert(notification);
            }
        });
    </script>
</head>
<body>
    <h1>Manage Exhibits</h1>
    
    <h2>Add New Exhibit</h2>
    <form action="exhibits.php" method="post">
        <input type="hidden" name="add_exhibit" value="1">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>
        <label for="tickets_available">Tickets Available:</label>
        <input type="number" id="tickets_available" name="tickets_available" required>
        <label for="price">Price per Ticket:</label>
        <input type="number" step="0.01" id="price" name="price" required>
        <button type="submit">Add Exhibit</button>
    </form>

    <h2>Current Exhibits</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Date</th>
            <th>Tickets Available</th>
            <th>Price per Ticket</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php while ($stmt->fetch()): ?>
        <tr>
            <td><?php echo htmlspecialchars($name); ?></td>
            <td><?php echo htmlspecialchars($description); ?></td>
            <td><?php echo htmlspecialchars($date); ?></td>
            <td><?php echo htmlspecialchars($tickets_available); ?></td>
            <td><?php echo htmlspecialchars($price); ?></td>
            <td>
                <form action="edit_exhibit.php" method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                    <input type="hidden" name="description" value="<?php echo htmlspecialchars($description); ?>">
                    <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
                    <input type="hidden" name="tickets_available" value="<?php echo htmlspecialchars($tickets_available); ?>">
                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
                    <button type="submit">Edit</button>
                </form>
            </td>
            <td>
                <form action="exhibits.php" method="post" style="display:inline;">
                    <input type="hidden" name="delete_exhibit" value="1">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this exhibit?');">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
