<?php
require_once 'auth_check.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Upload and Playback</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            background-color: #f0f0f0;
            color: #333;
        }
        h1 {
            margin-bottom: 20px;
        }
        #controls {
            margin-top: 10px;
        }
        #controls button {
            margin: 0 5px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            color: #fff;
            background-color: #007BFF;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #controls button:hover {
            background-color: #0056b3;
        }
        #screenshot {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Upload and Play a Video</h1>
    <input type="file" id="videoInput" accept="video/*">
    <br><br>
    <video id="videoPlayer" width="600" controls>
        Your browser does not support the video tag.
    </video>
    <div id="controls">
        <button onclick="stepBackward()">Step Backward</button>
        <button onclick="stepForward()">Step Forward</button>
        <button onclick="captureFrame()">Capture Frame</button>
        <button onclick="downloadImage()">Download Image</button> <!-- Added download button for image -->
    </div>
    <canvas id="canvas" style="display: none;"></canvas>
    <div id="screenshot"></div>

    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const frameTime = 1 / 30; // Assuming 30 fps

        let videoName = '';  // Add this line to store the video name
        let capturedImage = ''; // Add this line to store the captured image

        document.getElementById('videoInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            videoName = file.name;  // Store the video name when a file is selected
            const url = URL.createObjectURL(file);
            videoPlayer.src = url;
            videoPlayer.load();
            videoPlayer.play();
        });

        function stepForward() {
            videoPlayer.currentTime += frameTime;
        }

        function stepBackward() {
            videoPlayer.currentTime -= frameTime;
        }

        let allowCapture = true;

        function captureAnotherFrame() {
            allowCapture = true;
        }

        function captureFrame() {
            if (!allowCapture) {
                alert("Please wait, the previous frame is still being processed.");
                return;
            }

            allowCapture = false;

            const canvas = document.getElementById('canvas');
            canvas.width = videoPlayer.videoWidth;
            canvas.height = videoPlayer.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(videoPlayer, 0, 0, canvas.width, canvas.height);

            const img = document.createElement('img');
            img.src = canvas.toDataURL('image/png');
            img.width = 600; // To display the image in the same width as the video
            const screenshotDiv = document.getElementById('screenshot');
            screenshotDiv.innerHTML = ''; // Clear previous screenshot
            screenshotDiv.appendChild(img);

            capturedImage = img.src; // Store the captured image data

            // Send the captured frame to the server
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'extract_frame.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                allowCapture = true;
                if (xhr.status === 200 && xhr.responseText.trim() === 'success') {
                    alert("The image has been selected correctly");

                    // Delay the refresh to ensure server processing completes
                    setTimeout(function() {
                        //parent.location.href = './tabs.php';
                        //parent.location.reload(true);
                        window.opener.location.reload();
                        // completeFunction();
                    }, 500); // Adjust the delay as necessary (e.g., 500ms)
                } else {
                    alert("Sorry, there was an error processing your request.");
                }
            };

            const imgData = 'img=' + encodeURIComponent(img.src) + '&name=' + encodeURIComponent(videoName);
            xhr.send(imgData);
        }

        function downloadImage() {
            const a = document.createElement('a');
            a.href = capturedImage;
            a.download = 'captured_image.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // function completeFunction() {
        //     // Perform the necessary operations

        //     // Set a flag in localStorage to signal the original tab to reload
        //     localStorage.setItem('reloadPage', 'true');
        // }
    
    </script>
</body>
</html>