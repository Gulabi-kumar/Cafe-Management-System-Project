<?php
require_once 'config/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT m.*, c.name as category_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id WHERE m.is_available = 1";
$params = [];

if($search) {
    $sql .= " AND (m.name LIKE ? OR m.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if($category) {
    $sql .= " AND c.name = ?";
    $params[] = $category;
}

$sql .= " ORDER BY m.name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

// Get categories for filter
$categories = $pdo->query("SELECT name FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - CafeHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="menu-header">
            <h1>Our Menu</h1>
            <div class="menu-filters">
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search items..." value="<?php echo htmlspecialchars($search); ?>">
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['name']; ?>" <?php echo $category == $cat['name'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-secondary">Filter</button>
                </form>
            </div>
        </div>
        
        <div class="menu-grid">
            <?php foreach($items as $item): ?>
            <div class="menu-card">
                <img src="images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="menu-img" onerror="this.src='https://via.placeholder.com/300x200?text=Food'">
                <div class="menu-info">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p class="category"><?php echo $item['category_name']; ?></p>
                    <p class="description"><?php echo htmlspecialchars(substr($item['description'], 0, 80)); ?>...</p>
                    <p class="price">₹<?php echo number_format($item['price'], 2); ?></p>
                    <a href="add_to_cart.php?id=<?php echo $item['id']; ?>" class="btn-add">Add to Cart</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>