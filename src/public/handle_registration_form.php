<?php
session_start();

require_once 'validation.php';

$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$errors = validateRegistration($_POST, $pdo);


if (empty($errors)) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['psw']);

    $hasPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password) RETURNING id");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $hasPassword
    ]);

    $newUserId = $stmt->fetchColumn();
    $_SESSION['user_id'] = $newUserId;

    echo "<h2>Регистрация успешна!</h2>";
    echo "<h3>Данные пользователя:</h3>";
    echo "<pre><strong>ID: " . htmlspecialchars($newUserId) . "</strong></pre>";
    echo "<pre><strong>Имя: " . htmlspecialchars($name) . "</strong></pre>";
    echo "<pre><strong>Email: " . htmlspecialchars($email) . "</strong></pre>";
    echo '<meta http-equiv="refresh" content="3;url=/catalog.php">';
    echo '<p>Вы будете перенаправлены в каталог через 3 секунды. <a href="/catalog.php">Нажмите здесь</a>, если не хотите ждать.</p>';

    exit;
}
?>

<form action="" method="POST">
    <div class="container">
        <h1>Register</h1>
        <p>Please fill in this form to create an account.</p>
        <hr>

        <!-- Поле Name -->
        <div class="form-group">
            <label for="name"><b>Name</b></label>
            <input type="text" placeholder="Enter Name" name="name" id="name"
                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($errors['name']); ?></div>
            <?php endif; ?>
        </div>

        <!-- Поле Email -->
        <div class="form-group">
            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Enter Email" name="email" id="email"
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($errors['email']); ?></div>
            <?php endif; ?>
        </div>

        <!-- Поле Password -->
        <div class="form-group">
            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" id="psw">
            <?php if (isset($errors['psw'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($errors['psw']); ?></div>
            <?php endif; ?>
        </div>

        <!-- Поле Repeat Password -->
        <div class="form-group">
            <label for="psw-repeat"><b>Repeat Password</b></label>
            <input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat">
            <?php if (isset($errors['psw-repeat'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($errors['psw-repeat']); ?></div>
            <?php endif; ?>
        </div>

        <hr>
        <p>By creating an account you agree to our <a href="#">Terms & Privacy</a>.</p>
        <button type="submit" class="registerbtn">Register</button>
    </div>

    <div class="container signin">
        <p>Already have an account? <a href="#">Sign in</a>.</p>
    </div>
</form>

<!-- ================== ЕДИНЫЕ СТИЛИ (без конфликтов) ================== -->
<style>
    * { box-sizing: border-box; }

    .container {
        padding: 16px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 12px;
        margin: 5px 0 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #f1f1f1;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
        background-color: #ddd;
        outline: none;
    }

    .error-message {
        color: red;
        font-size: 14px;
        margin-top: 5px;
    }

    hr {
        border: 1px solid #f1f1f1;
        margin-bottom: 25px;
    }

    .registerbtn {
        background-color: #04AA6D;
        color: white;
        padding: 16px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
        opacity: 0.9;
        font-size: 16px;
    }

    .registerbtn:hover {
        opacity: 1;
    }

    a {
        color: dodgerblue;
    }

    .signin {
        background-color: #f1f1f1;
        text-align: center;
    }
</style>