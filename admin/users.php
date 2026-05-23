<?php
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Delete user
if(isset($_GET['delete'])) {
    // Check if user has orders
    $check = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
    $check->execute([$_GET['delete']]);
    if($check->fetchColumn() > 0) {
        $error = "Cannot delete user with existing orders!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
    }
    header("Location: users.php");
    exit();
}

// Search users
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT u.*, COUNT(o.id) as order_count, SUM(o.total_amount) as total_spent 
        FROM users u 
        LEFT JOIN orders o ON u.id = o.user_id 
        WHERE 1=1";
if($search) {
    $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
}
$sql .= " GROUP BY u.id ORDER BY u.created_at DESC";

if($search) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%", "%$search%"]);
    $users = $stmt->fetchAll();
} else {
    $users = $pdo->query($sql)->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>☕ CafeHub Admin</h3>
            <ul>
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="manage_products.php">🍔 Manage Products</a></li>
                <li><a href="manage_categories.php">📁 Manage Categories</a></li>
                <li><a href="orders.php">📦 Manage Orders</a></li>
                <li><a href="users.php" class="active">👥 Manage Users</a></li>
                <li><a href="contact_messages.php">💬 Contact Messages</a></li>
                <li><a href="reports.php">📈 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-main">
            <div class="admin-header">
                <h1>Manage Users</h1>
            </div>
            
            <div class="filter-bar">
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; width: 300px;">
                    <button type="submit" class="btn-secondary">Search</button>
                    <?php if($search): ?>
                        <a href="users.php" class="btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone'] ?: 'N/A'); ?></td>
                        <td><?php echo $user['order_count']; ?></td>
                        <td>₹<?php echo number_format($user['total_spent'] ?: 0, 2); ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <button onclick="viewUserOrders(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>')" class="btn-sm">View Orders</button>
                            <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('Delete this user? All their data will be removed.')" class="btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- User Orders Modal -->
    <div id="userOrdersModal" class="modal" style="display: none;">
        <div class="modal-content" style="width: 800px;">
            <span class="close" onclick="closeUserOrders()">&times;</span>
            <h2 id="userOrdersTitle">User Orders</h2>
            <div id="userOrdersList"></div>
        </div>
    </div>
    
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: white; padding: 20px; border-radius: 10px; max-height: 80%; overflow-y: auto; }
        .close { float: right; font-size: 28px; cursor: pointer; }
        .user-order-item { border: 1px solid #ddd; margin: 10px 0; padding: 10px; border-radius: 5px; }
    </style>
    
    <script>
        function viewUserOrders(userId, userName) {
            document.getElementById('userOrdersTitle').textContent = 'Orders: ' + userName;
            fetch('get_user_orders.php?id=' + userId)
                .then(response => response.json())
                .then(data => {
                    var html = '';
                    if(data.length === 0) {
                        html = '<p>No orders found.</p>';
                    } else {
                        for(var order of data) {
                            html += '<div class="user-order-item">';
                            html += '<strong>Order #:</strong> ' + order.order_number + '<br>';
                            html += '<strong>Date:</strong> ' + order.created_at + '<br>';
                            html += '<strong>Total:</strong> $' + parseFloat(order.total_amount).toFixed(2) + '<br>';
                            html += '<strong>Status:</strong> ' + order.status + '<br>';
                            html += '<strong>Items:</strong> ' + order.item_count + '<br>';
                            html += '</div>';
                        }
                    }
                    document.getElementById('userOrdersList').innerHTML = html;
                    document.getElementById('userOrdersModal').style.display = 'flex';
                });
        }
        
        function closeUserOrders() {
            document.getElementById('userOrdersModal').style.display = 'none';
        }
    </script>
</body>
</html>