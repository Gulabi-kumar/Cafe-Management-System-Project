<?php
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

$user_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT o.*, 
    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
    FROM orders o WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

foreach($orders as &$order) {
    $order['created_at'] = date('M d, Y h:i A', strtotime($order['created_at']));
}

echo json_encode($orders);
?>