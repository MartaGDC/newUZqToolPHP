<?php
require_once 'auth_check.php';
// Start the session
session_start();

// Specify the path to the echographies folder
$echographiesPath = 'Upload/';




// Check if the image has been submitted
if (isset($_SESSION['IMG'], $_SESSION['EvName'], $_SESSION['EvCount'])) {
    // Retrieve the selected image filename from the session variable
    $IMAGE = $_SESSION['IMG'];
    $evaluatorName = $_SESSION['EvName'];
    $assessmentCount = $_SESSION['EvCount'];
    $user = $_SESSION['User'];
    $caseButton = $_SESSION['button'];//Load session case (Button)

    // echo $caseButton;

    // Construct the image path
    $imagePath = $echographiesPath .'/'. $IMAGE;

    // Specify the path to the DATA folder
    $dataPath = 'DATA/'.$user.'/'.$caseButton.'/';
    // echo "<script>console.log('".json_encode($dataPath)."');</script>";
    // echo $dataPath;
    // Unset the session variable after retrieving its value
    // unset($_SESSION['IMG'], $_SESSION['EvName'], $_SESSION['EvCount']);

    // Get a list of files in the DATA directory
    $files = scandir($dataPath);

    // echo "<script>console.log('".json_encode($files)."');</script>";

    // Remove . and .. from the list
    $files = array_diff($files, array('.', '..'));



} else {
    // Set default values if no image is submitted
    $IMAGE = '';
    $imagePath = '';
}


// Function to read the last line of a file and extract columns
function readLastLine($filePath, $ind) {
    $lines = file($filePath);
    $reversedLines = array_reverse($lines);
    
    $line = $reversedLines[$ind];
    $line = trim($line); // Remove newline characters and any leading/trailing whitespaces
    $columns = explode(";", $line);
    return $columns;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Summary</title>
    <style>
        .selected-image {
            width: 95%;
            height: auto;
            max-width: 200px;
            max-height: 200px;
            display: block;
            margin: auto;
            object-fit: contain;
        }

        #image-container {
            position: relative;
            display: inline-block;
        }

        #image-canvas-1 {
            position: absolute;
            top: 0;
            left: 0;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .form-group label {
            width: 200px;
            padding-right: 10px;
        }

        .form-group input,
        .form-group button,
        .form-group select {
            width: 300px;
            padding: 5px;
            margin-right: 10px;
        }

        .slider {
            accent-color: blue;
        }

        .submit-button {
            padding: 8px 14px;
            font-size: 14px;
            font-weight: bold;
            color: black;
            background-color: lightgreen;
            border: 1px solid black;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: greenyellow;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .container{
        margin: 0 auto;
        width: 50%;
        text-align: center;
        }

        input[type=submit]{
        border: 0px;
        padding: 7px 15px;
        font-size: 16px;
        background-color: #00a1a1;
        color: white;
        font-weight: bold;
        }

        input[type=submit]:hover{
            background-color: greenyellow;
            color: red;
        }

        input[type=submit]:active{
            background-color: greenyellow;
            color: black;
        }

        .img{
            float:center;
            width: 350px;
        }

        .re-btn {
            padding: 8px 14px;
            font-size: 14px;
            font-weight: bold;
            color: Black;
            background-color: Orange;
            border: 1px solid black;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .re-btn:hover{
            background-color: yellowgreen;
        }

        .clickable {
            cursor: pointer;
            text-decoration: underline;
            color: blue; /* You can change the color to suit your design */
        }
    </style>
</head>
<body>
    <?php if ($IMAGE !== '') { ?>
  
        <div class="conten-active" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <div class="form-group">
                <label for='selected-image'>Selected Image:</label>
                <label> <?php echo $IMAGE ?></label>
            </div>
            <div class="form-group">
                <label for='evaluator'>Evaluator:</label>
                <label> <?php echo $evaluatorName ?></label>
            </div>
            <div class="form-group">
                <label for="assessment-count">Evaluation number:</label>
                <label> <?php echo $assessmentCount ?></label>
            </div> 
        </div>

        <div style="display: flex; justify-content: center;">
            <div id="image-container" class="img">
                <img src="<?php echo $imagePath; ?>" alt="Selected Image" class="selected-image">
            </div>
            <?php
            // Get the filename without the extension
            $filenameWithoutExtension = pathinfo($imagePath, PATHINFO_FILENAME);

            // Create the new filename for the second image
            $imagePath2 = "Upload" . $filenameWithoutExtension . "-Bone.png";
            // echo "<script>console.log('".json_encode($imagePath)."');</script>";
            ?>
            <!-- <div id="image-container2" class="img">
                <img src="<?php echo $imagePath2; ?>" alt="Selected Image 2" class="selected-image">
            </div> -->
        </div>
        <div style="display: flex; justify-content: center;">
            <h4 style="color:Royalblue">You can click on the file names in order to see their variables</h4>
        </div>
        <table>
            <tr>
                <th>File Name</th>
                <th>Your Assessment</th>
            </tr>
            <?php foreach ($files as $file) { ?>
                <tr>
                    <td class="clickable" onclick="showPopup('<?php echo htmlspecialchars($file); ?>')">
                        <?php echo substr($file, 0, -4); ?>
                    </td>
                    <?php
                    if ($file == 'borders.txt') {
                        $columns1 = readLastLine($dataPath . '/' . $file, 1);
                        $columns2 = readLastLine($dataPath . '/' . $file, 0);
                        echo "<td>" . $columns1[3] . '; ' . $columns2[3] . "</td>";
                    } else {
                        $columns = readLastLine($dataPath . '/' . $file, 0);
                        echo "<td>" . $columns[2] . "</td>";
                    }
                    ?>
                </tr>
            <?php } ?>
        </table>

        <!-- <div class='container'>
            <h3>Create and Download your Zip file</h3>
            <form method='post' action=''>
                <input type='submit' name='download' value='Download' />
            </form>
        </div> -->
        <div class='container'>
            <h3>Create and Download your files</h3>
            <?php
            $dir = $dataPath; // replace with your directory
            $files = scandir($dir);

            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo "<a href='$dir/$file' download>$file</a><br>";
                }
            }
            ?>
        </div>
        
        <div class='container'>
            <h3 style='color:blue'>Reevaluate</h3>
            <button class="re-btn" onclick="ReSame()">Same image</button>
            <button class="re-btn" onclick="ReOther()">Another image</button>
            <button class="re-btn" onclick="ReAnotherFromSameVideo()">Another Image from same video</button>
        </div>


    <?php } else { ?>
        <div style="display: flex; justify-content: center; align-items: center; height: 50px;">
            <div class="form-group">
                <label for='if not image'>Click the button to show a summary of your session</label>
                <button onclick="loadSum()" class="submit-button">Load Summary</button>
            </div>
        </div>
    <?php } ?>
</body>
</html>

<script>
    function loadSum() {
        location.reload();
    }

    function ReSame() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                parent.location.href = './tabs.php';
            }
        };
        xhr.open('POST', './re-evaluate.php', true);
        xhr.send();
    }

    function ReOther() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                parent.location.href = './tabs.php';
            }
        };
        xhr.open('POST', './re-evaluate-2.php', true);
        xhr.send();
    }

    function ReAnotherFromSameVideo() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                parent.location.href = './tabs.php';
            }
        };
        xhr.open('POST', './re-evaluate-3.php', true);
        xhr.send();
    }

    function showPopup(fileName) {
        // Display a popup (alert) with the file name
        switch (fileName) {
            case 'tissue-quality.txt':
                alert('Tissue Quality Variables:\nDate;File;Ev;Name;nº;GLCM_Contrast;GLCM_SumAverage;GLCM_SoSVariance;GLCM_DVariance;GLCM_Correlation;GLCM_IDMoment;GLDS_Homogeneity;GLDS_Contrast;GLDS_ASM;GLDS_Entopy;GLDS_Mean;haar_mean;haar_variance;geometry');
                break;
            case 'borders.txt':
                alert('Borders Variables:\nDate;File;Position;Ev;Name;nº;GLCM_Contrast;GLCM_SumAverage;GLCM_SoSVariance;GLCM_DVariance;GLCM_Correlation;GLCM_IDMoment;GLDS_Homogeneity;GLDS_Contrast;GLDS_ASM;GLDS_Entopy;GLDS_Mean;haar_mean;haar_variance;geometry');
                break;
            case 'morphology.txt':
                alert('Morphology Variables:\nDate;File;Ev;Name;Count;Max_width;Min_width;mean;Desvest;Ratio;Geometry');
                break;
            case 'bone.txt':
                alert('Bone Variables:\nDate;File;Ev;Name;nº;number;Area;Perimeter;convexity;homogeneity;contrast;ASM;geometry');
                break;
        }
        
    }
</script>
