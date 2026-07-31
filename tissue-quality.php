<?php
require_once 'auth_check.php';

// Start the session
session_start();

// Specify the path to the echographies folder
$echographiesPath = 'Upload/';

// Check if the image has been submitted
if (isset($_SESSION['IMG'], $_SESSION['EvName'], $_SESSION['EvCount'], $_SESSION['button'])) {
    // Retrieve the selected image filename from the session variable
    $IMAGE = $_SESSION['IMG'];
    $evaluatorName = $_SESSION['EvName'];
    $assessmentCount = $_SESSION['EvCount'];
    $user = $_SESSION['User'];
    $caseButton = $_SESSION['button'];

    // Construct the image path
    $imagePath = $echographiesPath . $IMAGE;

    // echo "<script>console.log('".$user."')</script>";
    // Unset the session variable after retrieving its value
    // unset($_SESSION['IMG'], $_SESSION['EvName'], $_SESSION['EvCount']);

} else {
    // Set default values if no image is submitted
    $IMAGE = '';
    $imagePath = '';
    // echo "<script>console.log('no image')</script>";
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
        color: royalblue;
        font-weight: bold;
    }
    
    g {color: darkgreen}

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

            <?php if($caseButton !== 'sarcopenia'): ?>
                <div class="form-group">
                    <label for="assessment-selector">Assessment:</label>
                    <input type="range" class='slider'
                    name="assessment" id="assessment-selector"
                    list="values" min='0' max='3' value='2'
                    oninput="updateValue(this.value,'value')"></input>
                    <div id="value"></div>
                </div> 
                <div class='message-box'>
                    Select one rectangle representative of the tissue quality
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label>Recto femoral:</label>
                    <input
                        type="range" class="slider" id="structure-selector-1"
                        list="values" min="0" max="3" value="2"
                        oninput="updateValue(this.value,'value1')">
                    <div id="value1"></div>
                </div>
                <div class="form-group">
                    <label>Vasto intermedio:</label>
                    <input
                        type="range" class="slider" id="structure-selector-2"
                        list="values" min="0" max="3" value="2"
                        oninput="updateValue(this.value,'value2')">
                    <div id="value2"></div>
                </div>
                <div class="form-group">
                    <label>Grasa subcutánea:</label>
                    <input
                        type="range" class="slider" id="structure-selector-3"
                        list="values" min="0" max="3" value="2"
                        oninput="updateValue(this.value,'value3')">
                    <div id="value3"></div>
                </div>
                <div class="message-box">
                    Selecciona 3 rectángulos en este orden: Recto femoral (red), Vasto intermedio (cyan), Grasa subcutánea (green).
                </div>
            <?php endif; ?>
        </div>

        <div style="display: flex; justify-content: center;">
            <div id="image-container">
                <img src="<?php echo $imagePath; ?>" alt="Selected Image" class="selected-image" onload="adjustCanvasSize()">
                <canvas id="image-canvas-1"></canvas>
            </div>
        </div>
        <br>
        <!--<div style="display: flex; justify-content: center;" id="image-info"></div>-->
        <!--<div style="display: flex; justify-content: center;" id="rectangle-coordinates"></div>-->


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

        <?php if($caseButton == 'sarcopenia'){ ?>
            const numRectangles = 3;
            const colors = ["red","cyan","lime"];
        <?php } else { ?>
            const numRectangles = 1;
            const colors = ["red"];
        <?php } ?>

        //functions for sliders
        function updateValue(value, ID) {
            document.getElementById(ID).textContent = value;
        }

        window.onload = function() {
            <?php if ($caseButton == 'menisco') { ?>
                updateValue(document.getElementById('structure-selector-1').value, 'value1');
                updateValue(document.getElementById('structure-selector-2').value, 'value2');
                updateValue(document.getElementById('structure-selector-3').value, 'value3');
            <?php } else { ?>
                var defaultValue = document.getElementById('assessment-selector').value;
                updateValue(defaultValue, 'value');
            <?php } ?>
            adjustCanvasSize1();
        }


        var canvas = document.getElementById('image-canvas-1');
        var ctx = canvas.getContext('2d');
        var isDrawing = false;
        var startPoint, endPoint, img;
        // var rectCoordinates = document.getElementById('rectangle-coordinates');
        var rectangles = []; // Initialize the rectangles array
        var rectCount = 0;

        // Function to handle mouse down event
        function handleMouseDown(event) {
            if(rectangles.length >= numRectangles) {
                return;
            }
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
                ctx.strokeStyle = colors[rectangles.length];
                ctx.lineWidth = 2;
                ctx.rect(startPoint.x, startPoint.y, endPoint.x - startPoint.x, endPoint.y - startPoint.y);
                ctx.stroke();

                // Reset the drawing flag
                isDrawing = false;

                // Display the coordinates of the rectangle
                var rectWidth = Math.abs(endPoint.x - startPoint.x);
                var rectHeight = Math.abs(endPoint.y - startPoint.y);
                // rectCoordinates.innerHTML = 'Rectangle Coordinates: x=' + startPoint.x + ', y=' + startPoint.y + ', width=' + rectWidth + ', height=' + rectHeight;
                
                var rectangle = {
                    x: startPoint.x,
                    y: startPoint.y,
                    width: rectWidth,
                    height: rectHeight
                };
                // Push the rectangle object into the rectangles array
                rectangles.push(rectangle);
                // Increment the rectangle count
                rectCount++;
            }
        }

        // Function to handle mouse move event
        function handleMouseMove(event) {
            if (isDrawing) {
                var x = event.offsetX;
                var y = event.offsetY;

                // Clear the canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Redraw previously drawn rectangles
                for (var i = 0; i < rectangles.length; i++) {
                    var rect = rectangles[i];
                    ctx.beginPath();
                    ctx.strokeStyle = colors[i];
                    ctx.lineWidth = 2;
                    ctx.rect(rect.x, rect.y, rect.width, rect.height);
                    ctx.stroke();
                }

                // Draw the rectangle outline
                ctx.beginPath();
                ctx.strokeStyle = colors[rectangles.length];
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

        // Function to send data to the PHP script
        function sendData() {
            // Check if any rectangles are drawn
            if(rectangles.length != numRectangles) {
                alert('Please draw ' + numRectangles + ' rectangles.');
                return;
            }

            // Get the dimensions of the canvas
            var canvasWidth = canvas.width;
            var canvasHeight = canvas.height;

            dimensions = JSON.stringify({width: img.width, height: img.height})

            var selectedImage = '<?php echo $IMAGE ?>';  // Access the PHP variable
            var evaluatorName = '<?php echo $evaluatorName ?>';  // Access the PHP variable
            var assessmentCount = '<?php echo $assessmentCount ?>';  // Access the PHP variable
            var user = '<?php echo $user ?>';

            // Create a FormData object and append the start and end points
            var formData = new FormData();
            formData.append('selectedImage', selectedImage);
            for(var i=0; i<numRectangles;i++) {
                let startPoint = JSON.stringify({
                    x:rectangles[i].x,
                    y:rectangles[i].y
                });
                let endPoint = JSON.stringify({
                    x:rectangles[i].x+rectangles[i].width,
                    y:rectangles[i].y+rectangles[i].height
                });
                formData.append("startPoint"+(i+1),startPoint);
                formData.append("endPoint"+(i+1),endPoint);
            }
            formData.append('dimensions', dimensions);
            formData.append('evaluatorName', evaluatorName);
            formData.append('assessmentCount', assessmentCount);
            <?php if($caseButton == 'sarcopenia'){ ?>
                formData.append('recto', document.getElementById('structure-selector-1').value);
                formData.append('vasto', document.getElementById('structure-selector-2').value);
                formData.append('grasa', document.getElementById('structure-selector-3').value);
            <?php } else { ?>
                formData.append('selectedAssessment', document.getElementById('assessment-selector').value);
            <?php } ?>

            formData.append('user',user);

            // Send an AJAX request to the PHP script
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'PHP/compute-data-1.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Update the result element with the response
                    document.getElementById('result').innerHTML = xhr.responseText;
                    alert(xhr.responseText);
                }
            };

            xhr.send(formData);
            <?php if ($caseButton == 'sarcopenia') { ?>
                window.parent.enableMorphologyRectoTab();
            <?php } else { ?>
                window.parent.enableMorphologyTab();
            <?php } ?>
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
