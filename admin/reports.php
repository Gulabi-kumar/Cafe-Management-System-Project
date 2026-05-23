<?php
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get date range for filtering
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Sales by period
$stmt = $pdo->prepare("SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue 
                        FROM orders 
                        WHERE created_at BETWEEN ? AND ? AND status != 'Cancelled'
                        GROUP BY DATE(created_at) 
                        ORDER BY date DESC");
$stmt->execute([$start_date, $end_date . ' 23:59:59']);
$daily_sales = $stmt->fetchAll();

// Top selling products
$stmt = $pdo->prepare("SELECT m.name, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue
                        FROM order_items oi
                        JOIN menu_items m ON oi.menu_item_id = m.id
                        JOIN orders o ON oi.order_id = o.id
                        WHERE o.created_at BETWEEN ? AND ? AND o.status != 'Cancelled'
                        GROUP BY m.id
                        ORDER BY total_sold DESC
                        LIMIT 10");
$stmt->execute([$start_date, $end_date . ' 23:59:59']);
$top_products = $stmt->fetchAll();

// Category sales
$stmt = $pdo->prepare("SELECT c.name, SUM(oi.quantity) as items_sold, SUM(oi.quantity * oi.price) as revenue
                        FROM order_items oi
                        JOIN menu_items m ON oi.menu_item_id = m.id
                        JOIN categories c ON m.category_id = c.id
                        JOIN orders o ON oi.order_id = o.id
                        WHERE o.created_at BETWEEN ? AND ? AND o.status != 'Cancelled'
                        GROUP BY c.id
                        ORDER BY revenue DESC");
$stmt->execute([$start_date, $end_date . ' 23:59:59']);
$category_sales = $stmt->fetchAll();

// Summary stats
$stmt = $pdo->prepare("SELECT 
                        COUNT(*) as total_orders,
                        SUM(total_amount) as total_revenue,
                        AVG(total_amount) as avg_order_value,
                        COUNT(DISTINCT user_id) as unique_customers
                        FROM orders 
                        WHERE created_at BETWEEN ? AND ? AND status != 'Cancelled'");
$stmt->execute([$start_date, $end_date . ' 23:59:59']);
$summary = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .report-header { margin-bottom: 30px; }
        .date-filter { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .date-filter input { padding: 8px; margin: 0 10px; border: 1px solid #ddd; border-radius: 5px; }
        .report-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .report-card { background: white; padding: 20px; border-radius: 10px; text-align: center; }
        .report-card h3 { color: #666; margin-bottom: 10px; }
        .report-card .value { font-size: 28px; font-weight: bold; color: #2c1810; }
        .report-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .report-section h2 { margin-bottom: 20px; color: #2c1810; }
        .chart-container { margin: 20px 0; }
        .bar { background: #ffd700; height: 30px; border-radius: 5px; margin: 5px 0; }
        .bar-label { display: flex; justify-content: space-between; margin: 5px 0; }
        @media (max-width: 768px) { .report-grid { grid-template-columns: repeat(2, 1fr); } }
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
                <li><a href="orders.php">📦 Manage Orders</a></li>
                <li><a href="users.php">👥 Manage Users</a></li>
                <li><a href="contact_messages.php">💬 Contact Messages</a></li>
                <li><a href="reports.php" class="active">📈 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-main">
            <div class="admin-header">
                <h1>Sales Reports</h1>
            </div>
            
            <div class="date-filter">
                <form method="GET">
                    <label>From:</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                    <label>To:</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                    <button type="submit" class="btn-primary">Generate Report</button>
                </form>
            </div>
            
            <div class="report-grid">
                <div class="report-card">
                    <h3>Total Revenue</h3>
                    <div class="value">₹<?php echo number_format($summary['total_revenue'] ?: 0, 2); ?></div>
                </div>
                <div class="report-card">
                    <h3>Total Orders</h3>
                    <div class="value"><?php echo $summary['total_orders'] ?: 0; ?></div>
                </div>
                <div class="report-card">
                    <h3>Avg Order Value</h3>
                    <div class="value">₹<?php echo number_format($summary['avg_order_value'] ?: 0, 2); ?></div>
                </div>
                <div class="report-card">
                    <h3>Unique Customers</h3>
                    <div class="value"><?php echo $summary['unique_customers'] ?: 0; ?></div>
                </div>
            </div>
            
            <div class="report-section">
                <h2>Top Selling Products</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>Product</th><th>Quantity Sold</th><th>Revenue</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($top_products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo $product['total_sold']; ?> units</td>
                            <td>₹<?php echo number_format($product['revenue'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="report-section">
                <h2>Sales by Category</h2>
                <div class="chart-container">
                    <?php 
                    $max_revenue = !empty($category_sales) ? max(array_column($category_sales, 'revenue')) : 1;
                    foreach($category_sales as $cat): 
                        $percentage = ($cat['revenue'] / $max_revenue) * 100;
                    ?>
                    <div class="bar-label">
                        <span><?php echo htmlspecialchars($cat['name']); ?></span>
                        <span>₹<?php echo number_format($cat['revenue'], 2); ?> (<?php echo $cat['items_sold']; ?> items)</span>
                    </div>
                    <div class="bar" style="width: <?php echo $percentage; ?>%;"></div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="report-section">
                <h2>Daily Sales</h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>Date</th><th>Orders</th><th>Revenue</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($daily_sales as $day): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                            <td><?php echo $day['orders']; ?></td>
                            <td>₹<?php echo number_format($day['revenue'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>