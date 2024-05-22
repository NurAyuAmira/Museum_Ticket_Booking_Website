<?php
session_start();

define('RATE_LIMIT', 100); // Max 100 requests
define('TIME_WINDOW', 3600); // Time window in seconds (1 hour)

// Array to hold blocked IPs
$blocked_ips = [
    '192.168.1.1', // Example blocked IP
    // Add more IPs as needed
];

// Function to check if an IP is blocked
function is_ip_blocked($ip) {
    global $blocked_ips;
    return in_array($ip, $blocked_ips);
}

// Function to log and check request rate
function rate_limit($ip) {
    if (!isset($_SESSION['requests'][$ip])) {
        $_SESSION['requests'][$ip] = [];
    }
    
    // Remove old requests
    $now = time();
    $_SESSION['requests'][$ip] = array_filter($_SESSION['requests'][$ip], function($timestamp) use ($now) {
        return ($now - $timestamp) < TIME_WINDOW;
    });
    
    // Add current request
    $_SESSION['requests'][$ip][] = $now;
    
    // Check if rate limit exceeded
    if (count($_SESSION['requests'][$ip]) > RATE_LIMIT) {
        return false;
    }
    return true;
}

// Get client IP
$client_ip = $_SERVER['REMOTE_ADDR'];

// Check if IP is blocked
if (is_ip_blocked($client_ip)) {
    die("Access denied.");
}

// Check rate limit
if (!rate_limit($client_ip)) {
    die("Too many requests. Please try again later.");
}
?>
