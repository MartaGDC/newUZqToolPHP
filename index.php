<?php error_reporting(E_ALL); ?>
<?php
session_start();

// Function to validate user credentials
function validateCredentials($username, $password) {
    $users = array_map('str_getcsv', file('users.csv'));

    foreach ($users as $user) {
        if ($user[0] === $username && $user[1] === $password) {
            return true; // Valid credentials
        }
    }

    return false; // Invalid credentials
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (validateCredentials($username, $password)) {
        $_SESSION['User'] = $username;
        header('Location: ./menu.php');
        exit;
    } else {
        // Display an alert message for invalid credentials
        echo '<script>alert("You have entered the wrong credentials.");</script>';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>UZ qTool</title>
    <link rel="icon" href="./favicon.png" type="image/png">

<style>
    body {
        font-family: 'Arial', sans-serif;
        color: #1A1A1A;
        font-weight: bold;
        margin: 0;
        padding: 0;
    }

    .container { /*fondo y contenido centrado, va a tener una ventana y un botón*/
        background-image: url('web-images/background.jpg');
        background-size: cover;
        background-position: center;
        width: 100%;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .start-page { /*usario y contraseña, dentro de container*/
        text-align: center;
        background-color: #B0B0B0;
        border-radius: 10px;
        padding: 10px 10px;
        display:flex;
        flex-direction:column;
        align-items: center;
    }

    .title {
        font-size: 24px;
    }

    .form-group {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 10px 0px;
    }

    .form-group label {
        width: 100px;
    }

    .form-group input {
        width: 200px;
        padding: 5px;
    }

    .button {
        display: inline-block;
        padding: 10px 20px;
        font-family: 'Arial', sans-serif;
        font-size: 15px;
        font-weight: bold;
        color: #F5F5F5;
        text-align: center;
        background-color: #1A1A1A;
        border: 1px solid #F5F5F5;
        border-radius: 5px;
        transition: background-color 0.1s, color 0.1s, border-color 0.1s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }

    .button:hover,
    .button:active {
        background-color: #3a3a3a;
        color: #FFFFFF;
        border: 1px solid #FFFFFF;
    }

    .error-message {
        color: red;
        font-size: 18px;
        margin-top: 2px;
        text-align: center;
    }

    .bottom-right {
        position: relative;
        bottom: -250px;
        right: -350px;
        font-size: 14px;
        font-weight: bold;
        color: Black;
        background-color: greenyellow;
        border: 1px solid black;
        border-radius: 30px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .bottom-right:hover{
        background-color: yellowgreen;
    }

</style>    

<body>
    <div class="container">
        <div class="start-page">
            <h1 class="title">UZ qTool</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button class="button" type="submit">Login</button>
            </form>
        </div>
        <button id="new-user-button" class="bottom-right">Request New User Creation</button>

        <script>
            document.getElementById('new-user-button').addEventListener('click', function () {
                var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
                var popupWidth = 500; // Set the width of the popup
                var popupHeight = 600; // Set the height of the popup
                var leftPosition = (screenWidth - popupWidth) / 2;
                var topPosition = (screenHeight - popupHeight) / 2;

                window.open('registration-popup.php', 'NewUserPopup', 'width=' + popupWidth + ',height=' + popupHeight + ',left=' + leftPosition + ',top=' + topPosition);

            });
        </script>

    </div>
</body>
</head>
</html> 
