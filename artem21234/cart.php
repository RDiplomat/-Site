<?php
session_start();
$host = 'localhost';
$db = 'shop';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

if (!isset($_SESSION['user_id'])) {
    die("Необходимо авторизоваться, чтобы просмотреть корзину.");
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT 
        cart.id AS cart_id,
        products.name,
        products.price,
        cart.quantity,
        (products.price * cart.quantity) AS total_price
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo"><a href="index.html">Полесье ДГ</a></div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.html">Главная</a></li>
                <li><a href="about.html">О нас</a></li>
                <li><a href="cart.php" class="active">Корзина</a></li>
                <li><a href="auth.php" class="auth-link">Вход</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="cart">
            <h1>Ваша корзина</h1>
            <?php if (empty($cartItems)): ?>
                <p>Ваша корзина пуста.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Итого</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= number_format($item['price'], 2, '.', '') ?> ₽</td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['total_price'], 2, '.', '') ?> ₽</td>
                                <td>
                                    <form action="remove_from_cart.php" method="POST">
                                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                        <button type="submit">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer class="fixed-bottom">
        <p>&copy; 2024 Полесье ДГ. Все права защищены.</p>
    </footer>
</body>
</html>
