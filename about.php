<?php
require_once 'config/database.php';

// Fetch about content from database
$about_content = [];
$stmt = $pdo->query("SELECT * FROM about_content");
while($row = $stmt->fetch()) {
    $about_content[$row['section_name']] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CafeHub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .about-hero {
            background: linear-gradient(rgba(44,24,16,0.8), rgba(44,24,16,0.8)), url('https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=1200') center/cover;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        
        .about-hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .about-section {
            padding: 60px 0;
            background: white;
        }
        
        .about-section:nth-child(even) {
            background: #f9f9f9;
        }
        
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        
        .about-content h2 {
            font-size: 2rem;
            color: #2c1810;
            margin-bottom: 20px;
        }
        
        .about-content p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .about-image img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-top: 40px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ffd700;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .team-member {
            text-align: center;
        }
        
        .team-member img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .about-container {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="about-hero">
        <div>
            <h1>About CafeHub</h1>
            <p><?php echo isset($about_content['hero']) ? htmlspecialchars($about_content['hero']['content']) : 'Serving happiness since 2020'; ?></p>
        </div>
    </div>
    
    <section class="about-section">
        <div class="about-container">
            <div class="about-content">
                <h2><?php echo isset($about_content['story']) ? htmlspecialchars($about_content['story']['title']) : 'Our Story'; ?></h2>
                <p><?php echo isset($about_content['story']) ? nl2br(htmlspecialchars($about_content['story']['content'])) : 'CafeHub started with a simple mission - to serve the finest coffee and delicious food in a warm, welcoming environment. What began as a small coffee shop has now grown into a beloved local establishment.'; ?></p>
                <p>We believe that great food brings people together. Every cup of coffee we serve is crafted with passion, and every meal is prepared with love using the freshest ingredients.</p>
            </div>
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600" alt="Our Cafe">
            </div>
        </div>
    </section>
    
    <section class="about-section">
        <div class="about-container">
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=600" alt="Our Mission">
            </div>
            <div class="about-content">
                <h2><?php echo isset($about_content['mission']) ? htmlspecialchars($about_content['mission']['title']) : 'Our Mission'; ?></h2>
                <p><?php echo isset($about_content['mission']) ? nl2br(htmlspecialchars($about_content['mission']['content'])) : 'To provide exceptional quality food and beverages while creating memorable experiences for our customers.'; ?></p>
                <h2 style="margin-top: 30px;"><?php echo isset($about_content['vision']) ? htmlspecialchars($about_content['vision']['title']) : 'Our Vision'; ?></h2>
                <p><?php echo isset($about_content['vision']) ? nl2br(htmlspecialchars($about_content['vision']['content'])) : 'To become the most loved cafe chain known for quality, innovation, and community service.'; ?></p>
            </div>
        </div>
    </section>
    
    <section class="about-section">
        <div class="container">
            <h2 style="text-align: center; font-size: 2rem; color: #2c1810; margin-bottom: 40px;">Why Choose Us</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">5000+</div>
                    <p>Happy Customers</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <p>Coffee Varieties</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <p>Delicious Dishes</p>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.8</div>
                    <p>Customer Rating</p>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'footer.php'; ?>
</body>
</html>