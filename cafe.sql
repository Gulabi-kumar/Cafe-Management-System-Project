-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 01:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
Create Database cafe_system;
use cafe_system;
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `section_name`, `title`, `content`, `image`, `updated_at`) VALUES
(1, 'hero', 'About CafeHub', 'Serving happiness since 2020', NULL, '2026-04-05 10:15:26'),
(2, 'story', 'Our Story', 'CafeHub started with a simple mission - to serve the finest coffee and delicious food in a warm, welcoming environment. What began as a small coffee shop has now grown into a beloved local establishment.', NULL, '2026-04-05 10:15:26'),
(3, 'mission', 'Our Mission', 'To provide exceptional quality food and beverages while creating memorable experiences for our customers.', NULL, '2026-04-05 10:15:26'),
(4, 'vision', 'Our Vision', 'To become the most loved cafe chain known for quality, innovation, and community service.', NULL, '2026-04-05 10:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$dUNZmRx87HBk5Oq063lEL.IIEHH7tm0zcWcC7qJr3SN.bRT4TZcHO');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Beverages', '2026-04-05 10:53:38'),
(2, 'Starters & Snacks', '2026-04-05 10:53:38'),
(3, 'Main Course', '2026-04-05 10:53:38'),
(4, 'Breads & Rice', '2026-04-05 10:53:38'),
(5, 'Desserts', '2026-04-05 10:53:38');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `admin_reply` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `status`, `admin_reply`, `created_at`) VALUES
(1, NULL, 'ajay', 'ajay@gmail.com', 'Feedback', 'Great service! Loved the coffee.', 'replied', 'ok', '2026-04-05 10:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `image`, `category_id`, `is_available`, `created_at`) VALUES
(62, 'Masala Chai', 'Traditional Indian spiced tea with ginger, cardamom, and cloves', 49.00, 'masala_chai.jpg', 1, 1, '2026-04-05 10:54:19'),
(63, 'Filter Coffee', 'South Indian style strong coffee with chicory', 59.00, 'filter_coffee.jpg', 1, 1, '2026-04-05 10:54:19'),
(64, 'Mango Lassi', 'Sweet yogurt smoothie with fresh mango pulp', 89.00, 'mango_lassi.jpg', 1, 1, '2026-04-05 10:54:19'),
(65, 'Jaljeera', 'Refreshing cumin-spiced lemonade', 49.00, 'jaljeera.jpg', 1, 1, '2026-04-05 10:54:19'),
(66, 'Badam Milk', 'Rich almond milk infused with saffron and cardamom', 79.00, 'badam_milk.jpg', 1, 1, '2026-04-05 10:54:19'),
(67, 'Samosa', 'Crispy pastry filled with spiced potatoes and peas', 39.00, 'samosa.jpg', 2, 1, '2026-04-05 10:54:19'),
(68, 'Pani Puri', 'Hollow puri filled with spicy tamarind water and chickpeas (6 pieces)', 59.00, 'pani_puri.jpg', 2, 1, '2026-04-05 10:54:19'),
(69, 'Aloo Tikki Chaat', 'Golden fried potato patties topped with chutney and yogurt', 79.00, 'aloo_tikki.jpg', 2, 1, '2026-04-05 10:54:19'),
(70, 'Chicken Tikka', 'Boneless chicken marinated in yogurt and spices, grilled', 199.00, 'chicken_tikka.jpg', 2, 1, '2026-04-05 10:54:19'),
(71, 'Hara Bhara Kabab', 'Vegetable kababs made with spinach and peas', 149.00, 'hara_bhara_kabab.jpg', 2, 1, '2026-04-05 10:54:19'),
(72, 'Onion Pakoda', 'Crispy onion fritters with gram flour served with chutney', 49.00, 'pakoda.jpg', 2, 1, '2026-04-05 10:54:19'),
(73, 'Butter Chicken', 'Creamy tomato curry with tender chicken pieces', 249.00, 'butter_chicken.jpg', 3, 1, '2026-04-05 10:54:19'),
(74, 'Paneer Butter Masala', 'Soft cottage cheese cubes in rich creamy gravy', 219.00, 'paneer_butter_masala.jpg', 3, 1, '2026-04-05 10:54:19'),
(75, 'Chicken Biryani', 'Aromatic basmati rice cooked with chicken and whole spices', 229.00, 'chicken_biryani.jpg', 3, 1, '2026-04-05 10:54:19'),
(76, 'Dal Makhani', 'Slow cooked black lentils with butter and cream overnight', 179.00, 'dal_makhani.jpg', 3, 1, '2026-04-05 10:54:19'),
(77, 'Palak Paneer', 'Fresh cottage cheese in creamy spinach gravy', 199.00, 'palak_paneer.jpg', 3, 1, '2026-04-05 10:54:19'),
(78, 'Rogan Josh', 'Kashmiri lamb curry with aromatic spices', 299.00, 'rogan_josh.jpg', 3, 1, '2026-04-05 10:54:19'),
(79, 'Garlic Naan', 'Soft leavened bread with fresh garlic and butter', 49.00, 'garlic_naan.jpg', 4, 1, '2026-04-05 10:54:19'),
(80, 'Butter Naan', 'Soft leavened bread brushed with butter', 39.00, 'butter_naan.jpg', 4, 1, '2026-04-05 10:54:19'),
(81, 'Tandoori Roti', 'Whole wheat bread baked in tandoor', 29.00, 'tandoori_roti.jpg', 4, 1, '2026-04-05 10:54:19'),
(82, 'Jeera Rice', 'Fragrant basmati rice tempered with cumin seeds', 99.00, 'jeera_rice.jpg', 4, 1, '2026-04-05 10:54:19'),
(83, 'Veg Pulao', 'Mixed vegetable rice with whole spices', 129.00, 'veg_pulao.jpg', 4, 1, '2026-04-05 10:54:19'),
(84, 'Gulab Jamun', 'Soft milk dumplings soaked in sugar syrup (2 pieces)', 89.00, 'gulab_jamun.jpg', 5, 1, '2026-04-05 10:54:19'),
(85, 'Rasmalai', 'Soft cottage cheese patties in creamy milk', 129.00, 'rasmalai.jpg', 5, 1, '2026-04-05 10:54:19'),
(86, 'Gajar Ka Halwa', 'Slow cooked carrot pudding with nuts', 119.00, 'gajar_halwa.jpg', 5, 1, '2026-04-05 10:54:19'),
(87, 'Kulfi Falooda', 'Traditional Indian ice cream with vermicelli and rose syrup', 149.00, 'kulfi_falooda.jpg', 5, 1, '2026-04-05 10:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Preparing','Completed','Cancelled') DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `created_at`) VALUES
(1, 'Ranjan kumar', 'abc@gmail.com', '$2y$10$dUNZmRx87HBk5Oq063lEL.IIEHH7tm0zcWcC7qJr3SN.bRT4TZcHO', '9388393834', 'Indrapuri', '2026-04-05 09:37:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `contact_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
