<?php 
    $log_file = 'log.txt'; // Set it to local for testing!
    $log_message = date('Y-m-d H:i:s') . " - Test write confirmation from standalone script.\n";
    if (file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX) === false) {
        echo "PHP Write Failure: Check PHP error logs for permissions.";
    } else {
        echo "PHP Write Success. File should now exist.\n";
    }
?>
