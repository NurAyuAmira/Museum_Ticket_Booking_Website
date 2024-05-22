<?php
session_start();
require_once 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$notification = "";

// Fetch user details
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_username = htmlspecialchars($_POST['username']);
        $new_email = htmlspecialchars($_POST['email']);
        
        if (empty($new_username) || empty($new_email)) {
            $notification = "All fields are required.";
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $notification = "Invalid email format.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $new_username)) {
            $notification = "Username must be 3-20 characters long and can only contain letters, numbers, and underscores.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
            
            if ($stmt->execute()) {
                $notification = "Profile updated successfully.";
            } else {
                $notification = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $notification = "All fields are required.";
        } elseif (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $new_password)) {
            $notification = "Password must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters.";
        } elseif ($new_password !== $confirm_password) {
            $notification = "Passwords do not match.";
        } else {
            // Check current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($stored_password);
            $stmt->fetch();
            $stmt->close();
            
            if (password_verify($current_password, $stored_password)) {
                $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);
                
                // Update to new password
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_new_password, $user_id);
                
                if ($stmt->execute()) {
                    $notification = "Password updated successfully.";
                } else {
                    $notification = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $notification = "Current password is incorrect.";
            }
        }
    } elseif (isset($_POST['delete_profile'])) {
        // Delete user profile
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $notification = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .invalid { color: red; }
        .valid { color: green; }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var notification = "<?php echo $notification; ?>";
            if (notification) {
                alert(notification);
            }

            var newPassword = document.getElementById("new_password");
            var letter = document.getElementById("letter");
            var capital = document.getElementById("capital");
            var number = document.getElementById("number");
            var length = document.getElementById("length");

            newPassword.onfocus = function() {
                document.getElementById("message").style.display = "block";
            }

            newPassword.onblur = function() {
                document.getElementById("message").style.display = "none";
            }

            newPassword.onkeyup = function() {
                // Validate lowercase letters
                var lowerCaseLetters = /[a-z]/g;
                if(newPassword.value.match(lowerCaseLetters)) {
                    letter.classList.remove("invalid");
                    letter.classList.add("valid");
                } else {
                    letter.classList.remove("valid");
                    letter.classList.add("invalid");
                }

                // Validate capital letters
                var upperCaseLetters = /[A-Z]/g;
                if(newPassword.value.match(upperCaseLetters)) {
                    capital.classList.remove("invalid");
                    capital.classList.add("valid");
                } else {
                    capital.classList.remove("valid");
                    capital.classList.add("invalid");
                }

                // Validate numbers
                var numbers = /[0-9]/g;
                if(newPassword.value.match(numbers)) {
                    number.classList.remove("invalid");
                    number.classList.add("valid");
                } else {
                    number.classList.remove("valid");
                    number.classList.add("invalid");
                }

                // Validate length
                if(newPassword.value.length >= 8) {
                    length.classList.remove("invalid");
                    length.classList.add("valid");
                } else {
                    length.classList.remove("valid");
                    length.classList.add("invalid");
                }
            }
        });
    </script>
</head>
<body>
    <h1>Profile</h1>
    <form action="profile.php" method="post">
        <h2>Update Profile</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" pattern="^[a-zA-Z0-9_]{3,20}$" title="Username must be 3-20 characters long and can only contain letters, numbers, and underscores." value="<?php echo htmlspecialchars($username); ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        
        <input type="hidden" name="update_profile" value="1">
        <button type="submit">Update Profile</button>
    </form>

    <form action="profile.php" method="post">
        <h2>Update Password</h2>
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>
        
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
        
        <div id="message">
            <h4>Password must contain the following:</h4>
            <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
            <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
            <p id="number" class="invalid">A <b>number</b></p>
            <p id="length" class="invalid">Minimum <b>8 characters</b></p>
        </div>
        
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
        
        <input type="hidden" name="update_password" value="1">
        <button type="submit">Update Password</button>
    </form>

    <form action="profile.php" method="post">
        <h2>Delete Profile</h2>
        <p>Warning: This action is irreversible. Your profile will be permanently deleted.</p>
        <input type="hidden" name="delete_profile" value="1">
        <button type="submit" style="background-color: red; color: white;">Delete Profile</button>
    </form>

    <a href="index.php">Back to Exhibits</a>
</body>
</html>
