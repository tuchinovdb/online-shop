<?php

session_start();
require_once 'validate_profile.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login-form.php');
    exit;
}

$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$userId = $_SESSION['user_id'];
$data = $_POST;

// Валидация
$errors = validateProfileUpdate($data, $pdo, $userId);

if (!empty($errors)) {
    // Сохраняем ошибки и старые значения в сессии, редиректим обратно
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = $data;
    header('Location: /edit-profile.php');
    exit;
}

// Если ошибок нет — обновляем данные
$name = trim($data['name']);
$email = trim($data['email']);

if (!empty($data['new_password'])) {
    // Меняем пароль
    $newPasswordHash = password_hash($data['new_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $newPasswordHash,
        ':id' => $userId
    ]);
} else {
    // Пароль не меняем
    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':id' => $userId
    ]);
}

// Успех — сохраняем сообщение и идём в профиль
$_SESSION['success_message'] = 'Данные успешно обновлены';
header('Location: /profile.php');
exit;