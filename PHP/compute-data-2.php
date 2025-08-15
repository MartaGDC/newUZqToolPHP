<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedImage = $_POST['selectedImage'];

    $startPoint1 = json_decode($_POST['startPoint1']);
    $endPoint1 = json_decode($_POST['endPoint1']);
    $endPoint2 = json_decode($_POST['endPoint2']);
    $startPoint2 = json_decode($_POST['startPoint2']); // Decode the JSON string into an array

    $evaluatorName = $_POST['evaluatorName'];
    $assessmentCount = $_POST['assessmentCount'];
    $AssessmentUp = $_POST['AssessmentUp'];
    $AssessmentDown = $_POST['AssessmentDown'];
    $user = $_POST['user'];


    $x1 = $startPoint1->x;
    $y1 = $startPoint1->y;
    $x2 = $endPoint1->x;
    $y2 = $endPoint1->y;


    $a1 = $startPoint2->x;
    $b1 = $startPoint2->y;
    $a2 = $endPoint2->x;
    $b2 = $endPoint2->y;

    $Name = $selectedImage;

    $width = $_POST['canvasWidth'];
    $height = $_POST['canvasHeight'];

    $caseButton = $_SESSION['button'];
    

    // echo $Name.' '.$x1.' '.$y1.' '.$x2.' '.$y2.' '.$a1.' '.$b1.' '.$a2.' '.$b2.' '.$height.' '.$width.' '.$evaluatorName.' '.$assessmentCount.' '.$AssessmentUp.' '.$AssessmentDown.'<br>'; 

    // Path to the Python interpreter in the virtual environment
    $venv_python = "C:/venvs/qtoolenv/Scripts/python.exe";

    // Build the command to execute the Python script using the virtual environment's Python interpreter
    $command = "$venv_python E:/UZqTool/uzqtool/html/Python/borders.py $Name $x1 $y1 $x2 $y2 $a1 $b1 $a2 $b2 $height $width $evaluatorName $assessmentCount $AssessmentUp $AssessmentDown $user $caseButton 2>&1";
        
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
