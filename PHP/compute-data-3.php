<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedImage = $_POST['selectedImage'];
    $Size = json_decode($_POST['dimensions']);
    $evaluatorName = $_POST['evaluatorName'];
    $assessmentCount = $_POST['assessmentCount'];
    $selectedAssessment = $_POST['selectedAssessment'];
    $Scale = $_POST['Scale'];
    $user = $_POST['user'];
    $caseButton = $_SESSION['button'];
    // echo 'session:'.$caseButton;

    // echo 'scale:'.$Scale.'<br>';


    $Name = $selectedImage;
    $width = $Size->width;
    $height = $Size->height;

    // Path to the Python interpreter in the virtual environment
    $venv_python = "C:/venvs/qtoolenv/Scripts/python.exe";

    // Build the command to execute the Python script
    if ($caseButton == 'nerve-transversal'){
        // Build the command to execute the Python script using the virtual environment's Python interpreter
        $command = "$venv_python E:/UZqTool/uzqtool/html/Python/morphologyTrans.py $Name $height $width $evaluatorName $assessmentCount $selectedAssessment $Scale $user $caseButton 2>&1";
    }
    else{
        $command = "$venv_python E:/UZqTool/uzqtool/html/Python/morphology.py $Name $height $width $evaluatorName $assessmentCount $selectedAssessment $Scale $user $caseButton 2>&1";
    }
    // echo $command;
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
