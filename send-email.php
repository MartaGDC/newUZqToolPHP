<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $organization = $_POST['organization'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];

    // Prepare email content
    $subject = "New User Request from M2TI";
    $message = "Organization: $organization\nEmail: $email\nComment: $comment";

    // Send email to your personal address
    mail('jsegovia@unizar.es', $subject, $message);

    echo "Request sent successfully!";
}
?>