<?php
require_once 'auth_check.php';

// Specify the path to the Upload folder
$uploadPath = 'Upload/';

// Delete only the current session's uploaded image and bone derivative
if (isset($_SESSION['IMG']) && !empty($_SESSION['IMG'])) {
    $currentFile = $uploadPath . basename($_SESSION['IMG']);
    if (is_file($currentFile)) {
        unlink($currentFile);
    }
    $boneFile = $uploadPath . pathinfo($_SESSION['IMG'], PATHINFO_FILENAME) . '-Bone.png';
    if (is_file($boneFile)) {
        unlink($boneFile);
    }
}

// Clear the session variables
unset($_SESSION['EvCount']);
unset($_SESSION['Threshold']);
unset($_SESSION['IMG']);
unset($_SESSION['Scale']);

exit;
?>