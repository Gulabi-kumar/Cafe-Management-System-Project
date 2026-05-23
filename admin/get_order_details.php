<?php
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

$order_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

$stmt = $pdo->prepare("SELECT oi.*, m.name FROM order_items oi JOIN menu_items m ON oi.menu_item_id = m.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

echo json_encode([
    'order_number' => $order['order_number'],
    'customer_name' => $order['customer_name'],
    'shipping_address' => $order['shipping_address'],
    'payment_method' => $order['payment_method'],
    'total_amount' => $order['total_amount'],
    'created_at' => date('M d, Y h:i A', strtotime($order['created_at'])),
    'items' => $items
]);
?>