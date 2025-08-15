<?php
session_start();
if (!isset($_SESSION['User']) || empty($_SESSION['User'])) {
header('Location: index.php');
exit;
}
?>