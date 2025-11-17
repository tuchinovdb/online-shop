<?php

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass');
$stmt = $pdo ->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $username]);

$user = $stmt -> fetch();

$errors = [];

if ($user === false) {
$errors['username'] = "Логин или пароль указанны неверно , попробуйте еще раз";
} else {
    $passwordDb = $user ['password'];

    if (password_verify($password, $passwordDb)) {
        setcookie('user_id', $user['id']);
        header('Location: /catalog.php');
    } else {
        $errors['username'] = 'Логин или пароль указанны неверно , попробуйте еще раз';
    }
}

require_once './login-form.php';
