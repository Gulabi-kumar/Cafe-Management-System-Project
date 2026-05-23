<?php
require_once 'config/database.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

// Get cart items
$cart_items = [];
$total = 0;
if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE id IN ($ids)");
    while($item = $stmt->fetch()) {
        $item['quantity'] = $_SESSION['cart'][$item['id']];
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $total += $item['subtotal'];
        $cart_items[] = $item;
    }
}

if(empty($cart_items)) {
    redirect('cart.php');
}

$discount = isset($_SESSION['discount']) ? $_SESSION['discount'] : 0;
$final_total = $total - $discount;
$coupon_code = isset($_SESSION['coupon']) ? $_SESSION['coupon'] : '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    
    // Generate order number
    $order_number = 'ORD' . time() . rand(100, 999);
    
    try {
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total_amount, coupon_code, discount, shipping_address, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $order_number, $final_total, $coupon_code, $discount, $address, $payment_method]);
        $order_id = $pdo->lastInsertId();
        
        // Insert order items
        foreach($cart_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }
        
        $pdo->commit();
        
        // Clear cart
        unset($_SESSION['cart']);
        unset($_SESSION['discount']);
        unset($_SESSION['coupon']);
        
        $_SESSION['order_success'] = "Order placed successfully! Order #: $order_number";
        redirect('orders.php');
        
    } catch(Exception $e) {
        $pdo->rollBack();
        $error = "Order failed: " . $e->getMessage();
    }
}

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - CafeHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>    
    
    <div class="container">
        <div class="checkout-grid">
            <div class="checkout-form">
                <h2>Delivery Information</h2>
                <?php if(isset($error)): ?><div class="alert error"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST">
                    <label>Full Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                    
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    
                    <label>Delivery Address *</label>
                    <textarea name="address" required rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    
                    <label>Payment Method *</label>
                    <select name="payment_method" required>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Online Payment">Online Payment</option>
                    </select>
                    
                    <button type="submit" class="btn-primary">Place Order</button>
                </form>
            </div>
            
            <div class="order-summary">
                <h2>Your Order</h2>
                <?php foreach($cart_items as $item): ?>
                <div class="order-item">
                    <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                    <span><?php echo number_format($item['subtotal'], 2); ?></span>
                </div>
                <?php endforeach; ?>
                <div class="summary-line">Subtotal: ₹<?php echo number_format($total, 2); ?></div>
                <?php if($discount > 0): ?>
                <div class="summary-line discount">Discount: -₹<?php echo number_format($discount, 2); ?></div>
                <?php endif; ?>
                <div class="summary-line total">Total: ₹<?php echo number_format($final_total, 2); ?></div>
            </div>
        </div>
    </div>
</body>
</html>