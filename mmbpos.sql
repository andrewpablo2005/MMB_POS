-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2026 at 11:19 AM
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
-- Table structure for table `activity_logs`
--
-- Audit trail: who did what, when, from where (Task 42).
-- Created automatically by conn/activity_log.php on first use.

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(155) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `module` varchar(30) NOT NULL DEFAULT 'system',
  `action` varchar(40) NOT NULL DEFAULT 'unknown',
  `entity_type` varchar(30) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(20, 'Chewable Tablet', 1, '2026-08-17 09:03:53'),
(21, 'bottles', 1, '2026-09-07 23:05:19');

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

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `supplier_id`, `batch_number`, `date_received`, `manufacture_date`, `purchase_cost`, `markup`, `sale_price`, `received_quantity`, `created_at`, `updated_at`, `current_quantity`, `expiry_date`) VALUES
(1, 1, 1, 'Batch-1', '2026-09-08', NULL, 110.00, 5.00, 115.50, 100, '2026-09-08 06:18:40', '2026-09-09 00:37:00', 0, '2030-02-02'),
(2, 1, 1, 'Batch-2', '2026-09-08', NULL, 50.00, 5.00, 52.50, 100, '2026-09-08 06:46:50', '2026-09-15 08:12:00', 1, '2027-05-08'),
(3, 1, 1, 'Batch-3', '2026-09-09', NULL, 152.00, 5.00, 159.60, 1000, '2026-09-09 00:35:06', '2026-09-15 09:07:43', 770, '2030-02-02'),
(4, 1, 1, 'Batch-4', '2026-09-14', NULL, 100.00, 5.00, 105.00, 100, '2026-09-14 07:28:12', '2026-09-15 08:59:06', 90, '2035-02-02'),
(5, 1, 1, 'Batch-5', '2026-09-14', NULL, 200.00, 5.00, 210.00, 100, '2026-09-14 07:28:53', '2026-09-14 07:28:53', 100, '2033-02-02'),
(6, 1, 1, 'Batch-6', '2026-09-14', NULL, 2000.00, 5.00, 2100.00, 200, '2026-09-14 07:31:21', '2026-09-14 08:27:44', 194, '2029-02-02'),
(7, 1, 1, 'Batch-7', '2026-09-14', NULL, 1000.00, 5.00, 1050.00, 100, '2026-09-14 07:31:40', '2026-09-14 07:31:40', 100, '2029-02-02'),
(8, 1, 1, 'Batch-8', '2026-09-14', NULL, 1000.00, 5.00, 1050.00, 200, '2026-09-14 07:31:58', '2026-09-14 07:31:58', 200, '2036-02-02'),
(9, 1, NULL, 'Batch-9', '2026-09-14', NULL, 1000.00, 5.00, 1050.00, 1000, '2026-09-14 07:32:20', '2026-09-14 07:32:20', 1000, '2036-02-02'),
(11, 1, 1, 'Batch-11', '2026-09-14', NULL, 1000.00, 5.00, 1050.00, 100, '2026-09-14 07:33:06', '2026-09-14 07:33:06', 100, '2029-02-10'),
(12, 2, 1, 'Batch-1', '2026-09-14', NULL, 20.00, 5.00, 21.00, 1000, '2026-09-14 07:49:45', '2026-09-15 01:27:56', 0, '2029-02-02'),
(17, 4, 1, 'Batch-1', '2026-09-15', NULL, 25.00, 5.00, 26.25, 1, '2026-09-15 04:29:23', '2026-09-15 04:29:50', 0, '2030-02-02'),
(19, 5, 1, 'Batch-1', '2026-09-15', NULL, 200.00, 5.00, 210.00, 1, '2026-09-15 07:20:22', '2026-09-15 07:20:37', 0, '2030-02-02'),
(20, 2, NULL, 'Batch-2', '2026-09-15', NULL, 50.00, 5.00, 52.50, 1, '2026-09-15 07:56:45', '2026-09-15 07:57:01', 0, '2029-02-02'),
(21, 1, 1, 'Batch-TEST', '2026-09-15', NULL, 10.00, 5.00, 10.50, 10, '2026-09-15 08:47:38', '2026-09-15 08:47:38', 10, '2030-12-31'),
(22, 1, 1, 'Batch-14', '2026-09-15', NULL, 10.00, 5.00, 10.50, 10, '2026-09-15 08:55:20', '2026-09-15 08:55:20', 10, '2030-12-31'),
(23, 5, 1, 'Batch-2', '2026-09-15', NULL, 50.00, 5.00, 52.50, 1, '2026-09-15 08:55:31', '2026-09-15 08:55:46', 0, '2029-02-02'),
(24, 5, 1, 'Batch-3', '2026-09-15', NULL, 50.00, 5.00, 52.50, 1, '2026-09-15 08:57:51', '2026-09-15 08:58:03', 0, '2030-02-02'),
(25, 5, 1, 'Batch-4', '2026-09-15', NULL, 50.00, 5.00, 52.50, 1, '2026-09-15 09:02:14', '2026-09-15 09:02:33', 0, '2036-02-02'),
(26, 5, 1, 'Batch-5', '2026-09-15', NULL, 50.00, 5.00, 52.50, 1, '2026-09-15 09:05:45', '2026-09-15 09:05:52', 0, '2036-02-02'),
(27, 5, 1, 'Batch-6', '2026-09-15', NULL, 50.00, 5.00, 52.50, 1, '2026-09-15 09:09:56', '2026-09-15 09:10:05', 0, '2029-02-02'),
(28, 5, 1, 'Batch-7', '2026-09-15', NULL, 50.00, 5.00, 52.50, 1, '2026-09-15 09:17:52', '2026-09-15 09:18:05', 0, '2030-02-02');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_alerts`
--

CREATE TABLE `inventory_alerts` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `alert_type` enum('low_stock','near_expiry','expired') NOT NULL,
  `severity` enum('warning','critical') NOT NULL,
  `message` varchar(255) NOT NULL,
  `current_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expiry_date` date DEFAULT NULL,
  `status` enum('open','resolved','dismissed') NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_alerts`
--

INSERT INTO `inventory_alerts` (`id`, `product_id`, `batch_id`, `alert_type`, `severity`, `message`, `current_quantity`, `minimum_stock`, `expiry_date`, `status`, `created_at`, `updated_at`, `resolved_at`) VALUES
(388, 1, 13, 'expired', 'critical', 'Restime - Simeticone (40.00) (Batch-12) has expired and should be removed from sale.', 0.00, 0.00, '2020-02-02', 'open', '2026-09-15 09:12:08', '2026-09-15 09:12:08', NULL),
(389, 3, 14, 'expired', 'critical', 'Amoxil - Paracetamol (500.00) (Batch-1) has expired and should be removed from sale.', 0.00, 0.00, '2026-02-02', 'open', '2026-09-15 09:12:08', '2026-09-15 09:12:08', NULL),
(390, 3, 15, 'expired', 'critical', 'Amoxil - Paracetamol (500.00) (Batch-2) has expired and should be removed from sale.', 0.00, 0.00, '2026-08-01', 'open', '2026-09-15 09:12:08', '2026-09-15 09:12:08', NULL);

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
  `quantity` int(11) DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `reason` varchar(100) NOT NULL DEFAULT 'Expired',
  `disposed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_disposals`
--

INSERT INTO `inventory_disposals` (`id`, `product_id`, `batch_number`, `quantity`, `expiry_date`, `reason`, `disposed_at`) VALUES
(1, 3, 'Batch-1', 1, '2026-02-02', 'Expired', '2026-09-15 09:20:19'),
(2, 1, 'Batch-1', 0, '2030-02-02', 'no stock', '2026-09-15 09:49:02'),
(3, 1, 'Batch-1', 0, '2030-02-02', 'no stock', '2026-09-15 09:49:29'),
(4, 3, 'Batch-2', 100, '2026-08-01', 'Expired', '2026-09-15 09:53:26'),
(5, 1, 'Batch-3', 50, '2030-02-02', 'Damage', '2026-09-15 12:16:59'),
(6, 1, 'Batch-10', 20, '2029-02-02', 'damage', '2026-09-15 12:25:20'),
(7, 4, 'TEST-DISP-1789446972', 0, '2026-10-15', 'Expired', '2026-09-15 12:36:12'),
(8, 1, 'Batch-3', 10, '2030-02-02', 'damaged', '2026-09-15 12:37:02'),
(9, 1, 'Batch-8', 0, '2036-02-02', 'Expired', '2026-09-15 15:19:26'),
(10, 1, 'Batch-3', 12, '2030-02-02', 'damages', '2026-09-15 15:35:29'),
(11, 1, 'Batch-12', 222, '2020-02-02', 'Expired', '2026-09-15 15:50:02'),
(12, 1, 'Batch-13', 1, '2020-02-02', 'Expired', '2026-09-15 15:50:02'),
(13, 1, 'Batch-3', 10, '2030-02-02', 'damages', '2026-09-15 15:50:45'),
(14, 1, 'Batch-3', 10, '2030-02-02', 'damages', '2026-09-15 16:34:35'),
(15, 1, 'Batch-4', 10, '2035-02-02', 'damaged', '2026-09-15 16:59:06'),
(16, 1, 'Batch-3', 10, '2030-02-02', 'dM', '2026-09-15 17:07:43');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_no_stock`
--

CREATE TABLE `inventory_no_stock` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `current_quantity` int(11) NOT NULL DEFAULT 0,
  `received_quantity` int(11) NOT NULL DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `reason` varchar(100) NOT NULL DEFAULT 'No stock',
  `moved_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_no_stock`
--

INSERT INTO `inventory_no_stock` (`id`, `product_id`, `batch_number`, `current_quantity`, `received_quantity`, `expiry_date`, `reason`, `moved_at`) VALUES
(1, 1, 'Batch-1', 0, 100, '2030-02-02', 'No stock', '2026-09-15 15:53:31'),
(2, 1, 'Batch-1', 0, 100, '2030-02-02', 'No stock', '2026-09-15 15:54:03'),
(3, 1, 'Batch-1', 0, 100, '2030-02-02', 'No stock', '2026-09-15 15:54:03'),
(4, 1, 'Batch-1', 0, 100, '2030-02-02', 'No stock', '2026-09-15 15:54:04'),
(5, 1, 'Batch-1', 0, 100, '2030-02-02', 'No stock', '2026-09-15 15:54:04'),
(6, 1, 'Batch-1', 0, 100, '2030-02-02', 'No stock', '2026-09-15 15:54:17'),
(7, 1, 'Batch-1', 0, 100, '2030-02-02', 'No stock', '2026-09-15 15:54:21'),
(8, 1, 'Batch-1', 0, 100, '2030-02-02', 'No stock', '2026-09-15 15:54:24'),
(9, 1, 'Batch-2', 0, 100, '2027-05-08', 'No stock', '2026-09-15 15:54:51'),
(10, 2, 'Batch-1', 0, 1000, '2029-02-02', 'No stock', '2026-09-15 15:54:51'),
(11, 4, 'Batch-1', 0, 1, '2030-02-02', 'No stock', '2026-09-15 15:54:51'),
(12, 5, 'Batch-1', 0, 1, '2030-02-02', 'No stock', '2026-09-15 15:54:51'),
(13, 2, 'Batch-2', 0, 1, '2029-02-02', 'No stock', '2026-09-15 15:57:05'),
(14, 5, 'Batch-2', 0, 1, '2029-02-02', 'No stock', '2026-09-15 16:56:20'),
(15, 5, 'Batch-3', 0, 1, '2030-02-02', 'No stock', '2026-09-15 16:58:26'),
(16, 5, 'Batch-4', 0, 1, '2036-02-02', 'No stock', '2026-09-15 17:03:23'),
(17, 5, 'Batch-5', 0, 1, '2036-02-02', 'No stock', '2026-09-15 17:06:04'),
(18, 5, 'Batch-6', 0, 1, '2029-02-02', 'No stock', '2026-09-15 17:10:19'),
(19, 5, 'Batch-7', 0, 1, '2030-02-02', 'No stock', '2026-09-15 17:18:24');

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

--
-- Dumping data for table `override_log`
--

INSERT INTO `override_log` (`id`, `transaction_id`, `product_id`, `product_name`, `cashier_id`, `cashier_name`, `approver_id`, `approver_name`, `original_price`, `discounted_price`, `discount_amount`, `discount_percent`, `reason`, `created_at`) VALUES
(1, NULL, 1, 'Restime Simeticone 40.00 mg Drops (10.00 mL per unit)', 9, 'owner321', 1, 'Andrew Pablo', 159.60, 119.70, 39.90, 25.00, 'prind', '2026-09-10 13:25:58'),
(2, NULL, 1, 'Restime Simeticone 40.00 mg Drops (10.00 mL per unit)', 9, 'owner321', 1, 'Andrew Pablo', 159.60, 156.41, 3.19, 2.00, 'prind', '2026-09-10 13:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 1, '6fef0807307f8dcebc4f720e0ad5d614188cd689c0128b40debc3807e6528a0f', '2026-09-10 12:48:10', '2026-09-10 12:19:00', '2026-09-10 04:18:10'),
(2, 1, 'ecf65bed90d968cd64411aa1e9cbb48e0fc7b6f7abb331d908091bf7e37095ab', '2026-09-10 12:49:00', '2026-09-10 12:21:08', '2026-09-10 04:19:00'),
(3, 1, 'e25a7052841f85a93c65c6398a808e4225ae17c7553b3852f041af3cb33f1578', '2026-09-10 12:51:08', '2026-09-10 12:21:42', '2026-09-10 04:21:08'),
(4, 1, '927b302f16beed8bdb2f08a1ba98ac027f2b125ea15e1cbad52c62b4cc8b6ad6', '2026-09-10 12:51:42', '2026-09-10 12:31:16', '2026-09-10 04:21:42'),
(5, 1, '69ba9db977a7638633b6f053cd0b36e2e2bcb1078e168137efc673d7ca689c99', '2026-09-10 13:01:16', '2026-09-10 12:31:20', '2026-09-10 04:31:16'),
(6, 1, 'f11f8a3a400f396c822c96cca0f70653a163fec86cc8779a23506bd34f513689', '2026-09-10 13:01:20', '2026-09-10 12:31:57', '2026-09-10 04:31:20'),
(7, 1, '91a7e2d88c5c2fff27cb313dae9034da15121eb057d2fe166c019fb2e1ec6a22', '2026-09-10 13:01:57', '2026-09-10 12:32:02', '2026-09-10 04:31:57'),
(8, 1, '309781ad046196afcd3ccd61788c11fd2ef3c846a16c49a4abe4f093a9140124', '2026-09-10 13:02:02', '2026-09-10 12:33:21', '2026-09-10 04:32:02'),
(9, 1, '782724b487cf20d9f0f85bc77ee694c366ba534292b0e274e703d9b64228b848', '2026-09-10 13:03:45', '2026-09-10 12:37:01', '2026-09-10 04:33:45'),
(10, 1, 'd445e5269b9598e3425dc379e3e2a848fa1b58c47962030c6eb0244fa931bcbf', '2026-09-10 13:08:03', '2026-09-10 12:42:18', '2026-09-10 04:38:03'),
(11, 1, 'fa15a4cf8c28b17185c2e028a3b3fc88b75b5e8af5ff6ffcbe90266b13d4b72d', '2026-09-10 13:12:18', '2026-09-10 12:43:06', '2026-09-10 04:42:18'),
(12, 1, 'cf3718badcf11e78d7fbde8d9f21f477af34d5012e78005700b41b710be78821', '2026-09-10 13:13:06', NULL, '2026-09-10 04:43:06');

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

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `branded_name`, `generic_name`, `strength`, `measurement_id`, `barcode`, `category_id`, `classification_id`, `units_per_package`, `imageproduct`, `is_basic_necessities`, `package_type`, `dosage_form`, `dosage_form_id`, `strength_per_quantity`, `strength_per_quantity_unit`, `is_hidden`) VALUES
(1, 'Restime', 'Simeticone', 40.00, 2, '4807788523709', 18, NULL, 0, '6a9fa888056ce-1788848264IMG_4549.jpeg', 0, '', 'Drops', 7, 10.00, 'mL', 0),
(2, '', 'chip ahoys', 65.00, 3, '427277220421', 17, NULL, 0, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, '', '', NULL, 1.00, 'pc', 0),
(3, 'Amoxil', 'Paracetamol', 500.00, 2, '962694121724', 18, NULL, 0, '6aa7b367bb010-1789375335ChatGPT Image Sep 7, 2026, 05_27_12 PM.png', 0, '', 'Capsule', 2, 1.00, 'pcs', 0),
(4, 'coke', 'DF', 750.00, 6, '569941321341', 27, NULL, 0, '6aa8c9a3ab9ef-178944656390cad0b147733b4e7eebebc091c701f1.jpg', 0, '', '', NULL, 1.00, 'pc', 0),
(5, '', 'fe', 344.00, 2, '534635950872', 24, NULL, 0, '', 0, '', '', NULL, NULL, '', 0);

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
(17, 'Prescription Medicines', 1, 1, 1),
(18, 'Over-the-Counter (OTC)', 1, 1, 1),
(19, 'Medical Supplies', 1, 1, 1),
(20, 'Vitamins & Supplements', 1, 1, 1),
(21, 'First Aid', 1, 1, 1),
(22, 'Diagnostics', 1, 1, 1),
(23, 'Herbal Products', 1, 1, 1),
(24, 'Health & Wellness', 1, 1, 1),
(25, 'Personal Care', 1, 1, 1),
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
  `verified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `verified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pwd_customers`
--

INSERT INTO `pwd_customers` (`id`, `customer_name`, `id_number`, `cashier_id`, `verified_at`, `verified_by`) VALUES
(1, 'ANDREW PABLO', '12345678910', 1, '2026-09-08 14:53:55', NULL),
(2, 'ANDREW PABLO', '123456789100', 1, '2026-09-09 08:04:53', 1);

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

--
-- Dumping data for table `register_closings`
--

INSERT INTO `register_closings` (`id`, `user_id`, `business_date`, `system_cash`, `counted_cash`, `variance`, `notes`, `closed_at`) VALUES
(1, 1, '2026-09-08', 1037.50, 1037.50, 0.00, NULL, '2026-09-10 03:29:36'),
(2, 1, '2026-09-09', 28437.10, 28437.10, 0.00, NULL, '2026-09-10 03:37:18'),
(3, 1, '2026-09-10', 1159.60, 1159.60, 0.00, NULL, '2026-09-10 03:38:36'),
(4, 2, '2026-09-10', 1159.60, 1159.60, 0.00, NULL, '2026-09-10 03:45:57'),
(5, 3, '2026-09-10', 1478.80, 1478.80, 0.00, NULL, '2026-09-10 03:47:00'),
(6, 1, '2026-09-14', 3525.10, 3525.10, 0.00, NULL, '2026-09-14 07:14:51'),
(7, 2, '2026-09-14', 6289.25, 6289.25, 0.00, NULL, '2026-09-15 01:27:27');

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

--
-- Dumping data for table `register_openings`
--

INSERT INTO `register_openings` (`id`, `user_id`, `business_date`, `opening_cash`, `notes`, `opened_at`) VALUES
(1, 1, '2026-09-08', 1000.00, NULL, '2026-09-08 06:47:28'),
(2, 1, '2026-09-09', 1000.00, NULL, '2026-09-08 23:45:34'),
(3, 1, '2026-09-10', 1000.00, NULL, '2026-09-10 03:37:32'),
(4, 2, '2026-09-10', 1000.00, NULL, '2026-09-10 03:45:37'),
(5, 3, '2026-09-10', 1000.00, NULL, '2026-09-10 03:46:26'),
(6, 9, '2026-09-10', 1000.00, NULL, '2026-09-10 05:18:33'),
(7, 1, '2026-09-14', 1000.00, NULL, '2026-09-14 06:51:24'),
(8, 2, '2026-09-14', 1000.00, NULL, '2026-09-14 07:38:01'),
(9, 3, '2026-09-14', 1000.00, NULL, '2026-09-14 08:27:32'),
(10, 2, '2026-09-15', 1000.00, NULL, '2026-09-15 01:27:33');

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

--
-- Dumping data for table `return_items`
--

INSERT INTO `return_items` (`id`, `return_transaction_id`, `product_id`, `quantity`, `price`, `subtotal`, `item_type`, `restocked`, `restockable`, `cost_of_goods`) VALUES
(1, 1, 1, 1, 37.50, 37.50, 'returned', 1, 0, 0.00);

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

--
-- Dumping data for table `return_transactions`
--

INSERT INTO `return_transactions` (`id`, `original_transaction_id`, `user_id`, `refund_amount`, `replacement_product_id`, `replacement_quantity`, `reason`, `refund_method`, `created_at`, `approver_id`) VALUES
(1, 1, 2, 37.50, NULL, 0, 'Customer Request / Change of Mind', 'Cash', '2026-09-15 16:12:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `senior_customers`
--

CREATE TABLE `senior_customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `verified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `verified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `senior_customers`
--

INSERT INTO `senior_customers` (`id`, `customer_name`, `id_number`, `cashier_id`, `verified_at`, `verified_by`) VALUES
(1, 'ANDREW PABLO', '12345678910', 1, '2026-09-09 08:03:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `serving_unit`
--

CREATE TABLE `serving_unit` (
  `id` int(11) NOT NULL,
  `serving_unit_name` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `serving_unit`
--

INSERT INTO `serving_unit` (`id`, `serving_unit_name`) VALUES
(2, 'mg'),
(3, 'grams'),
(6, 'ML');

-- --------------------------------------------------------

--
-- Table structure for table `store_settings`
--

CREATE TABLE `store_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_settings`
--

INSERT INTO `store_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('receipt_paper', '80', '2026-09-14 16:19:58'),
('statutory_discount_cap', '200.00', '2026-09-14 16:19:58');

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

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `contact_person`, `contact_number`, `email`, `address`, `supplier_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ABC PHARMA', NULL, '+639651800675', 'andrewpablo2005@gmail.com', 'Manila Philippines', NULL, 1, '2026-09-08 06:18:21', '2026-09-08 06:18:21');

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

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `discount_id`, `customer_name`, `customer_id`, `customer_type`, `total_amount`, `created_at`, `discount_total`, `total_vat_exemption`) VALUES
(1, 1, 3, 'ANDREW PABLO', '1', 'pwd', 37.50, '2026-09-08 06:55:29', 9.37, 5.63),
(2, 1, 1, 'Walk-in', NULL, '', 2992.50, '2026-09-08 23:51:26', 0.00, 0.00),
(3, 1, 1, 'Walk-in', NULL, '', 682.50, '2026-09-08 23:51:36', 0.00, 0.00),
(4, 1, 1, 'Walk-in', NULL, '', 2992.50, '2026-09-08 23:53:33', 0.00, 0.00),
(5, 1, 1, 'Walk-in', NULL, '', 52.50, '2026-09-08 23:56:21', 0.00, 0.00),
(6, 1, 3, 'ANDREW PABLO', '2', 'pwd', 37.50, '2026-09-09 00:05:15', 9.37, 5.63),
(7, 1, 1, 'Walk-in', NULL, '', 210.00, '2026-09-09 00:11:35', 0.00, 0.00),
(8, 1, 1, 'Walk-in', NULL, '', 472.50, '2026-09-09 00:16:23', 0.00, 0.00),
(9, 1, 1, 'Walk-in', NULL, '', 1312.50, '2026-09-09 00:18:15', 0.00, 0.00),
(10, 1, 1, 'Walk-in', NULL, '', 630.00, '2026-09-09 00:27:22', 0.00, 0.00),
(11, 1, 1, 'Walk-in', NULL, '', 367.50, '2026-09-09 00:29:38', 0.00, 0.00),
(12, 1, 1, 'Walk-in', NULL, '', 17556.00, '2026-09-09 00:37:00', 0.00, 0.00),
(13, 1, 2, 'ANDREW PABLO', '1', 'senior', 131.10, '2026-09-09 00:46:34', 28.50, 0.00),
(14, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-10 03:38:09', 0.00, 0.00),
(15, 1, 1, 'Walk-in', NULL, '', 319.20, '2026-09-10 03:39:01', 0.00, 0.00),
(16, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-10 03:43:54', 0.00, 0.00),
(17, 2, 1, 'Walk-in', NULL, '', 159.60, '2026-09-10 03:45:44', 0.00, 0.00),
(18, 3, 1, 'Walk-in', NULL, '', 478.80, '2026-09-10 03:46:33', 0.00, 0.00),
(19, 9, 1, 'Walk-in', NULL, '', 159.60, '2026-09-10 05:19:46', 0.00, 0.00),
(20, 9, 1, 'Walk-in', NULL, '', 478.80, '2026-09-10 05:23:17', 0.00, 0.00),
(21, 9, 1, 'Walk-in', NULL, '', 92.98, '2026-09-10 05:26:15', 26.72, 0.00),
(22, 9, 1, 'Walk-in', NULL, '', 153.22, '2026-09-10 05:27:35', 3.19, 0.00),
(23, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-14 06:53:59', 0.00, 0.00),
(24, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-14 06:54:33', 0.00, 0.00),
(25, 1, 1, 'Walk-in', NULL, '', 319.20, '2026-09-14 06:54:44', 0.00, 0.00),
(26, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-14 06:54:56', 0.00, 0.00),
(27, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-14 06:57:43', 0.00, 0.00),
(28, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-14 06:57:59', 0.00, 0.00),
(29, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-14 06:59:24', 0.00, 0.00),
(30, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-14 07:00:09', 0.00, 0.00),
(31, 1, 1, 'Walk-in', NULL, '', 159.60, '2026-09-14 07:02:49', 0.00, 0.00),
(32, 1, 1, 'Walk-in', NULL, '', 798.00, '2026-09-14 07:08:00', 0.00, 0.00),
(33, 1, 2, 'ANDREW PABLO', '1', 'senior', 131.10, '2026-09-14 07:09:42', 28.50, 0.00),
(34, 2, 1, 'Walk-in', NULL, '', 1050.00, '2026-09-14 07:38:30', 0.00, 0.00),
(35, 2, 1, 'Walk-in', NULL, '', 1050.00, '2026-09-14 07:39:14', 0.00, 0.00),
(36, 2, 1, 'Walk-in', NULL, '', 1050.00, '2026-09-14 07:40:02', 0.00, 0.00),
(37, 2, 2, 'ANDREW PABLO', '1', 'senior', 953.50, '2026-09-14 07:40:22', 96.50, 0.00),
(38, 2, 2, 'ANDREW PABLO', '1', 'senior', 1050.00, '2026-09-14 07:44:00', 0.00, 0.00),
(39, 2, 1, 'Walk-in', NULL, '', 21.00, '2026-09-14 07:50:35', 0.00, 0.00),
(40, 2, 2, 'ANDREW PABLO', '1', 'senior', 21.00, '2026-09-14 07:51:13', 0.00, 0.00),
(41, 2, 2, 'ANDREW PABLO', '1', 'senior', 21.00, '2026-09-14 07:52:38', 0.00, 0.00),
(42, 2, 2, 'ANDREW PABLO', '1', 'senior', 21.00, '2026-09-14 08:03:15', 0.00, 0.00),
(43, 2, 3, 'ANDREW PABLO', '1', 'pwd', 17.25, '2026-09-14 08:06:44', 3.75, 0.00),
(44, 2, 3, 'ANDREW PABLO', '1', 'pwd', 17.25, '2026-09-14 08:07:22', 3.75, 0.00),
(45, 2, 2, 'ANDREW PABLO', '1', 'senior', 17.25, '2026-09-14 08:20:26', 3.75, 0.00),
(46, 3, 1, 'Walk-in', NULL, '', 1050.00, '2026-09-14 08:27:44', 0.00, 0.00),
(47, 2, 1, 'Walk-in', NULL, '', 20853.00, '2026-09-15 01:27:56', 0.00, 0.00),
(48, 2, 1, 'Walk-in', NULL, '', 26.25, '2026-09-15 04:29:50', 0.00, 0.00),
(49, 2, 1, 'Walk-in', NULL, '', 210.00, '2026-09-15 07:20:37', 0.00, 0.00),
(50, 2, 1, 'Walk-in', NULL, '', 52.50, '2026-09-15 07:57:01', 0.00, 0.00),
(51, 2, 1, 'Walk-in', NULL, '', 52.50, '2026-09-15 08:55:46', 0.00, 0.00),
(52, 2, 1, 'Walk-in', NULL, '', 52.50, '2026-09-15 08:58:03', 0.00, 0.00),
(53, 2, 1, 'Walk-in', NULL, '', 52.50, '2026-09-15 09:02:33', 0.00, 0.00),
(54, 2, 1, 'Walk-in', NULL, '', 52.50, '2026-09-15 09:05:52', 0.00, 0.00),
(55, 2, 1, 'Walk-in', NULL, '', 52.50, '2026-09-15 09:10:05', 0.00, 0.00),
(56, 2, 1, 'Walk-in', NULL, '', 52.50, '2026-09-15 09:18:05', 0.00, 0.00);

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

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `batch_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 1, 2, 1, 52.50, 37.50),
(2, 2, 1, 2, 57, 52.50, 2992.50),
(3, 3, 1, 2, 13, 52.50, 682.50),
(4, 4, 1, NULL, 57, 52.50, 2992.50),
(5, 5, 1, 1, 1, 52.50, 52.50),
(6, 6, 1, 1, 1, 52.50, 37.50),
(7, 7, 1, 1, 4, 52.50, 210.00),
(8, 8, 1, 1, 9, 52.50, 472.50),
(9, 9, 1, 1, 25, 52.50, 1312.50),
(10, 10, 1, 1, 12, 52.50, 630.00),
(11, 11, 1, 1, 7, 52.50, 367.50),
(12, 12, 1, NULL, 110, 159.60, 17556.00),
(13, 13, 1, 3, 1, 159.60, 131.10),
(14, 14, 1, 3, 1, 159.60, 159.60),
(15, 15, 1, 3, 2, 159.60, 319.20),
(16, 16, 1, 3, 1, 159.60, 159.60),
(17, 17, 1, 3, 1, 159.60, 159.60),
(18, 18, 1, 3, 3, 159.60, 478.80),
(19, 19, 1, 3, 1, 159.60, 159.60),
(20, 20, 1, 3, 3, 159.60, 478.80),
(21, 21, 1, 3, 1, 119.70, 92.98),
(22, 22, 1, 3, 1, 156.41, 153.22),
(23, 23, 1, 3, 1, 159.60, 159.60),
(24, 24, 1, 3, 1, 159.60, 159.60),
(25, 25, 1, 3, 2, 159.60, 319.20),
(26, 26, 1, 3, 1, 159.60, 159.60),
(27, 27, 1, 3, 1, 159.60, 159.60),
(28, 28, 1, 3, 1, 159.60, 159.60),
(29, 29, 1, 3, 1, 159.60, 159.60),
(30, 30, 1, 3, 1, 159.60, 159.60),
(31, 31, 1, 3, 1, 159.60, 159.60),
(32, 32, 1, 3, 5, 159.60, 798.00),
(33, 33, 1, 3, 1, 159.60, 131.10),
(34, 34, 1, 6, 1, 1050.00, 1050.00),
(35, 35, 1, 6, 1, 1050.00, 1050.00),
(36, 36, 1, 6, 1, 1050.00, 1050.00),
(37, 37, 1, 6, 1, 1050.00, 953.50),
(38, 38, 1, 6, 1, 1050.00, 1050.00),
(39, 39, 2, 12, 1, 21.00, 21.00),
(40, 40, 2, 12, 1, 21.00, 21.00),
(41, 41, 2, 12, 1, 21.00, 21.00),
(42, 42, 2, 12, 1, 21.00, 21.00),
(43, 43, 2, 12, 1, 21.00, 17.25),
(44, 44, 2, 12, 1, 21.00, 17.25),
(45, 45, 2, 12, 1, 21.00, 17.25),
(46, 46, 1, 6, 1, 1050.00, 1050.00),
(47, 47, 2, 12, 993, 21.00, 20853.00),
(48, 48, 4, 17, 1, 26.25, 26.25),
(49, 49, 5, 19, 1, 210.00, 210.00),
(50, 50, 2, 20, 1, 52.50, 52.50),
(51, 51, 5, 23, 1, 52.50, 52.50),
(52, 52, 5, 24, 1, 52.50, 52.50),
(53, 53, 5, 25, 1, 52.50, 52.50),
(54, 54, 5, 26, 1, 52.50, 52.50),
(55, 55, 5, 27, 1, 52.50, 52.50),
(56, 56, 5, 28, 1, 52.50, 52.50);

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

--
-- Dumping data for table `transaction_item_batches`
--

INSERT INTO `transaction_item_batches` (`id`, `transaction_item_id`, `inventory_id`, `quantity`, `purchase_cost`, `created_at`) VALUES
(1, 1, 2, 1, 50.00, '2026-09-08 06:55:29'),
(2, 2, 2, 57, 50.00, '2026-09-08 23:51:26'),
(3, 3, 2, 13, 50.00, '2026-09-08 23:51:36'),
(4, 4, 2, 29, 50.00, '2026-09-08 23:53:33'),
(5, 4, 1, 28, 110.00, '2026-09-08 23:53:33'),
(6, 5, 1, 1, 110.00, '2026-09-08 23:56:21'),
(7, 6, 1, 1, 110.00, '2026-09-09 00:05:15'),
(8, 7, 1, 4, 110.00, '2026-09-09 00:11:35'),
(9, 8, 1, 9, 110.00, '2026-09-09 00:16:23'),
(10, 9, 1, 25, 110.00, '2026-09-09 00:18:15'),
(11, 10, 1, 12, 110.00, '2026-09-09 00:27:22'),
(12, 11, 1, 7, 110.00, '2026-09-09 00:29:38'),
(13, 12, 1, 13, 110.00, '2026-09-09 00:37:00'),
(14, 12, 3, 97, 152.00, '2026-09-09 00:37:00'),
(15, 13, 3, 1, 152.00, '2026-09-09 00:46:34'),
(16, 14, 3, 1, 152.00, '2026-09-10 03:38:09'),
(17, 15, 3, 2, 152.00, '2026-09-10 03:39:01'),
(18, 16, 3, 1, 152.00, '2026-09-10 03:43:54'),
(19, 17, 3, 1, 152.00, '2026-09-10 03:45:44'),
(20, 18, 3, 3, 152.00, '2026-09-10 03:46:33'),
(21, 19, 3, 1, 152.00, '2026-09-10 05:19:46'),
(22, 20, 3, 3, 152.00, '2026-09-10 05:23:17'),
(23, 21, 3, 1, 152.00, '2026-09-10 05:26:15'),
(24, 22, 3, 1, 152.00, '2026-09-10 05:27:35'),
(25, 23, 3, 1, 152.00, '2026-09-14 06:53:59'),
(26, 24, 3, 1, 152.00, '2026-09-14 06:54:33'),
(27, 25, 3, 2, 152.00, '2026-09-14 06:54:44'),
(28, 26, 3, 1, 152.00, '2026-09-14 06:54:56'),
(29, 27, 3, 1, 152.00, '2026-09-14 06:57:43'),
(30, 28, 3, 1, 152.00, '2026-09-14 06:57:59'),
(31, 29, 3, 1, 152.00, '2026-09-14 06:59:24'),
(32, 30, 3, 1, 152.00, '2026-09-14 07:00:09'),
(33, 31, 3, 1, 152.00, '2026-09-14 07:02:49'),
(34, 32, 3, 5, 152.00, '2026-09-14 07:08:00'),
(35, 33, 3, 1, 152.00, '2026-09-14 07:09:42'),
(36, 34, 6, 1, 2000.00, '2026-09-14 07:38:30'),
(37, 35, 6, 1, 2000.00, '2026-09-14 07:39:14'),
(38, 36, 6, 1, 2000.00, '2026-09-14 07:40:02'),
(39, 37, 6, 1, 2000.00, '2026-09-14 07:40:22'),
(40, 38, 6, 1, 2000.00, '2026-09-14 07:44:00'),
(41, 39, 12, 1, 20.00, '2026-09-14 07:50:35'),
(42, 40, 12, 1, 20.00, '2026-09-14 07:51:13'),
(43, 41, 12, 1, 20.00, '2026-09-14 07:52:38'),
(44, 42, 12, 1, 20.00, '2026-09-14 08:03:15'),
(45, 43, 12, 1, 20.00, '2026-09-14 08:06:44'),
(46, 44, 12, 1, 20.00, '2026-09-14 08:07:22'),
(47, 45, 12, 1, 20.00, '2026-09-14 08:20:26'),
(48, 46, 6, 1, 2000.00, '2026-09-14 08:27:44'),
(49, 47, 12, 993, 20.00, '2026-09-15 01:27:56'),
(50, 48, 17, 1, 25.00, '2026-09-15 04:29:50'),
(51, 49, 19, 1, 200.00, '2026-09-15 07:20:37'),
(52, 50, 20, 1, 50.00, '2026-09-15 07:57:01'),
(53, 51, 23, 1, 50.00, '2026-09-15 08:55:46'),
(54, 52, 24, 1, 50.00, '2026-09-15 08:58:03'),
(55, 53, 25, 1, 50.00, '2026-09-15 09:02:33'),
(56, 54, 26, 1, 50.00, '2026-09-15 09:05:52'),
(57, 55, 27, 1, 50.00, '2026-09-15 09:10:05'),
(58, 56, 28, 1, 50.00, '2026-09-15 09:18:05');

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
(20, 'box'),
(21, 'mgg');

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
(1, 'andrew_owner', '$2y$10$2Rqa4Se609iFKRTnJpX3SuAIE7v1vZwwCtGK2WHBvnBxQ2.w9RBAS', '1234567', 'Owner', 0, NULL, 'active', '2026-09-05 08:53:02'),
(2, 'andrew_admin', '$2y$10$.pMY78gCNdiWGwCw8DAIse7SS./j5d9T8pQ87YhhLoOum4yKzJL.m', '1234567', 'Admin', 0, '2026-08-20 01:56:58', 'active', '2026-09-05 08:53:02'),
(3, 'andrew_staff', '$2y$10$WESQ6f2mApseNhMhKMmW8e6gg.tp9AU8CsY/mQrU4g6GHEWmFCWGG', '1234567', 'Staff', 0, '2026-07-26 14:50:35', 'active', '2026-09-05 08:53:02'),
(4, 'staff1', '$2y$10$U60z2JyVRKxJ.x36cqpJkuzZDPRFtOF5aZqUO7QCyBBN.P614oIoy', NULL, 'Staff', 0, NULL, 'active', '2026-09-05 08:53:02'),
(5, 'sampleAccount', '$2y$10$G.IAWGX.MPWQ8E1bIvuykO7Ph//70zWAqxZdMdDVMFiZDu7qFtBLm', NULL, 'Staff', 0, NULL, 'active', '2026-09-05 08:53:02'),
(6, 'ownerewew', '$2y$10$ksJeBpMxV9gjhPxayLmxSexH68mbQj4MK8ImAs8aCsGYldOCYOJgG', NULL, 'Staff', 0, NULL, 'active', '2026-09-05 08:53:02'),
(8, 'nokkkkkk', '$2y$10$0X1yddbjSnlz62SzMZDrM.BXlBO7Jkj1eACodyDzl3ZOYB7Nn9D6q', '$2y$10$wZJHXgcm8XCPw62rB.AFqenueraiaxuy8q7/AR8ihhxs/.8tcQZ5K', 'Staff', 0, NULL, 'active', '2026-09-05 08:54:32'),
(9, 'owner321', '$2y$10$2G0o0WOiEwKFdF9As1LcoOsCKMCTRx3Sjm8u8NDeVFeuUh.RfTnKa', '$2y$10$wYIYqlaZgkiYJkNvKmLD3uzjntTKzDZYocLXa3aILQXlXB41ObkTC', 'Owner', 0, NULL, 'active', '2026-09-10 04:04:08');

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
(8, 8, 'Andrewsfsef', 'Gonzales', 'Pablo', 23, 'Purok', 'Poblacion', 'JAEN (NUEVA ECIJA)', 'Nueva Ecija', 'Philippines', 'andrewpablo2005@gmail.com', '09651800675'),
(9, 9, 'Andrews', 'Gonzales', 'Pablos', 21, 'Purok 3', 'Niyugan', 'JAEN (NUEVA ECIJA)', 'Nueva Ecija', 'Philippines', 'andrewpablo2005@gmail.com', '09651800675');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_created` (`created_at`),
  ADD KEY `idx_activity_user` (`user_id`),
  ADD KEY `idx_activity_action` (`action`),
  ADD KEY `idx_activity_module` (`module`);

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
-- Indexes for table `inventory_alerts`
--
ALTER TABLE `inventory_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_alerts_status` (`status`,`alert_type`),
  ADD KEY `idx_inventory_alerts_product_batch` (`product_id`,`batch_id`);

--
-- Indexes for table `inventory_disposals`
--
ALTER TABLE `inventory_disposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_disposals_product` (`product_id`),
  ADD KEY `idx_inventory_disposals_expiry` (`expiry_date`);

--
-- Indexes for table `inventory_no_stock`
--
ALTER TABLE `inventory_no_stock`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_password_reset_token_hash` (`token_hash`),
  ADD KEY `idx_password_reset_user` (`user_id`),
  ADD KEY `idx_password_reset_expires` (`expires_at`);

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
-- Indexes for table `serving_unit`
--
ALTER TABLE `serving_unit`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `dosage_forms`
--
ALTER TABLE `dosage_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `inventory_alerts`
--
ALTER TABLE `inventory_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=391;

--
-- AUTO_INCREMENT for table `inventory_disposals`
--
ALTER TABLE `inventory_disposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `inventory_no_stock`
--
ALTER TABLE `inventory_no_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `override_log`
--
ALTER TABLE `override_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `pwd_customers`
--
ALTER TABLE `pwd_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `register_closings`
--
ALTER TABLE `register_closings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `register_openings`
--
ALTER TABLE `register_openings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `return_items`
--
ALTER TABLE `return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `return_transactions`
--
ALTER TABLE `return_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `senior_customers`
--
ALTER TABLE `senior_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `serving_unit`
--
ALTER TABLE `serving_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `transaction_batch_allocations`
--
ALTER TABLE `transaction_batch_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `transaction_item_batches`
--
ALTER TABLE `transaction_item_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `unit_measurement`
--
ALTER TABLE `unit_measurement`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users_info`
--
ALTER TABLE `users_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `fk_password_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
