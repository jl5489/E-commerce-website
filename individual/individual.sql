-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2024 at 03:57 PM
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
-- Database: `individual`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `User_type` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Gender` varchar(50) DEFAULT NULL,
  `Birthdate` date DEFAULT NULL,
  `Address` varchar(50) DEFAULT NULL,
  `PhoneNumber` varchar(50) DEFAULT NULL,
  `Picture` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`username`, `password`, `User_type`, `Email`, `FirstName`, `LastName`, `Gender`, `Birthdate`, `Address`, `PhoneNumber`, `Picture`) VALUES
('1234', '1234', 'User', 'jiachin@gmail.com', 'Jia kae', 'Lai', 'Female', '2003-09-25', 'No1, Jalan Hope', '0174825750', ''),
('admin', 'admin', 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `username` varchar(50) NOT NULL,
  `ProductId` varchar(50) NOT NULL,
  `Quantity` int(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`username`, `ProductId`, `Quantity`) VALUES
('hhh', '4', 9),
('a', '2', 1),
('1234', '2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `username`, `order_date`, `product_id`, `quantity`) VALUES
(1, NULL, '2024-03-27 13:00:55', 2, 1),
(2, NULL, '2024-03-27 13:00:55', 3, 1),
(3, NULL, '2024-03-27 13:01:02', 2, 1),
(4, NULL, '2024-03-27 13:01:02', 3, 1),
(5, '0', '2024-03-27 13:02:19', 2, 1),
(6, '0', '2024-03-27 13:02:19', 3, 1),
(7, '1234', '2024-03-27 13:02:51', 5, 2),
(8, '1234', '2024-03-27 13:02:51', 2, 1),
(9, '1234', '2024-03-27 13:02:51', 3, 1),
(10, '1234', '2024-03-27 14:22:30', 5, 2),
(11, '1234', '2024-03-27 14:22:30', 2, 1),
(12, '1234', '2024-03-27 14:22:30', 3, 1),
(13, '1234', '2024-03-27 14:23:13', 2, 1),
(14, '1234', '2024-03-27 14:25:46', 2, 1),
(15, '1234', '2024-03-27 14:26:34', 2, 1),
(16, '1234', '2024-03-27 17:05:58', 3, 3),
(17, '1234', '2024-03-29 06:06:12', 4, 3),
(18, '1234', '2024-03-29 06:06:40', 8, 1),
(19, '1234', '2024-03-29 06:06:40', 7, 1),
(20, '0', '2024-03-30 08:48:29', 4, 4),
(21, '0', '2024-03-30 08:48:29', 3, 1),
(22, '0', '2024-03-30 08:51:41', 3, 1),
(23, '0', '2024-03-30 08:51:51', 2, 1),
(24, '0', '2024-03-30 08:52:10', 2, 1),
(25, 'a', '2024-03-30 08:53:51', 2, 1),
(26, '1234', '2024-03-30 11:17:06', 7, 1),
(27, '1234', '2024-03-30 11:17:06', 3, 3),
(28, 'jiawei', '2024-03-30 12:15:26', 2, 3),
(29, 'jiawei', '2024-03-30 12:15:26', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text DEFAULT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `product_category` varchar(100) DEFAULT NULL,
  `product_stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `product_price`, `product_image`, `product_category`, `product_stock`) VALUES
(2, 'AUSTRALIAN NEW ZEALAND MUTTON SHOULDER', 'Indulge in the rich and hearty flavor of Australian/New Zealand Mutton Shoulder (5479), a premium offering that brings the finest quality meat to your kitchen. Sourced from the pristine landscapes of Australia and New Zealand, this mutton shoulder boasts succulence and tenderness, promising a culinary experience that transcends ordinary meals. Ideal for slow roasting, braising, or creating flavorful stews, the 5479 cut ensures a depth of taste that is both savory and satisfying. Packed with convenience and freshness, this mutton shoulder is perfect for creating wholesome and delicious meals that celebrate the robust flavor of premium meat. Elevate your cooking endeavors with the superior taste of Australian/New Zealand Mutton Shoulder (5479) by ordering now and experiencing the epitome of culinary excellence.', 8.23, 'AUSTRALIAN NEW ZEALAND MUTTON SHOULDER.png', 'Meat', 15),
(3, '\r\nBRAZIL BEEF BLOCK', 'Experience the rich and flavorful Brazilian beef in a convenient block form. This high-quality beef is perfect for various culinary applications, from hearty stews to savory roasts. Elevate your meals with the premium taste and tenderness of Brazilian beef, making every dish a delightful experience.', 9.89, 'BRAZIL BEEF BLOCK.png', 'Meat', 25),
(4, 'MANGGA GOLDEN DRAGON', 'Indulge in the exotic sweetness of Mangga Golden Dragon, also known as Golden Dragon mango. This mango variety is celebrated for its golden skin and luscious, tropical flavor. Enjoy a taste of the tropics with the rich, juicy goodness of Golden Dragon mango in every bite.', 9.98, 'MANGGA GOLDEN DRAGON.png', 'Fruits', 10),
(5, 'BLUEBERRIES 125G', 'Enjoy the sweet and tangy taste of blueberries with this 125g pack of fresh blueberries. Blueberries are not only delicious but also packed with antioxidants and nutrients, making them a healthy and flavorful addition to your meals, snacks, or desserts.', 9.99, 'BLUEBERRIES 125G.png', 'Fruits', 7),
(6, 'MISTER POTATO CRISPS HONEY CHEESE 125G', 'Indulge in the irresistible snacking harmony of Mister Potato Crisps Honey Cheese 125g. These crispy potato crisps combine the sweetness of honey with the savory richness of cheese, creating a flavor that\'s both unique and utterly delicious. Whether you\'re enjoying them as a solo snack or sharing with friends, this 125g pack is perfect for satisfying your snack cravings. Elevate your snacking experience with Mister Potato Crisps Honey Cheese today.', 3.29, 'MISTER POTATO CRISPS HONEY CHEESE 125G.png', 'Snacks', 4),
(7, 'SUPER RING CHEESE 30X14G', 'Super Ring Cheese 30x14g offer an irresistible combination of cheesy flavor and a satisfying crunch. These iconic, ring-shaped snacks are loved by people of all ages. Perfectly cheesy and addictive, they make for an excellent snack option for parties, movie nights, or any time you\'re looking for a delicious and fun treat. With 30 individual packs, Super Ring Cheese ensures that you have plenty to share and enjoy, providing a burst of cheesy goodness in every bite. Indulge in the cheesy delight of Super Ring and satisfy your snack cravings with this classic favorite.', 13.69, 'SUPER RING CHEESE 30X14G.png', 'Snacks', 20),
(8, 'HONEY SWEET POTATO', 'Indonesia Honey Sweet Potato (Ubi Keledek Madu) 750g brings the unique and delightful flavor of Indonesian sweet potatoes to your kitchen. Known for their natural sweetness and versatility, these sweet potatoes are perfect for various culinary creations. Whether you\'re embracing Indonesian cuisine or simply looking for a wholesome and delicious ingredient, Ubi Keledek Madu is your perfect choice. Elevate your culinary adventures with the rich and natural sweetness of Indonesia Honey Sweet Potato (Ubi Keledek Madu). Add this 750g pack to your cart and infuse your meals with the authentic taste of Indonesia. Enjoy the goodness of nature\'s sweetness!', 8.99, 'HONEY SWEET POTATO.png', 'Vegetables', 4),
(9, 'TOMATO', 'Tomatoes are a versatile and nutrient-packed fruit known for their rich, juicy flavor. Whether used in salads, sauces, soups, or as a topping for sandwiches, tomatoes add a burst of freshness to your dishes. These vibrant red fruits are a good source of vitamins, minerals, and antioxidants, making them a healthy addition to your meals. Enjoy the delicious and vibrant taste of tomatoes in a variety of culinary creations.', 4.40, 'TOMATO.png', 'Vegetables', 6),
(10, 'CADBURY HAZEL NUTS 160G', 'Indulge in the rich and creamy taste of Cadbury Hazelnuts. This 160g chocolate bar is a delightful treat that combines the smooth sweetness of Cadbury chocolate with the satisfying crunch of roasted hazelnuts. Each bite offers a perfect balance of flavors and textures, making it a great choice for satisfying your sweet cravings. Whether you enjoy it as a snack or as a special treat, Cadbury Hazelnuts are sure to delight your taste buds with their irresistible combination of chocolate and nuts.', 6.49, 'CADBURY HAZEL NUTS 160G.png', 'Chocolates', 10),
(11, 'KIT KAT 2 FINGER COCOA SHAREBAG 12X17G', 'NESTLÉ KIT KAT 2 Finger is the perfect snack for any break time. It can be enjoyed with family and friends at home, or with colleagues during the day. Made from NESTLÉ COCOA PLAN sustainably sourced cocoa, it’s the ideal snack to enjoy as part of a balanced diet with only 90 calories per portion (1 portion = 1 individually wrapped bar). Just unwrap, break off a finger, snap it in two and savour the great tasting crispy wafer fingers covered with smooth milk chocolate.', 8.99, 'KIT KAT 2 FINGER COCOA SHAREBAG 12X17G.png', 'Chocolates', 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
