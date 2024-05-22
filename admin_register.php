<?php
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = htmlspecialchars($_POST['email']);

    $stmt = $conn->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $email);

    if ($stmt->execute()) {
        header("Location: admin_login.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Admin Register</h2>
    <form action="admin_register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" pattern="^[a-zA-Z0-9_]{3,20}$" title="Username must be 3-20 characters long and can only contain letters, numbers, and underscores." required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
        <button type="submit">Register</button>
    </form>
    <a href="admin_login.php">Admin Login</a>
</body>
</html>
