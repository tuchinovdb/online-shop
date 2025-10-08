<?php

// Валидация данных
$errors = [];

// Проверка наличия полей
if (empty($_GET['name'])) {
    $errors[] = 'Поле имени обязательно для заполнения';
}

if (empty($_GET['email'])) {
    $errors[] = 'Поле email обязательно для заполнения';
} elseif (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный формат email';
}

if (empty($_GET['psw'])) {
    $errors[] = 'Поле пароля обязательно для заполнения';
} elseif (strlen($_GET['psw']) < 6) {
    $errors[] = 'Пароль должен содержать минимум 6 символов';
}

if ($_GET['psw'] !== $_GET['psw-repeat']) {
    $errors[] = 'Пароли не совпадают';
}

// Если есть ошибки - выводим и останавливаем выполнение
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

// Получение данных после валидации
$name = $_GET['name'];
$email = $_GET['email'];
$password = $_GET['psw'];

try {
    // Подключение к БД
    $pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Хеширование пароля перед сохранением
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Подготовленный запрос для вставки данных
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $passwordHash
    ]);

    // Получение ID вставленной записи
    $lastInsertId = $pdo->lastInsertId();

    // Вывод сохраненных данных
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $lastInsertId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Данные сохранены в БД:<br>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Имя: " . $user['name'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";

} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

