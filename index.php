<?php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CafeHub - Best Coffee & Food</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    .category{
        color: red;
    }
</style>
<body>
    <?php include 'navbar.php'; ?>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to CafeHub</h1>
            <p>Experience the finest coffee and delicious meals in town</p>
            <a href="menu.php" class="btn-hero">Order Now</a>
        </div>
    </section>
    
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose Us?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">☕</div>
                    <h3>Premium Coffee</h3>
                    <p>Made from freshly roasted beans</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🍔</div>
                    <h3>Delicious Food</h3>
                    <p>Prepared by expert chefs</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3>Fast Delivery</h3>
                    <p>30 minutes or free</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="featured-items">
        <div class="container">
            <h2 class="section-title">Popular Items</h2>
            <div class="menu-grid">
                <?php
                $stmt = $pdo->query("SELECT m.*, c.name as category_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id WHERE m.is_available = 1 LIMIT 12");
                while($item = $stmt->fetch()):
                ?>
                <div class="menu-card">
                    <img src="images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="menu-img" onerror="this.src='https://via.placeholder.com/300x200?text=Food'">
                    <div class="menu-info">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="category"><?php echo $item['category_name']; ?></p>
                        <p class="price">₹<?php echo number_format($item['price'], 2); ?></p>
                        <a href="add_to_cart.php?id=<?php echo $item['id']; ?>" class="btn-add">Add to Cart</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>