<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация / Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo"><a href="index.html">Полесье ДГ</a></div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.html">Главная</a></li>
                <li><a href="about.html">О нас</a></li>
                <li><a href="cart.php">Корзина</a></li>
                <li><a href="auth.php" class="auth-link">Вход</a></li>
            </ul>
        </nav>
    </header>

    <section class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <button id="login-btn" class="active">Войти</button>
                <button id="register-btn">Зарегистрироваться</button>
            </div>
            <form id="auth-form" class="auth-form" method="POST" action="">
                <input type="hidden" id="action" name="action" value="login">
                <div class="input-group">
                    <label for="email">Электронная почта</label>
                    <input type="email" id="email" name="email" placeholder="Введите email" required />
                </div>
                <div class="input-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" placeholder="Введите пароль" required />
                </div>
                <div class="action-buttons">
                    <button type="submit" class="submit-btn">Войти</button>
                </div>
                <p id="toggle-msg">Нет аккаунта? <a href="#" id="toggle-link">Зарегистрироваться</a></p>
            </form>
        </div>
    </section>

    <footer class="fixed-bottom">
        <p>&copy; 2024 Полесье ДГ. Все права защищены.</p>
    </footer>
    <script>
        // Скрипт для переключения между формами входа и регистрации
        const loginBtn = document.getElementById('login-btn');
        const registerBtn = document.getElementById('register-btn');
        const authForm = document.getElementById('auth-form');
        const actionInput = document.getElementById('action');
        const toggleLink = document.getElementById('toggle-link');

        registerBtn.addEventListener('click', () => {
            actionInput.value = 'register';
            authForm.querySelector('.submit-btn').innerText = 'Зарегистрироваться';
            loginBtn.classList.remove('active');
            registerBtn.classList.add('active');
        });

        loginBtn.addEventListener('click', () => {
            actionInput.value = 'login';
            authForm.querySelector('.submit-btn').innerText = 'Войти';
            registerBtn.classList.remove('active');
            loginBtn.classList.add('active');
        });

        toggleLink.addEventListener('click', (e) => {
            e.preventDefault();
            const isLogin = actionInput.value === 'login';
            actionInput.value = isLogin ? 'register' : 'login';
            authForm.querySelector('.submit-btn').innerText = isLogin ? 'Зарегистрироваться' : 'Войти';
            loginBtn.classList.toggle('active', !isLogin);
            registerBtn.classList.toggle('active', isLogin);
        });
    </script>
</body>
</html>

<?php
session_start();
$host = 'localhost';
$db = 'auth_system';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($action === 'register') {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Пользователь с таким email уже существует.');</script>";
        } else {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
            $stmt->execute([$email, $passwordHash]);
            echo "<script>alert('Регистрация успешна!');</script>";
        }
    } elseif ($action === 'login') {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            echo "<script>alert('Авторизация успешна!');</script>";
        } else {
            echo "<script>alert('Неверный email или пароль.');</script>";
        }
    }
}
?>
