<?php
require_once 'config/database.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$order_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT o.*, u.name, u.email, u.phone, u.address FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if(!$order) {
    redirect('orders.php');
}

$stmt = $pdo->prepare("SELECT oi.*, m.name FROM order_items oi JOIN menu_items m ON oi.menu_item_id = m.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - CafeHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 40px; }
        .invoice-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .invoice-header { text-align: center; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .invoice-header h1 { color: #2c1810; }
        .invoice-details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .invoice-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .invoice-table th, .invoice-table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .invoice-table th { background: #2c1810; color: white; }
        .invoice-total { text-align: right; margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd; }
        .btn-print { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; }
        .btn-back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; margin-left: 10px; }
        @media print { .no-print { display: none; } body { padding: 0; background: white; } }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>☕ CafeHub</h1>
            <p>123 Coffee Street, Food City</p>
            <p>Email: orders@cafehub.com | Phone: +1 234 567 890</p>
            <h3>TAX INVOICE</h3>
        </div>
        
        <div class="invoice-details">
            <div>
                <strong>Bill To:</strong><br>
                <?php echo htmlspecialchars($order['name']); ?><br>
                <?php echo htmlspecialchars($order['email']); ?><br>
                <?php echo htmlspecialchars($order['phone']); ?><br>
                <?php echo htmlspecialchars($order['address']); ?>
            </div>
            <div>
                <strong>Order Details:</strong><br>
                Order #: <?php echo $order['order_number']; ?><br>
                Date: <?php echo date('F d, Y', strtotime($order['created_at'])); ?><br>
                Payment: <?php echo $order['payment_method']; ?><br>
                Status: <?php echo $order['status']; ?>
            </div>
        </div>
        
        <table class="invoice-table">
            <thead>
                <tr><th>Item</th><th>Quantity</th><th>Unit Price</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                    <td>₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="invoice-total">
            <p>Subtotal: ₹<?php echo number_format($order['total_amount'] + $order['discount'], 2); ?></p>
            <?php if($order['discount'] > 0): ?>
            <p>Discount (<?php echo $order['coupon_code']; ?>): -₹<?php echo number_format($order['discount'], 2); ?></p>
            <?php endif; ?>
            <h3>Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></h3>
        </div>
        
        <div class="no-print" style="text-align: center;">
            <button onclick="window.print()" class="btn-print">Print Invoice</button>
            <a href="orders.php" class="btn-back">Back to Orders</a>
        </div>
    </div>
</body>
</html>