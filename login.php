<?php
    session_start();
    require_once 'connection.php';

    function logActivity($user_id, $activity_type, $activity_details) {
        global $conn;
        $query = "INSERT INTO activity_logs (user_id, activity_type, activity_details) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iss', $user_id, $activity_type, $activity_details);
        $stmt->execute();
    }
    
    function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

$ip_address = get_client_ip();
$time_limit = 60; // Time frame in seconds
$request_limit = 100; // Max number of requests allowed in the time frame

// Check if the IP address exists in the traffic_monitor table
$query = "SELECT * FROM traffic_monitor WHERE ip_address = '$ip_address'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error executing query: ' . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

if ($row) {
    // Update request count and last request time
    if ($row['request_count'] >= $request_limit && (time() - strtotime($row['last_request']) < $time_limit)) {
        // Block the request if the limit is exceeded
        die('Too many requests. Please try again later.');
    } elseif (time() - strtotime($row['last_request']) > $time_limit) {
        // Reset the count if the time limit has passed
        $query = "UPDATE traffic_monitor SET request_count = 1, last_request = NOW() WHERE ip_address = '$ip_address'";
    } else {
        // Increment the request count
        $query = "UPDATE traffic_monitor SET request_count = request_count + 1, last_request = NOW() WHERE ip_address = '$ip_address'";
    }
} else {
    // Insert new IP address with request count 1
    $query = "INSERT INTO traffic_monitor (ip_address, request_count) VALUES ('$ip_address', 1)";
}

if (!mysqli_query($conn, $query)) {
    die('Error executing query: ' . mysqli_error($conn));
}

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $username = htmlspecialchars($_POST['username']);
             $password = $_POST['password'];
             $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
             $stmt->bind_param("s", $username);
             $stmt->execute();
             $stmt->store_result();
             $stmt->bind_result($id, $hashed_password);
             $stmt->fetch();

             // Query to check user credentials
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];

                    // Log the current request
                    $query = "INSERT INTO traffic_monitor (user_id, ip_address) VALUES ('$id', '$ip_address')";
                    if (!mysqli_query($conn, $query)) {
                    die('Error logging request: ' . mysqli_error($conn));
                    }

                    // Count the number of requests from this IP and user in the last $time_limit seconds
                    $query = "SELECT COUNT(*) AS request_count FROM traffic_monitor WHERE ip_address = '$ip_address' AND user_id = '$id' AND last_request > (NOW() - INTERVAL $time_limit SECOND)";
                    $result = mysqli_query($conn, $query);

                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        echo "Request count: " . $row['request_count']; // Log the request count
                        if ($row['request_count'] > $request_limit) {
                            die('Too many requests. Please try again later.');
                        }
                    } else {
                        die('Error counting requests: ' . mysqli_error($conn));
                    }

                    logActivity($user['id'], 'Login attempt', 'User Logged In');
                    header('Location: index.php');
                } else {
                    logActivity($user['id'], 'Failed login attempt', 'Incorrect password');
                    echo "Invalid username or password.";
                }
            } else {
                echo "Invalid username or password.";
            }

         if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
             $_SESSION['user_id'] = $id;
             // Check if the IP address and user ID exist in the traffic_monitor table
        $query = "SELECT * FROM traffic_monitor WHERE ip_address = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $ip_address, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // Update request count and last request time
            if ($row['request_count'] >= $request_limit && (time() - strtotime($row['last_request']) < $time_limit)) {
                // Block the request if the limit is exceeded
                die('Too many requests. Please try again later.');
            } elseif (time() - strtotime($row['last_request']) > $time_limit) {
                // Reset the count if the time limit has passed
                $query = "UPDATE traffic_monitor SET request_count = 1, last_request = NOW() WHERE ip_address = ? AND user_id = ?";
            } else {
                // Increment the request count
                $query = "UPDATE traffic_monitor SET request_count = request_count + 1, last_request = NOW() WHERE ip_address = ? AND user_id = ?";
            }
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $ip_address, $id);
        } else {
            // Insert new IP address and user ID with request count 1
            $query = "INSERT INTO traffic_monitor (user_id, ip_address, request_count) VALUES (?, ?, 1)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('is', $id, $ip_address);
        }

        if (!$stmt->execute()) {
            die('Error logging request: ' . mysqli_error($conn));
        }

        logActivity($id, 'Login attempt', 'User Logged In');
        header("Location: index.php");
    } else {
        logActivity($id, 'Failed login attempt', 'Incorrect password');
        echo "Invalid credentials";
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
 <title>Login</title>
 <link rel="stylesheet" href="css/styles.css">
</head>
<body>
     <h2>Login</h2>
         <form action="login.php" method="post">
         <label for="username">Username:</label>
         <input type="text" id="username" name="username" required>
         <label for="password">Password:</label>
         <input type="password" id="password" name="password" required>
         <button type="submit">Login</button>
     </form>
    <a href="register.php">Register</a>
    <a href="forgotpwd.php">Forgot Password</a>
</body>
</html>
