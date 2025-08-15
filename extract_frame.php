<?php
require_once 'auth_check.php';

session_start();

if (isset($_POST['img']) && isset($_POST['name'])) {
    $data = $_POST['img'];
    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    $data = base64_decode($data);
    $name = basename($_POST['name']);
    $name = pathinfo($name, PATHINFO_FILENAME) . '.png';

    // Reset session variables
    unset($_SESSION['IMG']);
    unset($_SESSION['EvName']);
    unset($_SESSION['EvCount']);
    
    if (file_put_contents('Upload/' . $name, $data)) {
        $selectedImage = $name;
        $_SESSION['IMG'] = $selectedImage;
        echo "success";
    } else {
        echo "failure";
    }
} else {
    echo "invalid";
}
?>
