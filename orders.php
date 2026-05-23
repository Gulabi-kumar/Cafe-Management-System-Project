<?php
require_once 'config/database.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$stmt = $pdo->prepare("SELECT o.*, 
    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
    FROM orders o WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$success = isset($_SESSION['order_success']) ? $_SESSION['order_success'] : '';
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - CafeHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>My Orders</h1>
        
        <?php if($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(empty($orders)): ?>
            <div class="empty-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="menu.php" class="btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <strong>Order #: <?php echo $order['order_number']; ?></strong>
                            <span class="order-date"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                        </div>
                        <span class="status status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span>
                    </div>
                    <div class="order-details">
                        <div>Items: <?php echo $order['item_count']; ?> products</div>
                        <div>Total: ₹<?php echo number_format($order['total_amount'], 2); ?></div>
                        <div>Payment: <?php echo $order['payment_method']; ?></div>
                    </div>
                    <div class="order-actions">
                        <a href="invoice.php?id=<?php echo $order['id']; ?>" class="btn-secondary">View Invoice</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>