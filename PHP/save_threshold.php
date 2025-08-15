<?php
session_start();

if (isset($_POST['threshold'])) {
    // Retrieve the threshold value from the POST request
    $threshold = $_POST['threshold'];

    // Save the threshold value in a session variable
    $_SESSION['Threshold'] = $threshold;
}
?>
