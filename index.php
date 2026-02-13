<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;


try {
    $pdo = new PDO(
        "pgsql:host=localhost;dbname=db_php_flask",
        "php_flask",
        "SdeSindrome$",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$showDialog = false;

//Cambio contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password']) && isset($_SESSION['User'])) {
    
    $username = $_SESSION['User'];
    $new_password = $_POST['new_password'];

    $hash = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        UPDATE users
        SET password_hash = :hash,
            must_change_password = false
        WHERE username = :username
    ");
    $stmt->execute([
        "hash"=>$hash,
        "username"=>$username
    ]);
    echo "<script>alert('Please, enter new credentials.');</script>";
}

//Contraseña no default:
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['username'])) {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(["username" => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['User'] =$username;
        
        //JWT para compartir info con Flask (para no autenticar en flask tambien)
        $key = "NH/a05xVQFOsoEk4uBFrdRVVOJw1hdu9txKRmyCTYrE="; //generada con openssl rand -base64 32 para asegurar random y tmñ
        $payload = [
            "username" => $username,
            "iat" => time(),
            "exp" => time() + 3600
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        $_SESSION['jwt'] = $jwt; //para que acceda menu.php directamente desde var de sesion

        if ($user['must_change_password']) {
            $showDialog=true;
        } else {
            header('Location: ./menu.php');
            exit();
        }
    } else { 
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
