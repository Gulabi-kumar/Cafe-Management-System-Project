<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">☕ CafeHub</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="cart.php">Cart (<span id="cartCount"><?php echo getCartCount(); ?></span>)</a></li>
            <?php if(isLoggedIn()): ?>
                <li class="user-dropdown">
                    <a href="#" class="user-name">
                        <i class="fas fa-user-circle"></i> 
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                        <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
                        <li><a href="change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
                        <li><hr></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
        <div class="menu-toggle" id="mobile-menu">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>

<style>
/* Navbar Styles */
.navbar {
    background: #2c1810;
    padding: 15px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    text-decoration: none;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 25px;
    margin: 0;
    padding: 0;
    align-items: center;
}

.nav-links li a {
    color: #fff;
    text-decoration: none;
    transition: 0.3s;
    font-size: 16px;
}

.nav-links li a:hover {
    color: #ffd700;
}

/* User Dropdown Styles */
.user-dropdown {
    position: relative;
}

.user-name {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 8px 12px;
    background: rgba(255,255,255,0.1);
    border-radius: 30px;
}

.user-name i {
    font-size: 14px;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    min-width: 200px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    list-style: none;
    padding: 10px 0;
    margin-top: 10px;
    opacity: 0;
    visibility: hidden;
    transition: 0.3s;
    z-index: 100;
}

.user-dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
}

.dropdown-menu li a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    color: #333;
    font-size: 14px;
}

.dropdown-menu li a:hover {
    background: #f5f5f5;
    color: #2c1810;
}

.dropdown-menu li hr {
    margin: 5px 0;
    border: none;
    border-top: 1px solid #eee;
}

/* Mobile Menu */
.menu-toggle {
    display: none;
    font-size: 24px;
    color: white;
    cursor: pointer;
}

@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }
    
    .nav-links {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #2c1810;
        flex-direction: column;
        padding: 20px;
        gap: 15px;
        text-align: center;
    }
    
    .nav-links.active {
        display: flex;
    }
    
    .user-dropdown .dropdown-menu {
        position: static;
        background: rgba(255,255,255,0.1);
        box-shadow: none;
        opacity: 1;
        visibility: visible;
        display: none;
        margin-top: 10px;
    }
    
    .user-dropdown:hover .dropdown-menu {
        display: block;
    }
    
    .dropdown-menu li a {
        color: white;
        justify-content: center;
    }
    
    .dropdown-menu li a:hover {
        background: rgba(255,255,255,0.2);
    }
    
    .dropdown-menu li hr {
        border-color: rgba(255,255,255,0.2);
    }
}
</style>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu')?.addEventListener('click', function() {
    document.querySelector('.nav-links').classList.toggle('active');
});

// Close dropdown when clicking outside (optional)
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.user-dropdown');
    if (dropdown && !dropdown.contains(event.target)) {
        const menu = dropdown.querySelector('.dropdown-menu');
        if (menu) {
            menu.style.opacity = '0';
            menu.style.visibility = 'hidden';
        }
    }
});
</script>