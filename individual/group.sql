-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 20, 2024 at 04:39 AM
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
-- Database: `group`
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
('1234', '2', 1),
('a', '2', 1),
('hhh', '4', 9);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `order_date` date DEFAULT NULL,
  `delivery` varchar(50) DEFAULT NULL,
  `shipping_fee` int(11) NOT NULL,
  `shipping_address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_products`
--

CREATE TABLE `order_products` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `customize` varchar(100) DEFAULT '0',
  `score` int(10) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_products`
--

INSERT INTO `order_products` (`order_id`, `product_id`, `quantity`, `customize`, `score`) VALUES
(1, 2, 1, '0', 0),
(2, 3, 1, '0', 0),
(3, 2, 1, '0', 0),
(4, 3, 1, '0', 0),
(5, 2, 1, '0', 0),
(6, 3, 1, '0', 0),
(7, 5, 2, '0', 0),
(8, 2, 1, '0', 0),
(9, 3, 1, '0', 0),
(10, 5, 2, '0', 0),
(11, 2, 1, '0', 0),
(12, 3, 1, '0', 0),
(13, 2, 1, '0', 0),
(14, 2, 1, '0', 0),
(15, 2, 1, '0', 0),
(16, 3, 3, '0', 0),
(17, 4, 3, '0', 0),
(18, 8, 1, '0', 0),
(19, 7, 1, '0', 0),
(20, 4, 4, '0', 0),
(21, 3, 1, '0', 0),
(22, 3, 1, '0', 0),
(23, 2, 1, '0', 0),
(24, 2, 1, '0', 0),
(25, 2, 1, '0', 0),
(26, 7, 1, '0', 0),
(27, 3, 3, '0', 0),
(28, 2, 3, '0', 0),
(29, 3, 1, '0', 0);

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
  `product_stock` int(11) DEFAULT 0,
  `sold` int(50) DEFAULT 0,
  `search` int(50) DEFAULT 0,
  `visit` int(50) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `product_price`, `product_image`, `product_category`, `product_stock`, `sold`, `search`, `visit`) VALUES
(2, 'AUSTRALIAN NEW ZEALAND MUTTON SHOULDER', 'Indulge in the rich and hearty flavor of Australian/New Zealand Mutton Shoulder (5479), a premium offering that brings the finest quality meat to your kitchen. Sourced from the pristine landscapes of Australia and New Zealand, this mutton shoulder boasts succulence and tenderness, promising a culinary experience that transcends ordinary meals. Ideal for slow roasting, braising, or creating flavorful stews, the 5479 cut ensures a depth of taste that is both savory and satisfying. Packed with convenience and freshness, this mutton shoulder is perfect for creating wholesome and delicious meals that celebrate the robust flavor of premium meat. Elevate your cooking endeavors with the superior taste of Australian/New Zealand Mutton Shoulder (5479) by ordering now and experiencing the epitome of culinary excellence.', 8.23, 'AUSTRALIAN NEW ZEALAND MUTTON SHOULDER.png', 'Meat', 15, 0, 0, 0),
(3, '\r\nBRAZIL BEEF BLOCK', 'Experience the rich and flavorful Brazilian beef in a convenient block form. This high-quality beef is perfect for various culinary applications, from hearty stews to savory roasts. Elevate your meals with the premium taste and tenderness of Brazilian beef, making every dish a delightful experience.', 9.89, 'BRAZIL BEEF BLOCK.png', 'Meat', 25, 0, 0, 0),
(4, 'MANGGA GOLDEN DRAGON', 'Indulge in the exotic sweetness of Mangga Golden Dragon, also known as Golden Dragon mango. This mango variety is celebrated for its golden skin and luscious, tropical flavor. Enjoy a taste of the tropics with the rich, juicy goodness of Golden Dragon mango in every bite.', 9.98, 'MANGGA GOLDEN DRAGON.png', 'Fruits', 10, 0, 0, 0),
(5, 'BLUEBERRIES 125G', 'Enjoy the sweet and tangy taste of blueberries with this 125g pack of fresh blueberries. Blueberries are not only delicious but also packed with antioxidants and nutrients, making them a healthy and flavorful addition to your meals, snacks, or desserts.', 9.99, 'BLUEBERRIES 125G.png', 'Fruits', 7, 0, 0, 0),
(6, 'MISTER POTATO CRISPS HONEY CHEESE 125G', 'Indulge in the irresistible snacking harmony of Mister Potato Crisps Honey Cheese 125g. These crispy potato crisps combine the sweetness of honey with the savory richness of cheese, creating a flavor that\'s both unique and utterly delicious. Whether you\'re enjoying them as a solo snack or sharing with friends, this 125g pack is perfect for satisfying your snack cravings. Elevate your snacking experience with Mister Potato Crisps Honey Cheese today.', 3.29, 'MISTER POTATO CRISPS HONEY CHEESE 125G.png', 'Snacks', 4, 0, 0, 0),
(7, 'SUPER RING CHEESE 30X14G', 'Super Ring Cheese 30x14g offer an irresistible combination of cheesy flavor and a satisfying crunch. These iconic, ring-shaped snacks are loved by people of all ages. Perfectly cheesy and addictive, they make for an excellent snack option for parties, movie nights, or any time you\'re looking for a delicious and fun treat. With 30 individual packs, Super Ring Cheese ensures that you have plenty to share and enjoy, providing a burst of cheesy goodness in every bite. Indulge in the cheesy delight of Super Ring and satisfy your snack cravings with this classic favorite.', 13.69, 'SUPER RING CHEESE 30X14G.png', 'Snacks', 20, 0, 0, 0),
(8, 'HONEY SWEET POTATO', 'Indonesia Honey Sweet Potato (Ubi Keledek Madu) 750g brings the unique and delightful flavor of Indonesian sweet potatoes to your kitchen. Known for their natural sweetness and versatility, these sweet potatoes are perfect for various culinary creations. Whether you\'re embracing Indonesian cuisine or simply looking for a wholesome and delicious ingredient, Ubi Keledek Madu is your perfect choice. Elevate your culinary adventures with the rich and natural sweetness of Indonesia Honey Sweet Potato (Ubi Keledek Madu). Add this 750g pack to your cart and infuse your meals with the authentic taste of Indonesia. Enjoy the goodness of nature\'s sweetness!', 8.99, 'HONEY SWEET POTATO.png', 'Vegetables', 4, 0, 0, 0),
(9, 'TOMATO', 'Tomatoes are a versatile and nutrient-packed fruit known for their rich, juicy flavor. Whether used in salads, sauces, soups, or as a topping for sandwiches, tomatoes add a burst of freshness to your dishes. These vibrant red fruits are a good source of vitamins, minerals, and antioxidants, making them a healthy addition to your meals. Enjoy the delicious and vibrant taste of tomatoes in a variety of culinary creations.', 4.40, 'TOMATO.png', 'Vegetables', 6, 0, 0, 0),
(10, 'CADBURY HAZEL NUTS 160G', 'Indulge in the rich and creamy taste of Cadbury Hazelnuts. This 160g chocolate bar is a delightful treat that combines the smooth sweetness of Cadbury chocolate with the satisfying crunch of roasted hazelnuts. Each bite offers a perfect balance of flavors and textures, making it a great choice for satisfying your sweet cravings. Whether you enjoy it as a snack or as a special treat, Cadbury Hazelnuts are sure to delight your taste buds with their irresistible combination of chocolate and nuts.', 6.49, 'CADBURY HAZEL NUTS 160G.png', 'Chocolates', 10, 0, 0, 0),
(11, 'KIT KAT 2 FINGER COCOA SHAREBAG 12X17G', 'NESTLÉ KIT KAT 2 Finger is the perfect snack for any break time. It can be enjoyed with family and friends at home, or with colleagues during the day. Made from NESTLÉ COCOA PLAN sustainably sourced cocoa, it’s the ideal snack to enjoy as part of a balanced diet with only 90 calories per portion (1 portion = 1 individually wrapped bar). Just unwrap, break off a finger, snap it in two and savour the great tasting crispy wafer fingers covered with smooth milk chocolate.', 8.99, 'KIT KAT 2 FINGER COCOA SHAREBAG 12X17G.png', 'Chocolates', 30, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`username`,`ProductId`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_products`
--
ALTER TABLE `order_products`
  ADD PRIMARY KEY (`order_id`);

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
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_products`
--
ALTER TABLE `order_products`
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
-- Constraints for table `order_products`
--
ALTER TABLE `order_products`
  ADD CONSTRAINT `order_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
