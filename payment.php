<?php
session_start();
require_once 'connection.php';

// Function to encrypt data
function encryptData($data, $encryption_key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

// Function to mask card number
function maskCardNumber($card_number) {
    return str_repeat('X', strlen($card_number) - 4) . substr($card_number, -4);
}

function maskExpiryDate($expiryDate) {
    $parts = explode('/', $expiryDate);
    $maskedYear = str_repeat('X', strlen($parts[1]));  // Mask the year part
    return $parts[0] . '/' . $maskedYear;
}

function maskCVV($cvv) {
    return str_repeat('X', strlen($cvv));
}

$encryption_key = 'q6g7VTCbhD0fRLHVNcV/5aVSnM4CvElwS+nyKOIHiQs=';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_GET['reservation_id']) || !isset($_GET['total_price'])) {
        die("Reservation ID and total price are required.");
    }
    $reservation_id = $_GET['reservation_id'];
    $total_price = $_GET['total_price'];
    $num_tickets = $_GET['num_tickets'];
    $exhibit_id = $_GET['exhibit_id'];
    $cardholder_name = encryptData($_POST['cardholder_name'], $encryption_key);
    $card_number = maskCardNumber($_POST['card_number']);
    $expiry_date = maskExpiryDate($_POST['expiry_date']);
    $cvv_number = maskCVV($_POST['cvv_number']);

    $stmt = $conn->prepare("INSERT INTO payments (reservation_id, cardholder_name, card_number, expiry_date, cvv_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $reservation_id, $cardholder_name, $card_number, $expiry_date, $cvv_number);

    if ($stmt->execute()) {
        echo "Payment processed successfully.";
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    if (!isset($_GET['reservation_id']) || !isset($_GET['total_price']) || !isset($_GET['num_tickets']) || !isset($_GET['exhibit_id'])) {
        die("Reservation ID, total price, number of tickets, and exhibit ID are required.");
    }
    $reservation_id = $_GET['reservation_id'];
    $total_price = $_GET['total_price'];
    $num_tickets = $_GET['num_tickets'];
    $exhibit_id = $_GET['exhibit_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Payment</h2>
    <form action="payment.php?reservation_id=<?php echo htmlspecialchars($reservation_id); ?>&total_price=<?php echo htmlspecialchars($total_price); ?>&num_tickets=<?php echo htmlspecialchars($num_tickets); ?>&exhibit_id=<?php echo htmlspecialchars($exhibit_id); ?>" method="post">
        <p>Total Price: RM<?php echo htmlspecialchars($total_price); ?></p>
        <label for="cardholder_name">Cardholder Name:</label>
        <input type="text" id="cardholder_name" name="cardholder_name" pattern="[a-zA-Z\s]+" title="Cardholder name must contain only letters and spaces." required>
        
        <label for="card_number">Card Number:</label>
        <input type="text" id="card_number" name="card_number" pattern="\d{16}" title="Card number must be 16 digits." required>
        
        <label for="expiry_date">Expiry Date (MM/YY):</label>
        <input type="text" id="expiry_date" name="expiry_date" pattern="(?:0[1-9]|1[0-2])/[0-9]{2}" title="Expiry date must be in MM/YY format." required>

        <label for="cvv_number">CVV Number:</label>
        <input type="text" id="cvv_number" name="cvv_number" pattern="\d{3,4}" title="CVV number must be 3 or 4 digits." required>
        
        <button type="submit">Pay</button>
    </form>
    <a href="cancel_reservation.php?reservation_id=<?php echo htmlspecialchars($reservation_id); ?>&num_tickets=<?php echo htmlspecialchars($num_tickets); ?>&exhibit_id=<?php echo htmlspecialchars($exhibit_id); ?>">Cancel Reservation</a>
</body>
</html>
