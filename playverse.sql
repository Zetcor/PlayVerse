-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 10:12 AM
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
-- Database: `playverse`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthdate` date NOT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `street` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `zipcode` varchar(4) NOT NULL,
  `country` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `gender`, `birthdate`, `contact_no`, `email`, `username`, `password`, `street`, `city`, `province`, `zipcode`, `country`) VALUES
(1, 'Vince Cortez', 'Male', '2000-12-09', '09123456789', 'vince@gmail.com', 'Zetcor', 'Zetcor123+', 'apple', 'QC', 'Davao', '1111', 'Philippines'),
(2, 'Vince Nicholai Cortez', 'Male', '2005-04-23', '09089149744', 'vincecholai@gmail.com', 'XxZetcor_Pogi_23xX', 'VincePogi123+', 'Macclessfield Street', 'Cainta', 'Rizal', '1900', 'Philippines');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `date_added` date DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `manufacturer` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `category`, `stock`, `date_added`, `rating`, `manufacturer`, `price`, `image`) VALUES
(1, 'LogiTech G502 Hero', 'The Logitech G502 Hero is a high-performance wired gaming mouse built for precision and speed perfect for gaming.', 'Input Devices', 120, '2018-05-21', 89, 'LogiTech', 2799.00, 'g502.jpg'),
(2, 'Razer Huntsman V2', 'The Razer Huntsman V2 is an optical mechanical RGB gaming keyboard offering lightning-fast key response.', 'Input Devices', 75, '2021-11-03', 94, 'Razer', 7279.00, 'huntsman.jpg'),
(3, 'Corsair K55 RGB Pro', 'The Corsair K55 RGB Pro is a quiet membrane keyboard featuring dynamic RGB backlighting and dedicated media controls.', 'Input Devices', 40, '2017-03-14', 80, 'Corsair', 3359.00, 'k55.png'),
(4, 'SteelSeries Rival 5', 'The SteelSeries Rival 5 is a precision gaming mouse designed for versatility across multiple gaming genres.', 'Input Devices', 85, '2019-08-28', 84, 'SteelSeries', 2519.00, 'rival5.png'),
(5, 'Cooler Master MM711', 'The Cooler Master MM711 is a lightweight honeycomb gaming mouse equipped with customizable RGB lighting.', 'Input Devices', 65, '2020-01-09', 77, 'Cooler Master', 2239.00, 'mm711.png'),
(6, 'Intel Core i9-13900K', 'The Intel Core i9-13900K is a 13th-generation CPU with 24 cores, optimized for gaming and content creation.', 'Processors', 30, '2022-09-22', 99, 'Intel', 33039.00, 'i9.jpg'),
(7, 'AMD Ryzen 9 7950X', 'The AMD Ryzen 9 7950X is a 16-core AM5 processor that delivers outstanding multithreaded performance.', 'Processors', 25, '2022-10-12', 97, 'AMD', 39144.00, 'r9.png'),
(8, 'Intel Core i5-12600K', 'The Intel Core i5-12600K is a 12th-generation 10-core processor that offers a balanced mix of performance and efficiency.', 'Processors', 45, '2021-06-30', 87, 'Intel', 16239.00, 'i5.png'),
(9, 'AMD Ryzen 5 7600X', 'The AMD Ryzen 5 7600X is a 6-core processor designed for efficient gaming and multitasking.', 'Processors', 50, '2020-02-18', 84, 'AMD', 13944.00, 'r5.jpg'),
(10, 'Intel Core i3-13100F', 'The Intel Core i3-13100F is a budget-friendly 4-core CPU ideal for entry-level gaming builds.', 'Processors', 60, '2017-12-05', 73, 'Intel', 6159.00, 'i3.jpg'),
(11, 'NVIDIA RTX 4090', 'The NVIDIA RTX 4090 is a flagship graphics card built for 4K gaming and extreme performance.', 'Graphics Cards', 15, '2022-10-14', 100, 'NVIDIA', 89544.00, 'rtx4090.png'),
(12, 'AMD Radeon RX 7900 XT', 'The AMD Radeon RX 7900 XT is a high-end graphics card with 20GB of GDDR6 memory.', 'Graphics Cards', 20, '2022-12-10', 90, 'AMD', 50399.00, 'rx7900.png'),
(13, 'NVIDIA RTX 4070 Ti', 'The NVIDIA RTX 4070 Ti is a mid-range GPU with DLSS 3 support for next-gen gaming performance.', 'Graphics Cards', 35, '2021-05-07', 88, 'NVIDIA', 44744.00, 'rtx4070.jpg'),
(14, 'ASUS TUF RX 7700 XT', 'The ASUS TUF RX 7700 XT is a durable performance graphics card featuring advanced cooling.', 'Graphics Cards', 28, '2019-03-29', 83, 'ASUS', 26824.00, 'rx7700.png'),
(15, 'Zotac RTX 3060', 'The Zotac RTX 3060 Twin Edge is an entry-level ray tracing GPU ideal for 1080p gaming.', 'Graphics Cards', 50, '2020-08-16', 76, 'Zotac', 17919.00, 'rtx3060.jpg'),
(16, 'Corsair RM750x', 'The Corsair RM750x is a fully modular 750W power supply designed for silent operation.', 'Power Supply', 32, '2018-11-27', 92, 'Corsair', 7279.00, 'rm750x.jpg'),
(17, 'Seasonic Focus GX-650', 'The Seasonic Focus GX-650 is a reliable 650W power supply unit certified with 80+ Gold efficiency.', 'Power Supply', 45, '2016-04-11', 89, 'Seasonic', 5599.00, 'gx650.png'),
(18, 'EVGA 600 BR', 'The EVGA 600 BR is a budget-friendly 600W PSU with 80+ Bronze certification for basic builds.', 'Power Supply', 60, '2015-09-22', 72, 'EVGA', 3079.00, 'evga.png'),
(19, 'Cooler Master MWE V2', 'The Cooler Master MWE V2 750W is a semi-modular PSU offering stable performance for gaming systems.', 'Power Supply', 38, '2017-07-03', 81, 'Cooler Master', 4759.00, 'mwev2.jpg'),
(20, 'Be Quiet! Pure Power 11', 'The Be Quiet! Pure Power 11 500W is a silent power supply designed for quiet and efficient operation.', 'Power Supply', 52, '2019-10-15', 78, 'Be Quiet!', 3864.00, 'bequiet.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `shipping_address` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `mode_of_payment` enum('Credit/Debit','Paypal','Gcash') NOT NULL,
  `transaction_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `customer_id`, `cart_id`, `shipping_address`, `total_amount`, `mode_of_payment`, `transaction_date`) VALUES
(1, 1, 1, 'apple, QC, Davao, Philippines 1111', 125937.28, 'Paypal', '2025-07-03'),
(2, 1, 1, 'apple, QC, Davao, Philippines 1111', 8152.48, 'Paypal', '2025-07-03'),
(3, 2, 2, 'Macclessfield Street, Cainta, Rizal, Philippines 1900', 37003.68, 'Gcash', '2025-07-03'),
(4, 2, 2, 'Macclessfield Street, Cainta, Rizal, Philippines 1900', 192292.80, 'Paypal', '2025-07-03');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 2, 2799.00),
(2, 1, 2, 1, 7279.00),
(3, 1, 10, 1, 6159.00),
(4, 1, 11, 1, 89544.00),
(5, 1, 20, 1, 3864.00),
(6, 2, 2, 1, 7279.00),
(7, 3, 6, 1, 33039.00),
(8, 4, 5, 3, 2239.00),
(9, 4, 1, 3, 2799.00),
(10, 4, 7, 4, 39144.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `cart_id` (`cart_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
