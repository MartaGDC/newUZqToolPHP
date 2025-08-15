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
    $Threshold = $_SESSION['Threshold'];
    $Scale = $_SESSION['Scale'];
    $user = $_SESSION['User'];

    // Construct the image path
    $imagePath = $echographiesPath . $IMAGE;

    // Unset the session variable after retrieving its value
    // unset($_SESSION['IMG'], $_SESSION['EvName'], $_SESSION['EvCount']);

} else {
    // Set default values if no image is submitted
    $IMAGE = '';
    $imagePath = '';
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

    .slider{
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
                <label for="selected-image">Selected Image: <?php echo $IMAGE ?></label>
                <button onclick="loadBorderImage()">Reload</button>
            </div>
            <div class="form-group">
                <label for='evaluator'>Evaluator:</label>
                <label> <?php echo $evaluatorName.'/'.$user ?></label>
            </div>
            <!-- <div class="form-group">
                <label for='threshold'>Threshold:</label>
                <label> <?php echo $Threshold ?></label>
            </div> -->
            <div class="form-group">
                <label for="assessment-selector">Assessment:</label>
                <input type="range" class='slider' name="assessment" id="assessment-selector" list="values" min='0' max='3' value='2' oninput="updateValue(this.value,'value')"></input>
                <div id="value"></div>
            </div> 
            <div class='message-box'>
                Draw a rectangle that include the bone region
            </div> 
            
        </div>


        <div style="display: flex; justify-content: center; margin-bottom: 10px;">
            <div id="image-container">
                <img src="<?php echo $imagePath; ?>" alt="Selected Image" class="selected-image" onload="adjustCanvasSize()">
                <canvas id="image-canvas-1"></canvas>
            </div>
        </div>
        <!--<div style="display: flex; justify-content: center;" id="image-info"></div>-->
        <!-- <div style="display: flex; justify-content: center;" id="rectangle-coordinates"></div> -->

        <div class="form-group" style="display: flex; justify-content: center;">
            <button onclick="sendData()">Compute Data</button>
        </div>
        
        <div style="display: flex; justify-content: center; align-items: center; height: 50px;">
            <div class='result'>
                <div id="result"'></div>
            </div>
        </div>  

    <?php } else { ?>
        <div style="display: flex; justify-content: center; align-items: center; height: 50px;">
            <div class="form-group">
                <label for='if not image'>No image selected. Please click the button to load the image.</label>
                <button onclick="loadTissueImage()">Load Image</button>
            </div>
        </div>
    <?php } ?>

    <script>

        //functions for sliders
        function updateValue(value, ID) {
            document.getElementById(ID).textContent = value;
        }
        // Set initial values
        var defaultValue = document.getElementById('assessment-selector').value;
        updateValue(defaultValue, 'value');


        var canvas = document.getElementById('image-canvas-1');
        var ctx = canvas.getContext('2d');
        var isDrawing = false;
        var startPoint, endPoint, img;
        var rectCoordinates = document.getElementById('rectangle-coordinates');

        // Function to handle mouse down event
        function handleMouseDown(event) {
            var x = event.offsetX;
            var y = event.offsetY;

            // Save the starting point of the rectangle
            startPoint = { x: x, y: y };

            // Set the drawing flag to true
            isDrawing = true;
        }

        //Function reload
        function loadBorderImage(){
            window.location.href = 'bone.php'
        }

        // Function to handle mouse up event
        function handleMouseUp(event) {
            if (isDrawing) {
                var x = event.offsetX;
                var y = event.offsetY;

                // Save the ending point of the rectangle
                endPoint = { x: x, y: y };

                // Draw the rectangle
                ctx.beginPath();
                ctx.strokeStyle = 'red';
                ctx.lineWidth = 2;
                ctx.rect(startPoint.x, startPoint.y, endPoint.x - startPoint.x, endPoint.y - startPoint.y);
                ctx.stroke();

                // Reset the drawing flag
                isDrawing = false;

                // Display the coordinates of the rectangle
                var rectWidth = Math.abs(endPoint.x - startPoint.x);
                var rectHeight = Math.abs(endPoint.y - startPoint.y);
                rectCoordinates.innerHTML = 'Rectangle Coordinates: x=' + startPoint.x + ', y=' + startPoint.y + ', width=' + rectWidth + ', height=' + rectHeight;
            }
        }

        // Function to handle mouse move event
        function handleMouseMove(event) {
            if (isDrawing) {
                var x = event.offsetX;
                var y = event.offsetY;

                // Clear the canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Draw the rectangle outline
                ctx.beginPath();
                ctx.strokeStyle = 'red';
                ctx.lineWidth = 2;
                ctx.rect(startPoint.x, startPoint.y, x - startPoint.x, y - startPoint.y);
                ctx.stroke();
            }
        }

        // Function to adjust the canvas size to match the image size
        function adjustCanvasSize() {
            img = document.querySelector('.selected-image');
            canvas.width = img.width;
            canvas.height = img.height;
            displayImageInfo();
        }

        // Function to display the image size and dimensions
        function displayImageInfo() {
            img = document.querySelector('.selected-image');
            var imageInfo = 'Image Size: ' + img.width + 'px x ' + img.height + 'px';
            document.getElementById('image-info').textContent = imageInfo;
        }

        // Function to send data to the PHP script
        function sendData() {
            // Get the start and end points of the rectangle
            startPoint = JSON.stringify({ x: startPoint.x, y: startPoint.y });
            endPoint = JSON.stringify({ x: endPoint.x, y: endPoint.y });
            dimensions = JSON.stringify({width: img.width, height: img.height})
            var selectedImage = '<?php echo $IMAGE ?>';  // Access the PHP variable
            var evaluatorName = '<?php echo $evaluatorName ?>';  // Access the PHP variable
            var assessmentCount = '<?php echo $assessmentCount ?>';  // Access the PHP variable
            var Threshold = '<?php echo $Threshold ?>';  
            var selectedAssessment = document.getElementById('assessment-selector').value;
            var Scale = '<?php echo $Scale ?>';  
            var user = '<?php echo $user ?>';


            // Create a FormData object and append the start and end points
            var formData = new FormData();
            formData.append('selectedImage', selectedImage);
            formData.append('startPoint', startPoint);
            formData.append('endPoint', endPoint);
            formData.append('dimensions', dimensions);
            formData.append('evaluatorName', evaluatorName);
            formData.append('assessmentCount', assessmentCount);
            formData.append('Threshold', Threshold);
            formData.append('Scale', Scale);
            formData.append('selectedAssessment', selectedAssessment);
            formData.append('user',user);
            

            // Send an AJAX request to the PHP script
            var xhr = new XMLHttpRequest();
            xhr.open('POST', './PHP/compute-data-4.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Update the result element with the response
                    document.getElementById('result').innerHTML = xhr.responseText;
                    alert(xhr.responseText);
                }
            };

            xhr.send(formData);

            window.parent.enableSummaryTab();
        }


        // Add event listeners to the canvas
        canvas.addEventListener('mousedown', handleMouseDown);
        canvas.addEventListener('mouseup', handleMouseUp);
        canvas.addEventListener('mousemove', handleMouseMove);

        // Display the image size and dimensions when the image is loaded
        window.addEventListener('load', adjustCanvasSize);
    </script>
</body>
</html>
