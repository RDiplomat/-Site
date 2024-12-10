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

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Необходимо авторизоваться']);
    exit;
}

$userId = $_SESSION['user_id'];
$productId = $_POST['product_id'] ?? null;
$productName = $_POST['product_name'] ?? null;
$productPrice = $_POST['product_price'] ?? null;

// Валидация данных
if (!$productId || !$productName || !$productPrice || !is_numeric($productPrice)) {
    echo json_encode(['status' => 'error', 'message' => 'Неверные данные товара']);
    exit;
}

// Проверяем, существует ли товар в корзине
$stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $productId]);
$cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cartItem) {
    // Обновляем количество, если товар уже есть в корзине
    $newQuantity = $cartItem['quantity'] + 1;
    $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $updateStmt->execute([$newQuantity, $cartItem['id']]);
} else {
    // Добавляем новый товар в корзину
    $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insertStmt->execute([$userId, $productId, 1]);
}

echo json_encode(['status' => 'success', 'message' => 'Товар добавлен в корзину']);
?>
