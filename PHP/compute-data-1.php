<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedImage = $_POST['selectedImage'];
    $startPoint = json_decode($_POST['startPoint']);
    $endPoint = json_decode($_POST['endPoint']);
    $Size = json_decode($_POST['dimensions']);
    $evaluatorName = $_POST['evaluatorName'];
    $assessmentCount = $_POST['assessmentCount'];
    $selectedAssessment = $_POST['selectedAssessment'];
    $user = $_POST['user'];
    $caseButton = $_SESSION['button'];


    $Name = $selectedImage;
    $x1 = $startPoint->x;
    $y1 = $startPoint->y;
    $x2 = $endPoint->x;
    $y2 = $endPoint->y;
    $width = $Size->width;
    $height = $Size->height;

    // Path to the Python interpreter in the virtual environment
    $venv_python = "C:/venvs/qtoolenv/Scripts/python.exe";

    // Build the command to execute the Python script using the virtual environment's Python interpreter
    $command = "$venv_python E:/UZqTool/uzqtool/html/Python/tissue-quality.py $Name $x1 $y1 $x2 $y2 $height $width $evaluatorName $assessmentCount $selectedAssessment $user $caseButton 2>&1";
    
    // echo $command."<br>";
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

}
?>
