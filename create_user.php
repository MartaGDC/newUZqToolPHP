<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = strtoupper($username);

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users(username, password_hash) 
        VALUES(:u,:p)
    ");
    $stmt->execute([
        "u"=>$username,
        "p"=>$hash
    ]);

    echo "Usuario creado correctamente";
}
?>

<form method="POST">
    Usuario: <input name="username"><br>
    <button>Crear</button>
</form>