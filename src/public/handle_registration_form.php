<?php

$pdo = new PDO('pgsql:host=postgres_db;port=5432;dbname=mydb', 'user', 'pass');

$errors = [];

// Валидация имени
if (isset($_POST['name'])) {
    $name = $_POST['name'];

    if (strlen($name) < 2) {
        $errors['name'] = 'Имя должно содержать не менее двух символов';
    }
    } else {
    $errors['name'] = 'Укажите имя';
}


// Валидация email
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    if (strlen($email) < 3) {
        $errors['email'] = 'email должен содержать не менее трёх символов';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'email некорректный';
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $checkStmt->execute([':email' => $email]);
        if ($checkStmt->fetch()) {
            $errors['email'] = 'Пользователь с таким email уже существует';
        }
    }
} else {
    $errors['email'] = 'Укажите email';
}

// Валидация пароля
if (isset($_POST['psw'])) {
    $password = $_POST['psw'];
    if (strlen($password) < 6) {
        $errors['psw'] = 'Пароль должен содержать не менее шести символов';
    }
    } else  {
    $errors['psw'] = 'Придумайте пароль';
}

if (isset($_POST['psw-repeat'])) {
    $passwordRep = $_POST['psw-repeat'];
    } else {
    $errors['psw-repeat'] = 'Повторите пароль';
}

if (isset($_POST['psw']) && isset($_POST['psw-repeat'])) {
    $password = $_POST['psw'];
    $passwordRep = $_POST['psw-repeat'];

    if ($password !== $passwordRep) {
        $errors['psw_match'] = 'Пароли не совпадают';
    }
}
// Если ошибок нет - регистрируем пользователя
if (empty($errors)) {
    $hasPassword = password_hash($password, PASSWORD_DEFAULT);

    // Вставка пользователя
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hasPassword]);

    $newUserId = $pdo->lastInsertId();

    // Получение данных нового пользователя
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = :id");
    $stmt->execute([':id' => $newUserId]);
    $newUser = $stmt->fetch();

    if ($newUser) {
        echo "<h2>Регистрация успешна!</h2>";
        echo "<h3>Данные пользователя:</h3>";
        echo "<pre><strong>ID: " . htmlspecialchars($newUser['id']) . "</strong></pre>";
        echo "<pre><strong>Имя: " . htmlspecialchars($newUser['name']) . "</strong></pre>";
        echo "<pre><strong>Email: " . htmlspecialchars($newUser['email']) . "</strong></pre>";
    }
}
?>

<form action="handle_registration_form.php" method="POST">
    <div class="container">
        <h1>Register</h1>
        <p>Please fill in this form to create an account.</p>
        <hr>

        <!-- Группа поля Name -->
        <div class="form-group">
            <label for="name"><b>Name</b></label>
            <input type="text" placeholder="Enter Name" name="name" id="name"
                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="error-message"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>

        <!-- Группа поля Email -->
        <div class="form-group">
            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Enter Email" name="email" id="email"
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="error-message"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <!-- Группа поля Password -->
        <div class="form-group">
            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" id="psw">
            <?php if (isset($errors['psw'])): ?>
                <div class="error-message"><?php echo $errors['psw']; ?></div>
            <?php endif; ?>
        </div>

        <!-- Группа поля Repeat Password -->
        <div class="form-group">
            <label for="psw-repeat"><b>Repeat Password</b></label>
            <input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat">
            <?php if (isset($errors['psw-repeat'])): ?>
                <div class="error-message"><?php echo $errors['psw-repeat']; ?></div>
            <?php endif; ?>
            <?php if (isset($errors['psw-match'])): ?>
                <div class="error-message"><?php echo $errors['psw-match']; ?></div>
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

<style>
    .form-group {
        margin-bottom: 20px;
    }

    .error-message {
        color: red;
        margin-top: 5px;
        font-size: 14px;
        padding: 3px 0;
    }

    label {
        display: block;
        margin-bottom: 8px;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
</style>



<style>
    * {box-sizing: border-box}

    /* Add padding to containers */
    .container {
    padding: 16px;
    }

    /* Full-width input fields */
    input[type=text], input[type=password] {
width: 100%;
        padding: 15px;
        margin: 5px 0 22px 0;
        display: inline-block;
        border: none;
        background: #f1f1f1;
    }

    input[type=text]:focus, input[type=password]:focus {
    background-color: #ddd;
        outline: none;
    }

    /* Overwrite default styles of hr */
    hr {
    border: 1px solid #f1f1f1;
        margin-bottom: 25px;
    }

    /* Set a style for the submit/register button */
    .registerbtn {
    background-color: #04AA6D;
        color: white;
        padding: 16px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
        opacity: 0.9;
    }

    .registerbtn:hover {
    opacity:1;
}

    /* Add a blue text color to links */
    a {
    color: dodgerblue;
}

    /* Set a grey background color and center the text of the "sign in" section */
    .signin {
    background-color: #f1f1f1;
        text-align: center;
    }
</style>