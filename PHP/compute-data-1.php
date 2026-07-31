<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['button'] == 'sarcopenia') {
        $startPoint1 = json_decode($_POST['startPoint1']);
        $endPoint1 = json_decode($_POST['endPoint1']);
        $endPoint2 = json_decode($_POST['endPoint2']);
        $startPoint2 = json_decode($_POST['startPoint2']);
        $startPoint3 = json_decode($_POST['startPoint3']);
        $endPoint3 = json_decode($_POST['endPoint3']);

        $x1 = $startPoint1->x;
        $y1 = $startPoint1->y;
        $x2 = $endPoint1->x;
        $y2 = $endPoint1->y;

        $a1 = $startPoint2->x;
        $b1 = $startPoint2->y;
        $a2 = $endPoint2->x;
        $b2 = $endPoint2->y;

        $c1 = $startPoint3->x;
        $d1 = $startPoint3->y;
        $c2 = $endPoint3->x;
        $d2 = $endPoint3->y;

        $recto = $_POST['recto'];
        $vasto = $_POST['vasto'];
        $grasa = $_POST['grasa'];
    }
    else {
        $startPoint1 = json_decode($_POST['startPoint1']);
        $endPoint1 = json_decode($_POST['endPoint1']);

        $x1 = $startPoint1->x;
        $y1 = $startPoint1->y;
        $x2 = $endPoint1->x;
        $y2 = $endPoint1->y;
        
        $selectedAssessment = $_POST['selectedAssessment'];
    }

    $selectedImage = $_POST['selectedImage'];
    $evaluatorName = $_POST['evaluatorName'];
    $assessmentCount = $_POST['assessmentCount'];
    
    $user = $_POST['user'];

    $Name = $selectedImage;
    
    $Size = json_decode($_POST['dimensions']);
    $width = $Size->width;
    $height = $Size->height;

    $caseButton = $_SESSION['button'];


    // Path to the Python interpreter in the virtual environment
    $venv_python = "/home/marta/appFlask/venv/bin/python3";

    // Build the command to execute the Python script using the virtual environment's Python interpreter
    if($caseButton == 'sarcopenia') {
        $command = "$venv_python /var/www/html/Python/tissue-quality.py $Name $x1 $y1 $x2 $y2 $a1 $b1 $a2 $b2 $c1 $d1 $c2 $d2 $height $width $evaluatorName $assessmentCount $recto $vasto $grasa $user $caseButton 2>&1";
    }
    else {
        $command = "$venv_python /var/www/html/Python/tissue-quality.py $Name $x1 $y1 $x2 $y2 $height $width $evaluatorName $assessmentCount $selectedAssessment $user $caseButton 2>&1";
    }
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
