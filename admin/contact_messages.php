<?php
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Update status
if(isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['message_id']]);
    header("Location: contact_messages.php");
    exit();
}

// Reply to message
if(isset($_POST['send_reply'])) {
    $stmt = $pdo->prepare("UPDATE contact_messages SET admin_reply = ?, status = 'replied' WHERE id = ?");
    $stmt->execute([$_POST['reply'], $_POST['message_id']]);
    header("Location: contact_messages.php");
    exit();
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$sql = "SELECT cm.*, u.name as user_name FROM contact_messages cm LEFT JOIN users u ON cm.user_id = u.id";
if($status_filter) {
    $sql .= " WHERE cm.status = ? ORDER BY cm.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status_filter]);
} else {
    $sql .= " ORDER BY cm.created_at DESC";
    $stmt = $pdo->query($sql);
}
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .message-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .message-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .status-unread { background: #dc3545; color: white; }
        .status-read { background: #28a745; color: white; }
        .status-replied { background: #17a2b8; color: white; }
        
        .reply-box {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: none;
        }
        
        .reply-box textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
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
                <li><a href="contact_messages.php" class="active">💬 Contact Messages</a></li>
                <li><a href="reports.php">📈 Reports</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </ul>
        </div>
        
        <div class="admin-main">
            <div class="admin-header">
                <h1>Contact Messages</h1>
            </div>
            
            <div class="filter-bar" style="margin-bottom: 20px;">
                <select id="statusFilter" onchange="filterMessages()">
                    <option value="">All Messages</option>
                    <option value="unread" <?php echo $status_filter == 'unread' ? 'selected' : ''; ?>>Unread</option>
                    <option value="read" <?php echo $status_filter == 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="replied" <?php echo $status_filter == 'replied' ? 'selected' : ''; ?>>Replied</option>
                </select>
            </div>
            
            <?php foreach($messages as $msg): ?>
            <div class="message-card">
                <div class="message-header">
                    <div>
                        <strong><?php echo htmlspecialchars($msg['name']); ?></strong> 
                        (<?php echo htmlspecialchars($msg['email']); ?>)
                        <?php if($msg['user_name']): ?>
                            <span style="color: #28a745;">✓ Registered User</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <span class="message-status status-<?php echo $msg['status']; ?>">
                            <?php echo ucfirst($msg['status']); ?>
                        </span>
                        <span style="margin-left: 10px; color: #666;">
                            <?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?>
                        </span>
                    </div>
                </div>
                
                <h4>Subject: <?php echo htmlspecialchars($msg['subject']); ?></h4>
                <p style="margin: 15px 0; background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                </p>
                
                <?php if($msg['admin_reply']): ?>
                <div style="background: #e8f5e9; padding: 10px; border-radius: 5px; margin-top: 10px;">
                    <strong>Your Reply:</strong><br>
                    <?php echo nl2br(htmlspecialchars($msg['admin_reply'])); ?>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 15px;">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <input type="hidden" name="status" value="read">
                        <button type="submit" name="update_status" class="btn-sm">Mark as Read</button>
                    </form>
                    
                    <button onclick="showReplyBox(<?php echo $msg['id']; ?>)" class="btn-sm">Reply</button>
                </div>
                
                <div id="replyBox_<?php echo $msg['id']; ?>" class="reply-box">
                    <form method="POST">
                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                        <textarea name="reply" rows="3" placeholder="Type your reply here..." required></textarea>
                        <button type="submit" name="send_reply" class="btn-primary">Send Reply</button>
                        <button type="button" onclick="hideReplyBox(<?php echo $msg['id']; ?>)" class="btn-secondary">Cancel</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        function filterMessages() {
            var status = document.getElementById('statusFilter').value;
            if(status) {
                window.location.href = '?status=' + status;
            } else {
                window.location.href = 'contact_messages.php';
            }
        }
        
        function showReplyBox(id) {
            document.getElementById('replyBox_' + id).style.display = 'block';
        }
        
        function hideReplyBox(id) {
            document.getElementById('replyBox_' + id).style.display = 'none';
        }
    </script>
</body>
</html>