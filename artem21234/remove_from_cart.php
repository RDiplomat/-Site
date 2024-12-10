<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemIndex = $_POST['item_index'] ?? null;

    if ($itemIndex !== null && isset($_SESSION['cart'][$itemIndex])) {
        unset($_SESSION['cart'][$itemIndex]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Переподключение индексов
    }
}

header('Location: cart.php');
exit;
?>
