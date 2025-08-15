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
    $venv_python = "C:/venvs/qtoolenv/Scripts/python.exe";

    // Build the command to execute the Python script using the virtual environment's Python interpreter
    $command = "$venv_python E:/UZqTool/uzqtool/html/Python/bone.py $Name $x1 $y1 $x2 $y2 $height $width $evaluatorName $assessmentCount $Threshold $selectedAssessment $Scale $user $caseButton 2>&1";
    
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

