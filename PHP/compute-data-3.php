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
    if ($caseButton=='sarcopenia') {
        $estructura = $_POST['estructura'];
    }
    // echo 'session:'.$caseButton;

    // echo 'scale:'.$Scale.'<br>';


    $Name = $selectedImage;
    $width = $Size->width;
    $height = $Size->height;

    if (isset($_POST['coordinates']) && !empty($_POST['coordinates'])) {
        file_put_contents(__DIR__ . '/polygon.txt', $_POST['coordinates'], LOCK_EX);
    }

    // Path to the Python interpreter in the virtual environment
    $venv_python = file_exists("/home/marta/appFlask/venv/bin/python3") ? "/home/marta/appFlask/venv/bin/python3" : "python3";
    $script_trans = file_exists("/var/www/html/Python/morphologyTrans.py") ? "/var/www/html/Python/morphologyTrans.py" : (__DIR__ . "/../Python/morphologyTrans.py");
    $script_long = file_exists("/var/www/html/Python/morphology.py") ? "/var/www/html/Python/morphology.py" : (__DIR__ . "/../Python/morphology.py");

    // Build the command to execute the Python script
    if ($caseButton == 'nerve-transversal'){
        // Build the command to execute the Python script using the virtual environment's Python interpreter
        $args = array_map('escapeshellarg', [
            $Name, $height, $width, $evaluatorName, $assessmentCount, $selectedAssessment, $Scale, $user, $caseButton
        ]);
        $command = "$venv_python " . escapeshellarg($script_trans) . " " . implode(' ', $args) . " 2>&1";
    }
    elseif ($caseButton == 'muscle') {
        $args = array_map('escapeshellarg', [
            $Name, $height, $width, $evaluatorName, $assessmentCount, $selectedAssessment, $Scale, $user, $caseButton
        ]);
        $command = "$venv_python " . escapeshellarg($script_trans) . " " . implode(' ', $args) . " 2>&1";
    }
    elseif ($caseButton == 'sarcopenia') {
        $args = array_map('escapeshellarg', [
            $Name, $height, $width, $evaluatorName, $assessmentCount, $selectedAssessment, $Scale, $estructura, $user, $caseButton
        ]);
        $command = "$venv_python " . escapeshellarg($script_trans) . " " . implode(' ', $args) . " 2>&1";
    }
    else{
        $args = array_map('escapeshellarg', [
            $Name, $height, $width, $evaluatorName, $assessmentCount, $selectedAssessment, $Scale, $user, $caseButton
        ]);
        $command = "$venv_python " . escapeshellarg($script_long) . " " . implode(' ', $args) . " 2>&1";
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
