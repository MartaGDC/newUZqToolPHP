<?php
require_once 'auth_check.php';

// Specify the path to the Upload folder
$uploadPath = 'Upload/';

// Delete only the current session's extracted frame and bone derivative
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

// Clear or reset session variables related to the current evaluation (preserve Scale for same video)
unset($_SESSION['EvCount']);
unset($_SESSION['Threshold']);
unset($_SESSION['IMG']);

echo "success";
exit;
?>