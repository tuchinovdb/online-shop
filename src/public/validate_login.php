<?php
function validateLogin(string $email, string $password, PDO $pdo): array
{
$errors = [];
if ($email === '') {
$errors['username'] = 'Укажите email';
}

if ($password === '') {
$errors['password'] = 'Укажите пароль';
}

if (empty($errors)) {
$stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
$errors['username'] = 'Неверный email или пароль';
}
}
return $errors;
}
?>