<?php
$coordinates = $_POST['coordinates'];
$filename = './polygon.txt';
echo $coordinates;
if (file_put_contents($filename, $coordinates) !== false) {
    echo 'Polygon coordinates saved successfully';
} else {
    echo 'Error saving polygon coordinates: ' . error_get_last()['message'];
}

?>
