<?php
session_start();

// Если не авторизован — на страницу входа
if (!isset($_SESSION['user_id'])) {
    header('Location: /login-form.php');
    exit;
}

$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Если пользователь не найден — что-то странное, разлогиниваем
if (!$user) {
    unset($_SESSION['user_id']);
    header('Location: /login-form.php');
    exit;
}

// Сообщение об успехе (может передаваться из обработчика через сессию)
$successMessage = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']); // удаляем, чтобы не висело
?>

<h1>Мой профиль</h1>
<?php if ($successMessage): ?>
    <p style="color: green;"><?= htmlspecialchars($successMessage) ?></p>
<?php endif; ?>
<p>Имя: <?= htmlspecialchars($user['name']) ?></p>
<p>Email: <?= htmlspecialchars($user['email']) ?></p>
<a href="edit_profile.php">Редактировать профиль</a>
<br>
<a href="/catalog.php">Вернуться в каталог</a>