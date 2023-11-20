-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2023 at 05:49 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pizza_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `image` varchar(100) NOT NULL,
  `regular_price` int(255) DEFAULT NULL,
  `medium_price` int(255) DEFAULT NULL,
  `large_price` int(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `regular_price`, `medium_price`, `large_price`, `category`) VALUES
(3, 'Cheesy Delight', 0, 'pizza-1.jpg', 320, 430, 500, 'Pizza'),
(4, 'Pepperoni Perfection ', 0, 'pizza-13.jpg', 450, 530, 600, 'Pizza'),
(5, 'Cheese & Tomato', 0, 'pizza-3.jpg', 380, 450, 560, 'Pizza'),
(6, 'Farmhouse', 0, 'pizza-12.jpg', 420, 510, 590, 'Pizza'),
(7, 'Mushroom Marvel', 0, 'pizza-5.jpg', 510, 615, 700, 'Pizza'),
(8, 'Margherita Classic ', 0, 'pizza-6.jpg', 290, 360, 440, 'Pizza'),
(9, 'Hawaiian Retreat', 0, 'pizza-7.jpg', 570, 630, 708, 'Pizza'),
(10, 'Veggie Delight', 0, 'pizza-8.jpg', 430, 500, 618, 'Pizza'),
(11, 'Mediterranean Harvest', 0, 'pizza-9.jpg', 450, 530, 650, 'Pizza'),
(17, 'Arrabiata Pasta', 0, 'pasta1.jpg', 280, 0, 0, 'Sides'),
(18, 'Alfredo Pasta', 0, 'pasta2.jpg', 280, 0, 0, 'Sides'),
(19, 'Pesto Pasta', 0, 'pasta3.jpg', 250, 0, 0, 'Sides'),
(20, 'Potato Fries', 0, 'fries.jpg', 150, 0, 0, 'Sides'),
(21, 'Loaded Nachos', 0, 'nachos.jpg', 200, 0, 0, 'Sides'),
(22, 'Zucchini Salad', 0, 'salad.jpg', 230, 0, 0, 'Sides'),
(23, 'Coca-Cola', 0, 'coke.jpg', 60, 0, 0, 'Beverages'),
(24, 'Fanta', 0, 'fanta.jpg', 60, 0, 0, 'Beverages'),
(25, 'Sprite', 0, 'sprite.jpg', 60, 0, 0, 'Beverages'),
(26, 'Choco Lava Cake', 0, 'cake1.jpg', 150, 0, 0, 'Desserts'),
(27, 'Cheesecake', 0, 'cake2.jpg', 180, 0, 0, 'Desserts'),
(28, 'Choco Sundae', 0, 'sundae.jpg', 200, 0, 0, 'Desserts');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
