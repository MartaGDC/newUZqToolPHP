<?php
require_once 'auth_check.php';

error_reporting(E_ALL); 
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
    .container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
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

    #image-container {
        position: relative;
        display: inline-block;
    }

    #canvas {
        position: absolute;
        top: 0;
        left: 0;
    }

    .canvas-container {
    position: relative;
    }

    .canvas-container canvas {
    position: absolute;
    top: 0;
    left: 0;
    }

    .selected-image {
    width: 100%;
    height: auto;
    max-width: 700px;
    max-height: 700px;
    display: block;
    margin: auto;
    object-fit: contain;
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


<!DOCTYPE html>
<html>
<head>
    <title>Bones</title>
    <script src="./Libraries/fabric.min.js"></script>

</head>
<body>

    <div class="container">
        <?php if ($imagePath !== '') { ?>
            
        <div class="conten-active" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <div class="form-group">
                <label for='selected-image'>Selected Image: <?php echo $IMAGE ?></label>
                <button onclick="loadTissueImage()">Reload Image</button>
            </div>
            <div class="form-group">
                <label for='evaluator'>Evaluator:</label>
                <label> <?php echo $evaluatorName ?></label>
            </div>

            <div class="form-group">
                <label for="threshold-selector">Threshold:</label>
                <input type="range" name="threshold" id="threshold-selector" min="1" max="256" value="95" oninput="updateThreshold(this.value)">
                <div id="threshold-value">95</div>
            </div>
            <div class='message-box'>
                Set the threshold so that you see barely anything else than bone in the region
            </div> 
        </div>

        <div id="image-container">
            <img src="<?php echo $imagePath; ?>" alt="Selected Image" class="selected-image" id="selected-image" onload="adjustCanvasSize()">
            <div class="canvas-container">
                <canvas id="canvas" class="selected-image"></canvas>
            </div>
        </div>

        <div class="form-group">
            <br>
            <button onclick="select_ROI()" id="continue-button">Continue</button>
        </div>

        <?php } else { ?>
            <div class="form-group">
                <label for="no-image">Please click the button to load the image.</label>
                <button onclick="loadImage()">Load Image</button>
            </div>
        <?php } ?>


    </div>
</body>
</html>

<script>

    var canvas = new fabric.Canvas('canvas'); // Declare canvas variable
    var selectedImage = document.getElementById('selected-image');
    var img; // Declare the global variable for the image
    var threshold = 95;

    //document.getElementById('send-button').style.display = 'none';


    // Load the image and apply image processing
    window.onload = function() {
        if (selectedImage.src) {
            fabric.Image.fromURL(selectedImage.src, function(image) {
                canvas.add(image);
                img = image; // Assign loaded image to global variable

                // Define initial threshold value
                var initialThreshold = 95;

                // Apply the threshold filter
                applyThreshold(selectedImage, initialThreshold);
            });
        }
    };

    function applyThreshold(imageElement, threshold) {
        // Create a temporary canvas to draw the image
        var canvasElement = document.createElement('canvas');
        var context = canvasElement.getContext('2d');
        canvasElement.width = 700;
        canvasElement.height = 500;
        context.drawImage(imageElement, 0, 0, 700, 500);

        // Get the image data
        var imageData = context.getImageData(0, 0, 700, 500);
        var data = imageData.data;

        // Apply the threshold to the image data
        var thresholdValue = threshold ;
        var len = data.length;
        for (var i = 0; i < len; i += 4) {
            var gray = data[i]; // Grayscale value (red channel since it's a black and white image)
            var color = gray < thresholdValue ? 0 : 255;

            data[i] = color; // red
            data[i + 1] = color; // green
            data[i + 2] = color; // blue
        }

        // Update the canvas with the modified image data
        context.putImageData(imageData, 0, 0);

        // Hide the selected-image element
        imageElement.style.display = 'none';

        // Create a fabric.Image object with the modified canvas
        fabric.Image.fromURL(canvasElement.toDataURL(), function(modifiedImg) {
            // Remove the original image from the canvas (if it exists)
            if (img) {
            canvas.remove(img);
            }

            // Add the modified image to the canvas
            canvas.add(modifiedImg);
            img = modifiedImg; // Assign the filtered image to the global variable

            canvas.renderAll();
        });
    }


    function updateThreshold(value) {
        // Update the threshold value display
        document.getElementById('threshold-value').textContent = value;

        // Apply the threshold filter
        threshold = parseInt(value, 10);
        applyThreshold(selectedImage, threshold);
    }

    function loadImage() {
        // Reload the page
        location.reload();
    }

    function updateValue(value) {
        // Update the assessment value display
        document.getElementById('value').textContent = value;
    }

    // Display the image size and dimensions when the image is loaded
    window.addEventListener('load', adjustCanvasSize);

    //funcion para la selección
    function select_ROI() {
        // Get the threshold value
        var thresholdValue = document.getElementById('threshold-selector').value;

        // Save the threshold value in a session variable using AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', './PHP/save_threshold.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            // Redirect to bone2.php
            window.location.href = 'bone2.php'
        };
        xhr.send('threshold=' + thresholdValue);
    }


    

    // Function to adjust the canvas size to match the image size
    function adjustCanvasSize() {
        var image = document.getElementById('selected-image');
        canvas.setWidth(image.width);
        canvas.setHeight(image.height);
    }

</script>

