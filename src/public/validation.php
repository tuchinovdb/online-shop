<?php
function validateRegistration(array $data, PDO $pdo): array
{
    $errors = [];

    $name = isset($data['name']) ? trim($data['name']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $password = isset($data['psw']) ? trim($data['psw']) : '';
    $passwordRep = isset($data['psw-repeat']) ? trim($data['psw-repeat']) : '';

    // Проверка имени
    if ($name === '') {
        $errors['name'] = 'Укажите имя';
    } elseif (strlen($name) < 2) {
        $errors['name'] = 'Имя должно содержать не менее двух символов';
    }

    // Проверка email
    if ($email === '') {
        $errors['email'] = 'Укажите email';
    } elseif (strlen($email) < 3) {
        $errors['email'] = 'Email должен содержать не менее трёх символов';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email некорректный';
    } else {
        // Проверка уникальности
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $checkStmt->execute([':email' => $email]);
        if ($checkStmt->fetch()) {
            $errors['email'] = 'Пользователь с таким email уже существует';
        }
    }

    // Проверка пароля
    if ($password === '') {
        $errors['psw'] = 'Придумайте пароль';
    } elseif (strlen($password) < 6) {
        $errors['psw'] = 'Пароль должен содержать не менее шести символов';
    }

    // Проверка повтора пароля
    if ($passwordRep === '') {
        $errors['psw-repeat'] = 'Повторите пароль';
    } elseif (!isset($errors['psw'])) { // сравниваем только если пароль без ошибок
        if ($password !== $passwordRep) {
            $errors['psw-repeat'] = 'Пароли не совпадают';
        }
    }

    return $errors;
}