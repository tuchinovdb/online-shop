<?php

function validateProfileUpdate(array $data, PDO $pdo, int $currentUserId): array
{
    $errors = [];

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $oldPassword = $data['old_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';
    $newPasswordRepeat = $data['new_password_repeat'] ?? '';

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
        // Проверка уникальности email (исключая текущего пользователя)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $currentUserId]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Этот email уже используется другим пользователем';
        }
    }

    // Смена пароля (только если заполнено поле нового пароля)
    if ($newPassword !== '') {
        // Проверяем старый пароль
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $currentUserId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($oldPassword, $user['password'])) {
            $errors['old_password'] = 'Неверный старый пароль';
        }

        if (strlen($newPassword) < 6) {
            $errors['new_password'] = 'Новый пароль должен содержать не менее шести символов';
        }

        if ($newPassword !== $newPasswordRepeat) {
            $errors['new_password_repeat'] = 'Пароли не совпадают';
        }
    }

    return $errors;
}