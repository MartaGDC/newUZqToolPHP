<?php
session_start();

if (isset($_POST['scale'])) {
    // Retrieve the threshold value from the POST request
    $scale = $_POST['scale'];

    // Save the threshold value in a session variable
    $_SESSION['Scale'] = number_format($scale,8);

    //echo 'scale('.$scale.') has been saved';
}
?>