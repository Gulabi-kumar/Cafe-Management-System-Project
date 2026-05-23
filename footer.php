<!-- Footer Section -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>☕ CafeHub</h3>
                <p>Experience the finest coffee and delicious meals in a warm, welcoming environment.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if(isLoggedIn()): ?>
                        <li><a href="orders.php">My Orders</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Contact Info</h3>
                <ul class="contact-info">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Coffee Street, Food City</li>
                    <li><i class="fas fa-phone"></i> +91 98765 43210</li>
                    <li><i class="fas fa-envelope"></i> info@cafehub.com</li>
                    <li><i class="fas fa-clock"></i> Mon-Sun: 8:00 AM - 10:00 PM</li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Newsletter</h3>
                <p>Subscribe for exclusive offers!</p>
                <form method="POST" action="subscribe.php" class="newsletter-form">
                    <input type="email" name="email" placeholder="Your Email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> CafeHub. All rights reserved. | Designed with ❤️ for coffee lovers</p>
        </div>
    </div>
</footer>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
.footer {
    background: #2c1810;
    color: #fff;
    padding: 60px 0 20px;
    margin-top: 60px;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    margin-bottom: 40px;
}

.footer-col h3 {
    font-size: 1.3rem;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 10px;
}

.footer-col h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 2px;
    background: #ffd700;
}

.footer-col p {
    color: #ccc;
    line-height: 1.6;
    margin-bottom: 15px;
}

.footer-col ul {
    list-style: none;
    padding: 0;
}

.footer-col ul li {
    margin-bottom: 12px;
}

.footer-col ul li a {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-col ul li a:hover {
    color: #ffd700;
    padding-left: 5px;
}

.contact-info li {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #ccc;
}

.contact-info li i {
    width: 25px;
    color: #ffd700;
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.social-links a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s;
}

.social-links a:hover {
    background: #ffd700;
    color: #2c1810;
    transform: translateY(-3px);
}

.newsletter-form {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.newsletter-form input {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 5px;
    outline: none;
}

.newsletter-form button {
    padding: 10px 15px;
    background: #ffd700;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.newsletter-form button:hover {
    background: #e6c200;
}

.footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    color: #ccc;
}

@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .footer-col h3::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .contact-info li {
        justify-content: center;
    }
    
    .social-links {
        justify-content: center;
    }
}
</style>