-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 07:02 PM
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
-- Database: `admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_login`
--

CREATE TABLE `admin_login` (
  `name` varchar(20) DEFAULT NULL,
  `password` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login`
--

INSERT INTO `admin_login` (`name`, `password`) VALUES
('Chris', '121212'),
('suraj', '12345'),
('Principal', '1234512'),
('Aryan Bhatt', '12345567hsjsk');

-- --------------------------------------------------------

--
-- Table structure for table `allotment`
--

CREATE TABLE `allotment` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(30) DEFAULT NULL,
  `srno` int(11) DEFAULT NULL,
  `bill_date` date DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `overall_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `branch` varchar(30) DEFAULT NULL,
  `lab` varchar(30) DEFAULT NULL,
  `permission` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `allotment_reject`
--

CREATE TABLE `allotment_reject` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(30) DEFAULT NULL,
  `srno` int(11) DEFAULT NULL,
  `bill_date` date DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `overall_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `branch` varchar(30) DEFAULT NULL,
  `lab` varchar(30) DEFAULT NULL,
  `rejected_by` varchar(50) DEFAULT NULL,
  `reason` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allotment_reject`
--

INSERT INTO `allotment_reject` (`sno`, `product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `srno`, `bill_date`, `unit_price`, `overall_price`, `units`, `branch`, `lab`, `rejected_by`, `reason`) VALUES
(30, 'mouse', 'T&P Item', '12/21', '2025-04-18', 'The Acer EnterPrise', 33532, '2025-04-02', 100.00, 200.00, 2, 'CSE', 'LINUX', 'Stock Officer', 'I just don\'t wanna accept it');

-- --------------------------------------------------------

--
-- Table structure for table `all_labs`
--

CREATE TABLE `all_labs` (
  `sno` int(11) NOT NULL,
  `branch_name` varchar(50) NOT NULL,
  `lab_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `all_labs`
--

INSERT INTO `all_labs` (`sno`, `branch_name`, `lab_name`) VALUES
(1, 'CSE', 'LINUX'),
(3, 'pharmecy', 'kks');

-- --------------------------------------------------------

--
-- Table structure for table `branch_deprecate`
--

CREATE TABLE `branch_deprecate` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `allotment_date` date NOT NULL DEFAULT curdate(),
  `branch` varchar(20) DEFAULT NULL,
  `lab` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(50) DEFAULT NULL,
  `deprecate_date` date NOT NULL DEFAULT curdate(),
  `reason` varchar(150) DEFAULT NULL,
  `current_condition` varchar(50) DEFAULT 'NA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_deprecate`
--

INSERT INTO `branch_deprecate` (`sno`, `product_name`, `type`, `rr_reg`, `allotment_date`, `branch`, `lab`, `unit_price`, `units`, `purchase_date`, `got_it_from`, `deprecate_date`, `reason`, `current_condition`) VALUES
(14, 'Chalk', 'Consumable Item', '23/12', '2025-04-03', 'CSE', 'LINUX', 25.00, 1, '2025-04-03', 'Shop', '2025-04-05', 'test purpose', 'good'),
(15, 'mouse', 'T&P Item', '12/21', '2025-04-03', 'CSE', 'LINUX', 100.00, 1, '2025-04-18', 'The Acer EnterPrise', '2025-04-05', 'testing', 'good');

-- --------------------------------------------------------

--
-- Table structure for table `branch_inventory`
--

CREATE TABLE `branch_inventory` (
  `sno` int(11) NOT NULL,
  `product_name` varchar(30) DEFAULT NULL,
  `model_name` varchar(20) DEFAULT NULL,
  `price_per_unit` decimal(10,2) DEFAULT NULL,
  `units` int(11) DEFAULT NULL,
  `branch_name` varchar(10) DEFAULT NULL,
  `lab_name` varchar(20) DEFAULT NULL,
  `rr_reg` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_items`
--

CREATE TABLE `branch_items` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `allotment_date` date NOT NULL DEFAULT curdate(),
  `branch` varchar(20) DEFAULT NULL,
  `lab` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(50) DEFAULT NULL,
  `current_condition` varchar(50) DEFAULT 'NA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_items`
--

INSERT INTO `branch_items` (`sno`, `product_name`, `type`, `rr_reg`, `allotment_date`, `branch`, `lab`, `unit_price`, `units`, `purchase_date`, `got_it_from`, `current_condition`) VALUES
(15, 'mouse', 'T&P Item', '12/21', '2025-04-03', 'CSE', 'LINUX', 100.00, 3, '2025-04-18', 'The Acer EnterPrise', 'Good'),
(16, 'mouse', 'T&P Item', '12/21', '2025-04-03', 'CSE', 'LINUX', 100.00, 2, '2025-04-18', 'The Acer EnterPrise', 'good'),
(17, 'Pen', 'Consumable Item', '07/32', '2025-04-03', 'CSE', 'LINUX', 10.00, 3, '2025-04-02', 'Pen wali Dukan', 'nice'),
(18, 'Chalk', 'Consumable Item', '23/12', '2025-04-03', 'CSE', 'LINUX', 25.00, 1, '2025-04-03', 'Shop', 'good'),
(19, 'keyboard', 'T&P Item', '12/27', '2025-04-03', 'CSE', 'LINUX', 450.00, 2, '2025-04-03', '', 'better');

-- --------------------------------------------------------

--
-- Table structure for table `branch_return_reject`
--

CREATE TABLE `branch_return_reject` (
  `sno` int(11) NOT NULL,
  `product_name` varchar(30) DEFAULT NULL,
  `model_name` varchar(20) DEFAULT NULL,
  `price_per_unit` decimal(10,2) DEFAULT NULL,
  `units` int(11) DEFAULT NULL,
  `branch` varchar(10) DEFAULT NULL,
  `lab` varchar(20) DEFAULT NULL,
  `rr_reg` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `reason` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumable_items`
--

CREATE TABLE `consumable_items` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `allotment_date` date NOT NULL DEFAULT curdate(),
  `branch` varchar(20) DEFAULT NULL,
  `lab` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `use_date` date DEFAULT NULL,
  `use_name` varchar(20) DEFAULT NULL,
  `use_for` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumable_items`
--

INSERT INTO `consumable_items` (`sno`, `product_name`, `rr_reg`, `allotment_date`, `branch`, `lab`, `unit_price`, `units`, `use_date`, `use_name`, `use_for`) VALUES
(17, 'Pen', '07/32', '2025-04-03', 'CSE', 'LINUX', 10.00, 1, '2025-04-05', 'Mrs Beauty', 'in class'),
(18, 'Chalk', '23/12', '2025-04-03', 'CSE', 'LINUX', 25.00, 1, '2025-04-03', 'Mrs Beauty', 'on class');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(30) DEFAULT NULL,
  `srno` int(11) DEFAULT NULL,
  `bill_date` date DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `overall_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`sno`, `product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `srno`, `bill_date`, `unit_price`, `overall_price`, `units`) VALUES
(17, 'mouse', 'T&P Item', '12/21', '2025-04-18', 'The Acer EnterPrise', 33532, '2025-04-02', 100.00, 1000.00, 1),
(18, 'Pen', 'Consumable Item', '07/32', '2025-04-02', 'Pen wali Dukan', 83719, '2025-04-02', 10.00, 100.00, 6),
(19, 'Chalk', 'Consumable Item', '23/12', '2025-04-03', 'Shop', 7843, '2025-04-03', 25.00, 250.00, 6),
(20, 'keyboard', 'T&P Item', '12/27', '2025-04-03', '', 783, '2025-04-03', 450.00, 4500.00, 8);

-- --------------------------------------------------------

--
-- Table structure for table `return_items_record`
--

CREATE TABLE `return_items_record` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(30) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `overall_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `branch` varchar(30) DEFAULT NULL,
  `lab` varchar(30) DEFAULT NULL,
  `product_condition` varchar(100) DEFAULT NULL,
  `return_by` varchar(20) DEFAULT NULL,
  `reason` varchar(150) DEFAULT NULL,
  `return_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_items_record`
--

INSERT INTO `return_items_record` (`sno`, `product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `unit_price`, `overall_price`, `units`, `branch`, `lab`, `product_condition`, `return_by`, `reason`, `return_date`) VALUES
(3, 'keyboard', 'T&P Item', '12/27', '2025-04-03', '', 450.00, 450.00, 1, 'CSE', 'LINUX', 'Good', 'Mrs Beauty', 'testing', '2025-04-05'),
(4, 'keyboard', 'T&P Item', '12/27', '2025-04-03', '', 450.00, 450.00, 1, 'CSE', 'LINUX', 'Good', 'Mrs Beauty', 'testing', '2025-04-05');

-- --------------------------------------------------------

--
-- Table structure for table `return_request`
--

CREATE TABLE `return_request` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(30) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `overall_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `branch` varchar(30) DEFAULT NULL,
  `lab` varchar(30) DEFAULT NULL,
  `permission` varchar(20) DEFAULT NULL,
  `product_condition` varchar(100) DEFAULT NULL,
  `return_by` varchar(20) DEFAULT NULL,
  `reason` varchar(150) DEFAULT NULL,
  `return_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_request`
--

INSERT INTO `return_request` (`sno`, `product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `unit_price`, `overall_price`, `units`, `branch`, `lab`, `permission`, `product_condition`, `return_by`, `reason`, `return_date`) VALUES
(18, 'mouse', 'T&P Item', '12/21', '2025-04-18', 'The Acer EnterPrise', 100.00, 100.00, 1, 'CSE', 'LINUX', 'Stock Manager', 'Good', 'Mrs Beauty', 'testing', '2025-04-05');

-- --------------------------------------------------------

--
-- Table structure for table `return_request_cancel`
--

CREATE TABLE `return_request_cancel` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(30) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `overall_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `branch` varchar(30) DEFAULT NULL,
  `lab` varchar(30) DEFAULT NULL,
  `rejected_by` varchar(20) DEFAULT NULL,
  `product_condition` varchar(100) DEFAULT NULL,
  `reason` varchar(150) DEFAULT NULL,
  `cancel_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_request_cancel`
--

INSERT INTO `return_request_cancel` (`sno`, `product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `unit_price`, `overall_price`, `units`, `branch`, `lab`, `rejected_by`, `product_condition`, `reason`, `cancel_date`) VALUES
(9, 'Chalk', 'Consumable Item', '23/12', '2025-04-03', 'Shop', 25.00, 25.00, 1, 'CSE', 'LINUX', 'Stock Manager', 'Good', 'I just right now', '2025-04-05');

-- --------------------------------------------------------

--
-- Table structure for table `rr_allocate_items`
--

CREATE TABLE `rr_allocate_items` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `allotment_date` date NOT NULL DEFAULT curdate(),
  `branch` varchar(20) DEFAULT NULL,
  `lab` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `used_for` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rr_allocate_items`
--

INSERT INTO `rr_allocate_items` (`sno`, `product_name`, `type`, `rr_reg`, `allotment_date`, `branch`, `lab`, `unit_price`, `units`, `used_for`) VALUES
(10, 'mouse', 'T&P Item', '12/21', '2025-04-03', 'CSE', 'LINUX', 100.00, 3, 'for the lab'),
(11, 'mouse', 'T&P Item', '12/21', '2025-04-03', 'CSE', 'LINUX', 100.00, 4, 'just want it'),
(12, 'Pen', 'Consumable Item', '07/32', '2025-04-03', 'CSE', 'LINUX', 10.00, 4, 'to add in store'),
(13, 'Chalk', 'Consumable Item', '23/12', '2025-04-03', 'CSE', 'LINUX', 25.00, 4, 'well to use it'),
(14, 'keyboard', 'T&P Item', '12/27', '2025-04-03', 'CSE', 'LINUX', 450.00, 4, 'to use with computers');

-- --------------------------------------------------------

--
-- Table structure for table `rr_received_items`
--

CREATE TABLE `rr_received_items` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `got_it_from` varchar(30) DEFAULT NULL,
  `srno` int(11) DEFAULT NULL,
  `bill_date` date DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `overall_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rr_received_items`
--

INSERT INTO `rr_received_items` (`sno`, `product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `srno`, `bill_date`, `unit_price`, `overall_price`, `units`) VALUES
(4, 'mouse', 'T&P Item', '12/21', '2025-04-18', 'The Acer EnterPrise', 33532, '2025-04-02', 100.00, 1000.00, 10),
(5, 'Pen', 'Consumable Item', '07/32', '2025-04-02', 'Pen wali Dukan', 83719, '2025-04-02', 10.00, 100.00, 10),
(6, 'Chalk', 'Consumable Item', '23/12', '2025-04-03', 'Shop', 7843, '2025-04-03', 25.00, 250.00, 10),
(7, 'keyboard', 'T&P Item', '12/27', '2025-04-03', '', 783, '2025-04-03', 450.00, 4500.00, 10);

-- --------------------------------------------------------

--
-- Table structure for table `rr_recevied_branch`
--

CREATE TABLE `rr_recevied_branch` (
  `sno` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `got_it_from` varchar(30) DEFAULT NULL,
  `current_condition` varchar(50) DEFAULT NULL,
  `rr_reg` varchar(10) DEFAULT NULL,
  `allotment_date` date NOT NULL DEFAULT curdate(),
  `branch` varchar(20) DEFAULT NULL,
  `lab` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rr_recevied_branch`
--

INSERT INTO `rr_recevied_branch` (`sno`, `product_name`, `type`, `got_it_from`, `current_condition`, `rr_reg`, `allotment_date`, `branch`, `lab`, `unit_price`, `units`) VALUES
(9, 'mouse', 'T&P Item', 'The Acer EnterPrise', 'Good', '12/21', '2025-04-03', 'CSE', 'LINUX', 100.00, 3),
(10, 'mouse', 'T&P Item', 'The Acer EnterPrise', 'good', '12/21', '2025-04-03', 'CSE', 'LINUX', 100.00, 4),
(11, 'Pen', 'Consumable Item', 'Pen wali Dukan', 'nice', '07/32', '2025-04-03', 'CSE', 'LINUX', 10.00, 4),
(12, 'Chalk', 'Consumable Item', 'Shop', 'good', '23/12', '2025-04-03', 'CSE', 'LINUX', 25.00, 4),
(13, 'keyboard', 'T&P Item', '', 'mid', '12/27', '2025-04-03', 'CSE', 'LINUX', 450.00, 4);

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `sno` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `branch` varchar(50) NOT NULL,
  `lab` varchar(50) NOT NULL,
  `password` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_login`
--

INSERT INTO `user_login` (`sno`, `name`, `branch`, `lab`, `password`) VALUES
(76, 'Stock Officer', 'INVENTORY_OFFICER', 'INVENTORY_OFFICER', '11111'),
(78, 'Mrs Beauty', 'CSE', 'LINUX', '22222'),
(80, 'KIRAN', 'INVENTORY', 'INVENTORY', '12345'),
(81, 'Suraj', 'INVENTORY_OFFICER', 'INVENTORY_OFFICER', 'password');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_login`
--
ALTER TABLE `admin_login`
  ADD PRIMARY KEY (`password`);

--
-- Indexes for table `allotment`
--
ALTER TABLE `allotment`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `allotment_reject`
--
ALTER TABLE `allotment_reject`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `all_labs`
--
ALTER TABLE `all_labs`
  ADD PRIMARY KEY (`sno`),
  ADD UNIQUE KEY `branch_name` (`branch_name`);

--
-- Indexes for table `branch_deprecate`
--
ALTER TABLE `branch_deprecate`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `branch_inventory`
--
ALTER TABLE `branch_inventory`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `branch_items`
--
ALTER TABLE `branch_items`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `branch_return_reject`
--
ALTER TABLE `branch_return_reject`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `consumable_items`
--
ALTER TABLE `consumable_items`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `return_items_record`
--
ALTER TABLE `return_items_record`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `return_request`
--
ALTER TABLE `return_request`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `return_request_cancel`
--
ALTER TABLE `return_request_cancel`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `rr_allocate_items`
--
ALTER TABLE `rr_allocate_items`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `rr_received_items`
--
ALTER TABLE `rr_received_items`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `rr_recevied_branch`
--
ALTER TABLE `rr_recevied_branch`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`sno`),
  ADD UNIQUE KEY `password` (`password`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `allotment`
--
ALTER TABLE `allotment`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `allotment_reject`
--
ALTER TABLE `allotment_reject`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `all_labs`
--
ALTER TABLE `all_labs`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branch_deprecate`
--
ALTER TABLE `branch_deprecate`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `branch_inventory`
--
ALTER TABLE `branch_inventory`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `branch_items`
--
ALTER TABLE `branch_items`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `branch_return_reject`
--
ALTER TABLE `branch_return_reject`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `consumable_items`
--
ALTER TABLE `consumable_items`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `return_items_record`
--
ALTER TABLE `return_items_record`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `return_request`
--
ALTER TABLE `return_request`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `return_request_cancel`
--
ALTER TABLE `return_request_cancel`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rr_allocate_items`
--
ALTER TABLE `rr_allocate_items`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rr_received_items`
--
ALTER TABLE `rr_received_items`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rr_recevied_branch`
--
ALTER TABLE `rr_recevied_branch`
  MODIFY `sno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_login`
--
ALTER TABLE `user_login`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
