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
        max-width: 950px;
        max-height: 950px;
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

    .message-box {
        background-color: #f2f2f2;
        padding: 10px;
        border-radius: 5px;
        color: royalblue;
        font-weight: bold;
    }

    .slider{
        accent-color: blue;
    }

    .submit-button {
            padding: 8px 14px;
            font-size: 14px;
            font-weight: bold;
            color: black;
            background-color: lightgoldenrodyellow;
            border: 1px solid black;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
    }
    
    .submit-button:hover{
        background-color:rgb(243, 218, 107);
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

<!DOCTYPE html>
<html>
<head>
    <title>Set the scale</title>
</head>
<body>
    <div class="conten-active" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <div class="form-group">
            <label for='selected-image'>Selected Image:</label>
            <label> <?php echo $IMAGE ?></label>
        </div>
        <div class="form-group">
            <div class="message-box">
                Select a rectangle of 1cm to set the scale
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: center;">
        <div id="image-container">
            <img src="<?php echo $imagePath; ?>" alt="Selected Image" class="selected-image" onload="adjustCanvasSize()">
            <canvas id="image-canvas-1"></canvas>
        </div>
    </div>
    <br>
    <div style="display: flex; justify-content: center;" id="image-info"></div>
    <div style="display: flex; justify-content: center;" id="rectangle-coordinates"></div>

    <div style="display: flex; justify-content: center;">
        <button onclick="compute()" class="submit-button" id='compute-button'>Compute Scale</button>
    </div> 

    <script>
        var canvas = document.getElementById('image-canvas-1');
        var ctx = canvas.getContext('2d');
        var isDrawing = false;
        var startPoint, endPoint, img;
        var rectCoordinates = document.getElementById('rectangle-coordinates');
        var rectHeight = 0;
        var Height = 0;

        // Function to handle mouse down event
        function handleMouseDown(event) {
            var x = event.offsetX;
            var y = event.offsetY;

            // Save the starting point of the rectangle
            startPoint = { x: x, y: y };

            // Set the drawing flag to true
            isDrawing = true;
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
                ctx.strokeStyle = 'fuchsia';
                ctx.lineWidth = 2;
                ctx.rect(startPoint.x, startPoint.y, endPoint.x - startPoint.x, endPoint.y - startPoint.y);
                ctx.stroke();

                // Reset the drawing flag
                isDrawing = false;

                // Display the coordinates of the rectangle
                var rectWidth = Math.abs(endPoint.x - startPoint.x);
                rectHeight = Math.abs(endPoint.y - startPoint.y);
                rectCoordinates.innerHTML = 'Rectangle height=' + rectHeight + '; Image Height=' + Height+'; Scale: '+(rectHeight/Height).toFixed(3);
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
                ctx.strokeStyle = 'fuchsia';
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
            Height = img.height;
            document.getElementById('image-info').textContent = imageInfo;
        }

        // Function to send data to the PHP script
        function compute() {
            // Get the scale
            var scale = rectHeight / Height;

            // Save the threshold value in a session variable using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', './PHP/save_scale.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('scale=' + scale);

            button = document.getElementById('compute-button');
            button.style.backgroundColor = 'lightgreen';
            button.textContent = 'Success';
            button.style.display = 'none';
            document.getElementById('rectangle-coordinates').textContent = 'You can go to the next tab (click >> button)';

            window.parent.enableTissueTab();
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
