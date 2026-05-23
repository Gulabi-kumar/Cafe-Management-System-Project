<?php
require_once 'config/database.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current user password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    // Validate
    if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif(!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect!";
    } elseif($new_password != $confirm_password) {
        $error = "New passwords do not match!";
    } elseif(strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
            $success = "Password changed successfully!";
            // Clear form
            $_POST = [];
        } else {
            $error = "Failed to change password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - CafeHub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .password-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 0 20px;
        }
        
        .password-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .password-card h2 {
            color: #2c1810;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .password-card .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .password-input-wrapper {
            position: relative;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 5px rgba(255,215,0,0.3);
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
        
        .password-strength {
            margin-top: 10px;
            height: 5px;
            background: #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: 0.3s;
        }
        
        .strength-text {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }
        
        .btn-primary {
            background: #2c1810;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: 0.3s;
        }
        
        .btn-primary:hover {
            background: #ffd700;
            color: #2c1810;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .password-requirements {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .password-requirements h4 {
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .password-requirements li {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .password-requirements li.valid {
            color: #28a745;
        }
        
        .password-requirements li.invalid {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="password-container">
        <div class="password-card">
            <h2><i class="fas fa-key"></i> Change Password</h2>
            <p class="subtitle">Secure your account with a strong password</p>
            
            <?php if($success): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="passwordForm">
                <div class="form-group">
                    <label>Current Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="current_password" id="current_password" required>
                        <span class="toggle-password" onclick="togglePassword('current_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="new_password" id="new_password" required onkeyup="checkPasswordStrength()">
                        <span class="toggle-password" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" required onkeyup="checkPasswordMatch()">
                        <span class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div id="matchMessage" style="font-size: 12px; margin-top: 5px;"></div>
                </div>
                
                <div class="password-requirements">
                    <h4>Password Requirements:</h4>
                    <ul>
                        <li id="req-length">✓ At least 6 characters</li>
                        <li id="req-number">✓ At least one number</li>
                        <li id="req-letter">✓ At least one letter</li>
                    </ul>
                </div>
                
                <button type="submit" class="btn-primary">Update Password</button>
                <a href="profile.php" class="btn-secondary">Back to Profile</a>
            </form>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        function checkPasswordStrength() {
            const password = document.getElementById('new_password').value;
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            
            // Check requirements
            const hasLength = password.length >= 6;
            const hasNumber = /[0-9]/.test(password);
            const hasLetter = /[a-zA-Z]/.test(password);
            
            // Update requirement list
            document.getElementById('req-length').style.color = hasLength ? '#28a745' : '#dc3545';
            document.getElementById('req-number').style.color = hasNumber ? '#28a745' : '#dc3545';
            document.getElementById('req-letter').style.color = hasLetter ? '#28a745' : '#dc3545';
            
            let strength = 0;
            if (hasLength) strength++;
            if (hasNumber) strength++;
            if (hasLetter) strength++;
            
            let width = 0;
            let color = '';
            let strengthText = '';
            
            switch(strength) {
                case 0:
                    width = 0;
                    color = '#dc3545';
                    strengthText = 'Very Weak';
                    break;
                case 1:
                    width = 33;
                    color = '#dc3545';
                    strengthText = 'Weak';
                    break;
                case 2:
                    width = 66;
                    color = '#ffc107';
                    strengthText = 'Medium';
                    break;
                case 3:
                    width = 100;
                    color = '#28a745';
                    strengthText = 'Strong';
                    break;
            }
            
            bar.style.width = width + '%';
            bar.style.background = color;
            text.textContent = strengthText;
            text.style.color = color;
            
            checkPasswordMatch();
        }
        
        function checkPasswordMatch() {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            const message = document.getElementById('matchMessage');
            
            if (confirmPass.length > 0) {
                if (newPass === confirmPass) {
                    message.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match!';
                    message.style.color = '#28a745';
                } else {
                    message.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match!';
                    message.style.color = '#dc3545';
                }
            } else {
                message.innerHTML = '';
            }
        }
    </script>
</body>
</html>