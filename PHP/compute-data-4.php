<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedImage = $_POST['selectedImage'];
    $startPoint = json_decode($_POST['startPoint']);
    $endPoint = json_decode($_POST['endPoint']);
    $Size = json_decode($_POST['dimensions']);
    $evaluatorName = $_POST['evaluatorName'];
    $assessmentCount = $_POST['assessmentCount'];
    $Threshold = $_POST['Threshold'];
    $Scale = $_POST['Scale'];
    $selectedAssessment = $_POST['selectedAssessment'];
    $user = $_POST['user'];
    $caseButton = $_SESSION['button'];

    // echo $Scale.'<br>';

    $Name = $selectedImage;
    $x1 = $startPoint->x;
    $y1 = $startPoint->y;
    $x2 = $endPoint->x;
    $y2 = $endPoint->y;
    $width = $Size->width;
    $height = $Size->height;


    //show variables
    // echo $Name.' '.$x1.' '.$y1.' '.$x2.' '.$y2.' '.$height.' '.$width.' '.$evaluatorName.' '.$assessmentCount.' '.$Threshold.' '.$selectedAssessment.'<br>';

    // Path to the Python interpreter in the virtual environment
    $venv_python = file_exists("/home/marta/appFlask/venv/bin/python3") ? "/home/marta/appFlask/venv/bin/python3" : "python3";
    $script_path = file_exists("/var/www/html/Python/bone.py") ? "/var/www/html/Python/bone.py" : (__DIR__ . "/../Python/bone.py");

    // Build the command to execute the Python script using the virtual environment's Python interpreter
    $args = array_map('escapeshellarg', [
        $Name, $x1, $y1, $x2, $y2, $height, $width,
        $evaluatorName, $assessmentCount, $Threshold, $selectedAssessment, $Scale, $user, $caseButton
    ]);
    $command = "$venv_python " . escapeshellarg($script_path) . " " . implode(' ', $args) . " 2>&1";
    
    // Execute the command and capture the output and errors
    exec($command, $output, $returnCode);

    // Display the output
    // Check if the first line of the output starts with "The"
    if (isset($output[0]) && strpos($output[0], 'The') === 0) {
        echo "✅: ";
    } else {
        echo "❌: try to log out\n";
    }
    foreach ($output as $line) {
        echo $line . "\n";
    }

    // Display the return code (0 for success)
    //echo "Return Code: " . $returnCode;
}
?>

