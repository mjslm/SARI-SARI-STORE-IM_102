-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 01:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salomon_im102_final`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `details`, `created_at`) VALUES
(1, 1, 'admin', 'LOGOUT', 'users', NULL, 'User logged out', '2026-06-27 11:24:32'),
(2, 1, 'admin', 'LOGIN', 'users', NULL, 'User logged in', '2026-06-27 11:24:36'),
(3, 1, 'admin', 'DELETE', 'products', 33, 'Deleted product: Testing', '2026-06-27 11:24:58'),
(4, 1, 'admin', 'ADD', 'products', 34, 'Added product: asda', '2026-06-27 11:25:50'),
(5, 1, 'admin', 'DELETE', 'products', 34, 'Deleted product: asda', '2026-06-27 11:28:50'),
(6, 1, 'admin', 'ADD', 'users', 7, 'Added user: hello', '2026-06-27 11:30:39'),
(7, 1, 'admin', 'LOGOUT', 'users', NULL, 'User logged out', '2026-06-27 11:31:43'),
(8, 5, 'justine', 'LOGIN', 'users', NULL, 'User logged in', '2026-06-27 11:31:49'),
(9, 5, 'justine', 'LOGOUT', 'users', NULL, 'User logged out', '2026-06-27 11:32:11'),
(10, 1, 'admin', 'LOGIN', 'users', NULL, 'User logged in', '2026-06-27 11:32:16');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Beverages', '2026-06-27 08:25:19'),
(2, 'Snacks', '2026-06-27 08:25:19'),
(3, 'Canned Goods', '2026-06-27 08:25:19'),
(4, 'Rice & Grains', '2026-06-27 08:25:19'),
(5, 'Cooking Essentials', '2026-06-27 08:25:19'),
(6, 'Household', '2026-06-27 08:25:19'),
(7, 'Personal Care', '2026-06-27 08:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `category_id`, `supplier_id`, `added_by`, `image`, `created_at`) VALUES
(1, 'Coca-Cola 1.5L', 'Regular Coke 1.5 Liters Bottle', 85.00, 50, 1, 1, 1, 'Coca-Cola 1.5L.jpg', '2026-06-27 08:25:19'),
(2, 'Pepsi 1.5L', 'Pepsi Cola 1.5 Liters Bottle', 80.00, 45, 1, 1, 1, 'Pepsi 1.5L.jpg', '2026-06-27 08:25:19'),
(3, 'Royal 1.5L', 'Royal Soft Drink 1.5 Liters', 75.00, 40, 1, 1, 1, 'Royal 1.5L.jpg', '2026-06-27 08:25:19'),
(4, 'Sprite 1.5L', 'Sprite Lemon-Lime 1.5 Liters', 82.00, 35, 1, 1, 1, 'Sprite 1.5L.jpg', '2026-06-27 08:25:19'),
(5, 'Nature\'s Spring 500ml', '500ml Bottled Water', 20.00, 120, 1, 6, 1, 'Nature\'s Spring 500ml.jpg', '2026-06-27 08:25:19'),
(6, 'Nescafe Cappuccino Coffee Drink', 'Nescafe Cappuccino Coffee Drink 180ml', 35.00, 200, 1, 6, 1, '1782554828_100000094194-Nescafe-Cappuccino-Coffee-Drink-180ml-260306.webp', '2026-06-27 08:25:19'),
(7, 'Low Fat Chocolate Milk Drink', 'Selecta Dolce UHT Low Fat Chocolate Milk Drink 1L', 110.00, 25, 1, 2, 1, '1782554900_100000029299-Selecta-Dolce-Signature-Chocolate-Milk-Drink-1L-250522.webp', '2026-06-27 08:25:19'),
(8, 'Ripe Mango Blended Juice', 'Locally Ripe Mango Blended Juice Drink 1L', 90.00, 60, 1, 3, 1, '1782554113_4801668607244_1024x1024.webp', '2026-06-27 08:25:19'),
(9, 'Pancit Cantoon', 'Instant Noodles Pack', 15.00, 200, 2, 3, 1, '1782554235_Lucky-Me-Pancit-Canton-Extra-Hot-Chili.png', '2026-06-27 08:25:19'),
(10, 'Skyflakes Crackers', 'Cracker Biscuits Pack', 12.00, 150, 2, 5, 1, '1782554311_Skyflakes-Crackers-10_s-768x768.webp', '2026-06-27 08:25:19'),
(11, 'Magic Chips Cheese Crackers', 'Chips Cheese Crackers', 18.00, 100, 2, 3, 1, '1782554361_Magic-Chips-Cheese-Crackers-30g.webp', '2026-06-27 08:25:19'),
(12, 'Gardenia White Bread Classic Loaf', 'Fresh White Bread Loaf from Gardenia', 45.00, 10, 2, 2, 1, '1782554448_9000000119-Gardenia-White-Bread-Classic-Loaf-400g-240129.webp', '2026-06-27 08:25:19'),
(13, 'Hi-ro Chocolate Sandwich Cookies', 'Chocolate Sandwich Cookies', 25.00, 80, 2, 5, 1, '1782554491_Hi-ro-Chocolate-Sandwich-Cookies-10_s.webp', '2026-06-27 08:25:19'),
(14, 'Fres Barley Mint Candy', 'Fres Barley Mint Candy 50’s', 30.00, 15, 2, 3, 1, '1782554548_Fres-Barley-Mint-Candy-50_s.webp', '2026-06-27 08:25:19'),
(15, 'CENTURY TUNA HOT & SPICY', 'Canned Tuna in Oil', 30.00, 120, 3, 2, 1, '1782554595_7987545.jpg', '2026-06-27 08:25:19'),
(16, 'MEGA SARDINES WITH CHILI', 'MEGA SARDINES IN TOMATO SAUCE WITH CHILI', 20.00, 150, 3, 2, 1, '1782554642_megahot-2-scaled.jpg', '2026-06-27 08:25:19'),
(17, 'HOLIDAY CORNED BEEF', 'Corned Beef 150g', 35.00, 100, 3, 4, 1, '1782554671_7854214254163.jpg', '2026-06-27 08:25:19'),
(18, 'ARGENTINA MEAT LOAF TOCINO STYLE', 'ARGENTINA MEAT LOAF TOCINO STYLE 170G', 25.00, 90, 3, 4, 1, '1782554947_327-1.png', '2026-06-27 08:25:19'),
(19, 'Jasmie Rice 2kg', '2kg Premium Jasmine Rice', 250.00, 30, 4, 4, 1, '1782555729_20514348-removebg-preview.webp', '2026-06-27 08:25:19'),
(20, 'Master Chef Jasmine Rice', 'Master Chef Jasmine Rice | 5kg', 340.00, 50, 4, 4, 1, '1782555837_20553485-removebg-preview.webp', '2026-06-27 08:25:19'),
(21, 'Great Value Brown Sugar', 'Refined Sugar 1kg', 80.00, 80, 5, 4, 1, '1782555008_DSC05248_1024x1024.webp', '2026-06-27 08:25:19'),
(22, 'Canola Oil', 'Golden Fiesta Canola Oil 1L', 120.00, 40, 5, 4, 1, '1782555057_4801668601235_1024x1024.webp', '2026-06-27 08:25:19'),
(23, 'Iodized Rock Salt', 'Iodized Salt 1kg', 150.00, 8, 5, 4, 1, '1782555135_10118325-sm_bonus_iodized_rock_salt_1kg_copy_.webp', '2026-06-27 08:25:19'),
(24, 'Datu Puti Vinegar Gin', 'Datu Puti Vinegar Gin | 350ml', 30.00, 70, 5, 4, 1, '1782555211_10223505_datu_puti_vinegar_gin_350ml_copy_.webp', '2026-06-27 08:25:19'),
(25, 'Silver Swan Soy Sauce', 'Silver Swan Soy Sauce Doy Pouch | 200ml', 15.00, 65, 5, 4, 1, '1782555259_10079484_copy__2.webp', '2026-06-27 08:25:19'),
(26, 'Tide Detergent Powder', 'Tide Detergent Powder with Downy Perfume Garden Bloom | 1.55kg', 274.99, 60, 6, 5, 1, '1782555359_20260691.webp', '2026-06-27 08:25:19'),
(27, 'Zonrox Bleach Purple', 'Zonrox Bleach Colorsafe Blossom Fresh | 450ml', 40.00, 50, 6, 5, 1, '1782555415_10222700_copy_.webp', '2026-06-27 08:25:19'),
(28, 'Joy Dishwashing Liquid Kalamansi', 'Joy Dishwashing Liquid Kalamansi Refill | 540ml', 150.00, 70, 6, 5, 1, '1782555463_20232750_joy_dishwashing_liquid_kalamansi_600ml_copy_2_.webp', '2026-06-27 08:25:19'),
(29, 'Safeguard Soap Pure White Jumbo', 'Safeguard Soap Pure White Jumbo | 175g', 70.00, 100, 7, 5, 1, '1782555503_4902430222891_pure_white_160g-removebg-preview.webp', '2026-06-27 08:25:19'),
(30, 'Head & Shoulders Shampoo Cool Menthol', 'Shampoo Sachet', 12.00, 200, 7, 5, 1, '1782555543_20340391 (1).webp', '2026-06-27 08:25:19'),
(31, 'Colgate Toothpaste', 'Colgate Toothpaste GRF | 132g 2pcs', 159.00, 10, 7, 5, 1, '1782555590_20512687_grf_132g_twin_pack_value_pack_copy.webp', '2026-06-27 08:25:19'),
(32, 'Celeteque Facial Wash Natural Moist', 'Celeteque Facial Wash Natural Moist | 60ml', 105.00, 60, 7, 5, 1, '1782555653_20077810_celetequefwash_naturalmoist_60ml.webp', '2026-06-27 08:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `created_at`) VALUES
(1, 'Puregold Supplier', 'Miro Valerio', '09123456789', '2026-06-27 08:25:19'),
(2, 'SM Mart', 'Aj Gasal', '09187654321', '2026-06-27 08:25:19'),
(3, 'Local Distributor', 'Mark Quinio', '09991234567', '2026-06-27 08:25:19'),
(4, 'Best Foods Inc.', 'Fhitz Balaba', '09234567890', '2026-06-27 08:25:19'),
(5, 'Prime Goods', 'Kyle Casia', '09345678901', '2026-06-27 08:25:19'),
(6, 'Quick Supply', 'Gaton Ton', '09456789012', '2026-06-27 08:25:19'),
(7, 'Mega Mart', 'Ronie Alonte', '09567890123', '2026-06-27 08:25:19'),
(8, 'Star Trading', 'Bading Dong', '09678901234', '2026-06-27 08:25:19'),
(9, 'Golden Harvest', 'Marchael Salomon', '09789012345', '2026-06-27 08:25:19'),
(10, 'Allied Products', 'Jenrex Pitogo', '09890123456', '2026-06-27 08:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$JYxt47rLNCLEerdn6iLUV.otVJzxaPm.fArcQ.M1w2J2CdYJPZnEC', 'admin', '2026-06-27 08:46:18'),
(2, 'staff1', 'staff1@store.com', '$2y$10$92IXUNpkjO0O0RQ5byMi.Ye4oKoEa3Ro9LLC/.og/at2.uheW6/igi', 'staff', '2026-06-27 08:25:19'),
(3, 'mj123', 'mj@gmail.com', '$2y$10$7LY.l1TOtU7C4dCk8FTIiuavHuhFwQiPi06dQpTLQY/hBeRns.7WK', 'staff', '2026-06-27 08:43:03'),
(4, 'admin2', 'admin2@gmail.com', '$2y$10$j4G3C4Ftkg8oy63KPSQReOrLJSl0QFG8FiT6wkwM8oM9ATl10R80e', 'admin', '2026-06-27 08:49:50'),
(5, 'justine', 'jj@gmail.com', '$2y$10$d6bMS12rej3LSZ.LhcBEL.13/zNRwxrWKqGdpmlpESc/mmGS2Md/6', 'staff', '2026-06-27 10:34:43'),
(7, 'hello', 'hello@gmail.com', '$2y$10$LujM3odAu/zm3g2sX59RpO.q.Y.udLYyGrLRFSZHetucYUa4QVHXi', 'staff', '2026-06-27 11:30:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_user` (`user_id`),
  ADD KEY `idx_activity_created` (`created_at`),
  ADD KEY `idx_activity_action` (`action`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
