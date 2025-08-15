<?php
require_once 'auth_check.php';

session_start();

// Specify the path to the Upload folder
$uploadPath = 'Upload/';

// Get all files in the Upload folder
$files = glob($uploadPath . '*');

// Delete each file
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

// Clear the session variables
unset($_SESSION['EvCount']);
unset($_SESSION['Threshold']);
unset($_SESSION['IMG']);
unset($_SESSION['Scale']);

exit;
?>