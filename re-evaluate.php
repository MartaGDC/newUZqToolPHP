<?php
require_once 'auth_check.php';

session_start();


// Get the filename without the extension
$filenameWithoutExtension = pathinfo($_SESSION['IMG'], PATHINFO_FILENAME);

// Create the new filename for the second image
$imagePath = "Upload/" . $filenameWithoutExtension . "-Bone.png";

if (is_file($imagePath)) {
    unlink($imagePath);
}


// Clear the session variables
unset($_SESSION['EvCount']);
unset($_SESSION['Threshold']);

exit;
?>

