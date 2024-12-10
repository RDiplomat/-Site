<?php
session_start();
require 'db_connection.php'; // файл для подключения к базе данных

// Проверка, если корзина пуста
if (empty($_SESSION['cart'])) {
    die("Корзина пуста. Невозможно оформить заказ.");
}

// Получение данных формы
$customerName = $_POST['name'] ?? '';
$customerEmail = $_POST['email'] ?? '';

// Проверка данных
if (!$customerName || !$customerEmail) {
    die("Не все данные заполнены.");
}

// Начало транзакции
mysqli_begin_transaction($conn);

try {
    // Вставка заказа в таблицу orders
    $totalPrice = array_reduce($_SESSION['cart'], function($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);
    
    $sqlOrder = "INSERT INTO orders (customer_name, customer_email, total_price) 
                 VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sqlOrder);
    $stmt->bind_param('ssd', $customerName, $customerEmail, $totalPrice);
    $stmt->execute();
    $orderId = $stmt->insert_id; // Получаем ID нового заказа

    // Вставка товаров в таблицу order_items
    $sqlItem = "INSERT INTO order_items (order_id, product_name, product_price, quantity) 
                VALUES (?, ?, ?, ?)";
    $stmtItem = $conn->prepare($sqlItem);

    foreach ($_SESSION['cart'] as $item) {
        $stmtItem->bind_param('isdi', $orderId, $item['name'], $item['price'], $item['quantity']);
        $stmtItem->execute();
    }

    // Подтверждение транзакции
    mysqli_commit($conn);

    // Очистка корзины после оформления заказа
    unset($_SESSION['cart']);

    echo "Заказ успешно оформлен!";
} catch (Exception $e) {
    // Отмена транзакции в случае ошибки
    mysqli_roll_back($conn);
    echo "Ошибка при оформлении заказа: " . $e->getMessage();
}
?>
