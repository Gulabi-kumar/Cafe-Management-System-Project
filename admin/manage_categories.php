<?php
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if(isset($_GET['delete'])) {
    // Check if category has items
    $check = $pdo->prepare("SELECT COUNT(*) FROM menu_items WHERE category_id = ?");
    $check->execute([$_GET['delete']]);
    if($check->fetchColumn() > 0) {
        $error = "Cannot delete category with existing products!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
    }
    header("Location: manage_categories.php");
    exit();
}

// Handle add/edit
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'];
    
    if($id) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
    }
    header("Location: manage_categories.php");
    exit();
}

$categories = $pdo->query("SELECT c.*, COUNT(m.id) as item_count FROM categories c LEFT JOIN menu_items m ON c.id = m.category_id GROUP BY c.id ORDER BY c.name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>☕ CafeHub Admin</h3>
            <ul>
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="manage_products.php">🍔 Manage Products</a></li>
                <li><a href="manage_categories.php" class="active">📁 Manage Categories</a></li>
                <li><a href="orders.php">📦 Manage Orders</a></li>
                <li><a href="users.php">👥 Manage Users</a></li>
                <li><a href="contact_messages.php">💬 Contact Messages</a></li>
                <li><a href="reports.php">📈 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-main">
            <div class="admin-header">
                <h1>Manage Categories</h1>
                <button onclick="openAddModal()" class="btn-primary">+ Add Category</button>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Products Count</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo $cat['item_count']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($cat['created_at'])); ?></td>
                        <td>
                            <button onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)" class="btn-sm">Edit</button>
                            <a href="?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Delete this category?')" class="btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Category</h2>
            <form method="POST" id="categoryForm">
                <input type="hidden" name="id" id="category_id">
                <label>Category Name *</label>
                <input type="text" name="name" id="category_name" required placeholder="e.g., Beverages, Fast Food">
                <button type="submit" class="btn-primary">Save Category</button>
            </form>
        </div>
    </div>
    
    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Category';
            document.getElementById('categoryForm').reset();
            document.getElementById('category_id').value = '';
            document.getElementById('categoryModal').style.display = 'flex';
        }
        
        function editCategory(cat) {
            document.getElementById('modalTitle').textContent = 'Edit Category';
            document.getElementById('category_id').value = cat.id;
            document.getElementById('category_name').value = cat.name;
            document.getElementById('categoryModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('categoryModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == document.getElementById('categoryModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>