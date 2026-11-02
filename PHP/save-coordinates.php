<?php
$coordinates = $_POST['coordinates'];
$filename = __DIR__ . '/polygon.txt';
echo $coordinates;
if (file_put_contents($filename, $coordinates, LOCK_EX) !== false) {
    echo 'Polygon coordinates saved successfully';
} else {
    echo 'Error saving polygon coordinates: ' . error_get_last()['message'];
}

?>
