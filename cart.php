<?php
require_once 'config/database.php';

// Update quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    header("Location: cart.php");
    exit();
}

// AJAX quantity update
if (isset($_GET['ajax_update'])) {
    $id = $_GET['id'];
    $qty = $_GET['qty'];
    if ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id] = $qty;
    }

    // Recalculate total
    $total = 0;
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $ids = implode(',', array_keys($_SESSION['cart']));
        $stmt = $pdo->query("SELECT * FROM menu_items WHERE id IN ($ids)");
        while ($item = $stmt->fetch()) {
            $total += $item['price'] * $_SESSION['cart'][$item['id']];
        }
    }

    $discount = isset($_SESSION['discount']) ? $_SESSION['discount'] : 0;
    echo json_encode(['success' => true, 'total' => $total, 'final' => $total - $discount]);
    exit();
}

// Remove item
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit();
}

// Calculate cart items
$cart_items = [];
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE id IN ($ids)");
    while ($item = $stmt->fetch()) {
        $item['quantity'] = $_SESSION['cart'][$item['id']];
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $total += $item['subtotal'];
        $cart_items[] = $item;
    }
}

// Apply coupon
$discount = 0;
$coupon_code = '';
$coupon_error = '';
if (isset($_POST['apply_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    if ($coupon_code == 'SAVE10') {
        $discount = $total * 0.10;
        $_SESSION['discount'] = $discount;
        $_SESSION['coupon'] = $coupon_code;
    } elseif ($coupon_code == 'WELCOME20') {
        $discount = $total * 0.20;
        $_SESSION['discount'] = $discount;
        $_SESSION['coupon'] = $coupon_code;
    } else {
        $coupon_error = "Invalid coupon code!";
    }
}
if (isset($_SESSION['discount'])) {
    $discount = $_SESSION['discount'];
    $coupon_code = $_SESSION['coupon'];
}
$final_total = $total - $discount;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - CafeHub</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .empty-cart {
            text-align: center;
            padding: 50px 20px;
            background: #fff;
            border-radius: 10px;
            margin: 30px 0;
        }

        .empty-cart p {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }

        .btn-primary {
            display: inline-block;
            background: #2c1810;
            color: #fff;
            padding: 10px 25px;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #ffd700;
            color: #2c1810;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Shopping Cart</h1>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Your cart is empty!</p>
                <a href="menu.php" class="btn-primary">Browse Menu</a>
            </div>
        <?php else: ?>
            <form method="POST" id="cartForm">
                <table class="cart-table" id="cartTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr id="row_<?php echo $item['id']; ?>">
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>₹<span class="price_<?php echo $item['id']; ?>"><?php echo number_format($item['price'], 2); ?></span></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $item['id']; ?>]"
                                        value="<?php echo $item['quantity']; ?>" min="0" class="qty-input"
                                        onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                                </td>
                                <td>₹<span class="subtotal_<?php echo $item['id']; ?>"><?php echo number_format($item['subtotal'], 2); ?></span></td>
                                <td><a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn-remove">Remove</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <?php if ($coupon_error): ?>
                    <div class="alert error"><?php echo $coupon_error; ?></div>
                <?php endif; ?>
                <div class="summary-row">Subtotal: <span id="subtotal">₹<?php echo number_format($total, 2); ?></span></div>

                <form method="POST" class="coupon-form">
                    <input type="text" name="coupon_code" placeholder="Coupon Code (SAVE10, WELCOME20)" value="<?php echo $coupon_code; ?>">
                    <button type="submit" name="apply_coupon" class="btn-secondary">Apply</button>
                </form>

                <?php if ($discount > 0): ?>
                    <div class="summary-row discount">Discount: <span id="discount">-₹<?php echo number_format($discount, 2); ?></span></div>
                <?php endif; ?>

                <div class="summary-row total">Total: <span id="finalTotal">₹<?php echo number_format($final_total, 2); ?></span></div>

                <a href="checkout.php" class="btn-primary btn-checkout">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function updateQuantity(id, qty) {
            $.ajax({
                url: 'cart.php?ajax_update=1&id=' + id + '&qty=' + qty,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update subtotal for this row
                        var price = parseFloat($('.price_' + id).text());
                        var newSubtotal = price * qty;
                        $('.subtotal_' + id).text(newSubtotal.toFixed(2));

                        // Update totals
                        $('#subtotal').text('₹' + response.total.toFixed(2));
                        $('#finalTotal').text('₹' + response.final.toFixed(2));

                        // Update cart count
                        var newCount = 0;
                        $('.qty-input').each(function() {
                            newCount += parseInt($(this).val()) || 0;
                        });
                        $('#cartCount').text(newCount);

                        if (qty == 0) {
                            $('#row_' + id).fadeOut();
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>