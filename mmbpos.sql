-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2026 at 04:43 AM
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
-- Database: `mmbpos`
--

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `discount_name` varchar(50) DEFAULT NULL,
  `discount_rate` decimal(5,2) DEFAULT NULL,
  `is_vat_exempt` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `discount_name`, `discount_rate`, `is_vat_exempt`) VALUES
(1, 'Regular', 0.00, 0),
(2, 'Senior Citizen', 20.00, 1),
(3, 'PWD', 20.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `dosage_forms`
--

CREATE TABLE `dosage_forms` (
  `id` int(11) NOT NULL,
  `form_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosage_forms`
--

INSERT INTO `dosage_forms` (`id`, `form_name`, `is_active`, `created_at`) VALUES
(1, 'Tablet', 1, '2026-08-17 09:03:53'),
(2, 'Capsule', 1, '2026-08-17 09:03:53'),
(3, 'Syrup', 1, '2026-08-17 09:03:53'),
(4, 'Suspension', 1, '2026-08-17 09:03:53'),
(5, 'Cream', 1, '2026-08-17 09:03:53'),
(6, 'Ointment', 1, '2026-08-17 09:03:53'),
(7, 'Drops', 1, '2026-08-17 09:03:53'),
(8, 'Injection', 1, '2026-08-17 09:03:53'),
(9, 'Powder', 1, '2026-08-17 09:03:53'),
(10, 'Granules', 1, '2026-08-17 09:03:53'),
(11, 'Solution', 1, '2026-08-17 09:03:53'),
(12, 'Gel', 1, '2026-08-17 09:03:53'),
(13, 'Lotion', 1, '2026-08-17 09:03:53'),
(14, 'Spray', 1, '2026-08-17 09:03:53'),
(15, 'Patch', 1, '2026-08-17 09:03:53'),
(16, 'Inhaler', 1, '2026-08-17 09:03:53'),
(17, 'Lozenge', 1, '2026-08-17 09:03:53'),
(18, 'Suppository', 1, '2026-08-17 09:03:53'),
(19, 'Oral Liquid', 1, '2026-08-17 09:03:53'),
(20, 'Chewable Tablet', 1, '2026-08-17 09:03:53');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL COMMENT 'Unique batch identifier',
  `date_received` date DEFAULT NULL COMMENT 'Date batch was received',
  `manufacture_date` date DEFAULT NULL COMMENT 'Manufacturing date',
  `purchase_cost` decimal(10,2) DEFAULT NULL COMMENT 'Cost per unit',
  `markup` decimal(5,2) DEFAULT 0.00 COMMENT 'Markup percentage',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT 'Selling price per unit',
  `received_quantity` int(11) DEFAULT 0 COMMENT 'Original quantity received in batch',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `current_quantity` int(11) DEFAULT 0 COMMENT 'Current available quantity after sales/adjustments',
  `expiry_date` date DEFAULT NULL COMMENT 'Expiry date (critical for FEFO)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_backup`
--

CREATE TABLE `inventory_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `manufacture_date` date DEFAULT NULL,
  `purchase_cost` decimal(10,2) DEFAULT NULL COMMENT 'Purchase cost per unit',
  `markup` decimal(5,2) DEFAULT 0.00 COMMENT 'Markup percentage',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT 'Selling price per unit',
  `received_quantity` int(11) DEFAULT 0 COMMENT 'Original quantity received',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `quantity` int(11) DEFAULT 0,
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_disposals`
--

CREATE TABLE `inventory_disposals` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `reason` varchar(100) NOT NULL DEFAULT 'Expired',
  `disposed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL COMMENT 'Which batch',
  `transaction_type` enum('received','sold','adjusted','damaged','returned','expired') NOT NULL,
  `quantity_change` int(11) NOT NULL COMMENT 'Positive or negative',
  `quantity_before` int(11) NOT NULL COMMENT 'Quantity before this transaction',
  `quantity_after` int(11) NOT NULL COMMENT 'Quantity after this transaction',
  `reason` varchar(255) DEFAULT NULL COMMENT 'Why: sales receipt, damage report, etc.',
  `reference_id` int(11) DEFAULT NULL COMMENT 'Transaction_id or return_transaction_id',
  `created_by` int(11) DEFAULT NULL COMMENT 'User who recorded this',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `override_log`
--

CREATE TABLE `override_log` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `cashier_name` varchar(255) NOT NULL,
  `approver_id` int(11) NOT NULL,
  `approver_name` varchar(255) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `reason` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pre_approved_users`
--

CREATE TABLE `pre_approved_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `void_password` varchar(255) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pre_approved_users_info`
--

CREATE TABLE `pre_approved_users_info` (
  `id` int(11) NOT NULL,
  `pre_user_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `street` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `email` varchar(155) NOT NULL,
  `contactnumber` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `branded_name` varchar(255) NOT NULL COMMENT 'Brand name (e.g., Amoxil)',
  `generic_name` varchar(255) NOT NULL COMMENT 'Generic/active ingredient (e.g., Amoxicillin)',
  `strength` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Main strength value (e.g., 500mg)',
  `measurement_id` int(11) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL COMMENT 'Barcode for POS scanning',
  `category_id` int(11) DEFAULT NULL,
  `classification_id` int(11) DEFAULT NULL,
  `units_per_package` int(11) DEFAULT NULL COMMENT 'Units per package/blister/bottle',
  `imageproduct` varchar(500) NOT NULL,
  `is_basic_necessities` tinyint(1) NOT NULL DEFAULT 0,
  `package_type` varchar(100) DEFAULT NULL COMMENT 'Blister, Bottle, Strip, Box, Jar, etc.',
  `dosage_form` varchar(100) DEFAULT NULL COMMENT 'Tablet, Capsule, Syrup, Suspension, Cream, Drops',
  `dosage_form_id` int(11) DEFAULT NULL,
  `strength_per_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Qty for strength (e.g., 5 for 5mL in syrup)',
  `strength_per_quantity_unit` varchar(50) DEFAULT NULL COMMENT 'Unit for strength_per_quantity (e.g., mL, g)',
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products_backup`
--

CREATE TABLE `products_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `branded_name` varchar(255) NOT NULL,
  `generic_name` varchar(255) NOT NULL,
  `strength` int(11) NOT NULL,
  `measurement_id` int(11) NOT NULL,
  `unit_measurement` varchar(50) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `classification_id` int(11) DEFAULT NULL,
  `pcs` int(50) DEFAULT NULL,
  `net_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `imageproduct` varchar(500) NOT NULL,
  `is_basic_necessities` tinyint(1) NOT NULL DEFAULT 0,
  `supplier_name` varchar(255) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `supplier_contact` int(11) DEFAULT NULL,
  `supplier_address` text DEFAULT NULL,
  `supplier_email` varchar(255) DEFAULT NULL,
  `package_type` varchar(100) DEFAULT NULL,
  `dosage_form` varchar(100) DEFAULT NULL,
  `strength_per_quantity` decimal(10,2) DEFAULT NULL,
  `strength_per_unit` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `has_vat` tinyint(1) NOT NULL DEFAULT 0,
  `senior_discount` tinyint(1) NOT NULL DEFAULT 0,
  `pwd_discount` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `category_name`, `has_vat`, `senior_discount`, `pwd_discount`) VALUES
(17, 'Prescription Medicines', 0, 1, 1),
(18, 'Over-the-Counter (OTC)', 0, 1, 1),
(19, 'Medical Supplies', 0, 1, 1),
(20, 'Vitamins & Supplements', 0, 1, 1),
(21, 'First Aid', 0, 1, 1),
(22, 'Diagnostics', 0, 1, 1),
(23, 'Herbal Products', 0, 1, 1),
(24, 'Health & Wellness', 0, 1, 1),
(25, 'Personal Care', 1, 0, 0),
(26, 'Baby Care', 1, 0, 0),
(27, 'Beverage/Beverages', 1, 0, 0),
(28, 'Snacks', 1, 0, 0),
(29, 'Canned Goods', 1, 0, 0),
(30, 'Instant Food', 1, 0, 0),
(31, 'Dairy Products', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pwd_customers`
--

CREATE TABLE `pwd_customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `verified_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `register_closings`
--

CREATE TABLE `register_closings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_date` date NOT NULL,
  `system_cash` decimal(10,2) NOT NULL DEFAULT 0.00,
  `counted_cash` decimal(10,2) NOT NULL DEFAULT 0.00,
  `variance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `closed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `register_openings`
--

CREATE TABLE `register_openings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_date` date NOT NULL,
  `opening_cash` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_items`
--

CREATE TABLE `return_items` (
  `id` int(11) NOT NULL,
  `return_transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `item_type` enum('returned','replacement') NOT NULL,
  `restocked` tinyint(1) NOT NULL DEFAULT 0,
  `restockable` tinyint(1) NOT NULL DEFAULT 0,
  `cost_of_goods` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_transactions`
--

CREATE TABLE `return_transactions` (
  `id` int(11) NOT NULL,
  `original_transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `replacement_product_id` int(11) DEFAULT NULL,
  `replacement_quantity` int(11) NOT NULL DEFAULT 0,
  `reason` varchar(255) DEFAULT NULL,
  `refund_method` varchar(50) DEFAULT 'cash',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `approver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `senior_customers`
--

CREATE TABLE `senior_customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `verified_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_settings`
--

CREATE TABLE `store_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `supplier_type` varchar(100) DEFAULT NULL COMMENT 'e.g., Pharmaceutical Distributor, Wholesaler, etc.',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_id` varchar(100) DEFAULT NULL,
  `customer_type` varchar(20) DEFAULT NULL COMMENT 'Type of customer: regular, pwd, senior',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_total` decimal(10,2) DEFAULT 0.00,
  `total_vat_exemption` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_batch_allocations`
--

CREATE TABLE `transaction_batch_allocations` (
  `id` int(11) NOT NULL,
  `transaction_item_id` int(11) NOT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_item_batches`
--

CREATE TABLE `transaction_item_batches` (
  `id` int(11) NOT NULL,
  `transaction_item_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unit_measurement`
--

CREATE TABLE `unit_measurement` (
  `unit_id` int(11) NOT NULL,
  `different_measurement` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit_measurement`
--

INSERT INTO `unit_measurement` (`unit_id`, `different_measurement`) VALUES
(1, 'mcg'),
(2, 'mg'),
(3, 'g'),
(4, 'kg'),
(5, 'µL'),
(6, 'mL'),
(7, 'L'),
(8, 'mm'),
(9, 'cm'),
(10, 'm'),
(11, '%'),
(12, 'IU'),
(13, 'mEq'),
(14, 'mmol'),
(15, 'Units'),
(16, 'pc'),
(17, 'pcs'),
(18, 'tbsp'),
(19, 'tsp'),
(20, 'box');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(155) NOT NULL,
  `password` varchar(255) NOT NULL,
  `void_password` varchar(255) DEFAULT NULL,
  `position` varchar(20) DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NULL DEFAULT NULL,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `void_password`, `position`, `failed_attempts`, `last_attempt`, `status`, `created_at`) VALUES
(1, 'andrew_owner', '$2y$10$U9nvr0YPYlqswVjqumcw1OTvtVJYVwdl5MFcCfvumt.nUzhqJTe9C', '1234567', 'Owner', 0, '2026-09-03 08:00:52', 'active', '2026-09-05 08:53:02'),
(2, 'andrew_admin', '$2y$10$.pMY78gCNdiWGwCw8DAIse7SS./j5d9T8pQ87YhhLoOum4yKzJL.m', '1234567', 'Admin', 0, '2026-08-20 01:56:58', 'active', '2026-09-05 08:53:02'),
(3, 'andrew_staff', '$2y$10$WESQ6f2mApseNhMhKMmW8e6gg.tp9AU8CsY/mQrU4g6GHEWmFCWGG', '1234567', 'Staff', 0, '2026-07-26 14:50:35', 'active', '2026-09-05 08:53:02'),
(4, 'staff1', '$2y$10$U60z2JyVRKxJ.x36cqpJkuzZDPRFtOF5aZqUO7QCyBBN.P614oIoy', NULL, 'Staff', 0, NULL, 'active', '2026-09-05 08:53:02'),
(5, 'sampleAccount', '$2y$10$G.IAWGX.MPWQ8E1bIvuykO7Ph//70zWAqxZdMdDVMFiZDu7qFtBLm', NULL, 'Staff', 0, NULL, 'active', '2026-09-05 08:53:02'),
(6, 'ownerewew', '$2y$10$ksJeBpMxV9gjhPxayLmxSexH68mbQj4MK8ImAs8aCsGYldOCYOJgG', NULL, 'Staff', 0, NULL, 'active', '2026-09-05 08:53:02'),
(8, 'nokkkkkk', '$2y$10$0X1yddbjSnlz62SzMZDrM.BXlBO7Jkj1eACodyDzl3ZOYB7Nn9D6q', '$2y$10$wZJHXgcm8XCPw62rB.AFqenueraiaxuy8q7/AR8ihhxs/.8tcQZ5K', 'Staff', 0, NULL, 'active', '2026-09-05 08:54:32');

-- --------------------------------------------------------

--
-- Table structure for table `users_info`
--

CREATE TABLE `users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `street` varchar(155) NOT NULL,
  `barangay` varchar(155) NOT NULL,
  `city` varchar(155) NOT NULL,
  `province` varchar(155) NOT NULL,
  `country` varchar(155) NOT NULL,
  `email` varchar(155) NOT NULL,
  `contactnumber` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_info`
--

INSERT INTO `users_info` (`id`, `user_id`, `firstname`, `middlename`, `lastname`, `age`, `street`, `barangay`, `city`, `province`, `country`, `email`, `contactnumber`) VALUES
(1, 1, 'Andrew', 'Gonzales', 'Pablo', 0, '', '', '', '', '', 'andrewpablo2005@gmail.com', '09651800675'),
(2, 2, 'Jhon Bryan', 'Gonzales', 'Palero', 0, '', '', '', '', '', 'palero@gmail.com', '09651800675'),
(3, 3, 'Neil Paolo', 'Gonzales', 'Cabrera', 21, 'N/A', 'Marelu', 'Gapan', 'Nueva Ecija', 'Philippines', 'cabrera@gmail.com', '09651800675'),
(4, 4, 'Ivhan Grace', 'De Belen', 'Aguilar', 20, 'N/A', 'Niyugan', 'Jaen', 'Nueva Ecija', 'Philippines', 'andrewpablo2005@gmail.com', '09651800675'),
(5, 5, 'Sample', 'Gonzales', 'Pablo', 19, 'Purok', 'Niyugan', 'JAEN (NUEVA ECIJA)', 'Nueva Ecija', 'Philippines', 'andrewpablo2005@gmail.com', '09651800675'),
(6, 6, 'Dut', 'Gonzales', 'Pablo', 27, 'df', 'Malapit', 'fd', 'df', 'Philippines', 'andrewpablo2005@gmail.com', '09651800675'),
(7, 7, 'Sampleee12', 'Gonzales', 'Pablo', 18, 'Purok', 'Poblacion', 'JAEN (NUEVA ECIJA)', 'Nueva Ecija', 'Philippines', 'andrewpablo2005@gmail.com', '09651800675'),
(8, 8, 'Andrewsfsef', 'Gonzales', 'Pablo', 23, 'Purok', 'Poblacion', 'JAEN (NUEVA ECIJA)', 'Nueva Ecija', 'Philippines', 'andrewpablo2005@gmail.com', '09651800675');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dosage_forms`
--
ALTER TABLE `dosage_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_dosage_form_name` (`form_name`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_batch_per_product` (`product_id`,`batch_number`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `idx_batch_number` (`batch_number`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_supplier_id` (`supplier_id`),
  ADD KEY `idx_expiry_date` (`expiry_date`),
  ADD KEY `idx_date_received` (`date_received`);

--
-- Indexes for table `inventory_disposals`
--
ALTER TABLE `inventory_disposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_disposals_product` (`product_id`),
  ADD KEY `idx_inventory_disposals_expiry` (`expiry_date`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_id` (`inventory_id`),
  ADD KEY `idx_transaction_type` (`transaction_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_reference_id` (`reference_id`),
  ADD KEY `fk_inv_trans_user` (`created_by`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `override_log`
--
ALTER TABLE `override_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pre_approved_users`
--
ALTER TABLE `pre_approved_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pre_approved_users_info`
--
ALTER TABLE `pre_approved_users_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pre_users_info` (`pre_user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD UNIQUE KEY `barcode_2` (`barcode`),
  ADD UNIQUE KEY `barcode_3` (`barcode`),
  ADD UNIQUE KEY `barcode_4` (`barcode`),
  ADD UNIQUE KEY `barcode_5` (`barcode`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `classification_id` (`classification_id`),
  ADD KEY `idx_barcode` (`barcode`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_measurement_id` (`measurement_id`),
  ADD KEY `idx_branded_name` (`branded_name`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `pwd_customers`
--
ALTER TABLE `pwd_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pwd_id` (`id_number`);

--
-- Indexes for table `register_closings`
--
ALTER TABLE `register_closings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_register_closing_user_date` (`user_id`,`business_date`),
  ADD KEY `idx_register_closings_date` (`business_date`);

--
-- Indexes for table `register_openings`
--
ALTER TABLE `register_openings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_register_opening_user_date` (`user_id`,`business_date`);

--
-- Indexes for table `return_items`
--
ALTER TABLE `return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_return_items_return_transaction` (`return_transaction_id`),
  ADD KEY `idx_return_items_product` (`product_id`);

--
-- Indexes for table `return_transactions`
--
ALTER TABLE `return_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_return_original_transaction` (`original_transaction_id`),
  ADD KEY `idx_return_user` (`user_id`);

--
-- Indexes for table `senior_customers`
--
ALTER TABLE `senior_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_senior_id` (`id_number`);

--
-- Indexes for table `store_settings`
--
ALTER TABLE `store_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_name` (`supplier_name`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_id` (`discount_id`),
  ADD KEY `idx_customer_type` (`customer_type`);

--
-- Indexes for table `transaction_batch_allocations`
--
ALTER TABLE `transaction_batch_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tba_transaction_item` (`transaction_item_id`),
  ADD KEY `idx_tba_inventory` (`inventory_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_transaction_items_batch_id` (`batch_id`);

--
-- Indexes for table `transaction_item_batches`
--
ALTER TABLE `transaction_item_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_transaction_item_batch` (`transaction_item_id`,`inventory_id`),
  ADD KEY `idx_transaction_item_batches_inventory` (`inventory_id`);

--
-- Indexes for table `unit_measurement`
--
ALTER TABLE `unit_measurement`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_info`
--
ALTER TABLE `users_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_info` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `dosage_forms`
--
ALTER TABLE `dosage_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_disposals`
--
ALTER TABLE `inventory_disposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `override_log`
--
ALTER TABLE `override_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pre_approved_users`
--
ALTER TABLE `pre_approved_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pre_approved_users_info`
--
ALTER TABLE `pre_approved_users_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `pwd_customers`
--
ALTER TABLE `pwd_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `register_closings`
--
ALTER TABLE `register_closings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `register_openings`
--
ALTER TABLE `register_openings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_items`
--
ALTER TABLE `return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_transactions`
--
ALTER TABLE `return_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `senior_customers`
--
ALTER TABLE `senior_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_batch_allocations`
--
ALTER TABLE `transaction_batch_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_item_batches`
--
ALTER TABLE `transaction_item_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unit_measurement`
--
ALTER TABLE `unit_measurement`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users_info`
--
ALTER TABLE `users_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `fk_inv_trans_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_trans_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pre_approved_users_info`
--
ALTER TABLE `pre_approved_users_info`
  ADD CONSTRAINT `fk_pre_users_info` FOREIGN KEY (`pre_user_id`) REFERENCES `pre_approved_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_products_measurement` FOREIGN KEY (`measurement_id`) REFERENCES `unit_measurement` (`unit_id`) ON UPDATE CASCADE;

--
-- Constraints for table `register_closings`
--
ALTER TABLE `register_closings`
  ADD CONSTRAINT `fk_register_closings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`);

--
-- Constraints for table `transaction_batch_allocations`
--
ALTER TABLE `transaction_batch_allocations`
  ADD CONSTRAINT `fk_tba_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tba_transaction_item` FOREIGN KEY (`transaction_item_id`) REFERENCES `transaction_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `fk_transaction_items_batch` FOREIGN KEY (`batch_id`) REFERENCES `inventory` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transaction_item_batches`
--
ALTER TABLE `transaction_item_batches`
  ADD CONSTRAINT `fk_transaction_item_batches_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`),
  ADD CONSTRAINT `fk_transaction_item_batches_item` FOREIGN KEY (`transaction_item_id`) REFERENCES `transaction_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
