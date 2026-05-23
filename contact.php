<?php
require_once 'config/database.php';

$success = '';
$error = '';

// Check if user is logged in
$is_logged_in = isLoggedIn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Only allow contact if logged in
    if (!$is_logged_in) {
        $error = "Please login to send a message.";
    } else {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);
        $user_id = $_SESSION['user_id'];
        
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $error = "All fields are required!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $name, $email, $subject, $message])) {
                $success = "Thank you for your message! We'll get back to you soon.";
                // Clear form
                $_POST = [];
            } else {
                $error = "Failed to send message. Please try again.";
            }
        }
    }
}

// Get user info if logged in
$user_info = [];
if ($is_logged_in) {
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_info = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - CafeHub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .contact-hero {
            background: linear-gradient(rgba(44,24,16,0.8), rgba(44,24,16,0.8)), url('https://images.unsplash.com/photo-1422207134147-65fb81f59e38?w=1200') center/cover;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        
        .contact-hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .contact-wrapper {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        
        .contact-info-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .contact-info-box h2 {
            color: #2c1810;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .info-item:hover {
            background: #f9f9f9;
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            background: #ffd700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #2c1810;
        }
        
        .info-content h4 {
            margin-bottom: 5px;
            color: #2c1810;
        }
        
        .info-content p {
            color: #666;
        }
        
        .contact-form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .contact-form-box h2 {
            color: #2c1810;
            margin-bottom: 20px;
        }
        
        .login-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .login-warning a {
            color: #2c1810;
            font-weight: bold;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ffd700;
        }
        
        .map-container {
            margin-top: 30px;
            border-radius: 10px;
            overflow: hidden;
        }
        
        @media (max-width: 768px) {
            .contact-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="contact-hero">
        <div>
            <h1>Contact Us</h1>
            <p>We'd love to hear from you!</p>
        </div>
    </div>
    
    <div class="contact-wrapper">
        <div class="contact-info-box">
            <h2>Get In Touch</h2>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-content">
                    <h4>Visit Us</h4>
                    <p>123 Coffee Street, Food City, India - 110001</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="info-content">
                    <h4>Call Us</h4>
                    <p>+91 98765 43210<br>+91 12345 67890</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-content">
                    <h4>Email Us</h4>
                    <p>info@cafehub.com<br>support@cafehub.com</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                    <h4>Working Hours</h4>
                    <p>Monday - Sunday: 8:00 AM - 10:00 PM</p>
                </div>
            </div>
            
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m28!1m12!1m3!1d7194.279336122216!2d85.09042401132238!3d25.633489649916136!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m13!3e6!4m5!1s0x39ed57d6a5ca4bc5%3A0xf13297437ad031f6!2sCIMAGE%20%7C%20Catalyst%20College%20%7C%20Best%20BCA%20College%20in%20Patna%20%7C%20Top%20BBA%20College%20in%20Patna%2C%20no2%2C%20On%2C%20CIMAGE%20Tower%2C%20C-16(P%2C%20Prabhat%20Khabar%20Rd%2C%20in%20front%20of%20Coca%20Cola%20gate%2C%20off%20Atal%20Path%2C%20Patliputra%20Industrial%20Area%2C%20Patliputra%20Colony%2C%20Patna%2C%20Bihar%20800013!3m2!1d25.6329884!2d85.1013066!4m5!1s0x39ed57d6a5ca4bc5%3A0xf13297437ad031f6!2sCIMAGE%20%7C%20Catalyst%20College%20%7C%20Best%20BCA%20College%20in%20Patna%20%7C%20Top%20BBA%20College%20in%20Patna%2C%20no2%2C%20On%2C%20CIMAGE%20Tower%2C%20C-16(P%2C%20Prabhat%20Khabar%20Rd%2C%20in%20front%20of%20Coca%20Cola%20gate%2C%20off%20Atal%20Path%2C%20Patliputra%20Industrial%20Area%2C%20Patliputra%20Colony%2C%20Patna%2C%20Bihar%20800013!3m2!1d25.6329884!2d85.1013066!5e0!3m2!1sen!2sin!4v1775384924257!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
        
        <div class="contact-form-box">
            <h2>Send Us a Message</h2>
            
            <?php if(!$is_logged_in): ?>
                <div class="login-warning">
                    <i class="fas fa-lock"></i> 
                    Please <a href="login.php">login</a> or <a href="register.php">register</a> to send us a message.
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="contactForm">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="name" required value="<?php echo $is_logged_in && isset($user_info['name']) ? htmlspecialchars($user_info['name']) : ''; ?>" <?php echo $is_logged_in ? 'readonly style="background:#f5f5f5"' : ''; ?>>
                </div>
                
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required value="<?php echo $is_logged_in && isset($user_info['email']) ? htmlspecialchars($user_info['email']) : ''; ?>" <?php echo $is_logged_in ? 'readonly style="background:#f5f5f5"' : ''; ?>>
                </div>
                
                <div class="form-group">
                    <label>Subject *</label>
                    <select name="subject" required <?php echo !$is_logged_in ? 'disabled' : ''; ?>>
                        <option value="">Select Subject</option>
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Order Issue">Order Issue</option>
                        <option value="Feedback">Feedback</option>
                        <option value="Collaboration">Collaboration</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" rows="5" required placeholder="Tell us how can we help you..." <?php echo !$is_logged_in ? 'disabled' : ''; ?>></textarea>
                </div>
                
                <button type="submit" class="btn-primary" <?php echo !$is_logged_in ? 'disabled style="opacity:0.5; cursor:not-allowed"' : ''; ?>>
                    <?php echo $is_logged_in ? 'Send Message' : 'Login to Send Message'; ?>
                </button>
            </form>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>