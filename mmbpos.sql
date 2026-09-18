-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2026 at 05:44 PM
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
(265, 2, 'andrew_admin', 'admin', 'auth', 'logout', NULL, NULL, 'Signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-17 15:43:59');

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
(1, 10001, 1, 'SEED-BATCH-001', '2026-09-16', NULL, 8.00, 20.00, 9.60, 100, '2026-09-16 15:10:31', '2026-09-17 07:06:55', 0, '2027-03-22'),
(2, 10002, 1, 'SEED-BATCH-002', '2026-09-16', NULL, 55.00, 20.00, 66.00, 80, '2026-09-16 15:10:31', '2026-09-17 07:31:46', 70, '2027-03-29'),
(3, 10003, 1, 'SEED-BATCH-003', '2026-09-16', NULL, 12.00, 20.00, 14.40, 100, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 100, '2027-04-05'),
(4, 10004, 1, 'SEED-BATCH-004', '2026-09-16', NULL, 14.00, 20.00, 16.80, 100, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 100, '2027-04-12'),
(5, 10005, 1, 'SEED-BATCH-005', '2026-09-16', NULL, 18.00, 20.00, 21.60, 90, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 90, '2027-04-19'),
(6, 10006, 1, 'SEED-BATCH-006', '2026-09-16', NULL, 9.00, 20.00, 10.80, 100, '2026-09-16 15:10:31', '2026-09-17 03:45:19', 98, '2027-04-26'),
(7, 10007, 1, 'SEED-BATCH-007', '2026-09-16', NULL, 22.00, 20.00, 26.40, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2027-05-03'),
(8, 10008, 1, 'SEED-BATCH-008', '2026-09-16', NULL, 10.00, 20.00, 12.00, 100, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 100, '2027-05-10'),
(9, 10009, 1, 'SEED-BATCH-009', '2026-09-16', NULL, 25.00, 20.00, 30.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2027-05-17'),
(10, 10010, 1, 'SEED-BATCH-010', '2026-09-16', NULL, 18.00, 20.00, 21.60, 90, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 90, '2027-05-24'),
(11, 10011, 1, 'SEED-BATCH-011', '2026-09-16', NULL, 20.00, 20.00, 24.00, 90, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 90, '2027-05-31'),
(12, 10012, 1, 'SEED-BATCH-012', '2026-09-16', NULL, 12.00, 20.00, 14.40, 100, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 100, '2027-06-07'),
(13, 10013, 1, 'SEED-BATCH-013', '2026-09-16', NULL, 14.00, 20.00, 16.80, 100, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 100, '2027-06-14'),
(14, 10014, 1, 'SEED-BATCH-014', '2026-09-16', NULL, 12.00, 20.00, 14.40, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2027-06-21'),
(15, 10015, 1, 'SEED-BATCH-015', '2026-09-16', NULL, 16.00, 20.00, 19.20, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2027-06-28'),
(16, 10016, 1, 'SEED-BATCH-016', '2026-09-16', NULL, 95.00, 20.00, 114.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2027-07-05'),
(17, 10017, 1, 'SEED-BATCH-017', '2026-09-16', NULL, 110.00, 20.00, 132.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2027-07-12'),
(18, 10018, 1, 'SEED-BATCH-018', '2026-09-16', NULL, 45.00, 20.00, 54.00, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2027-07-19'),
(19, 10019, 1, 'SEED-BATCH-019', '2026-09-16', NULL, 180.00, 20.00, 216.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 39, '2027-07-26'),
(20, 10020, 1, 'SEED-BATCH-020', '2026-09-16', NULL, 220.00, 20.00, 264.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2027-08-02'),
(21, 10021, 1, 'SEED-BATCH-021', '2026-09-16', NULL, 35.00, 20.00, 42.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2027-08-09'),
(22, 10022, 1, 'SEED-BATCH-022', '2026-09-16', NULL, 12.00, 20.00, 14.40, 80, '2026-09-16 15:10:31', '2026-09-17 02:51:56', 78, '2027-08-16'),
(23, 10023, 1, 'SEED-BATCH-023', '2026-09-16', NULL, 75.00, 20.00, 90.00, 50, '2026-09-16 15:10:31', '2026-09-17 02:25:14', 48, '2027-08-23'),
(24, 10024, 1, 'SEED-BATCH-024', '2026-09-16', NULL, 18.00, 20.00, 21.60, 60, '2026-09-16 15:10:31', '2026-09-17 02:23:50', 59, '2027-08-30'),
(25, 10025, 1, 'SEED-BATCH-025', '2026-09-16', NULL, 20.00, 20.00, 24.00, 50, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 49, '2027-09-06'),
(26, 10026, 1, 'SEED-BATCH-026', '2026-09-16', NULL, 8.00, 20.00, 9.60, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2027-09-13'),
(27, 10027, 1, 'SEED-BATCH-027', '2026-09-16', NULL, 16.00, 20.00, 19.20, 50, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 50, '2027-09-20'),
(28, 10028, 1, 'SEED-BATCH-028', '2026-09-16', NULL, 65.00, 20.00, 78.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 39, '2027-09-27'),
(29, 10029, 1, 'SEED-BATCH-029', '2026-09-16', NULL, 6.00, 20.00, 7.20, 100, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 100, '2027-10-04'),
(30, 10030, 1, 'SEED-BATCH-030', '2026-09-16', NULL, 5.00, 20.00, 6.00, 100, '2026-09-16 15:10:31', '2026-09-17 15:41:03', 90, '2027-10-11'),
(31, 10031, 1, 'SEED-BATCH-031', '2026-09-16', NULL, 4.00, 20.00, 4.80, 100, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 100, '2027-10-18'),
(32, 10032, 1, 'SEED-BATCH-032', '2026-09-16', NULL, 8.00, 20.00, 9.60, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2027-10-25'),
(33, 10033, 1, 'SEED-BATCH-033', '2026-09-16', NULL, 18.00, 20.00, 21.60, 60, '2026-09-16 15:10:31', '2026-09-17 02:00:37', 58, '2027-11-01'),
(34, 10034, 1, 'SEED-BATCH-034', '2026-09-16', NULL, 25.00, 20.00, 30.00, 50, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 49, '2027-11-08'),
(35, 10035, 1, 'SEED-BATCH-035', '2026-09-16', NULL, 10.00, 20.00, 12.00, 50, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 50, '2027-11-15'),
(36, 10036, 1, 'SEED-BATCH-036', '2026-09-16', NULL, 320.00, 20.00, 384.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2027-11-22'),
(37, 10037, 1, 'SEED-BATCH-037', '2026-09-16', NULL, 450.00, 20.00, 540.00, 20, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 20, '2027-11-29'),
(38, 10038, 1, 'SEED-BATCH-038', '2026-09-16', NULL, 950.00, 20.00, 1140.00, 20, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 19, '2027-12-06'),
(39, 10039, 1, 'SEED-BATCH-039', '2026-09-16', NULL, 125.00, 20.00, 150.00, 50, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 50, '2027-12-13'),
(40, 10040, 1, 'SEED-BATCH-040', '2026-09-16', NULL, 180.00, 20.00, 216.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2027-12-20'),
(41, 10041, 1, 'SEED-BATCH-041', '2026-09-16', NULL, 12.00, 20.00, 14.40, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2027-12-27'),
(42, 10042, 1, 'SEED-BATCH-042', '2026-09-16', NULL, 9.00, 20.00, 10.80, 100, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 99, '2028-01-03'),
(43, 10043, 1, 'SEED-BATCH-043', '2026-09-16', NULL, 18.00, 20.00, 21.60, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2028-01-10'),
(44, 10044, 1, 'SEED-BATCH-044', '2026-09-16', NULL, 15.00, 20.00, 18.00, 70, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 70, '2028-01-17'),
(45, 10045, 1, 'SEED-BATCH-045', '2026-09-16', NULL, 20.00, 20.00, 24.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 59, '2028-01-24'),
(46, 10046, 1, 'SEED-BATCH-046', '2026-09-16', NULL, 12.00, 20.00, 14.40, 70, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 70, '2028-01-31'),
(47, 10047, 1, 'SEED-BATCH-047', '2026-09-16', NULL, 10.00, 20.00, 12.00, 80, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 79, '2028-02-07'),
(48, 10048, 1, 'SEED-BATCH-048', '2026-09-16', NULL, 8.00, 20.00, 9.60, 40, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 39, '2028-02-14'),
(49, 10049, 1, 'SEED-BATCH-049', '2026-09-16', NULL, 7.00, 20.00, 8.40, 70, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 69, '2028-02-21'),
(50, 10050, 1, 'SEED-BATCH-050', '2026-09-16', NULL, 5.00, 20.00, 6.00, 70, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 70, '2028-02-28'),
(51, 10051, 1, 'SEED-BATCH-051', '2026-09-16', NULL, 95.00, 20.00, 114.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2028-03-06'),
(52, 10052, 1, 'SEED-BATCH-052', '2026-09-16', NULL, 85.00, 20.00, 102.00, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2028-03-13'),
(53, 10053, 1, 'SEED-BATCH-053', '2026-09-16', NULL, 35.00, 20.00, 42.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2028-03-20'),
(54, 10054, 1, 'SEED-BATCH-054', '2026-09-16', NULL, 180.00, 20.00, 216.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-03-27'),
(55, 10055, 1, 'SEED-BATCH-055', '2026-09-16', NULL, 160.00, 20.00, 192.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-04-03'),
(56, 10056, 1, 'SEED-BATCH-056', '2026-09-16', NULL, 125.00, 20.00, 150.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 39, '2028-04-10'),
(57, 10057, 1, 'SEED-BATCH-057', '2026-09-16', NULL, 12.00, 20.00, 14.40, 80, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 80, '2028-04-17'),
(58, 10058, 1, 'SEED-BATCH-058', '2026-09-16', NULL, 2.00, 20.00, 2.40, 100, '2026-09-16 15:10:31', '2026-09-17 03:58:48', 95, '2028-04-24'),
(59, 10059, 1, 'SEED-BATCH-059', '2026-09-16', NULL, 110.00, 20.00, 132.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-05-01'),
(60, 10060, 1, 'SEED-BATCH-060', '2026-09-16', NULL, 95.00, 20.00, 114.00, 50, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 50, '2028-05-08'),
(61, 10061, 1, 'SEED-BATCH-061', '2026-09-16', NULL, 250.00, 20.00, 300.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-05-15'),
(62, 10062, 1, 'SEED-BATCH-062', '2026-09-16', NULL, 120.00, 20.00, 144.00, 50, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 49, '2028-05-22'),
(63, 10063, 1, 'SEED-BATCH-063', '2026-09-16', NULL, 350.00, 20.00, 420.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2028-05-29'),
(64, 10064, 1, 'SEED-BATCH-064', '2026-09-16', NULL, 250.00, 20.00, 300.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2028-06-05'),
(65, 10065, 1, 'SEED-BATCH-065', '2026-09-16', NULL, 220.00, 20.00, 264.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2028-06-12'),
(66, 10066, 1, 'SEED-BATCH-066', '2026-09-16', NULL, 45.00, 20.00, 54.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2028-06-19'),
(67, 10067, 1, 'SEED-BATCH-067', '2026-09-16', NULL, 75.00, 20.00, 90.00, 50, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 50, '2028-06-26'),
(68, 10068, 1, 'SEED-BATCH-068', '2026-09-16', NULL, 65.00, 20.00, 78.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-07-03'),
(69, 10069, 1, 'SEED-BATCH-069', '2026-09-16', NULL, 180.00, 20.00, 216.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 29, '2028-07-10'),
(70, 10070, 1, 'SEED-BATCH-070', '2026-09-16', NULL, 450.00, 20.00, 540.00, 25, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 24, '2028-07-17'),
(71, 10071, 1, 'SEED-BATCH-071', '2026-09-16', NULL, 35.00, 20.00, 42.00, 80, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 79, '2028-07-24'),
(72, 10072, 1, 'SEED-BATCH-072', '2026-09-16', NULL, 180.00, 20.00, 216.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-07-31'),
(73, 10073, 1, 'SEED-BATCH-073', '2026-09-16', NULL, 160.00, 20.00, 192.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 39, '2028-08-07'),
(74, 10074, 1, 'SEED-BATCH-074', '2026-09-16', NULL, 180.00, 20.00, 216.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-08-14'),
(75, 10075, 1, 'SEED-BATCH-075', '2026-09-16', NULL, 45.00, 20.00, 54.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2028-08-21'),
(76, 10076, 1, 'SEED-BATCH-076', '2026-09-16', NULL, 350.00, 20.00, 420.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2028-08-28'),
(77, 10077, 1, 'SEED-BATCH-077', '2026-09-16', NULL, 220.00, 20.00, 264.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 29, '2028-09-04'),
(78, 10078, 1, 'SEED-BATCH-078', '2026-09-16', NULL, 95.00, 20.00, 114.00, 60, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 60, '2028-09-11'),
(79, 10079, 1, 'SEED-BATCH-079', '2026-09-16', NULL, 180.00, 20.00, 216.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-09-18'),
(80, 10080, 1, 'SEED-BATCH-080', '2026-09-16', NULL, 85.00, 20.00, 102.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-09-25'),
(81, 10081, 1, 'SEED-BATCH-081', '2026-09-16', NULL, 120.00, 20.00, 144.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 39, '2028-10-02'),
(82, 10082, 1, 'SEED-BATCH-082', '2026-09-16', NULL, 280.00, 20.00, 336.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2028-10-09'),
(83, 10083, 1, 'SEED-BATCH-083', '2026-09-16', NULL, 300.00, 20.00, 360.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2028-10-16'),
(84, 10084, 1, 'SEED-BATCH-084', '2026-09-16', NULL, 310.00, 20.00, 372.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 29, '2028-10-23'),
(85, 10085, 1, 'SEED-BATCH-085', '2026-09-16', NULL, 950.00, 20.00, 1140.00, 20, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 20, '2028-10-30'),
(86, 10086, 1, 'SEED-BATCH-086', '2026-09-16', NULL, 900.00, 20.00, 1080.00, 20, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 20, '2028-11-06'),
(87, 10087, 1, 'SEED-BATCH-087', '2026-09-16', NULL, 85.00, 20.00, 102.00, 50, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 49, '2028-11-13'),
(88, 10088, 1, 'SEED-BATCH-088', '2026-09-16', NULL, 150.00, 20.00, 180.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 39, '2028-11-20'),
(89, 10089, 1, 'SEED-BATCH-089', '2026-09-16', NULL, 180.00, 20.00, 216.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 39, '2028-11-27'),
(90, 10090, 1, 'SEED-BATCH-090', '2026-09-16', NULL, 220.00, 20.00, 264.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:11:26', 29, '2028-12-04'),
(91, 10091, 1, 'SEED-BATCH-091', '2026-09-16', NULL, 12.00, 20.00, 14.40, 100, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 100, '2028-12-11'),
(92, 10092, 1, 'SEED-BATCH-092', '2026-09-16', NULL, 180.00, 20.00, 216.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-12-18'),
(93, 10093, 1, 'SEED-BATCH-093', '2026-09-16', NULL, 150.00, 20.00, 180.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2028-12-25'),
(94, 10094, 1, 'SEED-BATCH-094', '2026-09-16', NULL, 250.00, 20.00, 300.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2029-01-01'),
(95, 10095, 1, 'SEED-BATCH-095', '2026-09-16', NULL, 210.00, 20.00, 252.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2029-01-08'),
(96, 10096, 1, 'SEED-BATCH-096', '2026-09-16', NULL, 150.00, 20.00, 180.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2029-01-15'),
(97, 10097, 1, 'SEED-BATCH-097', '2026-09-16', NULL, 120.00, 20.00, 144.00, 40, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 40, '2029-01-22'),
(98, 10098, 1, 'SEED-BATCH-098', '2026-09-16', NULL, 180.00, 20.00, 216.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2029-01-29'),
(99, 10099, 1, 'SEED-BATCH-099', '2026-09-16', NULL, 260.00, 20.00, 312.00, 30, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 30, '2029-02-05'),
(100, 10100, 1, 'SEED-BATCH-100', '2026-09-16', NULL, 8.00, 20.00, 9.60, 70, '2026-09-16 15:10:31', '2026-09-16 15:10:31', 70, '2029-02-12'),
(128, 10075, 1, 'Batch-1', '2026-09-16', NULL, 50.00, 5.00, 52.50, 1, '2026-09-16 15:26:39', '2026-09-16 15:26:39', 1, '2029-02-02'),
(129, 10075, NULL, 'Batch-2', '2026-09-16', NULL, 50.00, 5.00, 52.50, 10, '2026-09-16 15:27:16', '2026-09-16 15:27:16', 10, '2020-02-02'),
(130, 10101, 2, 'Batch-1', '2026-09-17', NULL, 50.00, 5.00, 52.50, 100, '2026-09-17 02:27:24', '2026-09-17 02:29:58', 97, '2030-02-02'),
(131, 10102, 1, 'Batch-1', '2026-09-17', NULL, 10.00, 5.00, 10.50, 100, '2026-09-17 03:50:55', '2026-09-17 03:50:55', 100, '2029-01-02'),
(132, 10103, 1, 'Batch-1', '2026-09-17', NULL, 10.00, 5.00, 10.50, 952, '2026-09-17 08:21:41', '2026-09-17 08:22:32', 0, '2029-02-02'),
(133, 10104, 2, 'Batch-1', '2026-09-17', NULL, 243.00, 5.00, 255.15, 50, '2026-09-17 10:17:22', '2026-09-17 10:17:22', 50, '2026-09-20'),
(134, 10103, 1, 'Batch-2', '2026-09-17', NULL, 50.00, 5.00, 52.50, 100, '2026-09-17 14:51:43', '2026-09-17 14:51:43', 100, '2030-02-02');

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
  `disposed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_disposals`
--

INSERT INTO `inventory_disposals` (`id`, `product_id`, `batch_number`, `quantity`, `expiry_date`, `reason`, `disposed_at`) VALUES
(1, 10002, 'SEED-BATCH-002', 10, '2027-03-29', 'Expired', '2026-09-17 15:31:46'),
(2, 10103, 'Batch-1', 952, '2029-02-02', 'damages', '2026-09-17 16:22:32');

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
(1, 10001, 'SEED-BATCH-001', 0, 100, '2027-03-22', 'No stock', '2026-09-17 15:27:17');

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
(10001, 'Biogesic', 'Paracetamol', 500.00, 2, '2999000000001', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10002, 'Tempra', 'Paracetamol', 250.00, 2, '2999000000002', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Syrup', 3, 5.00, 'mL', 0),
(10003, 'Advil', 'Ibuprofen', 200.00, 2, '2999000000003', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 10.00, 'pcs', 0),
(10004, 'Alaxan FR', 'Ibuprofen + Paracetamol', 200.00, 2, '2999000000004', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 10.00, 'pcs', 0),
(10005, 'Mefinamic', 'Mefenamic Acid', 500.00, 2, '2999000000005', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 10.00, 'pcs', 0),
(10006, 'Kremil-S', 'Aluminum Hydroxide + Magnesium Hydroxide', 178.00, 2, '2999000000006', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10007, 'Buscopan', 'Hyoscine Butylbromide', 10.00, 2, '2999000000007', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10008, 'Diatabs', 'Loperamide', 2.00, 2, '2999000000008', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 4.00, 'pcs', 0),
(10009, 'Imodium', 'Loperamide', 2.00, 2, '2999000000009', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 4.00, 'pcs', 0),
(10010, 'Zyrtec', 'Cetirizine', 10.00, 2, '2999000000010', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10011, 'Claritin', 'Loratadine', 10.00, 2, '2999000000011', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10012, 'Neozep', 'Phenylephrine + Chlorphenamine + Paracetamol', 500.00, 2, '2999000000012', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10013, 'Bioflu', 'Phenylephrine + Chlorphenamine + Paracetamol', 500.00, 2, '2999000000013', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10014, 'Decolgen', 'Phenylephrine + Chlorphenamine + Paracetamol', 500.00, 2, '2999000000014', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10015, 'Solmux', 'Carbocisteine', 500.00, 2, '2999000000015', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 10.00, 'pcs', 0),
(10016, 'Ascof', 'Lagundi Leaf Extract', 600.00, 2, '2999000000016', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Syrup', 3, 60.00, 'mL', 0),
(10017, 'Ventolin', 'Salbutamol', 2.00, 2, '2999000000017', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Syrup', 3, 60.00, 'mL', 0),
(10018, 'Strepsils', 'Amylmetacresol + Dichlorobenzyl Alcohol', 1.00, 2, '2999000000018', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 16.00, 'pcs', 0),
(10019, 'Difflam', 'Benzydamine Hydrochloride', 3.00, 2, '2999000000019', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Solution', 11, 100.00, 'mL', 0),
(10020, 'Gaviscon', 'Sodium Alginate + Sodium Bicarbonate', 500.00, 2, '2999000000020', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Suspension', 3, 150.00, 'mL', 0),
(10021, 'Losec', 'Omeprazole', 20.00, 2, '2999000000021', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 14.00, 'pcs', 0),
(10022, 'Amoxil', 'Amoxicillin', 500.00, 2, '2999000000022', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 20.00, 'pcs', 0),
(10023, 'Augmentin', 'Amoxicillin + Clavulanic Acid', 625.00, 2, '2999000000023', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 14.00, 'pcs', 0),
(10024, 'Keflex', 'Cephalexin', 500.00, 2, '2999000000024', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 20.00, 'pcs', 0),
(10025, 'Cipro', 'Ciprofloxacin', 500.00, 2, '2999000000025', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10026, 'Flagyl', 'Metronidazole', 500.00, 2, '2999000000026', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 20.00, 'pcs', 0),
(10027, 'Bactrim', 'Sulfamethoxazole + Trimethoprim', 800.00, 2, '2999000000027', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 10.00, 'pcs', 0),
(10028, 'Diflucan', 'Fluconazole', 150.00, 2, '2999000000028', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 1.00, 'pcs', 0),
(10029, 'Losartan', 'Losartan Potassium', 50.00, 2, '2999000000029', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10030, 'Norvasc', 'Amlodipine', 5.00, 2, '2999000000030', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10031, 'Metformin', 'Metformin Hydrochloride', 500.00, 2, '2999000000031', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10032, 'Glucophage', 'Metformin Hydrochloride', 850.00, 2, '2999000000032', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10033, 'Lipitor', 'Atorvastatin', 20.00, 2, '2999000000033', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10034, 'Plavix', 'Clopidogrel', 75.00, 2, '2999000000034', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 28.00, 'pcs', 0),
(10035, 'Levothyroxine', 'Levothyroxine Sodium', 50.00, 2, '2999000000035', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10036, 'Ventolin', 'Salbutamol', 100.00, 2, '2999000000036', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Inhaler', 16, 1.00, 'pc', 0),
(10037, 'Insulin Pen', 'Human Insulin', 100.00, 12, '2999000000037', 17, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Injection', 8, 1.00, 'pc', 0),
(10038, 'PediaSure', 'Complete Nutrition Formula', 850.00, 3, '2999000000038', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Can', 'Powder', 9, 850.00, 'g', 0),
(10039, 'Ceelin', 'Vitamin C', 30.00, 2, '2999000000039', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Drops', 7, 30.00, 'mL', 0),
(10040, 'Cherifer', 'Multivitamins + Lysine', 500.00, 2, '2999000000040', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Syrup', 3, 60.00, 'mL', 0),
(10041, 'Conzace', 'Vitamin A + C + E + Zinc', 500.00, 2, '2999000000041', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Capsule', 2, 30.00, 'pcs', 0),
(10042, 'Enervon', 'B-Complex + Vitamin C', 500.00, 2, '2999000000042', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10043, 'Stresstabs', 'Multivitamins + Minerals', 1.00, 15, '2999000000043', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10044, 'Immunomax', 'Vitamin C + Zinc', 500.00, 2, '2999000000044', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10045, 'Caltrate', 'Calcium Carbonate + Vitamin D3', 600.00, 2, '2999000000045', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10046, 'Fern-C', 'Ascorbic Acid + Zinc', 500.00, 2, '2999000000046', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10047, 'Potencee', 'Ascorbic Acid', 500.00, 2, '2999000000047', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10048, 'Kirkland', 'Fish Oil', 1000.00, 2, '2999000000048', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Capsule', 2, 100.00, 'pcs', 0),
(10049, 'Iron Plus', 'Ferrous Sulfate', 325.00, 2, '2999000000049', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10050, 'Folart', 'Folic Acid', 5.00, 2, '2999000000050', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10051, 'Betadine', 'Povidone Iodine', 10.00, 11, '2999000000051', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Solution', 11, 60.00, 'mL', 0),
(10052, 'Alcohol 70%', 'Ethyl Alcohol', 70.00, 11, '2999000000052', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Solution', 11, 500.00, 'mL', 0),
(10053, 'Agua Oxigenada', 'Hydrogen Peroxide', 3.00, 11, '2999000000053', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Solution', 11, 120.00, 'mL', 0),
(10054, 'Bactroban', 'Mupirocin', 2.00, 11, '2999000000054', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Tube', 'Ointment', 6, 15.00, 'g', 0),
(10055, 'Fucidin', 'Fusidic Acid', 2.00, 11, '2999000000055', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Tube', 'Cream', 5, 15.00, 'g', 0),
(10056, 'Caladryl', 'Calamine + Diphenhydramine', 8.00, 11, '2999000000056', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Lotion', 13, 60.00, 'mL', 0),
(10057, 'Salonpas', 'Methyl Salicylate + Menthol', 1.00, 15, '2999000000057', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Patch', 15, 20.00, 'pcs', 0),
(10058, 'Omega Plaster', 'Adhesive Bandage', 1.00, 15, '2999000000058', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Patch', 15, 100.00, 'pcs', 0),
(10059, 'Betadine Gargle', 'Povidone Iodine', 1.00, 11, '2999000000059', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Solution', 11, 120.00, 'mL', 0),
(10060, 'Efficascent Oil', 'Menthol + Methyl Salicylate', 1.00, 15, '2999000000060', 21, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Solution', 11, 100.00, 'mL', 0),
(10061, 'N95 Mask', 'Particulate Respirator', 1.00, 15, '2999000000061', 19, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Powder', 9, 20.00, 'pcs', 0),
(10062, 'Surgical Mask', 'Disposable Face Mask', 1.00, 15, '2999000000062', 19, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Powder', 9, 50.00, 'pcs', 0),
(10063, 'Latex Gloves', 'Examination Gloves', 1.00, 15, '2999000000063', 19, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Powder', 9, 100.00, 'pcs', 0),
(10064, 'Syringe 5mL', 'Sterile Disposable Syringe', 1.00, 15, '2999000000064', 19, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Powder', 9, 100.00, 'pcs', 0),
(10065, 'Syringe 10mL', 'Sterile Disposable Syringe', 1.00, 15, '2999000000065', 19, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Powder', 9, 50.00, 'pcs', 0),
(10066, 'Cotton Balls', 'Sterile Cotton Balls', 1.00, 15, '2999000000066', 19, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Pack', 'Powder', 9, 100.00, 'pcs', 0),
(10067, 'Gauze Pads', 'Sterile Gauze Pad', 1.00, 15, '2999000000067', 19, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Pack', 'Powder', 9, 25.00, 'pcs', 0),
(10068, 'Elastic Bandage', 'Elastic Crepe Bandage', 1.00, 15, '2999000000068', 19, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Roll', 'Powder', 9, 1.00, 'pcs', 0),
(10069, 'Digital Thermometer', 'Digital Clinical Thermometer', 1.00, 15, '2999000000069', 22, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Powder', 9, 1.00, 'pcs', 0),
(10070, 'Blood Glucose Strips', 'Blood Glucose Test Strips', 1.00, 15, '2999000000070', 22, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Powder', 9, 50.00, 'pcs', 0),
(10071, 'Safeguard', 'Antibacterial Soap', 90.00, 3, '2999000000071', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bar', 'Powder', 9, 1.00, 'pcs', 0),
(10072, 'Cetaphil', 'Gentle Cleansing Bar', 127.00, 3, '2999000000072', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Powder', 9, 1.00, 'pcs', 0),
(10073, 'Lactacyd', 'Feminine Wash', 200.00, 6, '2999000000073', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Powder', 9, 1.00, 'pcs', 0),
(10074, 'Nivea', 'Moisturizing Lotion', 250.00, 6, '2999000000074', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Powder', 9, 1.00, 'pcs', 0),
(10075, 'Kojic', 'Kojic Acid Soap', 65.00, 3, '2999000000075', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bar', 'Powder', 9, 1.00, 'pcs', 0),
(10076, 'Sunscreen SPF50', 'Broad Spectrum Sunscreen', 50.00, 11, '2999000000076', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Tube', 'Powder', 9, 1.00, 'pcs', 0),
(10077, 'Pond\'s', 'Facial Moisturizer', 50.00, 3, '2999000000077', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Jar', 'Powder', 9, 1.00, 'pcs', 0),
(10078, 'Colgate', 'Fluoride Toothpaste', 1450.00, 11, '2999000000078', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Tube', 'Powder', 9, 100.00, 'pcs', 0),
(10079, 'Listerine', 'Mouthwash', 250.00, 6, '2999000000079', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Powder', 9, 1.00, 'pcs', 0),
(10080, 'Vaseline', 'Petroleum Jelly', 100.00, 3, '2999000000080', 25, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Jar', 'Powder', 9, 1.00, 'pcs', 0),
(10081, 'Johnson\'s Baby', 'Baby Powder', 200.00, 3, '2999000000081', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Powder', 9, 1.00, 'pcs', 0),
(10082, 'Huggies', 'Baby Diaper Small', 1.00, 15, '2999000000082', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Pack', 'Powder', 9, 24.00, 'pcs', 0),
(10083, 'Huggies', 'Baby Diaper Medium', 1.00, 15, '2999000000083', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Pack', 'Powder', 9, 24.00, 'pcs', 0),
(10084, 'Huggies', 'Baby Diaper Large', 1.00, 15, '2999000000084', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Pack', 'Powder', 9, 20.00, 'pcs', 0),
(10085, 'Enfamil', 'Infant Formula', 800.00, 3, '2999000000085', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Can', 'Powder', 9, 1.00, 'pcs', 0),
(10086, 'Similac', 'Infant Formula', 800.00, 3, '2999000000086', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Can', 'Powder', 9, 1.00, 'pcs', 0),
(10087, 'Nursicare', 'Baby Wipes', 80.00, 15, '2999000000087', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Pack', 'Powder', 9, 1.00, 'pcs', 0),
(10088, 'Johnson\'s Baby', 'Baby Shampoo', 200.00, 6, '2999000000088', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Powder', 9, 1.00, 'pcs', 0),
(10089, 'Baby Dove', 'Baby Lotion', 200.00, 6, '2999000000089', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Powder', 9, 1.00, 'pcs', 0),
(10090, 'Pigeon', 'Feeding Bottle', 240.00, 6, '2999000000090', 26, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Powder', 9, 1.00, 'pcs', 0),
(10091, 'ORS Hydrite', 'Oral Rehydration Salts', 1.00, 15, '2999000000091', 24, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Sachet', 'Powder', 9, 1.00, 'pcs', 0),
(10092, 'Daktarin', 'Miconazole Nitrate', 2.00, 11, '2999000000092', 24, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Tube', 'Cream', 5, 15.00, 'g', 0),
(10093, 'Canesten', 'Clotrimazole', 1.00, 11, '2999000000093', 24, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Tube', 'Cream', 5, 15.00, 'g', 0),
(10094, 'Tears Naturale', 'Hypromellose', 3.00, 11, '2999000000094', 24, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Drops', 7, 15.00, 'mL', 0),
(10095, 'Nafcon-A', 'Naphazoline + Pheniramine', 1.00, 11, '2999000000095', 24, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Drops', 7, 15.00, 'mL', 0),
(10096, 'Vicks', 'Menthol + Camphor', 1.00, 15, '2999000000096', 24, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Jar', 'Ointment', 6, 50.00, 'g', 0),
(10097, 'Tiger Balm', 'Camphor + Menthol', 1.00, 15, '2999000000097', 24, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Jar', 'Ointment', 6, 20.00, 'g', 0),
(10098, 'Salonpas Gel', 'Methyl Salicylate + Menthol', 1.00, 15, '2999000000098', 24, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Tube', 'Gel', 12, 30.00, 'g', 0),
(10099, 'Gaviscon Double Action', 'Sodium Alginate + Calcium Carbonate', 500.00, 2, '2999000000099', 18, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Bottle', 'Suspension', 3, 150.00, 'mL', 0),
(10100, 'Pharex', 'Vitamin B Complex', 1.00, 15, '2999000000100', 20, NULL, 1, '6aa7a6fe8617a-1789372158IMG_4549.jpeg', 0, 'Box', 'Tablet', 1, 30.00, 'pcs', 0),
(10101, '', 'boba', 100.00, 3, '170669953033', 17, NULL, 0, '6aab500c85da6-1789612044Screenshot 2026-07-23 144850.png', 0, '', '', NULL, 1.00, 'pack', 0),
(10102, '', 'TEST', 100.00, 3, '102673930621', 17, NULL, 0, '6aab639f342b2-1789617055Screenshot 2026-07-23 150101.png', 0, '', '', NULL, NULL, '', 0),
(10103, 'dsfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', 'dsfffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', 1000.00, 3, '952347658942', 17, NULL, 0, '6aaba2f0533b7-1789633264Screenshot 2026-07-23 150101.png', 0, '', '', NULL, 1.00, 'pc', 0),
(10104, '', 'brief', 1.00, 7, '1789639999241', 20, NULL, 0, '', 0, '', '', NULL, NULL, '', 0),
(10105, '', 'Amoxicillindd', 1.00, 3, '530569427720', 26, NULL, 0, '', 0, '', '', NULL, NULL, '', 0),
(10106, '', 'tut', 1.00, 7, '887476421576', 28, NULL, 0, '', 0, '', '', NULL, NULL, '', 0),
(10107, '', 'Paracetamolddd', 1.00, 2, '1789640517401', 26, NULL, 0, '', 0, '', '', NULL, NULL, '', 0),
(10108, '', 'dd', 1.00, 6, '1789640587339', 28, NULL, 0, '', 0, '', '', NULL, NULL, '', 0),
(10109, '', 'ddd', 1.00, 8, '286125365552', 28, NULL, 0, '', 0, '', 'Lozenge', 17, 1.00, 'box', 0),
(10110, '', 'dddddss', 1.00, 7, '278470884217', 28, NULL, 0, '', 0, '', '', NULL, NULL, '', 0),
(10111, '', 'tiss', 266.00, 9, '008878145600', 25, NULL, 0, '', 0, '', '', NULL, NULL, '', 0);

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
(1, 'ANDREW PABLO', '12345678910', 1, '2026-09-17 08:56:01', 1);

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
(1, 1, '2026-09-16', 5413.60, 5413.30, -0.30, 'no cents', '2026-09-16 15:47:04');

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
(1, 1, '2026-09-16', 1000.00, NULL, '2026-09-16 14:56:33'),
(2, 1, '2026-09-17', 1000.00, NULL, '2026-09-17 00:52:04'),
(3, 2, '2026-09-17', 100.00, NULL, '2026-09-17 15:27:07');

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
  `verified_at` datetime NOT NULL DEFAULT current_timestamp(),
  `verified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `senior_customers`
--

INSERT INTO `senior_customers` (`id`, `customer_name`, `id_number`, `cashier_id`, `verified_at`, `verified_by`) VALUES
(1, 'ANDREW PABLO', '12345678910', 1, '2026-09-17 08:54:16', 1);

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
(6, 'ML'),
(7, 'pc'),
(8, 'L'),
(9, 'Liters');

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
(1, 'ABC PHARMA', NULL, '+639651800675', 'andrewpablo2005@gmail.com', 'Manila Philippines', 'Pharmaceutical Distributor', 1, '2026-09-16 14:54:43', '2026-09-16 14:54:43'),
(2, 'Andrew Gonzales Pablo', NULL, '+639651800675', 'andrewpablo2005@gmail.com', 'N/A', NULL, 1, '2026-09-17 02:26:55', '2026-09-17 02:26:55');

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
(1, 1, 1, 'Walk-in', NULL, '', 4413.60, '2026-09-16 15:11:26', 0.00, 0.00),
(2, 1, 3, 'ANDREW PABLO', '1', 'pwd', 4.93, '2026-09-17 01:08:27', 1.07, 0.00),
(3, 1, 1, 'Walk-in', NULL, '', 2.40, '2026-09-17 01:09:04', 0.00, 0.00),
(4, 1, 2, 'ANDREW PABLO', '1', 'senior', 4.93, '2026-09-17 01:55:01', 1.07, 0.00),
(5, 1, 2, 'ANDREW PABLO', '1', 'senior', 18.71, '2026-09-17 02:00:37', 2.89, 0.00),
(6, 1, 2, 'ANDREW PABLO', '1', 'senior', 77.95, '2026-09-17 02:12:26', 12.05, 0.00),
(7, 1, 2, 'ANDREW PABLO', '1', 'senior', 5.20, '2026-09-17 02:14:33', 0.80, 0.00),
(8, 1, 2, 'ANDREW PABLO', '1', 'senior', 5.20, '2026-09-17 02:21:19', 0.80, 0.00),
(9, 1, 2, 'ANDREW PABLO', '1', 'senior', 17.74, '2026-09-17 02:23:50', 3.86, 0.00),
(10, 1, 2, 'ANDREW PABLO', '1', 'senior', 73.93, '2026-09-17 02:25:14', 16.07, 0.00),
(11, 1, 1, 'Walk-in', NULL, '', 52.50, '2026-09-17 02:27:53', 0.00, 0.00),
(12, 1, 2, 'ANDREW PABLO', '1', 'senior', 43.12, '2026-09-17 02:29:31', 9.38, 0.00),
(13, 1, 3, 'ANDREW PABLO', '1', 'pwd', 43.12, '2026-09-17 02:29:58', 9.38, 0.00),
(14, 1, 2, 'ANDREW PABLO', '1', 'senior', 18.58, '2026-09-17 02:44:45', 1.82, 0.00),
(15, 1, 2, 'ANDREW PABLO', '1', 'senior', 5.46, '2026-09-17 02:46:59', 0.54, 0.00),
(16, 1, 2, 'ANDREW PABLO', '1', 'senior', 12.96, '2026-09-17 02:51:56', 1.44, 0.00),
(17, 1, 1, 'Walk-in', NULL, '', 13.20, '2026-09-17 03:45:19', 0.00, 0.00),
(18, 1, 1, 'Walk-in', NULL, '', 2.40, '2026-09-17 03:58:24', 0.00, 0.00),
(19, 1, 1, 'Walk-in', NULL, '', 2.40, '2026-09-17 03:58:48', 0.00, 0.00),
(20, 1, 1, 'Walk-in', NULL, '', 960.00, '2026-09-17 07:06:55', 0.00, 0.00),
(21, 2, 2, 'ANDREW PABLO', '1', 'senior', 6.00, '2026-09-17 15:27:42', 0.00, 0.00),
(22, 2, 2, 'ANDREW PABLO', '1', 'senior', 4.80, '2026-09-17 15:31:24', 1.20, 0.00),
(23, 2, 2, 'ANDREW PABLO', '1', 'senior', 6.00, '2026-09-17 15:40:24', 0.00, 0.00),
(24, 2, 2, 'ANDREW PABLO', '1', 'senior', 4.80, '2026-09-17 15:41:03', 1.20, 0.00);

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
(1, 1, 10006, 6, 1, 10.80, 10.80),
(2, 1, 10019, 19, 1, 216.00, 216.00),
(3, 1, 10025, 25, 1, 24.00, 24.00),
(4, 1, 10028, 28, 1, 78.00, 78.00),
(5, 1, 10033, 33, 1, 21.60, 21.60),
(6, 1, 10034, 34, 1, 30.00, 30.00),
(7, 1, 10038, 38, 1, 1140.00, 1140.00),
(8, 1, 10042, 42, 1, 10.80, 10.80),
(9, 1, 10045, 45, 1, 24.00, 24.00),
(10, 1, 10047, 47, 1, 12.00, 12.00),
(11, 1, 10048, 48, 1, 9.60, 9.60),
(12, 1, 10049, 49, 1, 8.40, 8.40),
(13, 1, 10056, 56, 1, 150.00, 150.00),
(14, 1, 10058, 58, 1, 2.40, 2.40),
(15, 1, 10062, 62, 1, 144.00, 144.00),
(16, 1, 10069, 69, 1, 216.00, 216.00),
(17, 1, 10070, 70, 1, 540.00, 540.00),
(18, 1, 10071, 71, 1, 42.00, 42.00),
(19, 1, 10073, 73, 1, 192.00, 192.00),
(20, 1, 10077, 77, 1, 264.00, 264.00),
(21, 1, 10081, 81, 1, 144.00, 144.00),
(22, 1, 10084, 84, 1, 372.00, 372.00),
(23, 1, 10087, 87, 1, 102.00, 102.00),
(24, 1, 10088, 88, 1, 180.00, 180.00),
(25, 1, 10089, 89, 1, 216.00, 216.00),
(26, 1, 10090, 90, 1, 264.00, 264.00),
(27, 2, 10030, 30, 1, 6.00, 4.93),
(28, 3, 10058, 58, 1, 2.40, 2.40),
(29, 4, 10030, 30, 1, 6.00, 4.93),
(30, 5, 10033, 33, 1, 21.60, 18.71),
(31, 6, 10023, 23, 1, 90.00, 77.95),
(32, 7, 10030, 30, 1, 6.00, 5.20),
(33, 8, 10030, 30, 1, 6.00, 5.20),
(34, 9, 10024, 24, 1, 21.60, 17.74),
(35, 10, 10023, 23, 1, 90.00, 73.93),
(36, 11, 10101, 130, 1, 52.50, 52.50),
(37, 12, 10101, 130, 1, 52.50, 43.12),
(38, 13, 10101, 130, 1, 52.50, 43.12),
(39, 14, 10022, 22, 1, 14.40, 13.12),
(40, 14, 10030, 30, 1, 6.00, 5.46),
(41, 15, 10030, 30, 1, 6.00, 5.46),
(42, 16, 10022, 22, 1, 14.40, 12.96),
(43, 17, 10006, 6, 1, 10.80, 10.80),
(44, 17, 10058, 58, 1, 2.40, 2.40),
(45, 18, 10058, 58, 1, 2.40, 2.40),
(46, 19, 10058, 58, 1, 2.40, 2.40),
(47, 20, 10001, 1, 100, 9.60, 960.00),
(48, 21, 10030, 30, 1, 6.00, 6.00),
(49, 22, 10030, 30, 1, 6.00, 4.80),
(50, 23, 10030, 30, 1, 6.00, 6.00),
(51, 24, 10030, 30, 1, 6.00, 4.80);

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
(1, 1, 6, 1, 9.00, '2026-09-16 15:11:26'),
(2, 2, 19, 1, 180.00, '2026-09-16 15:11:26'),
(3, 3, 25, 1, 20.00, '2026-09-16 15:11:26'),
(4, 4, 28, 1, 65.00, '2026-09-16 15:11:26'),
(5, 5, 33, 1, 18.00, '2026-09-16 15:11:26'),
(6, 6, 34, 1, 25.00, '2026-09-16 15:11:26'),
(7, 7, 38, 1, 950.00, '2026-09-16 15:11:26'),
(8, 8, 42, 1, 9.00, '2026-09-16 15:11:26'),
(9, 9, 45, 1, 20.00, '2026-09-16 15:11:26'),
(10, 10, 47, 1, 10.00, '2026-09-16 15:11:26'),
(11, 11, 48, 1, 8.00, '2026-09-16 15:11:26'),
(12, 12, 49, 1, 7.00, '2026-09-16 15:11:26'),
(13, 13, 56, 1, 125.00, '2026-09-16 15:11:26'),
(14, 14, 58, 1, 2.00, '2026-09-16 15:11:26'),
(15, 15, 62, 1, 120.00, '2026-09-16 15:11:26'),
(16, 16, 69, 1, 180.00, '2026-09-16 15:11:26'),
(17, 17, 70, 1, 450.00, '2026-09-16 15:11:26'),
(18, 18, 71, 1, 35.00, '2026-09-16 15:11:26'),
(19, 19, 73, 1, 160.00, '2026-09-16 15:11:26'),
(20, 20, 77, 1, 220.00, '2026-09-16 15:11:26'),
(21, 21, 81, 1, 120.00, '2026-09-16 15:11:26'),
(22, 22, 84, 1, 310.00, '2026-09-16 15:11:26'),
(23, 23, 87, 1, 85.00, '2026-09-16 15:11:26'),
(24, 24, 88, 1, 150.00, '2026-09-16 15:11:26'),
(25, 25, 89, 1, 180.00, '2026-09-16 15:11:26'),
(26, 26, 90, 1, 220.00, '2026-09-16 15:11:26'),
(27, 27, 30, 1, 5.00, '2026-09-17 01:08:27'),
(28, 28, 58, 1, 2.00, '2026-09-17 01:09:04'),
(29, 29, 30, 1, 5.00, '2026-09-17 01:55:01'),
(30, 30, 33, 1, 18.00, '2026-09-17 02:00:37'),
(31, 31, 23, 1, 75.00, '2026-09-17 02:12:26'),
(32, 32, 30, 1, 5.00, '2026-09-17 02:14:33'),
(33, 33, 30, 1, 5.00, '2026-09-17 02:21:19'),
(34, 34, 24, 1, 18.00, '2026-09-17 02:23:50'),
(35, 35, 23, 1, 75.00, '2026-09-17 02:25:14'),
(36, 36, 130, 1, 50.00, '2026-09-17 02:27:53'),
(37, 37, 130, 1, 50.00, '2026-09-17 02:29:31'),
(38, 38, 130, 1, 50.00, '2026-09-17 02:29:58'),
(39, 39, 22, 1, 12.00, '2026-09-17 02:44:45'),
(40, 40, 30, 1, 5.00, '2026-09-17 02:44:45'),
(41, 41, 30, 1, 5.00, '2026-09-17 02:46:59'),
(42, 42, 22, 1, 12.00, '2026-09-17 02:51:56'),
(43, 43, 6, 1, 9.00, '2026-09-17 03:45:19'),
(44, 44, 58, 1, 2.00, '2026-09-17 03:45:19'),
(45, 45, 58, 1, 2.00, '2026-09-17 03:58:24'),
(46, 46, 58, 1, 2.00, '2026-09-17 03:58:48'),
(47, 47, 1, 100, 8.00, '2026-09-17 07:06:55'),
(48, 48, 30, 1, 5.00, '2026-09-17 15:27:42'),
(49, 49, 30, 1, 5.00, '2026-09-17 15:31:24'),
(50, 50, 30, 1, 5.00, '2026-09-17 15:40:24'),
(51, 51, 30, 1, 5.00, '2026-09-17 15:41:03');

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
(21, 'mgg'),
(25, 'pack');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=266;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `inventory_alerts`
--
ALTER TABLE `inventory_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_disposals`
--
ALTER TABLE `inventory_disposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_no_stock`
--
ALTER TABLE `inventory_no_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10112;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `pwd_customers`
--
ALTER TABLE `pwd_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `register_closings`
--
ALTER TABLE `register_closings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `register_openings`
--
ALTER TABLE `register_openings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `serving_unit`
--
ALTER TABLE `serving_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `transaction_batch_allocations`
--
ALTER TABLE `transaction_batch_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `transaction_item_batches`
--
ALTER TABLE `transaction_item_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `unit_measurement`
--
ALTER TABLE `unit_measurement`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
