<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login-form.php');
    exit;
}

$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Получаем текущие данные для заполнения формы
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    unset($_SESSION['user_id']);
    header('Location: /login-form.php');
    exit;
}

// Достаём ошибки, которые мог передать обработчик
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

// Старые значения полей (чтобы не стирать при ошибке)
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<h1>Редактирование профиля</h1>

<?php if (!empty($errors)): ?>
    <div style="color: red;">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form action="handle_edit_profile.php" method="POST">
    <label>Имя:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? $user['name']) ?>"><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? $user['email']) ?>"><br>

    <h3>Сменить пароль (необязательно)</h3>
    <label>Старый пароль:</label>
    <input type="password" name="old_password"><br>
    <label>Новый пароль:</label>
    <input type="password" name="new_password"><br>
    <label>Повторите новый пароль:</label>
    <input type="password" name="new_password_repeat"><br>

    <button type="submit">Сохранить изменения</button>
</form>