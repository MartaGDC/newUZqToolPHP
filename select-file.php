<?php
require_once 'auth_check.php';

session_start();

// Specify the path to the echographies folder
$echographiesPath = 'Upload/';

// Handle file upload if a file is selected
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['uploaded-file'])) {
        $file = $_FILES['uploaded-file'];

        // Check if the file is uploaded successfully
        if ($file['error'] === UPLOAD_ERR_OK) {
            $fileName = basename($file['name']);
            $destination = $echographiesPath . $fileName;

            // Move the uploaded file to the echographies folder
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // File moved successfully
                // Rest of the code
            } else {
                // Failed to move the file
                echo "Error moving the uploaded file.";
            }
        } else {
            // File upload error
            echo "Error uploading the file.";
        }

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Next Page</title>

    <style>
        .selected-image {
            width: 95%;
            height: auto;
            max-width: 400px;
            max-height: 400px;
            display: block;
            margin: auto;
            object-fit: contain;
        }

        /* CSS styles for form elements */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form label {
            width: 200px;
            text-align: left;
            padding-left: 10px;
        }

        form input[type="text"],
        form input[type="file"] {
            width: 300px;
            padding: 5px;
            margin-bottom: 10px;
        }

        .form-group {
            display: flex;
            align-items: center;
            justify-content: center; /* Center-align children vertically (if needed) */
            margin-bottom: 10px;
        }

        .form-group label {
            width: 200px;
            text-align: right;
            padding-right: 10px;
        }

        .form-group input,
        .form-group select {
            width: 300px;
            padding: 5px;
            margin-right: 10px;
        }

        .form-group input[type="loadimage"] {
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            color: black;
            background-color: #FFD700; /* Pastel yellow */
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: auto; /* Adjust to text width */
        }

        .form-group input[type="loadimage"]:hover {
            background-color: #FFC107; /* Slightly darker pastel yellow on hover */
        }

        .form-group input[type="submit"] {
            padding: 10px 20px; /* Enlarged padding */
            font-size: 16px; /* Larger font size */
            font-weight: bold;
            color: white; /* Changed text color */
            background-color: #007BFF; /* New background color */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group input[type="submit"]:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }

        .form-group input[type="file"] {
            padding: 5px 10px;
            font-size: 14px;
            font-weight: bold;
            color: black;
            background-color: #FFFACD; /* Light pastel yellow */
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group input[type="file"]:hover {
            background-color: #FFEFD5; /* Slightly darker pastel yellow on hover */
        }

        .videoBtn {
            box-shadow: 3px 4px 0px 0px #3e7327;
            background:linear-gradient(to bottom, #77b55a 5%, #72b352 100%);
            background-color:#77b55a;
            border-radius:18px;
            border:1px solid #4b8f29;
            display:inline-block;
            cursor:pointer;
            color:#000000;
            font-family:Arial;
            font-size:17px;
            padding:7px 25px;
            text-decoration:none;
            text-shadow:0px 1px 0px #5b8a3c;
        }
        .videoBtn:hover {
            background:linear-gradient(to bottom, #72b352 5%, #77b55a 100%);
            background-color:#72b352;
        }
        .videoBtn:active {
            position:relative;
            top:1px;
        }

    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Image preview function
            function previewImage(file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#selected-image').attr('src', e.target.result);
                }

                reader.readAsDataURL(file);
            }

            // Handle file input change event
            $('#uploaded-file').on('change', function() {
                var file = this.files[0];

                if (file) {
                    previewImage(file);
                }
            });

            <?php if (isset($file)): ?>
            var selectedImage = '<?php echo $fileName; ?>';
            var echographiesPath = '<?php echo $echographiesPath; ?>';
            var imagePath = echographiesPath + selectedImage;
            $('#selected-image').attr('src', imagePath);
            $('#selected-image').attr('alt', selectedImage);
            <?php endif; ?>
        });
    </script>
</head>
<body>
<div class="container">
        <div class="tabs">
            <!-- Tabs omitted for brevity -->
        </div>

        <div class="content active">
        <!-- Two Column Menu for Actions with Titles and Left Alignment Corrected for Form Elements -->
        <form method="post" enctype="multipart/form-data" action="" id="main-form">
    <div style="display: flex; justify-content: space-between; margin: 20px;">
        <div style="flex: 1; margin-right: 10px;">
            <h2>Image</h2>
            <div class="form-group" style="align-items: flex-start; flex-direction: column;">
                <label for="uploaded-file" style="text-align: left"> Uploaded Image:</label>
                <?php if (isset($_SESSION['IMG'])) { ?>
                    <div>
                        <p><?php echo htmlspecialchars($_SESSION['IMG']); $file = $_SESSION['IMG'];$fileName = $_SESSION['IMG']?></p>
                    </div>
                    <div>
                        <img id="re-selected" src="<?php echo $echographiesPath . $_SESSION['IMG']; ?>" alt="Re-Selected" class="selected-image">
                    </div>
                <?php } else { ?>
                    <input type="file" name="uploaded-file" id="uploaded-file" required>
                <?php } ?>
            </div>
        </div>
        <div style="flex: 1; margin-left: 10px;">
            <h2>Video</h2>
            <button class="videoBtn" onclick="window.open('video2frame.php', '_blank');">
                Extract your image from video
            </button>
        </div>
    </div>
    <div style="margin: 60px 20px 20px 20px;">
    <div class="form-group">
        <label for="evaluator-name">Name:</label>
        <input type="text" name="evaluator-name" id="evaluator-name" placeholder="Enter evaluator name" value="<?php echo isset($_SESSION['EvName']) ? htmlspecialchars($_SESSION['EvName']) : ''; ?>" required>
    </div>

    <div class="form-group">
        <label for="assessment-count">Number of Assessment:</label>
        <input type="number" name="assessment-count" id="assessment-count" placeholder="Enter number of assessments" value="1">
    </div>

    <div class="form-group">
        <input type="submit" value="Submit" id="submit-btn">
    </div>
    </div>
</form>

<div>
    <img src="" alt="" id="selected-image" class="selected-image">
</div>


            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($file)): ?>
                    <?php
                    // Retrieve the selected image filename and evaluator name
                    $selectedImage = $fileName;
                    $_SESSION['IMG'] = $selectedImage;

                    $evaluatorName = $_POST['evaluator-name'];
                    $_SESSION['EvName'] = $evaluatorName;

                    $assessmentCount = $_POST['assessment-count'];
                    $_SESSION['EvCount'] = $assessmentCount;

                    // // Display the selected image filename and evaluator name
                    // echo "Selected Image: " . htmlspecialchars($selectedImage) . "<br>";
                    // echo "Evaluator Name: " . htmlspecialchars($evaluatorName) . "<br>";
                    // echo "Assessment Count: " . htmlspecialchars($assessmentCount) . "<br><br>";
                    // echo "Please, click the >> button below to continue<br>";
                    ?>
                    <script>
                        // Check if the session variable 'Scale' is set
                        <?php if (isset($_SESSION['Scale'])) { ?>
                            // If 'Scale' is set, call the enableTissueTab() function
                            window.parent.enableTissueTab();
                            alert("Scale already set, please click the '>>' button to proceed.");
                        <?php } else { ?>
                            // If 'Scale' is not set, redirect to 'scale.php'
                            window.location.href = 'scale.php';
                        <?php } ?>

                    // Display the selected image filename and evaluator name in the console
                    console.log("Selected Image: <?php echo isset($selectedImage) ? htmlspecialchars($selectedImage) : 'Not available'; ?>");
                    console.log("Evaluator Name: <?php echo isset($evaluatorName) ? htmlspecialchars($evaluatorName) : 'Not available'; ?>");
                    console.log("Assessment Count: <?php echo isset($assessmentCount) ? htmlspecialchars($assessmentCount) : 'Not available'; ?>");
                    console.log("Scale: <?php echo isset($_SESSION['Scale']) ? htmlspecialchars($_SESSION['Scale']) : 'Not available'; ?>");
                </script>
            <?php endif; ?>
        </div>
</div>
    <!-- Hidden form to refresh the page -->
    <form id="refreshForm" method="post" action="">
        <input type="hidden" name="refresh" value="1">
    </form>
</body>
</html>