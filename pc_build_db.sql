-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 08:02 AM
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
-- Database: `pc_build_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `full_name`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', 'admin123', 'System Administrator', '2025-09-25 10:51:50');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `masterbuild`
--

CREATE TABLE `masterbuild` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `build_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `masterbuild_items`
--

CREATE TABLE `masterbuild_items` (
  `id` int(11) NOT NULL,
  `masterbuild_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `socket` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `ram_type` varchar(20) DEFAULT NULL,
  `wattage` int(11) DEFAULT NULL,
  `tdp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `brand`, `socket`, `price`, `ram_type`, `wattage`, `tdp`) VALUES
(1, 'Intel Core i3-12100F', 'CPU', 'Intel', 'LGA1700', 7000.00, NULL, NULL, 58),
(2, 'Intel Core i5-12400F', 'CPU', 'Intel', 'LGA1700', 12000.00, NULL, NULL, 65),
(3, 'Intel Core i7-12700K', 'CPU', 'Intel', 'LGA1700', 20000.00, NULL, NULL, 125),
(4, 'Intel Core i9-12900K', 'CPU', 'Intel', 'LGA1700', 30000.00, NULL, NULL, 125),
(5, 'Intel Pentium G7400', 'CPU', 'Intel', 'LGA1700', 5000.00, NULL, NULL, 46),
(6, 'Intel Core i5-13600K', 'CPU', 'Intel', 'LGA1700', 22000.00, NULL, NULL, 125),
(7, 'Intel Core i7-13700K', 'CPU', 'Intel', 'LGA1700', 28000.00, NULL, NULL, 125),
(8, 'Intel Core i9-13900K', 'CPU', 'Intel', 'LGA1700', 38000.00, NULL, NULL, 125),
(9, 'AMD Ryzen 3 4100', 'CPU', 'AMD', 'AM4', 6000.00, NULL, NULL, 65),
(10, 'AMD Ryzen 5 5600', 'CPU', 'AMD', 'AM4', 9500.00, NULL, NULL, 65),
(11, 'AMD Ryzen 5 5600X', 'CPU', 'AMD', 'AM4', 11000.00, NULL, NULL, 65),
(12, 'AMD Ryzen 7 5700X', 'CPU', 'AMD', 'AM4', 15000.00, NULL, NULL, 65),
(13, 'AMD Ryzen 7 5800X3D', 'CPU', 'AMD', 'AM4', 20000.00, NULL, NULL, 105),
(14, 'AMD Ryzen 9 5900X', 'CPU', 'AMD', 'AM4', 25000.00, NULL, NULL, 105),
(15, 'AMD Ryzen 9 5950X', 'CPU', 'AMD', 'AM4', 30000.00, NULL, NULL, 105),
(16, 'AMD Ryzen 5 7600', 'CPU', 'AMD', 'AM5', 15000.00, NULL, NULL, 65),
(17, 'AMD Ryzen 5 7600X', 'CPU', 'AMD', 'AM5', 17000.00, NULL, NULL, 105),
(18, 'AMD Ryzen 7 7700X', 'CPU', 'AMD', 'AM5', 23000.00, NULL, NULL, 105),
(19, 'AMD Ryzen 9 7900X', 'CPU', 'AMD', 'AM5', 32000.00, NULL, NULL, 170),
(20, 'AMD Ryzen 9 7950X', 'CPU', 'AMD', 'AM5', 40000.00, NULL, NULL, 170),
(21, 'ASUS Prime H610M-A', 'Motherboard', 'ASUS', 'LGA1700', 5000.00, 'DDR4', NULL, NULL),
(22, 'Gigabyte B660M DS3H', 'Motherboard', 'Gigabyte', 'LGA1700', 7000.00, 'DDR4', NULL, NULL),
(23, 'MSI B660M Mortar', 'Motherboard', 'MSI', 'LGA1700', 9000.00, 'DDR4', NULL, NULL),
(24, 'ASUS TUF Gaming Z690-Plus', 'Motherboard', 'ASUS', 'LGA1700', 15000.00, 'DDR5', NULL, NULL),
(25, 'Gigabyte Z790 Aorus Elite', 'Motherboard', 'Gigabyte', 'LGA1700', 20000.00, 'DDR5', NULL, NULL),
(26, 'MSI MAG B550M Bazooka', 'Motherboard', 'MSI', 'AM4', 6000.00, 'DDR4', NULL, NULL),
(27, 'ASUS Prime B550-Plus', 'Motherboard', 'ASUS', 'AM4', 8000.00, 'DDR4', NULL, NULL),
(28, 'Gigabyte X570 Aorus Elite', 'Motherboard', 'Gigabyte', 'AM4', 12000.00, 'DDR4', NULL, NULL),
(29, 'MSI MPG X570 Gaming Edge', 'Motherboard', 'MSI', 'AM4', 14000.00, 'DDR4', NULL, NULL),
(30, 'ASUS ROG Strix B550-F', 'Motherboard', 'ASUS', 'AM4', 10000.00, 'DDR4', NULL, NULL),
(31, 'MSI PRO B650M-P', 'Motherboard', 'MSI', 'AM5', 10000.00, 'DDR5', NULL, NULL),
(32, 'ASUS Prime B650M-A', 'Motherboard', 'ASUS', 'AM5', 12000.00, 'DDR5', NULL, NULL),
(33, 'Gigabyte B650 Aorus Elite', 'Motherboard', 'Gigabyte', 'AM5', 15000.00, 'DDR5', NULL, NULL),
(34, 'MSI MPG B650 Tomahawk', 'Motherboard', 'MSI', 'AM5', 17000.00, 'DDR5', NULL, NULL),
(35, 'ASUS ROG Strix X670E-F', 'Motherboard', 'ASUS', 'AM5', 25000.00, 'DDR5', NULL, NULL),
(36, 'Gigabyte X670 Aorus Master', 'Motherboard', 'Gigabyte', 'AM5', 28000.00, 'DDR5', NULL, NULL),
(37, 'MSI MEG X670E ACE', 'Motherboard', 'MSI', 'AM5', 30000.00, 'DDR5', NULL, NULL),
(38, 'ASUS ROG Crosshair X670E Hero', 'Motherboard', 'ASUS', 'AM5', 35000.00, 'DDR5', NULL, NULL),
(39, 'Gigabyte B550M DS3H', 'Motherboard', 'Gigabyte', 'AM4', 5500.00, 'DDR4', NULL, NULL),
(40, 'ASRock B550 Steel Legend', 'Motherboard', 'ASRock', 'AM4', 9500.00, 'DDR4', NULL, NULL),
(41, 'Corsair Vengeance LPX 16GB DDR4', 'RAM', 'Corsair', NULL, 3500.00, 'DDR4', NULL, NULL),
(42, 'G.Skill Ripjaws V 16GB DDR4', 'RAM', 'G.Skill', NULL, 3600.00, 'DDR4', NULL, NULL),
(43, 'Kingston Fury Beast 16GB DDR4', 'RAM', 'Kingston', NULL, 3400.00, 'DDR4', NULL, NULL),
(44, 'Team T-Force Vulcan Z 16GB DDR4', 'RAM', 'Team', NULL, 3300.00, 'DDR4', NULL, NULL),
(45, 'Patriot Viper Steel 16GB DDR4', 'RAM', 'Patriot', NULL, 3400.00, 'DDR4', NULL, NULL),
(46, 'Corsair Dominator Platinum 32GB DDR4', 'RAM', 'Corsair', NULL, 8000.00, 'DDR4', NULL, NULL),
(47, 'G.Skill Trident Z RGB 32GB DDR4', 'RAM', 'G.Skill', NULL, 7500.00, 'DDR4', NULL, NULL),
(48, 'Kingston Fury Renegade 32GB DDR4', 'RAM', 'Kingston', NULL, 7700.00, 'DDR4', NULL, NULL),
(49, 'Corsair Vengeance DDR5 32GB', 'RAM', 'Corsair', NULL, 12000.00, 'DDR5', NULL, NULL),
(50, 'G.Skill Trident Z5 RGB 32GB DDR5', 'RAM', 'G.Skill', NULL, 12500.00, 'DDR5', NULL, NULL),
(51, 'Kingston Fury Beast 32GB DDR5', 'RAM', 'Kingston', NULL, 11800.00, 'DDR5', NULL, NULL),
(52, 'Team T-Force Delta RGB 32GB DDR5', 'RAM', 'Team', NULL, 12200.00, 'DDR5', NULL, NULL),
(53, 'Corsair Dominator Platinum 64GB DDR5', 'RAM', 'Corsair', NULL, 22000.00, 'DDR5', NULL, NULL),
(54, 'G.Skill Trident Z5 RGB 64GB DDR5', 'RAM', 'G.Skill', NULL, 21500.00, 'DDR5', NULL, NULL),
(55, 'Kingston Fury Renegade 64GB DDR5', 'RAM', 'Kingston', NULL, 21800.00, 'DDR5', NULL, NULL),
(56, 'Patriot Viper Venom 32GB DDR5', 'RAM', 'Patriot', NULL, 11900.00, 'DDR5', NULL, NULL),
(57, 'ADATA XPG Lancer RGB 32GB DDR5', 'RAM', 'ADATA', NULL, 12100.00, 'DDR5', NULL, NULL),
(58, 'Crucial DDR5 32GB Kit', 'RAM', 'Crucial', NULL, 11500.00, 'DDR5', NULL, NULL),
(59, 'Corsair Vengeance DDR5 64GB', 'RAM', 'Corsair', NULL, 23000.00, 'DDR5', NULL, NULL),
(60, 'G.Skill Trident Z5 Neo 64GB DDR5', 'RAM', 'G.Skill', NULL, 22500.00, 'DDR5', NULL, NULL),
(61, 'Samsung 970 EVO Plus 500GB NVMe SSD', 'Storage', 'Samsung', NULL, 3500.00, NULL, NULL, NULL),
(62, 'Samsung 970 EVO Plus 1TB NVMe SSD', 'Storage', 'Samsung', NULL, 6000.00, NULL, NULL, NULL),
(63, 'Samsung 980 PRO 1TB NVMe SSD', 'Storage', 'Samsung', NULL, 7000.00, NULL, NULL, NULL),
(64, 'WD Blue 500GB SATA SSD', 'Storage', 'Western Digital', NULL, 2800.00, NULL, NULL, NULL),
(65, 'WD Blue 1TB SATA SSD', 'Storage', 'Western Digital', NULL, 4500.00, NULL, NULL, NULL),
(66, 'WD Black SN850X 1TB NVMe SSD', 'Storage', 'Western Digital', NULL, 8000.00, NULL, NULL, NULL),
(67, 'Crucial MX500 500GB SATA SSD', 'Storage', 'Crucial', NULL, 3000.00, NULL, NULL, NULL),
(68, 'Crucial MX500 1TB SATA SSD', 'Storage', 'Crucial', NULL, 5200.00, NULL, NULL, NULL),
(69, 'Crucial P5 Plus 1TB NVMe SSD', 'Storage', 'Crucial', NULL, 6500.00, NULL, NULL, NULL),
(70, 'Kingston A2000 500GB NVMe SSD', 'Storage', 'Kingston', NULL, 3200.00, NULL, NULL, NULL),
(71, 'Kingston A2000 1TB NVMe SSD', 'Storage', 'Kingston', NULL, 5400.00, NULL, NULL, NULL),
(72, 'Seagate Barracuda 1TB HDD', 'Storage', 'Seagate', NULL, 2500.00, NULL, NULL, NULL),
(73, 'Seagate Barracuda 2TB HDD', 'Storage', 'Seagate', NULL, 3500.00, NULL, NULL, NULL),
(74, 'Seagate IronWolf 4TB HDD', 'Storage', 'Seagate', NULL, 7500.00, NULL, NULL, NULL),
(75, 'Toshiba P300 1TB HDD', 'Storage', 'Toshiba', NULL, 2700.00, NULL, NULL, NULL),
(76, 'Toshiba P300 2TB HDD', 'Storage', 'Toshiba', NULL, 3600.00, NULL, NULL, NULL),
(77, 'ADATA SU800 512GB SSD', 'Storage', 'ADATA', NULL, 3100.00, NULL, NULL, NULL),
(78, 'ADATA SU800 1TB SSD', 'Storage', 'ADATA', NULL, 5000.00, NULL, NULL, NULL),
(79, 'Intel 670p 1TB NVMe SSD', 'Storage', 'Intel', NULL, 5900.00, NULL, NULL, NULL),
(80, 'Intel 670p 2TB NVMe SSD', 'Storage', 'Intel', NULL, 11000.00, NULL, NULL, NULL),
(81, 'NVIDIA GeForce GTX 1650', 'GPU', 'NVIDIA', NULL, 10000.00, NULL, NULL, 75),
(82, 'NVIDIA GeForce GTX 1660 Super', 'GPU', 'NVIDIA', NULL, 15000.00, NULL, NULL, 125),
(83, 'NVIDIA GeForce RTX 2060', 'GPU', 'NVIDIA', NULL, 20000.00, NULL, NULL, 160),
(84, 'NVIDIA GeForce RTX 3060', 'GPU', 'NVIDIA', NULL, 25000.00, NULL, NULL, 170),
(85, 'NVIDIA GeForce RTX 3060 Ti', 'GPU', 'NVIDIA', NULL, 30000.00, NULL, NULL, 200),
(86, 'NVIDIA GeForce RTX 3070', 'GPU', 'NVIDIA', NULL, 40000.00, NULL, NULL, 220),
(87, 'NVIDIA GeForce RTX 3080', 'GPU', 'NVIDIA', NULL, 55000.00, NULL, NULL, 320),
(88, 'NVIDIA GeForce RTX 3090', 'GPU', 'NVIDIA', NULL, 80000.00, NULL, NULL, 350),
(89, 'NVIDIA GeForce RTX 4070', 'GPU', 'NVIDIA', NULL, 45000.00, NULL, NULL, 200),
(90, 'NVIDIA GeForce RTX 4080', 'GPU', 'NVIDIA', NULL, 80000.00, NULL, NULL, 320),
(91, 'NVIDIA GeForce RTX 4090', 'GPU', 'NVIDIA', NULL, 120000.00, NULL, NULL, 450),
(92, 'AMD Radeon RX 6500 XT', 'GPU', 'AMD', NULL, 12000.00, NULL, NULL, 107),
(93, 'AMD Radeon RX 6600', 'GPU', 'AMD', NULL, 18000.00, NULL, NULL, 132),
(94, 'AMD Radeon RX 6700 XT', 'GPU', 'AMD', NULL, 30000.00, NULL, NULL, 230),
(95, 'AMD Radeon RX 6800', 'GPU', 'AMD', NULL, 40000.00, NULL, NULL, 250),
(96, 'AMD Radeon RX 6800 XT', 'GPU', 'AMD', NULL, 48000.00, NULL, NULL, 300),
(97, 'AMD Radeon RX 6900 XT', 'GPU', 'AMD', NULL, 65000.00, NULL, NULL, 300),
(98, 'AMD Radeon RX 7600', 'GPU', 'AMD', NULL, 20000.00, NULL, NULL, 165),
(99, 'AMD Radeon RX 7700 XT', 'GPU', 'AMD', NULL, 35000.00, NULL, NULL, 245),
(100, 'Intel Arc A770', 'GPU', 'Intel', NULL, 25000.00, NULL, NULL, 225),
(101, 'Corsair CV450 450W 80+ Bronze', 'PSU', 'Corsair', NULL, 2500.00, NULL, 450, NULL),
(102, 'Corsair CV550 550W 80+ Bronze', 'PSU', 'Corsair', NULL, 3000.00, NULL, 550, NULL),
(103, 'Corsair CV650 650W 80+ Bronze', 'PSU', 'Corsair', NULL, 3500.00, NULL, 650, NULL),
(104, 'Corsair RM750x 750W 80+ Gold', 'PSU', 'Corsair', NULL, 5500.00, NULL, 750, NULL),
(105, 'Corsair RM850x 850W 80+ Gold', 'PSU', 'Corsair', NULL, 6500.00, NULL, 850, NULL),
(106, 'EVGA 500W 80+ White', 'PSU', 'EVGA', NULL, 2800.00, NULL, 500, NULL),
(107, 'EVGA 600W 80+ Bronze', 'PSU', 'EVGA', NULL, 3200.00, NULL, 600, NULL),
(108, 'EVGA 700W 80+ Bronze', 'PSU', 'EVGA', NULL, 3800.00, NULL, 700, NULL),
(109, 'Seasonic S12III 550W 80+ Bronze', 'PSU', 'Seasonic', NULL, 3500.00, NULL, 550, NULL),
(110, 'Seasonic S12III 650W 80+ Bronze', 'PSU', 'Seasonic', NULL, 4200.00, NULL, 650, NULL),
(111, 'Seasonic Focus GX-750 80+ Gold', 'PSU', 'Seasonic', NULL, 6000.00, NULL, 750, NULL),
(112, 'Seasonic Focus GX-850 80+ Gold', 'PSU', 'Seasonic', NULL, 7000.00, NULL, 850, NULL),
(113, 'Cooler Master MWE 550W 80+ Bronze', 'PSU', 'Cooler Master', NULL, 3300.00, NULL, 550, NULL),
(114, 'Cooler Master MWE 650W 80+ Bronze', 'PSU', 'Cooler Master', NULL, 3800.00, NULL, 650, NULL),
(115, 'Cooler Master MWE 750W 80+ Bronze', 'PSU', 'Cooler Master', NULL, 4300.00, NULL, 750, NULL),
(116, 'Cooler Master V850 850W 80+ Gold', 'PSU', 'Cooler Master', NULL, 7200.00, NULL, 850, NULL),
(117, 'Thermaltake Smart 500W 80+ White', 'PSU', 'Thermaltake', NULL, 2900.00, NULL, 500, NULL),
(118, 'Thermaltake Smart 600W 80+ White', 'PSU', 'Thermaltake', NULL, 3400.00, NULL, 600, NULL),
(119, 'Thermaltake Toughpower GF1 750W 80+ Gold', 'PSU', 'Thermaltake', NULL, 5800.00, NULL, 750, NULL),
(120, 'Thermaltake Toughpower GF1 850W 80+ Gold', 'PSU', 'Thermaltake', NULL, 6800.00, NULL, 850, NULL),
(121, 'Cooler Master Hyper 212 Black Edition', 'CPU Cooler', 'Cooler Master', NULL, 1500.00, NULL, NULL, NULL),
(122, 'Noctua NH-U12S', 'CPU Cooler', 'Noctua', NULL, 3000.00, NULL, NULL, NULL),
(123, 'Noctua NH-D15', 'CPU Cooler', 'Noctua', NULL, 4500.00, NULL, NULL, NULL),
(124, 'be quiet! Pure Rock 2', 'CPU Cooler', 'be quiet!', NULL, 2500.00, NULL, NULL, NULL),
(125, 'be quiet! Dark Rock Pro 4', 'CPU Cooler', 'be quiet!', NULL, 5500.00, NULL, NULL, NULL),
(126, 'DeepCool Gammaxx 400', 'CPU Cooler', 'DeepCool', NULL, 1200.00, NULL, NULL, NULL),
(127, 'DeepCool AK620', 'CPU Cooler', 'DeepCool', NULL, 4000.00, NULL, NULL, NULL),
(128, 'Arctic Freezer 34 eSports DUO', 'CPU Cooler', 'Arctic', NULL, 2800.00, NULL, NULL, NULL),
(129, 'Arctic Liquid Freezer II 240', 'CPU Cooler', 'Arctic', NULL, 6000.00, NULL, NULL, NULL),
(130, 'NZXT Kraken X53 240mm AIO', 'CPU Cooler', 'NZXT', NULL, 7000.00, NULL, NULL, NULL),
(131, 'NZXT Kraken X63 280mm AIO', 'CPU Cooler', 'NZXT', NULL, 9000.00, NULL, NULL, NULL),
(132, 'NZXT Kraken X73 360mm AIO', 'CPU Cooler', 'NZXT', NULL, 11000.00, NULL, NULL, NULL),
(133, 'Corsair H100i RGB Platinum 240mm', 'CPU Cooler', 'Corsair', NULL, 8000.00, NULL, NULL, NULL),
(134, 'Corsair H150i RGB Platinum 360mm', 'CPU Cooler', 'Corsair', NULL, 11000.00, NULL, NULL, NULL),
(135, 'Cooler Master MasterLiquid ML240L', 'CPU Cooler', 'Cooler Master', NULL, 5000.00, NULL, NULL, NULL),
(136, 'Cooler Master MasterLiquid ML360R', 'CPU Cooler', 'Cooler Master', NULL, 8500.00, NULL, NULL, NULL),
(137, 'Thermaltake TH240 ARGB Sync', 'CPU Cooler', 'Thermaltake', NULL, 5200.00, NULL, NULL, NULL),
(138, 'Thermaltake TH360 ARGB Sync', 'CPU Cooler', 'Thermaltake', NULL, 7800.00, NULL, NULL, NULL),
(139, 'Lian Li Galahad 240 AIO', 'CPU Cooler', 'Lian Li', NULL, 7200.00, NULL, NULL, NULL),
(140, 'Lian Li Galahad 360 AIO', 'CPU Cooler', 'Lian Li', NULL, 10500.00, NULL, NULL, NULL),
(141, 'NZXT H510', 'Case', 'NZXT', NULL, 4000.00, NULL, NULL, NULL),
(142, 'NZXT H510 Elite', 'Case', 'NZXT', NULL, 7000.00, NULL, NULL, NULL),
(143, 'NZXT H7 Flow', 'Case', 'NZXT', NULL, 6500.00, NULL, NULL, NULL),
(144, 'NZXT H7 Elite', 'Case', 'NZXT', NULL, 9500.00, NULL, NULL, NULL),
(145, 'Corsair 4000D Airflow', 'Case', 'Corsair', NULL, 5000.00, NULL, NULL, NULL),
(146, 'Corsair 5000D Airflow', 'Case', 'Corsair', NULL, 7500.00, NULL, NULL, NULL),
(147, 'Corsair iCUE 220T RGB', 'Case', 'Corsair', NULL, 5200.00, NULL, NULL, NULL),
(148, 'Corsair Crystal 680X RGB', 'Case', 'Corsair', NULL, 12000.00, NULL, NULL, NULL),
(149, 'Cooler Master NR200P', 'Case', 'Cooler Master', NULL, 4500.00, NULL, NULL, NULL),
(150, 'Cooler Master TD500 Mesh', 'Case', 'Cooler Master', NULL, 6000.00, NULL, NULL, NULL),
(151, 'Cooler Master H500', 'Case', 'Cooler Master', NULL, 7000.00, NULL, NULL, NULL),
(152, 'Cooler Master Cosmos C700P', 'Case', 'Cooler Master', NULL, 16000.00, NULL, NULL, NULL),
(153, 'Fractal Design Meshify C', 'Case', 'Fractal', NULL, 5500.00, NULL, NULL, NULL),
(154, 'Fractal Design Meshify 2', 'Case', 'Fractal', NULL, 9500.00, NULL, NULL, NULL),
(155, 'Fractal Design Define 7', 'Case', 'Fractal', NULL, 11000.00, NULL, NULL, NULL),
(156, 'Lian Li PC-O11 Dynamic', 'Case', 'Lian Li', NULL, 8000.00, NULL, NULL, NULL),
(157, 'Lian Li PC-O11 Dynamic XL', 'Case', 'Lian Li', NULL, 12000.00, NULL, NULL, NULL),
(158, 'Phanteks Eclipse P400A', 'Case', 'Phanteks', NULL, 4800.00, NULL, NULL, NULL),
(159, 'Phanteks Eclipse P500A', 'Case', 'Phanteks', NULL, 7200.00, NULL, NULL, NULL),
(160, 'Phanteks Enthoo Pro 2', 'Case', 'Phanteks', NULL, 10000.00, NULL, NULL, NULL),
(161, 'Corsair AF120 120mm', 'Fan', 'Corsair', NULL, 600.00, NULL, NULL, NULL),
(162, 'Corsair AF140 140mm', 'Fan', 'Corsair', NULL, 700.00, NULL, NULL, NULL),
(163, 'Corsair ML120 120mm', 'Fan', 'Corsair', NULL, 800.00, NULL, NULL, NULL),
(164, 'Corsair ML140 140mm', 'Fan', 'Corsair', NULL, 900.00, NULL, NULL, NULL),
(165, 'Corsair LL120 RGB 120mm', 'Fan', 'Corsair', NULL, 1500.00, NULL, NULL, NULL),
(166, 'Corsair LL140 RGB 140mm', 'Fan', 'Corsair', NULL, 1600.00, NULL, NULL, NULL),
(167, 'Noctua NF-P12 120mm', 'Fan', 'Noctua', NULL, 1000.00, NULL, NULL, NULL),
(168, 'Noctua NF-A14 140mm', 'Fan', 'Noctua', NULL, 1200.00, NULL, NULL, NULL),
(169, 'Noctua NF-A12x25 120mm', 'Fan', 'Noctua', NULL, 1800.00, NULL, NULL, NULL),
(170, 'Noctua NF-F12 120mm', 'Fan', 'Noctua', NULL, 1100.00, NULL, NULL, NULL),
(171, 'Arctic F12 120mm', 'Fan', 'Arctic', NULL, 500.00, NULL, NULL, NULL),
(172, 'Arctic F14 140mm', 'Fan', 'Arctic', NULL, 600.00, NULL, NULL, NULL),
(173, 'Arctic P12 120mm', 'Fan', 'Arctic', NULL, 700.00, NULL, NULL, NULL),
(174, 'Arctic P14 140mm', 'Fan', 'Arctic', NULL, 800.00, NULL, NULL, NULL),
(175, 'be quiet! Pure Wings 2 120mm', 'Fan', 'be quiet!', NULL, 650.00, NULL, NULL, NULL),
(176, 'be quiet! Pure Wings 2 140mm', 'Fan', 'be quiet!', NULL, 750.00, NULL, NULL, NULL),
(177, 'be quiet! Silent Wings 3 120mm', 'Fan', 'be quiet!', NULL, 1400.00, NULL, NULL, NULL),
(178, 'be quiet! Silent Wings 3 140mm', 'Fan', 'be quiet!', NULL, 1500.00, NULL, NULL, NULL),
(179, 'Thermaltake Riing Quad 120mm RGB', 'Fan', 'Thermaltake', NULL, 1800.00, NULL, NULL, NULL),
(180, 'Thermaltake Riing Quad 140mm RGB', 'Fan', 'Thermaltake', NULL, 1900.00, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT '',
  `middle_name` varchar(50) DEFAULT '',
  `last_name` varchar(50) DEFAULT '',
  `street` varchar(100) DEFAULT '',
  `barangay` varchar(100) DEFAULT '',
  `municipality` varchar(100) DEFAULT '',
  `province` varchar(100) DEFAULT '',
  `postal_code` varchar(20) DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `profile_pic` varchar(255) DEFAULT 'profile_placeholder.png',
  `role` enum('user','admin') DEFAULT 'user',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `middle_name`, `last_name`, `street`, `barangay`, `municipality`, `province`, `postal_code`, `mobile`, `profile_pic`, `role`, `email_verified`, `verification_token`, `otp_code`, `otp_expires_at`, `reset_token`, `reset_expires`, `reset_expires_at`) VALUES
(16, 'tester2', 'borgmanok@gmail.com', '$2y$10$/WouckE0fZoiabLPp57SIuMpTESLrmp0jr0fvWs582QPoaAMwYM06', '', '', '', '', '', '', '', '', '', 'profile_placeholder.png', 'user', 0, NULL, '719941', '2025-09-27 18:28:45', NULL, NULL, NULL),
(18, 'kate1', 'jaschaperrion@gmail.com', '$2y$10$5ASB/vvSO5XFBl/IShMjQ.RGToDYL.HhNYBKXzZT6EzGLeYX0PYv6', '', '', '', '', '', '', '', '', '', 'profile_placeholder.png', 'user', 0, NULL, '301007', '2025-09-27 18:49:19', NULL, NULL, NULL),
(25, 'TESTER', 'eric.soriano.ecoast@panpacificu.edu.ph', '$2y$10$WJCyTpjQ0dobHABMGi3OKup9TF1oXe7iGyg9Yyew5FIjbRMkmjVi.', '', '', '', '', '', '', '', '', '', 'profile_placeholder.png', 'user', 1, NULL, NULL, NULL, NULL, NULL, '2025-09-28 18:59:26'),
(27, 'jamesian', 'james.fadriquela.ecoast@panpacificu.edu.ph', '$2y$10$/3KeL3TQLhCEnIY/jBTCEOynX.kPs75mpECYT7Dup3XUOKB/M7/9e', '', '', '', '', '', '', '', '', '', 'profile_placeholder.png', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `masterbuild`
--
ALTER TABLE `masterbuild`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `masterbuild_items`
--
ALTER TABLE `masterbuild_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `masterbuild_id` (`masterbuild_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`),
  ADD KEY `brand` (`brand`),
  ADD KEY `socket` (`socket`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `masterbuild`
--
ALTER TABLE `masterbuild`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `masterbuild_items`
--
ALTER TABLE `masterbuild_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `masterbuild`
--
ALTER TABLE `masterbuild`
  ADD CONSTRAINT `masterbuild_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `masterbuild_items`
--
ALTER TABLE `masterbuild_items`
  ADD CONSTRAINT `masterbuild_items_ibfk_1` FOREIGN KEY (`masterbuild_id`) REFERENCES `masterbuild` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `masterbuild_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
