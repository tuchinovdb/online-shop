<?php
session_start();

require_once 'validate_login.php';


$email = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');


$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);


$errors = validateLogin($email, $password, $pdo);


if (empty($errors))
{
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $userId = $stmt->fetchColumn();


    $_SESSION['user_id'] = $userId;

    header('Location: /catalog.php');
    exit;
}


require_once './login-form.php';
