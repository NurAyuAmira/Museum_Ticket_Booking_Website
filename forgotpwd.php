<?php
include("connection.php");
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST["sendotpbtn"])) {
    $email = htmlspecialchars($_POST["email"]);
    $_SESSION["email"] = $email;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email LIKE ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $otp = rand(100000, 999999);
        $_SESSION["otp"] = $otp;
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '';
            $mail->Password = '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('', 'Museum');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Museum - Verify Your OTP';
            $mail->Body = "<p>Dear User,</p><br><p>Your OTP Number Is <b>$otp</b></p><br><p>Thank You!</p>";

            $mail->send();
            $_SESSION["step"] = 2;
            echo '<script>alert("OTP Already Sent To Your Email...\\nPlease Check!"); location.href="forgotpwd.php?step=2";</script>';
        } catch (Exception $e) {
            echo '<script>alert("Something went wrong...Please Try Later");</script>';
        }
    } else {
        echo '<script>alert("Your Email Has Not Found!"); location.href="forgotpwd.php?step=1";</script>';
    }
    $stmt->close();
}

if (isset($_POST["verifyotpbtn"])) {
    $inputOtp = $_POST["otp"];
    if ($inputOtp == $_SESSION["otp"]) {
        $_SESSION["step"] = 3;
        echo '<script>location.href="forgotpwd.php?step=3";</script>';
    } else {
        echo '<script>alert("Your OTP Is Not Correct!"); location.href="forgotpwd.php?step=2";</script>';
    }
}

if (isset($_POST["resetbtn"])) {
    $newPwd = password_hash($_POST["newpwd"], PASSWORD_BCRYPT);
    $email = $_SESSION["email"];
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email LIKE ?");
    $stmt->bind_param("ss", $newPwd, $email);
    $stmt->execute();
    $stmt->close();
    unset($_SESSION["otp"]);
    unset($_SESSION["email"]);
    unset($_SESSION["step"]);
    echo '<script>alert("Your Password Has Been Reset...\\nPlease Login"); location.href="login.php";</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Forgot Password</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <?php if(!isset($_SESSION["step"]) || $_SESSION["step"] == 1): ?>
    <form method="post" action="forgotpwd.php">
        <div class="mb-3 mt-3">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary" name="sendotpbtn">Send OTP</button>
    </form>
    <?php elseif($_SESSION["step"] == 2): ?>
    <form method="post" action="forgotpwd.php">
        <div class="mb-3 mt-3">
            <label for="otp">OTP:</label>
            <input type="text" class="form-control" id="otp" placeholder="Enter OTP" name="otp" required>
        </div>
        <button type="submit" class="btn btn-primary" name="verifyotpbtn">Verify OTP</button>
    </form>
    <?php elseif($_SESSION["step"] == 3): ?>
    <form method="post" action="forgotpwd.php">
        <div class="mb-3 mt-3">
            <label for="newpwd">New Password:</label>
            <input type="password" class="form-control" id="newpwd" placeholder="Enter new password" name="newpwd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"  required>
        </div>
        <button type="submit" class="btn btn-primary" name="resetbtn">Reset Password</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
