-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2026 at 06:22 AM
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

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `username`, `role`, `module`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 00:53:07'),
(2, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 5, 'Added batch \'Batch-8\' (1 packs) to fe', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 00:55:27'),
(3, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 11, 'Opened register with 1,000.00 PHP opening cash for 2026-09-16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 00:55:33'),
(4, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 57, 'Completed sale #000057 — 52.50 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 00:55:39'),
(5, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-3\' (1 packs) to chip ahoys', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 00:57:25'),
(6, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 58, 'Completed sale #000058 — 52.50 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 00:57:35'),
(7, 1, 'andrew_owner', 'owner', 'inventory', 'batch_disposal', 'inventory', 3, 'Disposed 10 packs from batch \'Batch-3\' of Restime (reason: DAMAGES)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:00:15'),
(8, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 4, 'Added batch \'Batch-2\' (1 packs) to coke', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:02:03'),
(9, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 59, 'Completed sale #000059 — 52.50 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:02:28'),
(10, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 1, 'Added batch \'Batch-15\' (1 packs) to Restime', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:07:33'),
(11, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 60, 'Completed sale #000060 — 269,430.00 PHP, 2566 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:08:22'),
(12, 1, 'andrew_owner', 'owner', 'sales', 'register_close', 'register_closing', 8, 'Closed register for 2026-09-16 — system 270,587.50 PHP, counted 270,587.50 PHP, variance 0.00 PHP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:10:50'),
(13, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:10:53'),
(14, NULL, 'andrew_owner', 'guest', 'auth', 'login_failed', NULL, NULL, 'Failed sign-in (wrong password) for username \'andrew_owner\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:10:58'),
(15, NULL, 'andrew_owner', 'guest', 'auth', 'login_failed', NULL, NULL, 'Failed sign-in (wrong password) for username \'andrew_owner\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:11:04'),
(16, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:11:23'),
(17, 1, 'andrew_owner', 'owner', 'inventory', 'batch_disposal', 'inventory', 7, 'Disposed 0 packs from batch \'Batch-7\' of Restime (reason: Expired)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:15:52'),
(18, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-4\' (1 packs) to chip ahoys', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 01:16:13'),
(19, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-5\' (1000 packs) to chip ahoys', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 04:28:31'),
(20, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 04:57:47'),
(21, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 04:58:14'),
(22, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-6\' (16 packs) to chip ahoys', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 04:58:49'),
(23, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 4, 'Added batch \'Batch-3\' (16 packs) to coke', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 04:59:24'),
(24, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 04:59:34'),
(25, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 04:59:40'),
(26, 2, 'andrew_admin', 'admin', 'sales', 'register_close', 'register_closing', 9, 'Closed register for 2026-09-15 — system 22,456.75 PHP, counted 22,456.75 PHP, variance 0.00 PHP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 04:59:54'),
(27, 2, 'andrew_admin', 'admin', 'sales', 'register_open', 'register_opening', 12, 'Opened register with 100.00 PHP opening cash for 2026-09-16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:00:04'),
(28, 2, 'andrew_admin', 'admin', 'sales', 'sale_completed', 'transaction', 61, 'Completed sale #000061 — 267.75 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:00:10'),
(29, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 14, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:00:23'),
(30, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 14, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:00:28'),
(31, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:00:35'),
(32, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:00:37'),
(33, 2, 'andrew_admin', 'admin', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-7\' (15 packs) to chip ahoys', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:05:30'),
(34, 2, 'andrew_admin', 'admin', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-8\' (100 packs) to chip ahoys', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:07:00'),
(35, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 50 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:07:31'),
(36, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 50 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:07:35'),
(37, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 50 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:07:39'),
(38, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 70 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:08:00'),
(39, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 70 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:08:03'),
(40, 2, 'andrew_admin', 'admin', 'inventory', 'batch_add', 'product', 4, 'Added batch \'Batch-4\' (100 packs) to coke', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:08:38'),
(41, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 2 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:08:55'),
(42, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 2 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:08:58'),
(43, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 3 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:09:05'),
(44, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 3 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:09:07'),
(45, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:09:17'),
(46, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 05:10:32'),
(47, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 2, 'Added supplier \'Andrew Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 14:35:13'),
(48, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 3, 'Added supplier \'Andrew Gonzales Pabloff\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 14:36:49'),
(49, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 1, 'Opened register with 1,000.00 PHP opening cash for 2026-09-16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 14:56:33'),
(50, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 1, 'Completed sale #000001 — 14.40 PHP, 2 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 14:58:27'),
(51, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 7, 'Completed sale #000007 — 6.00 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:03:11'),
(52, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:06:09'),
(53, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:10:47'),
(54, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 1, 'Completed sale #000001 — 4,413.60 PHP, 26 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:11:26'),
(55, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:20:56'),
(56, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 10075, 'Added batch \'Batch-1\' (1 packs) to Kojic', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:26:39'),
(57, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 10075, 'Added batch \'Batch-2\' (10 packs) to Kojic', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:27:16'),
(58, 1, 'andrew_owner', 'owner', 'sales', 'register_close', 'register_closing', 1, 'Closed register for 2026-09-16 — system 5,413.60 PHP, counted 5,413.30 PHP, variance -0.30 PHP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:47:04'),
(59, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:47:07'),
(60, 1, 'Andrew Pablo', NULL, 'auth', 'password_reset_requested', 'user', 1, 'Requested a password reset link for \'andrewpablo2005@gmail.com\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-16 15:47:21'),
(61, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 00:47:34'),
(62, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 2, 'Opened register with 1,000.00 PHP opening cash for 2026-09-17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 00:52:04'),
(63, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:07:33'),
(64, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 2, 'Completed sale #000002 — 4.93 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:08:27'),
(65, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 3, 'Completed sale #000003 — 2.40 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:09:04'),
(66, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:28:23'),
(67, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:28:30'),
(68, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:35:57'),
(69, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:36:03'),
(70, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, senior discount: 20%, pwd discount: 5%, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:54:00'),
(71, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, senior discount: 20%, pwd discount: 20%, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:54:04'),
(72, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, senior discount: 15%, pwd discount: 20%, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:54:08'),
(73, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 4, 'Completed sale #000004 — 4.93 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:55:01'),
(74, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, senior discount: 15%, pwd discount: 15%, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:59:14'),
(75, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, senior discount: 15%, pwd discount: 15%, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 01:59:18'),
(76, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 5, 'Completed sale #000005 — 18.71 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:00:37'),
(77, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — receipt paper: 80mm, weekly discount limit: 200.00 PHP, senior discount: 20%, pwd discount: 15%, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:05:19'),
(78, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — Senior discount: 15.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:11:10'),
(79, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — Senior discount: 15.00%, PWD discount: 15.00%, receipt paper: 80mm, weekly discount limit: 200.00 PHP, low stock: 100, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:11:14'),
(80, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 6, 'Completed sale #000006 — 77.95 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:12:26'),
(81, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 7, 'Completed sale #000007 — 5.20 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:14:33'),
(82, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 8, 'Completed sale #000008 — 5.20 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:21:19'),
(83, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — Senior discount: 20.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:22:49'),
(84, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — Senior discount: 14.36%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:23:09'),
(85, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — Senior discount: 20.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:23:19'),
(86, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 9, 'Completed sale #000009 — 17.74 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:23:50'),
(87, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 10, 'Completed sale #000010 — 73.93 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:25:14'),
(88, 1, 'andrew_owner', 'owner', 'products', 'measurement_add', 'unit_measurement', 25, 'Added unit measurement \'pack\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:26:42'),
(89, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 2, 'Added supplier \'Andrew Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:26:55'),
(90, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 10101, 'Added product \'boba\' with initial batch \'Batch-1\' (100 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:27:24'),
(91, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 11, 'Completed sale #000011 — 52.50 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:27:53'),
(92, 1, 'andrew_owner', 'owner', 'products', 'product_update', 'product', 10101, 'Updated product \'boba\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:28:32'),
(93, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 12, 'Completed sale #000012 — 43.12 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:29:31'),
(94, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 13, 'Completed sale #000013 — 43.12 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:29:58'),
(95, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — Senior discount: 10.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:42:45'),
(96, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 14, 'Completed sale #000014 — 18.58 PHP, 2 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:44:45'),
(97, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 15, 'Completed sale #000015 — 5.46 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:46:59'),
(98, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 16, 'Completed sale #000016 — 12.96 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 02:51:56'),
(99, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — VAT: 36.00%, Senior discount: 10.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:45:00'),
(100, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 17, 'Completed sale #000017 — 13.20 PHP, 2 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:45:19'),
(101, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 10102, 'Added product \'TEST\' with initial batch \'Batch-1\' (100 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:50:55'),
(102, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — VAT: 10.00%, Senior discount: 10.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:52:17'),
(103, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — VAT: 12.00%, Senior discount: 10.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:57:44'),
(104, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 18, 'Completed sale #000018 — 2.40 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:58:24'),
(105, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — VAT: 0.00%, Senior discount: 10.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:58:38'),
(106, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 19, 'Completed sale #000019 — 2.40 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 03:58:48'),
(107, 1, 'andrew_owner', 'owner', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — VAT: 12.00%, Senior discount: 10.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 04:05:56'),
(108, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:01:07'),
(109, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.137.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-17 07:04:16'),
(110, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 20, 'Completed sale #000020 — 960.00 PHP, 100 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:06:55'),
(111, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:10:36'),
(112, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:10:53'),
(113, NULL, 'andrew_owner', 'guest', 'auth', 'login_failed', NULL, NULL, 'Failed sign-in (wrong password) for username \'andrew_owner\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:11:32'),
(114, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:11:53'),
(115, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-17 07:13:36'),
(116, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:15:06'),
(117, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:15:12'),
(118, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.137.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-17 07:15:34'),
(119, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', '2026-09-17 07:17:28'),
(120, 2, 'andrew_admin', 'admin', 'inventory', 'batch_disposal', 'inventory', 2, 'Disposed 10 packs from batch \'SEED-BATCH-002\' of Tempra (reason: Expired)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 07:31:46'),
(121, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10103, 'Added product \'dsfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 08:21:04'),
(122, 2, 'andrew_admin', 'admin', 'inventory', 'batch_add', 'product', 10103, 'Added batch \'Batch-1\' (952 packs) to dsfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 08:21:41'),
(123, 2, 'andrew_admin', 'admin', 'inventory', 'batch_disposal', 'inventory', 132, 'Disposed 952 packs from batch \'Batch-1\' of dsfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff (reason: damages)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 08:22:32'),
(124, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 09:42:04'),
(125, 2, 'andrew_admin', 'admin', 'products', 'serving_unit_add', 'serving_unit', 7, 'Added serving unit \'pc\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:13:14'),
(126, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10104, 'Added product \'brief\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:13:19'),
(127, 2, 'andrew_admin', 'admin', 'inventory', 'batch_add', 'product', 10104, 'Added batch \'Batch-1\' (50 packs) to brief', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:17:22'),
(128, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10105, 'Added product \'Amoxicillindd\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:20:41'),
(129, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10106, 'Added product \'tut\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:21:24'),
(130, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10107, 'Added product \'Paracetamolddd\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:21:57'),
(131, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10108, 'Added product \'dd\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:23:07'),
(132, 2, 'andrew_admin', 'admin', 'products', 'serving_unit_add', 'serving_unit', 8, 'Added serving unit \'L\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:25:55'),
(133, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10109, 'Added product \'ddd\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:26:29'),
(134, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10110, 'Added product \'dddddss\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:31:21'),
(135, 2, 'andrew_admin', 'admin', 'products', 'serving_unit_add', 'serving_unit', 9, 'Added serving unit \'Liters\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:32:17'),
(136, 2, 'andrew_admin', 'admin', 'products', 'product_add', 'product', 10111, 'Added product \'tiss\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 10:32:24'),
(137, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 14:20:11'),
(138, 2, 'andrew_admin', 'admin', 'inventory', 'batch_add', 'product', 10103, 'Added batch \'Batch-2\' (100 packs) to dsfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 14:51:43'),
(139, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 18, 'Updated category \'Over-the-Counter (OTC)\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:43'),
(140, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 17, 'Updated category \'Prescription Medicines\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:43'),
(141, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 20, 'Updated category \'Vitamins & Supplements\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(142, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 21, 'Updated category \'First Aid\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(143, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 19, 'Updated category \'Medical Supplies\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(144, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 22, 'Updated category \'Diagnostics\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(145, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 23, 'Updated category \'Herbal Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(146, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 24, 'Updated category \'Health & Wellness\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(147, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 25, 'Updated category \'Personal Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(148, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 26, 'Updated category \'Baby Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(149, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 27, 'Updated category \'Beverage/Beverages\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(150, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 28, 'Updated category \'Snacks\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(151, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 29, 'Updated category \'Canned Goods\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(152, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 30, 'Updated category \'Instant Food\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(153, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 31, 'Updated category \'Dairy Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:23:44'),
(154, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 18, 'Updated category \'Over-the-Counter (OTC)\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:34'),
(155, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 17, 'Updated category \'Prescription Medicines\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:34'),
(156, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 20, 'Updated category \'Vitamins & Supplements\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:34'),
(157, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 21, 'Updated category \'First Aid\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(158, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 19, 'Updated category \'Medical Supplies\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(159, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 22, 'Updated category \'Diagnostics\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(160, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 23, 'Updated category \'Herbal Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(161, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 24, 'Updated category \'Health & Wellness\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(162, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 25, 'Updated category \'Personal Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(163, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 26, 'Updated category \'Baby Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(164, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 27, 'Updated category \'Beverage/Beverages\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(165, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 28, 'Updated category \'Snacks\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(166, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 29, 'Updated category \'Canned Goods\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(167, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 30, 'Updated category \'Instant Food\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(168, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 31, 'Updated category \'Dairy Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:35'),
(169, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 17, 'Updated category \'Prescription Medicines\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53');
INSERT INTO `activity_logs` (`id`, `user_id`, `username`, `role`, `module`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(170, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 18, 'Updated category \'Over-the-Counter (OTC)\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(171, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 19, 'Updated category \'Medical Supplies\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(172, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 20, 'Updated category \'Vitamins & Supplements\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(173, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 22, 'Updated category \'Diagnostics\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(174, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 21, 'Updated category \'First Aid\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(175, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 23, 'Updated category \'Herbal Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(176, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 24, 'Updated category \'Health & Wellness\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(177, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 25, 'Updated category \'Personal Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(178, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 26, 'Updated category \'Baby Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(179, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 27, 'Updated category \'Beverage/Beverages\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(180, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 28, 'Updated category \'Snacks\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(181, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 29, 'Updated category \'Canned Goods\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(182, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 30, 'Updated category \'Instant Food\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:53'),
(183, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 31, 'Updated category \'Dairy Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:26:54'),
(184, 2, 'andrew_admin', 'admin', 'sales', 'register_open', 'register_opening', 3, 'Opened register with 100.00 PHP opening cash for 2026-09-17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:27:07'),
(185, 2, 'andrew_admin', 'admin', 'sales', 'sale_completed', 'transaction', 21, 'Completed sale #000021 — 6.00 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:27:42'),
(186, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 17, 'Updated category \'Prescription Medicines\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(187, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 19, 'Updated category \'Medical Supplies\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(188, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 18, 'Updated category \'Over-the-Counter (OTC)\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(189, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 21, 'Updated category \'First Aid\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(190, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 20, 'Updated category \'Vitamins & Supplements\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(191, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 22, 'Updated category \'Diagnostics\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(192, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 23, 'Updated category \'Herbal Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(193, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 24, 'Updated category \'Health & Wellness\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(194, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 25, 'Updated category \'Personal Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(195, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 26, 'Updated category \'Baby Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(196, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 27, 'Updated category \'Beverage/Beverages\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(197, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 28, 'Updated category \'Snacks\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(198, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 29, 'Updated category \'Canned Goods\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(199, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 30, 'Updated category \'Instant Food\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(200, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 31, 'Updated category \'Dairy Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:12'),
(201, 2, 'andrew_admin', 'admin', 'settings', 'settings_update', NULL, NULL, 'Updated store settings — VAT: 0.00%, Senior discount: 20.00%, PWD discount: 20.00%, receipt paper: 80mm, weekly discount limit: 125.00 PHP, low stock: 15, near expiry: 60 days', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:28:38'),
(202, 2, 'andrew_admin', 'admin', 'sales', 'sale_completed', 'transaction', 22, 'Completed sale #000022 — 4.80 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:31:24'),
(203, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 17, 'Updated category \'Prescription Medicines\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(204, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 18, 'Updated category \'Over-the-Counter (OTC)\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(205, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 19, 'Updated category \'Medical Supplies\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(206, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 21, 'Updated category \'First Aid\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(207, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 22, 'Updated category \'Diagnostics\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(208, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 20, 'Updated category \'Vitamins & Supplements\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(209, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 23, 'Updated category \'Herbal Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(210, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 24, 'Updated category \'Health & Wellness\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(211, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 25, 'Updated category \'Personal Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(212, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 26, 'Updated category \'Baby Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(213, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 27, 'Updated category \'Beverage/Beverages\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(214, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 28, 'Updated category \'Snacks\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:38'),
(215, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 29, 'Updated category \'Canned Goods\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:39'),
(216, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 30, 'Updated category \'Instant Food\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:39'),
(217, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 31, 'Updated category \'Dairy Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:39'),
(218, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 17, 'Updated category \'Prescription Medicines\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(219, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 19, 'Updated category \'Medical Supplies\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(220, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 18, 'Updated category \'Over-the-Counter (OTC)\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(221, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 20, 'Updated category \'Vitamins & Supplements\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(222, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 21, 'Updated category \'First Aid\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(223, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 22, 'Updated category \'Diagnostics\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(224, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 23, 'Updated category \'Herbal Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(225, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 24, 'Updated category \'Health & Wellness\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(226, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 25, 'Updated category \'Personal Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(227, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 26, 'Updated category \'Baby Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(228, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 27, 'Updated category \'Beverage/Beverages\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(229, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 28, 'Updated category \'Snacks\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(230, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 29, 'Updated category \'Canned Goods\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(231, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 30, 'Updated category \'Instant Food\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(232, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 31, 'Updated category \'Dairy Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:38:46'),
(233, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 17, 'Updated category \'Prescription Medicines\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(234, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 18, 'Updated category \'Over-the-Counter (OTC)\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(235, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 19, 'Updated category \'Medical Supplies\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(236, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 22, 'Updated category \'Diagnostics\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(237, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 20, 'Updated category \'Vitamins & Supplements\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(238, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 21, 'Updated category \'First Aid\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(239, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 23, 'Updated category \'Herbal Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(240, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 24, 'Updated category \'Health & Wellness\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(241, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 25, 'Updated category \'Personal Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(242, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 26, 'Updated category \'Baby Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(243, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 27, 'Updated category \'Beverage/Beverages\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(244, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 28, 'Updated category \'Snacks\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(245, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 29, 'Updated category \'Canned Goods\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(246, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 30, 'Updated category \'Instant Food\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(247, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 31, 'Updated category \'Dairy Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:39:59'),
(248, 2, 'andrew_admin', 'admin', 'sales', 'sale_completed', 'transaction', 23, 'Completed sale #000023 — 6.00 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:24'),
(249, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 17, 'Updated category \'Prescription Medicines\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:38'),
(250, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 20, 'Updated category \'Vitamins & Supplements\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:38'),
(251, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 18, 'Updated category \'Over-the-Counter (OTC)\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:38'),
(252, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 21, 'Updated category \'First Aid\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:38'),
(253, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 19, 'Updated category \'Medical Supplies\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:38'),
(254, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 22, 'Updated category \'Diagnostics\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:38'),
(255, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 23, 'Updated category \'Herbal Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:38'),
(256, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 24, 'Updated category \'Health & Wellness\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:38'),
(257, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 25, 'Updated category \'Personal Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:39'),
(258, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 26, 'Updated category \'Baby Care\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:39'),
(259, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 27, 'Updated category \'Beverage/Beverages\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:39'),
(260, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 28, 'Updated category \'Snacks\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:39'),
(261, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 29, 'Updated category \'Canned Goods\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:39'),
(262, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 30, 'Updated category \'Instant Food\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:39'),
(263, 2, 'andrew_admin', 'admin', 'products', 'category_update', 'category', 31, 'Updated category \'Dairy Products\' settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:40:39'),
(264, 2, 'andrew_admin', 'admin', 'sales', 'sale_completed', 'transaction', 24, 'Completed sale #000024 — 4.80 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:41:03'),
(265, 2, 'andrew_admin', 'admin', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:43:59'),
(266, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 10058, 'Added batch \'Batch-1\' (100 packs) to Omega Plaster', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 01:16:48'),
(267, 1, 'andrew_owner', 'owner', 'sales', 'register_close', 'register_closing', 2, 'Closed register for 2026-09-17 — system 2,364.73 PHP, counted 2,364.73 PHP, variance 0.00 PHP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 01:17:11'),
(268, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 4, 'Opened register with 1,000.00 PHP opening cash for 2026-09-19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 01:17:17'),
(269, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 25, 'Completed sale #000025 — 50.00 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 01:17:34'),
(270, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 10075, 'Added batch \'Batch-3\' (1000 packs) to Kojic', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 01:22:49'),
(271, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:10:55'),
(272, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 1, 'Added product \'Restime\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:16:53'),
(273, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 1, 'Added batch \'Batch-1\' (100 packs) to Restime', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:17:37'),
(274, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 1, 'Opened register with 1,000.00 PHP opening cash for 2026-09-21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:17:44'),
(275, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 2, 'Added product \'Fita\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:37:50'),
(276, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-1\' (100 packs) to Fita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:38:13'),
(277, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-2\' (100 packs) to Fita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:38:51'),
(278, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 1, 'Completed sale #000001 — 10,890.00 PHP, 99 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:40:32'),
(279, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-3\' (100 packs) to Fita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:41:19'),
(280, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 2, 'Completed sale #000002 — 115.50 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:42:42'),
(281, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 3, 'Completed sale #000003 — 115.50 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:43:32'),
(282, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 4, 'Completed sale #000004 — 11,434.50 PHP, 99 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:44:06'),
(283, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-4\' (1 packs) to Fita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:45:15'),
(284, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-5\' (1 packs) to Fita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:46:05'),
(285, 1, 'andrew_owner', 'owner', 'inventory', 'batch_disposal', 'inventory', 2, 'Disposed 100 packs from batch \'Batch-1\' of Fita (reason: Expired)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 08:52:59'),
(286, 1, 'andrew_owner', 'owner', 'products', 'product_status', 'product', 1, 'Disabled product Restime', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 09:08:10'),
(287, 1, 'andrew_owner', 'owner', 'products', 'product_status', 'product', 2, 'Disabled product Fita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 09:08:19'),
(288, 1, 'andrew_owner', 'owner', 'products', 'product_status', 'product', 2, 'Enabled product Fita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 09:08:27'),
(289, 1, 'andrew_owner', 'owner', 'products', 'product_status', 'product', 1, 'Enabled product Restime', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 09:08:33'),
(290, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 5, 'Completed sale #000005 — 14,850.00 PHP, 99 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 09:17:55'),
(291, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-21 10:06:56'),
(292, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:12:52'),
(293, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:13:25'),
(294, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:13:32'),
(295, 3, 'andrew_staff', 'staff', 'auth', 'login', 'user', 3, 'Signed in successfully (staff)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:13:37'),
(296, 3, 'andrew_staff', 'staff', 'sales', 'register_open', 'register_opening', 2, 'Opened register with 100.00 PHP opening cash for 2026-09-21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:13:42'),
(297, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:18:38'),
(298, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:21:38'),
(299, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:21:42'),
(300, 2, 'andrew_admin', 'admin', 'sales', 'register_open', 'register_opening', 3, 'Opened register with 100.00 PHP opening cash for 2026-09-21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 10:21:48'),
(301, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:34:45'),
(302, 2, 'andrew_admin', 'admin', 'products', 'serving_unit_delete', 'serving_unit', 9, 'Deleted serving unit \'Liters\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:41:43'),
(303, 2, 'andrew_admin', 'admin', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:42:06'),
(304, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:42:19'),
(305, 1, 'andrew_owner', 'owner', 'products', 'serving_unit_delete', 'serving_unit', 7, 'Deleted serving unit \'pc\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:43:18'),
(306, 1, 'andrew_owner', 'owner', 'products', 'serving_unit_delete', 'serving_unit', 6, 'Deleted serving unit \'ML\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:43:23'),
(307, 1, 'andrew_owner', 'owner', 'products', 'serving_unit_delete', 'serving_unit', 8, 'Deleted serving unit \'L\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:43:33'),
(308, 1, 'andrew_owner', 'owner', 'products', 'serving_unit_delete', 'serving_unit', 2, 'Deleted serving unit \'mg\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:43:39'),
(309, 1, 'andrew_owner', 'owner', 'products', 'serving_unit_delete', 'serving_unit', 3, 'Deleted serving unit \'grams\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 14:43:44'),
(310, 1, 'andrew_owner', 'owner', 'products', 'serving_unit_add', 'serving_unit', 42, 'Added serving unit \'f\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:12:01'),
(311, 1, 'andrew_owner', 'owner', 'products', 'serving_unit_delete', 'serving_unit', 42, 'Deleted serving unit \'f\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:12:04'),
(312, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 4, 'Added product \'Amoxil\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:13:33'),
(313, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 5, 'Added product \'tests\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:16:06'),
(314, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 6, 'Added product \'tests\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:21:49'),
(315, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 8, 'Added product \'ggd\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:28:49'),
(316, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 1, 'Added supplier \'testttt\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:30:42'),
(317, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 2, 'Added supplier \'testttttestttt\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:31:29'),
(318, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 3, 'Added supplier \'Andrew Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:34:00'),
(319, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 4, 'Added supplier \'Dut Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:34:40'),
(320, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 9, 'Added product \'fdfs\' with initial batch \'Batch-1\' (1000 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:35:41'),
(321, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 9, 'Added batch \'Batch-2\' (152 packs) to fdfs', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:36:59'),
(322, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 13, 'Added product \'fdfsfdfs\' with initial batch \'Batch-1\' (10 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:43:37'),
(323, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 5, 'Added supplier \'Sample Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:44:47'),
(324, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 14, 'Added product \'Unilabd\' with initial batch \'Batch-1\' (520 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:45:13'),
(325, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 14, 'Added batch \'Batch-2\' (524 packs) to Unilabd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:45:50'),
(326, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 6, 'Added supplier \'coke\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:52:11'),
(327, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 15, 'Added product \'Centrum\' with initial batch \'Batch-1\' (522 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:52:32'),
(328, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 1, 'Opened register with 100.00 PHP opening cash for 2026-09-21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:53:01'),
(329, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 1, 'Added supplier \'Andrew Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:55:01'),
(330, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 1, 'Added product \'Restime\' with initial batch \'Batch-1\' (100 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:55:21'),
(331, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 1, 'Opened register with 100.00 PHP opening cash for 2026-09-21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:55:37'),
(332, 1, 'andrew_owner', 'owner', 'inventory', 'batch_disposal', 'inventory', 1, 'Disposed 5 packs from batch \'Batch-1\' of Restime (reason: damages)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:56:34'),
(333, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 1, 'Completed sale #000001 — 630.00 PHP, 6 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:57:02'),
(334, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 15:59:14'),
(335, 1, 'andrew_owner', 'owner', 'sales', 'register_close', 'register_closing', 1, 'Closed register for 2026-09-21 — system 730.00 PHP, counted 730.00 PHP, variance 0.00 PHP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:04:29'),
(336, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:04:31'),
(337, 3, 'andrew_staff', 'staff', 'auth', 'login', 'user', 3, 'Signed in successfully (staff)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:06:41'),
(338, 3, 'andrew_staff', 'staff', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:06:53'),
(339, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:11:14'),
(340, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 2, 'Opened register with 1,000.00 PHP opening cash for 2026-09-22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:11:21'),
(341, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 2, 'Completed sale #000002 — 1,470.00 PHP, 14 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:11:31'),
(342, 1, 'andrew_owner', 'owner', 'sales', 'register_close', 'register_closing', 2, 'Closed register for 2026-09-22 — system 2,470.00 PHP, counted 2,470.00 PHP, variance 0.00 PHP', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:11:48'),
(343, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:11:50'),
(344, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-21 16:12:00'),
(345, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:31:23'),
(346, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 1, 'Added batch \'Batch-3\' (100 packs) to Restime', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:36:00'),
(347, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 1, 'Added batch \'Batch-4\' (155 packs) to Restime', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:36:34'),
(348, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:40:27'),
(349, 1, 'andrew_owner', 'owner', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:42:56'),
(350, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:43:08'),
(351, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 1, 'Added supplier \'Dut Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:44:12'),
(352, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 1, 'Added product \'sad\' with initial batch \'Batch-1\' (100 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:44:34'),
(353, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 2, 'Added supplier \'Andrew Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:45:17');
INSERT INTO `activity_logs` (`id`, `user_id`, `username`, `role`, `module`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(354, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 2, 'Added product \'test\' with initial batch \'Batch-1\' (100 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:45:39'),
(355, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 1, 'Opened register with 100.00 PHP opening cash for 2026-09-22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:46:01'),
(356, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 1, 'Completed sale #000001 — 54.60 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:46:10'),
(357, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 2, 'Completed sale #000002 — 54.60 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:46:16'),
(358, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 1, 'Processed return #1 for sale #2 — refund 54.60 PHP via Cash (1 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:47:38'),
(359, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 2, 'Processed return #2 for sale #1 — refund 54.60 PHP via Cash (1 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:50:17'),
(360, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 3, 'Completed sale #000003 — 54.60 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:50:23'),
(361, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-2\' (100 packs) to test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:53:06'),
(362, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 4, 'Completed sale #000004 — 53,014.50 PHP, 198 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:53:30'),
(363, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 3, 'Processed return #3 for sale #4 — refund 267.75 PHP via Cash (1 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:54:13'),
(364, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 4, 'Processed return #4 for sale #4 — refund 40,162.50 PHP via Cash (150 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:54:50'),
(365, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 5, 'Processed return #5 for sale #4 — refund 12,584.25 PHP via Cash (47 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:55:23'),
(366, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 5, 'Completed sale #000005 — 52,746.75 PHP, 197 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 02:56:29'),
(367, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 9, 'Completed sale #000009 — 21,000.00 PHP, 100 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:05:29'),
(368, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-3\' (1000 packs) to test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:05:59'),
(369, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 10, 'Completed sale #000010 — 15,750.00 PHP, 3 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:06:12'),
(370, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 6, 'Processed return #6 for sale #10 — refund 15,750.00 PHP via Cash (3 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:06:40'),
(371, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 1, 'Added batch \'Batch-2\' (100 packs) to sad', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:07:15'),
(372, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 11, 'Completed sale #000011 — 630.00 PHP, 3 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:07:24'),
(373, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 7, 'Processed return #7 for sale #11 — refund 210.00 PHP via Cash (1 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:07:41'),
(374, NULL, 'andrew_admin', 'guest', 'auth', 'login_failed', NULL, NULL, 'Failed sign-in (wrong password) for username \'andrew_admin\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 03:26:28'),
(375, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:26:44'),
(376, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 03:27:26'),
(377, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:28:02'),
(378, NULL, 'andrew_admin', 'guest', 'auth', 'login_failed', NULL, NULL, 'Failed sign-in (wrong password) for username \'andrew_admin\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 03:30:11'),
(379, 2, 'andrew_admin', 'admin', 'auth', 'login', 'user', 2, 'Signed in successfully (admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.138.0 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36', '2026-09-22 03:30:37'),
(380, NULL, 'andrew_admin', 'guest', 'auth', 'login_failed', NULL, NULL, 'Failed sign-in (wrong password) for username \'andrew_admin\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:45:30'),
(381, NULL, 'andrew_admin', 'guest', 'auth', 'login_failed', NULL, NULL, 'Failed sign-in (wrong password) for username \'andrew_admin\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:45:36'),
(382, 1, 'andrew_owner', 'owner', 'auth', 'login', 'user', 1, 'Signed in successfully (owner)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:45:57'),
(383, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 2, 'Added batch \'Batch-4\' (100 packs) to test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:46:52'),
(384, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 8, 'Processed return #8 for sale #5 — refund 267.75 PHP via Cash (1 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:53:29'),
(385, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 1, 'Added supplier \'Andrew Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:56:29'),
(386, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 1, 'Added product \'Biogesic\' with initial batch \'Batch-1\' (100 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 03:56:54'),
(387, 1, 'andrew_owner', 'owner', 'products', 'supplier_add', 'supplier', 2, 'Added supplier \'Dut Gonzales Pablo\'', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:07:22'),
(388, 1, 'andrew_owner', 'owner', 'products', 'product_add', 'product', 2, 'Added product \'Amoxil\' with initial batch \'Batch-1\' (100 packs)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:07:46'),
(389, 1, 'andrew_owner', 'owner', 'inventory', 'batch_add', 'product', 1, 'Added batch \'Batch-2\' (200 packs) to Biogesic', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:11:17'),
(390, 1, 'andrew_owner', 'owner', 'inventory', 'batch_disposal', 'inventory', 3, 'Disposed 10 packs from batch \'Batch-2\' of Biogesic (reason: Expired)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:11:43'),
(391, 1, 'andrew_owner', 'owner', 'sales', 'register_open', 'register_opening', 1, 'Opened register with 100.00 PHP opening cash for 2026-09-22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:11:58'),
(392, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 1, 'Completed sale #000001 — 525.00 PHP, 1 items, customer: Walk-in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:12:02'),
(393, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 1, 'Processed return #1 for sale #1 — refund 525.00 PHP via Cash (1 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:12:20'),
(394, 1, 'andrew_owner', 'owner', 'sales', 'sale_completed', 'transaction', 2, 'Completed sale #000002 — 420.00 PHP, 1 items, customer: ANDREW PABLO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:15:50'),
(395, 1, 'andrew_owner', 'owner', 'sales', 'return_processed', 'return', 2, 'Processed return #2 for sale #2 — refund 420.00 PHP via Cash (1 items)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-22 04:16:04');

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
  `expiry_date` date DEFAULT NULL COMMENT 'Expiry date (critical for FEFO)',
  `lot_number` varchar(255) DEFAULT NULL COMMENT 'Lot or batch reference number'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `supplier_id`, `batch_number`, `date_received`, `manufacture_date`, `purchase_cost`, `markup`, `sale_price`, `received_quantity`, `created_at`, `updated_at`, `current_quantity`, `expiry_date`, `lot_number`) VALUES
(1, 1, 1, 'Batch-1', '2026-09-22', NULL, 20.00, 5.00, 21.00, 100, '2026-09-22 03:56:54', '2026-09-22 03:56:54', 100, '2029-02-02', NULL),
(2, 2, 2, 'Batch-1', '2026-09-22', NULL, 500.00, 5.00, 525.00, 100, '2026-09-22 04:07:46', '2026-09-22 04:16:04', 99, '2030-02-02', '556'),
(3, 1, 1, 'Batch-2', '2026-09-22', NULL, 200.00, 5.00, 210.00, 200, '2026-09-22 04:11:17', '2026-09-22 04:11:43', 190, '2030-02-02', '741');

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
  `disposal_proof_filename` varchar(255) DEFAULT NULL,
  `disposed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `lot_number` varchar(255) DEFAULT NULL COMMENT 'Lot or batch reference number'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_disposals`
--

INSERT INTO `inventory_disposals` (`id`, `product_id`, `batch_number`, `quantity`, `expiry_date`, `reason`, `disposal_proof_filename`, `disposed_at`, `lot_number`) VALUES
(1, 1, 'Batch-2', 10, '2030-02-02', 'Expired', 'cd6437d7884a6db965448141bfd84d34.jpg', '2026-09-22 12:11:43', '741');

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
  `moved_at` datetime NOT NULL DEFAULT current_timestamp(),
  `lot_number` varchar(255) DEFAULT NULL COMMENT 'Lot or batch reference number'
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
(1, 1, '84691ccb8115e897d6ec5b4dc06ed7f90d46bb92b6ea5c1cd72ea7dafe6564c2', '2026-09-17 00:17:16', NULL, '2026-09-16 15:47:16');

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
(1, 'Biogesic', 'Paracetamol', 500.00, 32, '245085540029', 18, NULL, 0, '6ab1fc86ec52a-1790049414images (5).jpg', 0, '', 'Tablet', 1, 10.00, 'Piece', 0),
(2, 'Amoxil', 'tests', 250.00, 32, '030412972594', 18, NULL, 0, '6ab1ff12be335-1790050066IMG_4549.png', 0, '', 'Capsule', 2, 10.00, 'Piece', 0);

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

--
-- Dumping data for table `register_openings`
--

INSERT INTO `register_openings` (`id`, `user_id`, `business_date`, `opening_cash`, `notes`, `opened_at`) VALUES
(1, 1, '2026-09-22', 100.00, NULL, '2026-09-22 04:11:58');

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
(1, 1, 2, 1, 525.00, 525.00, 'returned', 0, 0, 0.00),
(2, 2, 2, 1, 420.00, 420.00, 'returned', 1, 0, 0.00);

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
(1, 1, 1, 525.00, NULL, 0, 'Customer Request / Change of Mind', 'Cash', '2026-09-22 12:12:20', 1),
(2, 2, 1, 420.00, NULL, 0, 'Customer Request / Change of Mind', 'Cash', '2026-09-22 12:16:04', 1);

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
(1, 'ANDREW PABLO', '12345678910', 1, '2026-09-22 12:15:37', 1);

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
(10, 'Piece'),
(11, 'Tablet'),
(12, 'Capsule'),
(13, 'Softgel'),
(14, 'Sachet'),
(15, 'Bottle'),
(16, 'Vial'),
(17, 'Ampule'),
(18, 'Tube'),
(19, 'Box'),
(20, 'Pack'),
(21, 'Strip'),
(22, 'Blister'),
(23, 'Can'),
(24, 'Jar'),
(25, 'Pouch'),
(26, 'Bag'),
(27, 'Roll'),
(28, 'Set'),
(29, 'Pair'),
(30, 'Bundle'),
(31, 'Kit'),
(32, 'Milligram'),
(33, 'Microgram'),
(34, 'Gram'),
(35, 'Kilogram'),
(36, 'Milliliter'),
(37, 'Liter'),
(38, 'Centimeter'),
(39, 'Meter'),
(40, 'Ounce'),
(41, 'Pound');

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
('low_stock_threshold', '15', '2026-09-17 23:28:38'),
('near_expiry_days', '60', '2026-09-17 23:28:38'),
('pwd_discount_rate', '20.00', '2026-09-17 23:28:38'),
('receipt_paper', '80', '2026-09-17 23:28:38'),
('senior_discount_rate', '20.00', '2026-09-17 23:28:38'),
('statutory_discount_cap', '125.00', '2026-09-17 23:28:38'),
('vat_rate', '0.00', '2026-09-17 23:28:38');

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
(1, 'Andrew Gonzales Pablo', NULL, '+639651800675', 'andrewpablo2005@gmail.com', 'N/A', NULL, 1, '2026-09-22 03:56:29', '2026-09-22 03:56:29'),
(2, 'Dut Gonzales Pablo', NULL, '+639651800675', 'andrewpablo2005@gmail.com', 'df', NULL, 1, '2026-09-22 04:07:22', '2026-09-22 04:07:22');

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
(1, 1, 1, 'Walk-in', NULL, '', 525.00, '2026-09-22 04:12:02', 0.00, 0.00),
(2, 1, 2, 'ANDREW PABLO', '1', 'senior', 420.00, '2026-09-22 04:15:50', 105.00, 0.00);

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
(1, 1, 2, 2, 1, 525.00, 525.00),
(2, 2, 2, 2, 1, 525.00, 420.00);

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
(1, 1, 2, 1, 500.00, '2026-09-22 04:12:02'),
(2, 2, 2, 1, 500.00, '2026-09-22 04:15:50');

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
(26, 'Piece'),
(27, 'Tablet'),
(28, 'Capsule'),
(29, 'Softgel'),
(30, 'Sachet'),
(31, 'Bottle'),
(32, 'Vial'),
(33, 'Ampule'),
(34, 'Tube'),
(35, 'Box'),
(36, 'Pack'),
(37, 'Strip'),
(38, 'Blister'),
(39, 'Can'),
(40, 'Jar'),
(41, 'Pouch'),
(42, 'Bag'),
(43, 'Roll'),
(44, 'Set'),
(45, 'Pair'),
(46, 'Bundle'),
(47, 'Kit'),
(48, 'Milligram'),
(49, 'Microgram'),
(50, 'Gram'),
(51, 'Kilogram'),
(52, 'Milliliter'),
(53, 'Liter'),
(54, 'Centimeter'),
(55, 'Meter'),
(56, 'Ounce'),
(57, 'Pound');

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
(1, 'andrew_owner', '$2y$10$2Rqa4Se609iFKRTnJpX3SuAIE7v1vZwwCtGK2WHBvnBxQ2.w9RBAS', '1234567', 'Owner', 0, '2026-09-17 07:11:32', 'active', '2026-09-05 08:53:02'),
(2, 'andrew_admin', '$2y$12$vjlvsM6RkKHSeS.UFac2dez3pGedEsYlu7SgZe6kiticAcaPD6c7q', '1234567', 'Admin', 2, '2026-09-22 03:45:36', 'active', '2026-09-05 08:53:02'),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=396;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory_alerts`
--
ALTER TABLE `inventory_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_disposals`
--
ALTER TABLE `inventory_disposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_no_stock`
--
ALTER TABLE `inventory_no_stock`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `override_log`
--
ALTER TABLE `override_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `return_items`
--
ALTER TABLE `return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `return_transactions`
--
ALTER TABLE `return_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `senior_customers`
--
ALTER TABLE `senior_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `serving_unit`
--
ALTER TABLE `serving_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction_batch_allocations`
--
ALTER TABLE `transaction_batch_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaction_item_batches`
--
ALTER TABLE `transaction_item_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `unit_measurement`
--
ALTER TABLE `unit_measurement`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

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
  ADD CONSTRAINT `fk_products_measurement` FOREIGN KEY (`measurement_id`) REFERENCES `serving_unit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
