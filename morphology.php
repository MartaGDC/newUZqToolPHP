<?php
require_once 'auth_check.php';
// Start the session
session_start();

// Specify the path to the echographies folder
$echographiesPath = 'Upload/';

// Check if the image has been submitted
if (isset($_SESSION['IMG'], $_SESSION['EvName'], $_SESSION['EvCount'], $_SESSION['Scale'])) {
    // Retrieve the selected image filename from the session variable
    $IMAGE = $_SESSION['IMG'];
    $evaluatorName = $_SESSION['EvName'];
    $assessmentCount = $_SESSION['EvCount'];
    $Scale = $_SESSION['Scale'];
    $user = $_SESSION['User'];
    $caseButton = $_SESSION['button'];

    echo 'cas0'.$caseButton.'<br>';


    // Construct the image path
    $imagePath = $echographiesPath . $IMAGE;

    // Unset the session variable after retrieving its value
    // unset($_SESSION['IMG'], $_SESSION['EvName'], $_SESSION['EvCount']);
} else {
    // Set default values if no image is submitted
    $IMAGE = '';
    $imagePath = '';
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted polygon coordinates
    $polygonCoordinates = $_POST['polygon-coordinates'];

    // Write the coordinates to a text file
    $filename = 'polygon_coordinates.txt';
    file_put_contents($filename, $polygonCoordinates);

    // Output a success message
    echo 'Polygon coordinates saved to file: ' . $filename;
}
?>

<style>
    .selected-image {
        width: 100%;
        height: auto;
        max-width: 700px;
        max-height: 700px;
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

    .message-box {
        background-color: #f2f2f2;
        padding: 10px;
        border-radius: 5px;
        max-width: 600px;
        color: royalblue;
        font-weight: bold;
    }

    .form-group button {
        padding: 5px 10px; /* Increase padding for a larger button */
        font-size: 14px; /* Increase font size for better readability */
        font-weight: bold;
        color: black;
        background-color: rgb(173, 230, 99); /* Use a blue color for the background */
        border: none; /* Remove the border */
        border-radius: 30px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }


    .form-group button:hover {
        background-color: rgb(138, 190, 79); /* Darker shade on hover */
    }
</style>

<style>
    .result {
        background-color: lightgoldenrodyellow;
        padding: 10px;
        border-radius: 5px;
        margin-top: 5px;
        width: 100%;
        display: flex;
        justify-content: center;
    }
</style>

<datalist id='values'>
    <option value="0" label="0"></option>
    <option value="1" label="1"></option>
    <option value="2" label="2"></option>
    <option value="3" label="3"></option>
</datalist>

<!DOCTYPE html>
<html>
<head>
    <title>Tissue File</title>
</head>
<body>
    <?php if ($IMAGE !== '') { ?>

        <div class="conten-active" style="display: flex; flex-direction: column; justify-content: center; align-items: center; margin-bottom: 10px;">
            <div class="form-group">
                <label for='selected-image'>Selected Image: <?php echo $IMAGE ?></label>
                <button onclick="loadTissueImage()">Reload Image</button>
            </div>
            <div class="form-group">
                <label for='scale'>Scale:</label>
                <label> <?php echo number_format($Scale,3) ?></label>
            </div>
            <div class="form-group">
                <label for="assessment-selector">Assessment:</label>
                <input type="range" class='slider' name="assessment" id="assessment-selector" list="values" min='0' max='3' value='2' oninput="updateValue(this.value,'value')">
                <div id="value"></div>
            </div>
            <div class='message-box'>
                Start at the top-left corner and draw a polygon that encloses the tendon
            </div> 
        </div>


        <div style="display: flex; justify-content: center;">
            <div id="image-container">
                <img src="<?php echo $imagePath; ?>" alt="Selected Image" class="selected-image" onload="adjustCanvasSize()">
                <canvas id="image-canvas-1"></canvas>
            </div>
        </div>
        <br>
        <div style="display: flex; justify-content: center;" id="polygon-coordinates"></div>

        <div class="form-group" style="display: flex; justify-content: center;">
            <button onclick="sendData()">Compute Data</button>
        </div>
        <br>
        <div style="display: flex; justify-content: center; align-items: center; height: 50px;">
            <div class='result'>
                <div id="result"></div>
            </div>
        </div>

    <?php } else { ?>
        <div style="display: flex; justify-content: center; align-items: center; height: 50px;">
            <div class="form-group">
                <label for='if not image'>Please click the button to load the image.</label>
                <button onclick="loadTissueImage()">Load Image</button>
            </div>
        </div>
    <?php } ?>

    <script>
        // Set initial values
        var defaultValue = document.getElementById('assessment-selector').value;
        updateValue(defaultValue, 'value');

        var canvas = document.getElementById('image-canvas-1');
        var ctx = canvas.getContext('2d');
        var isDrawing = false;
        var startPoint, endPoint, img;
        var polygonCoordinates = document.getElementById('polygon-coordinates');
        var points = [];

        // Function to adjust the canvas size to match the image size
        function adjustCanvasSize() {
            img = document.querySelector('.selected-image');
            canvas.width = img.width;
            canvas.height = img.height;
            displayImageInfo();
        }

        // Function to load the image
        function loadTissueImage() {
            window.location.reload();
        }

        // Function to display the image size and dimensions
        function displayImageInfo() {
            img = document.querySelector('.selected-image');
            var imageInfo = 'Image Size: ' + img.width + 'px x ' + img.height + 'px';
            document.getElementById('image-info').textContent = imageInfo;
        }

        // Function to update the value of a slider
        function updateValue(value, ID) {
            document.getElementById(ID).textContent = value;
        }

        // Function to handle click events on the canvas
        function handleCanvasClick(event) {
            var rect = canvas.getBoundingClientRect();
            var x = event.clientX - rect.left;
            var y = event.clientY - rect.top;
            points.push({ x: x, y: y });
            drawPolygon();
        }

        // Function to draw the polygon on the canvas
        function drawPolygon() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            if (points.length < 2) return;

            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            for (var i = 1; i < points.length; i++) {
                ctx.lineTo(points[i].x, points[i].y);
            }
            ctx.closePath();
            ctx.strokeStyle = 'red';
            ctx.lineWidth = 2;
            ctx.stroke();
        }

        // Function to save the polygon coordinates to a text file
        function savePolygonCoordinates() {
            var coordinates = 'Polygon Coordinates:\n';
            for (var i = 0; i < points.length; i++) {
                coordinates += `${points[i].x}\t${points[i].y}\n`;
            }

            var formData = new FormData();
            formData.append('coordinates', coordinates);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', './PHP/save-coordinates.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText); // Optionally, handle the response from the PHP script
                }
            };
            xhr.send(formData);
        }


        // Function to send data to the PHP script
        function sendData() {
            var dimensions = JSON.stringify({ width: img.width, height: img.height });
            var selectedImage = '<?php echo $IMAGE ?>';
            var evaluatorName = '<?php echo $evaluatorName ?>';
            var assessmentCount = '<?php echo $assessmentCount ?>';
            var selectedAssessment = document.getElementById('assessment-selector').value;
            var Scale = '<?php echo $Scale ?>';
            var user = '<?php echo $user ?>';

            // Save the polygon coordinates before sending data
            savePolygonCoordinates();

            var formData = new FormData();
            formData.append('selectedImage', selectedImage);
            formData.append('dimensions', dimensions);
            formData.append('evaluatorName', evaluatorName);
            formData.append('assessmentCount', assessmentCount);
            formData.append('selectedAssessment', selectedAssessment);
            formData.append('Scale', Scale);
            formData.append('user',user);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', './PHP/compute-data-3.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('result').innerHTML = xhr.responseText;
                    alert(xhr.responseText);
                }
            };

            xhr.send(formData);

            <?php
            if($caseButton == 'tendon'){
            ?>    
                window.parent.enableBoneTab();
            
            <?php
            }
            else{
            ?>
                window.parent.enableSummaryTab();
            <?php
            }
            ?>
        }


        // Add event listeners to the canvas
        canvas.addEventListener('click', handleCanvasClick);

        // Display the image size and dimensions when the image is loaded
        window.addEventListener('load', adjustCanvasSize);
    </script>
</body>
</html>
