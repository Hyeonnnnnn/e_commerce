-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 02:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `computer_shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action_type`, `description`, `created_at`) VALUES
(1, 1, 'user_login', 'User logged in: admin', '2025-05-06 06:41:10'),
(2, 1, 'user_logout', 'User logged out: admin', '2025-05-06 08:49:57'),
(3, 1, 'user_login', 'User logged in: admin', '2025-05-07 05:56:25'),
(4, 1, 'product_added', 'New product added: ars', '2025-05-07 07:30:12'),
(5, 1, 'product_deleted', 'Deleted product ID: 33', '2025-05-07 07:30:35'),
(6, 1, 'user_logout', 'User logged out: admin', '2025-05-07 07:35:28'),
(7, 1, 'user_login', 'User logged in: admin', '2025-05-07 07:35:32'),
(8, 1, 'product_added', 'New product added: ars', '2025-05-07 07:35:47'),
(9, 1, 'user_logout', 'User logged out: admin', '2025-05-07 07:37:45'),
(10, 1, 'user_login', 'User logged in: admin', '2025-05-07 07:47:49'),
(11, 1, 'product_deleted', 'Deleted product ID: 34', '2025-05-07 07:50:52'),
(12, 1, 'product_added', 'New product added: ars', '2025-05-07 07:51:06'),
(13, 1, 'product_added', 'New product added: ars', '2025-05-07 07:54:09'),
(14, 1, 'product_deleted', 'Deleted product ID: 36', '2025-05-07 07:54:13'),
(15, 1, 'product_deleted', 'Deleted product ID: 2', '2025-05-07 07:54:15'),
(16, 1, 'product_deleted', 'Deleted product ID: 35', '2025-05-07 07:54:19'),
(17, 1, 'product_added', 'New product added: ars', '2025-05-07 07:54:43'),
(18, 1, 'user_logout', 'User logged out: admin', '2025-05-07 07:58:49'),
(19, 1, 'user_login', 'User logged in: admin', '2025-05-07 07:58:55'),
(20, 1, 'product_deleted', 'Deleted product ID: 37', '2025-05-07 07:58:59'),
(21, 1, 'product_added', 'New product added: asd (ID: 38)', '2025-05-07 07:59:09'),
(22, 1, 'product_deleted', 'Deleted product ID: 38', '2025-05-07 08:04:57'),
(23, 1, 'product_updated', 'Updated product: AMD RX 7900 XTXs', '2025-05-07 08:05:04'),
(24, 1, 'product_updated', 'Updated product: AMD RX 7900 XTX', '2025-05-07 08:05:34'),
(25, 1, 'product_added', 'New product added: 12323 (ID: 39)', '2025-05-07 08:06:11'),
(26, 1, 'product_deleted', 'Deleted product ID: 39', '2025-05-07 08:10:50'),
(27, 1, 'product_added', 'New product added: 123 (ID: 40)', '2025-05-07 08:11:01'),
(28, 1, 'product_deleted', 'Deleted product ID: 40', '2025-05-07 08:16:33'),
(29, 1, 'product_added', 'New product added: AMD RX 7900 XTXssssssssssss (ID: 41)', '2025-05-07 08:16:47'),
(30, 1, 'product_added', 'New product added: Hyeon', '2025-05-07 08:21:54'),
(31, 1, 'user_logout', 'User logged out: admin', '2025-05-07 13:48:20'),
(32, 1, 'user_login', 'User logged in: admin', '2025-05-07 13:48:25'),
(33, 1, 'product_updated', 'Updated product: WD Black SN850X 2TB', '2025-05-07 14:00:25'),
(34, 1, 'product_updated', 'Updated product: AMD RX 7900 XTX', '2025-05-07 14:00:39'),
(35, 1, 'product_updated', 'Updated product: AMD Ryzen 7 7800X3D', '2025-05-07 14:00:48'),
(36, 1, 'product_updated', 'Updated product: ASRock Z790 Taichi', '2025-05-07 14:01:22'),
(37, 1, 'product_updated', 'Updated product: ASUS ROG Maximus Z790', '2025-05-07 14:01:30'),
(38, 1, 'product_updated', 'Updated product: be quiet! Dark Base Pro 900', '2025-05-07 14:01:40'),
(39, 1, 'product_updated', 'Updated product: be quiet! Dark Power 13', '2025-05-07 14:01:46'),
(40, 1, 'product_updated', 'Updated product: Corsair Dominator 32GB', '2025-05-07 14:01:53'),
(41, 1, 'product_updated', 'Updated product: Corsair HX1200i', '2025-05-07 14:02:03'),
(42, 1, 'product_updated', 'Updated product: Crucial 64GB', '2025-05-07 14:02:11'),
(43, 1, 'product_updated', 'Updated product: Crucial P5 Plus 2TB', '2025-05-07 14:02:19'),
(44, 1, 'product_updated', 'Updated product: EVGA SuperNOVA 1000', '2025-05-07 14:02:27'),
(45, 1, 'product_updated', 'Updated product: Fractal Design Torrent', '2025-05-07 14:04:07'),
(46, 1, 'product_updated', 'Updated product: G.Skill Trident Z5 32GB', '2025-05-07 14:04:21'),
(47, 1, 'product_updated', 'Updated product: Gigabyte X670E Aorus', '2025-05-07 14:04:33'),
(48, 1, 'product_updated', 'Updated product: Intel Core i7-13700K', '2025-05-07 14:04:47'),
(49, 1, 'product_updated', 'Updated product: Intel Core i9-13900K', '2025-05-07 14:04:56'),
(50, 1, 'product_updated', 'Updated product: Kingston Fury 32GB', '2025-05-07 14:05:07'),
(51, 1, 'product_updated', 'Updated product: Lian Li O11 Dynamic', '2025-05-07 14:06:02'),
(52, 1, 'product_updated', 'Updated product: Logitech G Pro X', '2025-05-07 14:06:18'),
(53, 1, 'product_updated', 'Updated product: MSI MEG X670E', '2025-05-07 14:06:28'),
(54, 1, 'product_updated', 'Updated product: NVIDIA RTX 4070 Ti', '2025-05-07 14:06:57'),
(55, 1, 'product_updated', 'Updated product: NVIDIA RTX 4080', '2025-05-07 14:07:05'),
(56, 1, 'product_updated', 'Updated product: NVIDIA RTX 4090', '2025-05-07 14:07:14'),
(57, 1, 'product_updated', 'Updated product: Phanteks Evolv X', '2025-05-07 14:07:23'),
(58, 1, 'product_updated', 'Updated product: Razer Huntsman V2', '2025-05-07 14:07:33'),
(59, 1, 'product_updated', 'Updated product: Samsung 990 Pro 2TB', '2025-05-07 14:07:43'),
(60, 1, 'product_updated', 'Updated product: Samsung Odyssey G7', '2025-05-07 14:07:53'),
(61, 1, 'product_updated', 'Updated product: Seagate FireCuda 4TB', '2025-05-07 14:08:01'),
(62, 1, 'product_updated', 'Updated product: Seasonic Prime TX-1300', '2025-05-07 14:08:10'),
(63, 1, 'product_updated', 'Updated product: SteelSeries Arctis Pro', '2025-05-07 14:08:17'),
(64, 1, 'product_added', 'New product added: ars (ID: 43)', '2025-05-07 14:23:39'),
(65, 1, 'product_updated', 'Updated product: AMD RX 7900 XTXs', '2025-05-07 14:24:21'),
(66, 1, 'product_updated', 'Updated product: AMD RX 7900 XTX', '2025-05-07 14:24:26'),
(67, 1, 'user_logout', 'User logged out: admin', '2025-05-07 14:41:03'),
(68, 1, 'user_login', 'User logged in: admin', '2025-05-07 14:45:45'),
(69, 1, 'user_logout', 'User logged out: admin', '2025-05-07 14:46:06'),
(70, 1, 'user_login', 'User logged in: admin', '2025-05-07 14:49:56'),
(71, 1, 'user_logout', 'User logged out: admin', '2025-05-07 14:50:06'),
(72, 1, 'user_login', 'User logged in: admin', '2025-05-07 15:09:54'),
(73, 1, 'user_logout', 'User logged out: admin', '2025-05-07 15:12:54'),
(74, 1, 'user_login', 'User logged in: admin', '2025-05-07 15:13:02'),
(75, 1, 'user_logout', 'User logged out: admin', '2025-05-07 15:24:34'),
(76, 1, 'user_login', 'User logged in: admin', '2025-05-07 15:24:38'),
(77, 2, 'user_login', 'User logged in: edgar', '2025-05-13 05:00:56'),
(78, 2, 'user_logout', 'User logged out: edgar', '2025-05-13 05:40:09'),
(79, 2, 'user_login', 'User logged in: edgar', '2025-05-13 05:56:53'),
(80, 2, 'user_logout', 'User logged out: edgar', '2025-05-13 06:06:01'),
(81, 1, 'user_login', 'User logged in: admin', '2025-05-13 06:06:05'),
(82, 1, 'product_updated', 'Updated product: AMD RX 7900 XTs', '2025-05-13 06:52:12'),
(83, 1, 'product_updated', 'Updated product: AMD RX 7900 XTX', '2025-05-13 06:52:19'),
(84, 2, 'user_login', 'User logged in: edgar', '2025-05-13 06:52:46'),
(85, 2, 'stock_updated', 'Stock updated for product ID: 6', '2025-05-13 07:11:03'),
(86, 2, 'stock_updated', 'Stock updated for product ID: 20', '2025-05-13 07:11:03'),
(87, 2, 'transaction_created', 'New transaction created: 1', '2025-05-13 07:11:03'),
(88, 2, 'stock_updated', 'Stock updated for product ID: 4', '2025-05-13 07:12:43'),
(89, 2, 'stock_updated', 'Stock updated for product ID: 28', '2025-05-13 07:12:43'),
(90, 2, 'transaction_created', 'New transaction created: 2', '2025-05-13 07:12:43'),
(91, 2, 'stock_updated', 'Stock updated for product ID: 12', '2025-05-13 07:13:59'),
(92, 2, 'transaction_created', 'New transaction created: 3', '2025-05-13 07:13:59'),
(93, 2, 'user_logout', 'User logged out: edgar', '2025-05-13 11:52:24'),
(94, 1, 'user_login', 'User logged in: admin', '2025-05-13 11:52:38'),
(95, 1, 'user_logout', 'User logged out: admin', '2025-05-13 11:56:34'),
(96, 1, 'user_logout', 'User logged out: admin', '2025-05-13 11:57:43'),
(97, 2, 'user_login', 'User logged in: edgar', '2025-05-13 11:57:51'),
(98, 2, 'stock_updated', 'Stock updated for product ID: 4', '2025-05-13 11:58:05'),
(99, 2, 'transaction_created', 'New transaction created: 4', '2025-05-13 11:58:05'),
(100, 2, 'user_logout', 'User logged out: edgar', '2025-05-13 12:01:57'),
(101, 2, 'user_login', 'User logged in: edgar', '2025-05-13 12:02:01');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `tax_rate` decimal(5,2) DEFAULT 12.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_picture` varchar(255) DEFAULT NULL COMMENT 'Path to product image file'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `stock_quantity`, `tax_rate`, `created_at`, `product_picture`) VALUES
(1, 'Intel Core i9-13900K', 'CPU', '24-Core (8P+16E) Desktop Processor, Up to 5.8 GHz', 34999.00, 5, 12.00, '2025-05-06 06:39:48', 'Intel Core i9-13900K.webp'),
(3, 'Intel Core i7-13700K', 'CPU', '16-Core (8P+8E) Desktop Processor, Up to 5.4 GHz', 27999.00, 10, 12.00, '2025-05-06 06:39:48', 'Intel Core i7-13700K.webp'),
(4, 'AMD Ryzen 7 7800X3D', 'CPU', '8-Core, 16-Thread Processor with 3D V-Cache', 25999.00, 10, 12.00, '2025-05-06 06:39:48', 'AMD Ryzen 7 7800X3D.webp'),
(5, 'NVIDIA RTX 4090', 'GPU', 'NVIDIA GeForce RTX 4090 24GB GDDR6X', 89999.00, 3, 12.00, '2025-05-06 06:39:48', 'NVIDIA RTX 4090.jpg'),
(6, 'AMD RX 7900 XTX', 'GPU', 'AMD Radeon RX 7900 XTX 24GB GDDR6', 59999.00, 3, 12.00, '2025-05-06 06:39:48', 'AMD RX 7900 XTX.webp'),
(7, 'NVIDIA RTX 4080', 'GPU', 'NVIDIA GeForce RTX 4080 16GB GDDR6X', 69999.00, 5, 12.00, '2025-05-06 06:39:48', 'NVIDIA RTX 4080.webp'),
(8, 'NVIDIA RTX 4070 Ti', 'GPU', 'NVIDIA GeForce RTX 4070 Ti 12GB GDDR6X', 49999.00, 6, 12.00, '2025-05-06 06:39:48', 'NVIDIA RTX 4070 Ti.webp'),
(9, 'Corsair Dominator 32GB', 'RAM', 'Corsair Dominator Platinum RGB DDR5 6000MHz (2x16GB)', 12999.00, 15, 12.00, '2025-05-06 06:39:48', 'Corsair Dominator 32GB.webp'),
(10, 'G.Skill Trident Z5 32GB', 'RAM', 'G.Skill Trident Z5 RGB DDR5 6400MHz (2x16GB)', 13999.00, 10, 12.00, '2025-05-06 06:39:48', 'G.Skill Trident Z5 32GB.webp'),
(11, 'Crucial 64GB', 'RAM', 'Crucial DDR5 5600MHz (2x32GB)', 15999.00, 8, 12.00, '2025-05-06 06:39:48', 'Crucial 64GB.webp'),
(12, 'Kingston Fury 32GB', 'RAM', 'Kingston Fury Beast RGB DDR5 6000MHz (2x16GB)', 11999.00, 19, 12.00, '2025-05-06 06:39:48', 'Kingston Fury 32GB.webp'),
(13, 'Samsung 990 Pro 2TB', 'Storage', 'Samsung 990 Pro NVMe PCIe 4.0 M.2 SSD', 14999.00, 10, 12.00, '2025-05-06 06:39:48', 'Samsung 990 Pro 2TB.webp'),
(14, 'WD Black SN850X 2TB', 'Storage', 'Western Digital Black SN850X NVMe PCIe 4.0', 13999.00, 12, 12.00, '2025-05-06 06:39:48', 'WD Black SN850X 2TB.webp'),
(15, 'Seagate FireCuda 4TB', 'Storage', 'Seagate FireCuda 530 NVMe PCIe 4.0', 24999.00, 6, 12.00, '2025-05-06 06:39:48', 'Seagate FireCuda 4TB.png'),
(16, 'Crucial P5 Plus 2TB', 'Storage', 'Crucial P5 Plus NVMe PCIe 4.0', 11999.00, 15, 12.00, '2025-05-06 06:39:48', 'Crucial P5 Plus 2TB.jpg'),
(17, 'ASUS ROG Maximus Z790', 'Motherboard', 'ASUS ROG Maximus Z790 Hero DDR5 ATX', 29999.00, 5, 12.00, '2025-05-06 06:39:48', 'ASUS ROG Maximus Z790.png'),
(18, 'MSI MEG X670E', 'Motherboard', 'MSI MEG X670E ACE AM5 DDR5 ATX', 32999.00, 4, 12.00, '2025-05-06 06:39:48', 'MSI MEG X670E.png'),
(19, 'Gigabyte X670E Aorus', 'Motherboard', 'Gigabyte X670E Aorus Master AM5 DDR5', 27999.00, 6, 12.00, '2025-05-06 06:39:48', 'Gigabyte X670E Aorus.png'),
(20, 'ASRock Z790 Taichi', 'Motherboard', 'ASRock Z790 Taichi DDR5 ATX', 26999.00, 6, 12.00, '2025-05-06 06:39:48', 'ASRock Z790 Taichi.png'),
(21, 'Corsair HX1200i', 'PSU', 'Corsair HX1200i Platinum 1200W Fully Modular', 15999.00, 8, 12.00, '2025-05-06 06:39:48', 'Corsair HX1200i.jpg'),
(22, 'be quiet! Dark Power 13', 'PSU', 'be quiet! Dark Power 13 1000W Titanium', 14999.00, 6, 12.00, '2025-05-06 06:39:48', 'be quiet! Dark Power 13.jpg'),
(23, 'Seasonic Prime TX-1300', 'PSU', 'Seasonic Prime TX-1300 Titanium 1300W', 16999.00, 5, 12.00, '2025-05-06 06:39:48', 'Seasonic Prime TX-1300.webp'),
(24, 'EVGA SuperNOVA 1000', 'PSU', 'EVGA SuperNOVA 1000 G6 Gold 1000W', 12999.00, 10, 12.00, '2025-05-06 06:39:48', 'EVGA SuperNOVA 1000.png'),
(25, 'Lian Li O11 Dynamic', 'Case', 'Lian Li O11 Dynamic EVO Full Tower', 8999.00, 10, 12.00, '2025-05-06 06:39:48', 'Lian Li O11 Dynamic.webp'),
(26, 'Fractal Design Torrent', 'Case', 'Fractal Design Torrent RGB Black E-ATX', 9999.00, 8, 12.00, '2025-05-06 06:39:48', 'Fractal Design Torrent.jpg'),
(27, 'Phanteks Evolv X', 'Case', 'Phanteks Evolv X Digital RGB E-ATX', 11999.00, 6, 12.00, '2025-05-06 06:39:48', 'Phanteks Evolv X.jpg'),
(28, 'be quiet! Dark Base Pro 900', 'Case', 'be quiet! Dark Base Pro 900 Black Rev. 2', 13999.00, 4, 12.00, '2025-05-06 06:39:48', 'be quiet! Dark Base Pro 900.jpg'),
(29, 'Logitech G Pro X', 'Peripheral', 'Logitech G Pro X Superlight Wireless Mouse', 7999.00, 15, 12.00, '2025-05-06 06:39:48', 'Logitech G Pro X.jpg'),
(30, 'Razer Huntsman V2', 'Peripheral', 'Razer Huntsman V2 Analog Optical Gaming Keyboard', 9999.00, 12, 12.00, '2025-05-06 06:39:48', 'Razer Huntsman V2.jpg'),
(31, 'Samsung Odyssey G7', 'Peripheral', 'Samsung Odyssey G7 32\" 240Hz Gaming Monitor', 34999.00, 5, 12.00, '2025-05-06 06:39:48', 'Samsung Odyssey G7.jpg'),
(32, 'SteelSeries Arctis Pro', 'Peripheral', 'SteelSeries Arctis Pro Wireless Gaming Headset', 12999.00, 10, 12.00, '2025-05-06 06:39:48', 'SteelSeries Arctis Pro.webp');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL,
  `tender_amount` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `total_amount`, `tax_amount`, `tender_amount`, `change_amount`, `transaction_date`) VALUES
(1, 2, 97437.76, 10439.76, 100000.00, 2562.24, '2025-05-13 07:11:03'),
(2, 2, 44797.76, 4799.76, 50000.00, 5202.24, '2025-05-13 07:12:43'),
(3, 2, 13438.88, 1439.88, 15000.00, 1561.12, '2025-05-13 07:13:59'),
(4, 2, 29118.88, 3119.88, 30000.00, 881.12, '2025-05-13 11:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `tax_amount`) VALUES
(1, 1, 6, 1, 59999.00, 59999.00, 7199.88),
(2, 1, 20, 1, 26999.00, 26999.00, 3239.88),
(3, 2, 4, 1, 25999.00, 25999.00, 3119.88),
(4, 2, 28, 1, 13999.00, 13999.00, 1679.88),
(5, 3, 12, 1, 11999.00, 11999.00, 1439.88),
(6, 4, 4, 1, 25999.00, 25999.00, 3119.88);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '123123', 'admin@shop.com', 'admin', '2025-05-06 06:39:48'),
(2, 'edgar', '123123', 'edgar@gmail.com', 'user', '2025-05-13 04:59:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`),
  ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
