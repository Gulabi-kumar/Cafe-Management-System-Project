<?php
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Update order status
if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    header("Location: orders.php");
    exit();
}

// Filter orders
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$sql = "SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id";
if($status_filter) {
    $sql .= " WHERE o.status = ?";
    $orders = $pdo->prepare($sql);
    $orders->execute([$status_filter]);
} else {
    $orders = $pdo->query($sql . " ORDER BY o.created_at DESC");
}
if(!$status_filter) {
    $orders = $pdo->query($sql . " ORDER BY o.created_at DESC");
} else {
    $stmt = $pdo->prepare($sql . " AND o.status = ? ORDER BY o.created_at DESC");
    $stmt->execute([$status_filter]);
    $orders = $stmt;
}
$orders = $orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .filter-bar { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }
        .filter-bar select, .filter-bar button { padding: 8px 15px; border-radius: 5px; }
        .order-details-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .order-details-content { background: white; padding: 30px; border-radius: 10px; width: 600px; max-width: 90%; max-height: 80%; overflow-y: auto; }
        .order-item-list { margin: 15px 0; }
        .order-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>☕ CafeHub Admin</h3>
            <ul>
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="manage_products.php">🍔 Manage Products</a></li>
                <li><a href="manage_categories.php">📁 Manage Categories</a></li>
                <li><a href="orders.php" class="active">📦 Manage Orders</a></li>
                <li><a href="users.php">👥 Manage Users</a></li>
                <li><a href="contact_messages.php">💬 Contact Messages</a></li>
                <li><a href="reports.php">📈 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-main">
            <div class="admin-header">
                <h1>Manage Orders</h1>
            </div>
            
            <div class="filter-bar">
                <label>Filter by Status:</label>
                <select id="statusFilter" onchange="filterOrders()">
                    <option value="">All Orders</option>
                    <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Preparing" <?php echo $status_filter == 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                    <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button onclick="filterOrders()" class="btn-secondary">Apply</button>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_number']; ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" onchange="this.form.submit()" class="status-select">
                                    <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Preparing" <?php echo $order['status'] == 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                    <option value="Completed" <?php echo $order['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="submit" name="update_status" value="Update" style="display: none;">
                            </form>
                        </td>
                        <td><?php echo $order['payment_method']; ?></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                        <td>
                            <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)" class="btn-sm">View Details</button>
                            <a href="../invoice.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn-sm">Invoice</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="order-details-modal">
        <div class="order-details-content">
            <span style="float: right; font-size: 28px; cursor: pointer;" onclick="closeOrderDetails()">&times;</span>
            <h2>Order Details</h2>
            <div id="orderDetails"></div>
        </div>
    </div>
    
    <script>
        function filterOrders() {
            var status = document.getElementById('statusFilter').value;
            if(status) {
                window.location.href = '?status=' + status;
            } else {
                window.location.href = 'orders.php';
            }
        }
        
        function viewOrderDetails(orderId) {
            fetch('get_order_details.php?id=' + orderId)
                .then(response => response.json())
                .then(data => {
                    var html = '<p><strong>Order #:</strong> ' + data.order_number + '</p>';
                    html += '<p><strong>Customer:</strong> ' + data.customer_name + '</p>';
                    html += '<p><strong>Address:</strong> ' + data.shipping_address + '</p>';
                    html += '<p><strong>Payment:</strong> ' + data.payment_method + '</p>';
                    html += '<p><strong>Date:</strong> ' + data.created_at + '</p>';
                    html += '<h3>Items:</h3><div class="order-item-list">';
                    for(var item of data.items) {
                        html += '<div class="order-item"><span>' + item.name + ' x ' + item.quantity + '</span><span>$' + parseFloat(item.price * item.quantity).toFixed(2) + '</span></div>';
                    }
                    html += '</div><p><strong>Total:</strong> $' + parseFloat(data.total_amount).toFixed(2) + '</p>';
                    document.getElementById('orderDetails').innerHTML = html;
                    document.getElementById('orderDetailsModal').style.display = 'flex';
                });
        }
        
        function closeOrderDetails() {
            document.getElementById('orderDetailsModal').style.display = 'none';
        }
    </script>
</body>
</html>