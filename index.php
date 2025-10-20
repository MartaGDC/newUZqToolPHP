<?php error_reporting(E_ALL); ?>
<?php
session_unset();
session_destroy();
session_start();

$file = 'users.csv';

function readUsers($file) {
    return array_map('str_getcsv', file($file));
}
function saveUsers($file, $users) {
    $handle = fopen($file, 'w');
    foreach ($users as $user) {
        fputcsv($handle, $user);
    }
    fclose($handle);
}

$showDialog = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_password']) && isset($_SESSION['User']) ) { //constraseña nueva desde dialog y usuario sujeto a cambio
        $username = $_SESSION['User'];
        $old_password = $_SESSION['password'];
        $new_password = $_POST['new_password'];

        $users = readUsers($file);
        $updated = false;
        foreach ($users as &$user) {
            if ($user[0] === $username && $user[1] === $old_password) {
                $user[1] = password_hash($new_password, PASSWORD_DEFAULT); //Actualizar la contraseña con hash, más seguro ya que estamos dando demasiados permisos de acceso al archivo
                saveUsers($file, $users);
                echo "<script>alert('Please, enter new credentials.');</script>";
                $updated=true;
                break;
            }
        }
    }
    else if (isset($_POST['username']) && isset($_POST['password'])) { //No hay dialogo activo. Contraseña normal
        $username = $_POST['username'];
        $password = $_POST['password'];
        $users = readUsers($file);
        $found = false;

        foreach ($users as $user) {
            if (($user[0] === $username && password_verify($password, $user[1])) || ($user[0] === $username && $user[1] === $password)) {
                $found = true; //usuario y contraseña correctos
                if (strtolower($username) === strtolower(password_verify($password, $user[1])) || (strtolower($username) === strtolower($password))) { //necesario activar dialogo
                    $_SESSION['User'] = $username;
                    $_SESSION['password'] = $password;
                    $showDialog = true;
                    break;
                }
                else {
                    $_SESSION['User'] = $username;
                    header('Location: ./menu.php');
                    break;
                }
                break;
            }
        }
        if (!$found) {
            echo '<script>alert("You have entered the wrong credentials.");</script>';
        }
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

    dialog {     
        border: none;  
        border-radius: 10px;
        padding: 10px 10px;
        text-align: center;
        background-color: #B0B0B0;
    }
    dialog::backdrop {
        background: rgba(0,0,0,0.4);
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
        <dialog id="changePasswordDialog">
            <p>Please, change the default password for a new one.</p>
            <form id="changePasswordForm" method="POST" action="">
                <input type="password" id="newPassword" name="new_password" required><br><br>
                <button type="submit">Submit</button>
            </form>
        </dialog>
    </div>
    <script>
        <?php if ($showDialog): ?>
            document.getElementById('changePasswordDialog').showModal();
            const oldPassword = "<?php echo $password; ?>"; // contraseña antigua
            const username = "<?php echo $username; ?>";
            const form = document.getElementById('changePasswordForm');
            form.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('newPassword').value;
                alert(username);
                if (newPassword.toLowerCase() === oldPassword.toLowerCase()) {
                    e.preventDefault(); // detener envío del formulario
                    alert('The new password must be different from the old one.');
                }
            });
        <?php endif; ?>
    </script>
</body>
</head>
</html> 
