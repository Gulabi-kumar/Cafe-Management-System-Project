<?php
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: manage_products.php");
    exit();
}

// Handle add/edit
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Handle image upload
    $image = $_POST['existing_image'] ?? 'default.jpg';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $new_filename);
            $image = $new_filename;
        }
    }
    
    if($id) {
        $stmt = $pdo->prepare("UPDATE menu_items SET name=?, description=?, price=?, image=?, category_id=?, is_available=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $image, $category_id, $is_available, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, image, category_id, is_available) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $image, $category_id, $is_available]);
    }
    header("Location: manage_products.php");
    exit();
}

$items = $pdo->query("SELECT m.*, c.name as category_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <h3>☕ CafeHub Admin</h3>
            <ul>
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="manage_products.php" class="active">🍔 Manage Products</a></li>
                <li><a href="manage_categories.php">📁 Manage Categories</a></li>
                <li><a href="orders.php">📦 Manage Orders</a></li>
                <li><a href="users.php">👥 Manage Users</a></li>
                <li><a href="contact_messages.php">💬 Contact Messages</a></li>
                <li><a href="reports.php">📈 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-main">
            <div class="admin-header">
                <h1>Manage Products</h1>
                <button onclick="openAddModal()" class="btn-primary">+ Add Product</button>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td>
                            <img src="../images/<?php echo $item['image']; ?>" width="50" height="50" style="object-fit:cover; border-radius:5px;" 
                                 onerror="this.src='https://via.placeholder.com/50'">
                        </td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['category_name']; ?></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <span class="status <?php echo $item['is_available'] ? 'available' : 'unavailable'; ?>">
                                <?php echo $item['is_available'] ? 'Available' : 'Unavailable'; ?>
                            </span>
                        </td>
                        <td>
                            <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($item)); ?>)" class="btn-sm">Edit</button>
                            <a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete this item?')" class="btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add Product</h2>
            <form method="POST" enctype="multipart/form-data" id="productForm">
                <input type="hidden" name="id" id="product_id">
                <input type="hidden" name="existing_image" id="existing_image">
                
                <label>Product Name *</label>
                <input type="text" name="name" id="product_name" required>
                
                <label>Description</label>
                <textarea name="description" id="product_description" rows="3"></textarea>
                
                <label>Price *</label>
                <input type="number" name="price" id="product_price" step="0.01" required>
                
                <label>Category *</label>
                <select name="category_id" id="product_category" required>
                    <option value="">Select Category</option>
                    <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*">
                <small id="imageHint" style="color: #666;"></small>
                
                <label style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="is_available" id="product_available" checked>
                    Available for sale
                </label>
                
                <button type="submit" class="btn-primary">Save Product</button>
            </form>
        </div>
    </div>
    
    <style>
        .status.available { background: #28a745; color: white; padding: 4px 8px; border-radius: 5px; font-size: 12px; }
        .status.unavailable { background: #dc3545; color: white; padding: 4px 8px; border-radius: 5px; font-size: 12px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        small { font-size: 12px; }
    </style>
    
    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.getElementById('productForm').reset();
            document.getElementById('product_id').value = '';
            document.getElementById('existing_image').value = '';
            document.getElementById('imageHint').textContent = '';
            document.getElementById('productModal').style.display = 'flex';
        }
        
        function editProduct(item) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('product_id').value = item.id;
            document.getElementById('product_name').value = item.name;
            document.getElementById('product_description').value = item.description;
            document.getElementById('product_price').value = item.price;
            document.getElementById('product_category').value = item.category_id;
            document.getElementById('product_available').checked = item.is_available == 1;
            document.getElementById('existing_image').value = item.image;
            document.getElementById('imageHint').textContent = 'Leave empty to keep current image';
            document.getElementById('productModal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == document.getElementById('productModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>