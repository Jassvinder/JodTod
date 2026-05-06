-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 17, 2026 at 07:40 AM
-- Server version: 9.1.0
-- PHP Version: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jodtod`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` bigint UNSIGNED NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  KEY `blog_posts_author_id_foreign` (`author_id`),
  KEY `blog_posts_is_published_index` (`is_published`),
  KEY `blog_posts_published_at_index` (`published_at`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `content`, `excerpt`, `meta_title`, `meta_description`, `featured_image`, `author_id`, `is_published`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Test', 'test', '<p>Hello How are you?</p><img src=\"/storage/uploads/69bba90d727d5.webp\"><p></p>', 'asdfasdf', NULL, 'asdf asdfasdfasdfd', '/storage/uploads/69bbcf0c215da.webp', 1, 1, '2026-03-19 07:42:38', '2026-03-19 07:42:38', '2026-03-19 10:25:19'),
(2, 'This just a post', 'this-just-a-post', '<h2 style=\"-webkit-text-stroke-width:0px;background-color:rgb(255, 255, 255);color:rgb(0, 0, 0);font-family:DauphinPlain;font-size:24px;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;line-height:24px;margin:0px 0px 10px;orphans:2;padding:0px;text-align:left;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">What is Lorem Ipsum?</h2><p style=\"-webkit-text-stroke-width:0px;background-color:rgb(255, 255, 255);color:rgb(0, 0, 0);font-family:&quot;Open Sans&quot;, Arial, sans-serif;font-size:14px;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;margin:0px 0px 15px;orphans:2;padding:0px;text-align:justify;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\"><strong style=\"margin:0px;padding:0px;\">Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><figure class=\"image image_resized image-style-align-left\" style=\"width:22.74%;\"><img style=\"aspect-ratio:640/533;\" src=\"/storage/uploads/69bbd8d9cd8ca.webp\" width=\"640\" height=\"533\"></figure><p><strong style=\"margin:0px;padding:0px;\">Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p><strong style=\"margin:0px;padding:0px;\">Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>', NULL, NULL, NULL, '/storage/uploads/69bbd7378cc2b.webp', 1, 1, '2026-03-19 11:00:21', '2026-03-19 11:00:21', '2026-03-19 11:08:01');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('jodtod-cache-4f6a1a4ba6ca170abad82ba5e8054e9d6bffb1db:timer', 'i:1774775554;', 1774775554),
('jodtod-cache-4f6a1a4ba6ca170abad82ba5e8054e9d6bffb1db', 'i:1;', 1774775554),
('jodtod-cache-4e3de7cd4af87bbeabd7e05aa62e56e93471551e:timer', 'i:1775060105;', 1775060105),
('jodtod-cache-4e3de7cd4af87bbeabd7e05aa62e56e93471551e', 'i:1;', 1775060105),
('jodtod-cache-4d134bc072212ace2df385dae143139da74ec0ef:timer', 'i:1775060188;', 1775060188),
('jodtod-cache-4d134bc072212ace2df385dae143139da74ec0ef', 'i:1;', 1775060188),
('jodtod-cache-03da1f397831338ca3de0b0788a4250c5bca9f6d:timer', 'i:1775060393;', 1775060393),
('jodtod-cache-03da1f397831338ca3de0b0788a4250c5bca9f6d', 'i:1;', 1775060393),
('jodtod-cache-620f7addd110b3b8c4789f7442e3f5a256ba5a92:timer', 'i:1775091685;', 1775091685),
('jodtod-cache-620f7addd110b3b8c4789f7442e3f5a256ba5a92', 'i:1;', 1775091685),
('jodtod-cache-887309d048beef83ad3eabf2a79a64a389ab1c9f:timer', 'i:1775151688;', 1775151688),
('jodtod-cache-887309d048beef83ad3eabf2a79a64a389ab1c9f', 'i:1;', 1775151688),
('jodtod-cache-d125341655a9b7e0259b79b9b3e962f61627c6e0:timer', 'i:1775228247;', 1775228247),
('jodtod-cache-d125341655a9b7e0259b79b9b3e962f61627c6e0', 'i:1;', 1775228247),
('jodtod-cache-82ed20e2d0b468d1eaa3dff120e743f3794a26f1:timer', 'i:1775228492;', 1775228492),
('jodtod-cache-82ed20e2d0b468d1eaa3dff120e743f3794a26f1', 'i:2;', 1775228492),
('jodtod-cache-2465b9f3aefb04b3fd8cc9f0eebc1ccc5be8c319:timer', 'i:1775229044;', 1775229044),
('jodtod-cache-2465b9f3aefb04b3fd8cc9f0eebc1ccc5be8c319', 'i:1;', 1775229044),
('jodtod-cache-ea50e275f43923125bb1aaba7c68fd85efac5213:timer', 'i:1775370473;', 1775370473),
('jodtod-cache-ea50e275f43923125bb1aaba7c68fd85efac5213', 'i:1;', 1775370473),
('jodtod-cache-aaf0b7e316bb3d59310382fadb306b9f5e1d7d31:timer', 'i:1775370593;', 1775370593),
('jodtod-cache-aaf0b7e316bb3d59310382fadb306b9f5e1d7d31', 'i:1;', 1775370593),
('jodtod-cache-05e9d2dfe32c5eb92939658286d8c74fb1774f25:timer', 'i:1775372360;', 1775372360),
('jodtod-cache-05e9d2dfe32c5eb92939658286d8c74fb1774f25', 'i:3;', 1775372360),
('jodtod-cache-b4a427c8512bf2b86d37d25962b1d280d823e131:timer', 'i:1775381881;', 1775381881),
('jodtod-cache-b4a427c8512bf2b86d37d25962b1d280d823e131', 'i:1;', 1775381881),
('jodtod-cache-259b6d7db40ce2088799078f1196de27c47d61c6:timer', 'i:1775716849;', 1775716849),
('jodtod-cache-259b6d7db40ce2088799078f1196de27c47d61c6', 'i:1;', 1775716849),
('jodtod-cache-2834433a75da8410bc866b1644710b9408ea3c07:timer', 'i:1775799792;', 1775799792),
('jodtod-cache-2834433a75da8410bc866b1644710b9408ea3c07', 'i:4;', 1775799792),
('jodtod-cache-2889a6dc006d6795f78351bfe397a5c9054905ef:timer', 'i:1775842678;', 1775842678),
('jodtod-cache-2889a6dc006d6795f78351bfe397a5c9054905ef', 'i:1;', 1775842678),
('jodtod-cache-0ab5337f5fe017d0638ab998b9aba673218fa72d:timer', 'i:1776238102;', 1776238102),
('jodtod-cache-0ab5337f5fe017d0638ab998b9aba673218fa72d', 'i:1;', 1776238102);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Food', '🍽️', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(2, 'Travel', '🚗', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(3, 'Shopping', '🛍️', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(4, 'Bills', '📄', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(5, 'Entertainment', '🎬', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(6, 'Medical', '🏥', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(7, 'Education', '📚', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(8, 'Rent', '🏠', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(9, 'Other', '📦', '2026-03-09 06:02:13', '2026-03-19 10:00:21'),
(10, 'Books', '📖', '2026-03-19 09:57:44', '2026-03-19 10:00:28');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `contact_user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contacts_user_id_contact_user_id_unique` (`user_id`,`contact_user_id`),
  KEY `contacts_contact_user_id_foreign` (`contact_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `user_id`, `contact_user_id`, `created_at`, `updated_at`) VALUES
(1, 4, 2, '2026-03-18 11:59:56', '2026-03-18 11:59:56'),
(2, 4, 3, '2026-03-18 12:00:04', '2026-03-18 12:00:04'),
(3, 4, 5, '2026-03-18 12:00:21', '2026-03-18 12:00:21'),
(4, 1, 3, '2026-03-19 09:22:05', '2026-03-19 09:22:05'),
(5, 1, 2, '2026-03-19 09:22:12', '2026-03-19 09:22:12'),
(6, 1, 4, '2026-03-19 09:22:17', '2026-03-19 09:22:17'),
(7, 1, 5, '2026-03-19 09:22:24', '2026-03-19 09:22:24'),
(8, 3, 2, '2026-03-19 13:19:13', '2026-03-19 13:19:13'),
(9, 3, 1, '2026-03-19 13:50:23', '2026-03-19 13:50:23'),
(10, 3, 5, '2026-03-23 16:06:13', '2026-03-23 16:06:13'),
(11, 27, 1, '2026-04-03 15:03:00', '2026-04-03 15:03:00');

-- --------------------------------------------------------

--
-- Table structure for table `device_tokens`
--

DROP TABLE IF EXISTS `device_tokens`;
CREATE TABLE IF NOT EXISTS `device_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'expo',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_tokens_token_unique` (`token`),
  KEY `device_tokens_user_id_token_index` (`user_id`,`token`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `device_tokens`
--

INSERT INTO `device_tokens` (`id`, `user_id`, `token`, `platform`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 5, 'cUQ-PKHORTa1mgMe4Qu6Cp:APA91bENQADo5QnFwbSHs6ZAc-4Mhi-VMDjlTCYj-qitM_2xdeIwb0iINgND8BrIjdDmNtaK3ZGnMJ6zRdlsEMbZ0E0xTgvzB18bfVw4Yy3c0T7aHXKGd30', 'fcm', '2026-03-23 16:28:24', '2026-03-23 15:50:52', '2026-03-23 16:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `paid_by` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_date` datetime NOT NULL,
  `split_type` enum('equal','custom','percentage') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_settled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_expense_date_index` (`expense_date`),
  KEY `expenses_is_settled_index` (`is_settled`),
  KEY `expenses_user_id_index` (`user_id`),
  KEY `expenses_group_id_index` (`group_id`),
  KEY `expenses_category_id_index` (`category_id`),
  KEY `expenses_paid_by_index` (`paid_by`),
  KEY `expenses_user_id_group_id_is_settled_index` (`user_id`,`group_id`,`is_settled`),
  KEY `expenses_group_settled_idx` (`group_id`,`is_settled`,`deleted_at`),
  KEY `expenses_payer_settled_idx` (`paid_by`,`is_settled`,`deleted_at`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `group_id`, `user_id`, `paid_by`, `amount`, `category_id`, `description`, `image_1`, `image_2`, `expense_date`, `split_type`, `is_settled`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 5, 5, 4500.00, 9, 'Disel', NULL, NULL, '2026-03-10 18:30:00', 'equal', 1, '2026-03-11 01:18:44', '2026-03-21 07:16:19', NULL),
(2, 2, 5, 5, 700.00, 1, 'Dinner in dhaba', NULL, NULL, '2026-03-10 18:30:00', 'equal', 1, '2026-03-11 01:20:12', '2026-03-21 07:16:19', NULL),
(3, 2, 1, 3, 300.00, 1, 'Tea nasta', NULL, NULL, '2026-03-10 18:30:00', 'equal', 1, '2026-03-11 01:21:07', '2026-03-21 07:16:19', NULL),
(4, 2, 5, 3, 200.00, 1, 'Water bottles', NULL, NULL, '2026-03-10 18:30:00', 'equal', 1, '2026-03-11 01:21:47', '2026-03-21 07:16:19', NULL),
(5, 2, 1, 1, 2700.00, 8, 'Room Rent', NULL, NULL, '2026-03-03 00:00:00', 'equal', 1, '2026-03-11 01:22:24', '2026-03-21 07:16:19', NULL),
(6, NULL, 1, 1, 210.00, 1, 'chaye nasta', NULL, NULL, '2026-02-28 18:30:00', NULL, 0, '2026-03-11 01:35:19', '2026-03-18 09:14:36', NULL),
(7, NULL, 4, 4, 5000.00, 6, 'chemists', NULL, NULL, '2026-03-10 06:14:00', NULL, 0, '2026-03-17 06:14:47', '2026-03-18 09:14:36', NULL),
(8, NULL, 4, 4, 301.00, 7, 'lunch 1', NULL, NULL, '2026-03-11 08:18:00', NULL, 0, '2026-03-17 06:15:35', '2026-03-18 09:14:36', NULL),
(9, NULL, 4, 4, 180.00, 2, 'Uber ride', NULL, NULL, '2026-03-13 07:30:00', NULL, 0, '2026-03-17 06:22:07', '2026-03-18 09:14:36', NULL),
(10, NULL, 4, 4, 935.00, 1, 'cylender', NULL, NULL, '2026-03-12 06:27:00', NULL, 0, '2026-03-17 06:28:12', '2026-03-18 09:14:36', NULL),
(11, NULL, 4, 4, 2500.00, 5, 'c', NULL, NULL, '2026-02-05 06:29:00', NULL, 0, '2026-03-17 06:29:52', '2026-03-18 09:14:36', NULL),
(12, NULL, 3, 3, 3600.00, 4, 'Home Electricity', NULL, 'expenses/L1pZy1ekaTMcRsXjmK4GQIyfIMEff5ytF2xRtCEm.jpg', '2026-03-16 23:00:00', NULL, 0, '2026-03-17 22:26:56', '2026-03-18 12:23:28', NULL),
(13, NULL, 4, 4, 150.00, 1, 'Lunch', NULL, NULL, '2026-03-17 13:00:00', NULL, 0, '2026-03-18 09:20:49', '2026-03-18 09:20:49', NULL),
(14, NULL, 3, 3, 1825.00, 9, 'Settlement: Goa Trip - paid to Laddy', NULL, NULL, '2026-03-19 13:40:28', NULL, 1, '2026-03-19 08:10:28', '2026-03-19 08:10:28', NULL),
(15, NULL, 3, 3, 1825.00, 9, 'Settlement: Goa Trip - paid to Laddy', NULL, NULL, '2026-03-19 14:02:10', NULL, 1, '2026-03-19 08:32:10', '2026-03-19 08:32:10', NULL),
(16, NULL, 5, 5, 7500.00, 4, 'Home Electric bll', 'expenses/Kpf7gDcPDm4czfJz6PGWnCAG68nBck0tsjmZZGTX.jpg', NULL, '2026-03-18 00:00:00', NULL, 0, '2026-03-19 14:57:51', '2026-03-19 14:57:51', NULL),
(17, 5, 1, 2, 1100.00, 1, 'Have dinner', NULL, NULL, '2026-03-20 00:00:00', 'equal', 1, '2026-03-20 08:04:55', '2026-03-20 13:06:44', NULL),
(18, 5, 3, 3, 2700.00, 8, 'Room Rent', 'expenses/69bd00cb8ae8b.webp', NULL, '2026-03-19 00:00:00', 'equal', 1, '2026-03-20 08:09:48', '2026-03-20 13:06:44', NULL),
(19, 5, 3, 3, 800.00, 1, 'Nasta', NULL, NULL, '2026-03-20 00:00:00', 'equal', 1, '2026-03-20 11:52:13', '2026-03-20 13:06:44', NULL),
(20, 6, 3, 3, 100.00, 1, 'Nasta', NULL, NULL, '2026-03-19 00:00:00', 'equal', 1, '2026-03-20 13:01:38', '2026-03-20 13:40:19', NULL),
(21, NULL, 1, 1, 920.00, 9, 'Settlement: Varindavan - paid to Vikas Bansalaa', NULL, NULL, '2026-03-20 18:36:43', NULL, 1, '2026-03-20 13:06:43', '2026-03-20 13:06:43', NULL),
(22, NULL, 4, 4, 920.00, 9, 'Settlement: Varindavan - paid to Vikas Bansalaa', NULL, NULL, '2026-03-20 18:36:43', NULL, 1, '2026-03-20 13:06:43', '2026-03-20 13:06:43', NULL),
(23, NULL, 5, 5, 740.00, 9, 'Settlement: Varindavan - paid to Vikas Bansalaa', NULL, NULL, '2026-03-20 18:36:43', NULL, 1, '2026-03-20 13:06:43', '2026-03-20 13:06:43', NULL),
(24, NULL, 5, 5, 180.00, 9, 'Settlement: Varindavan - paid to Mohan Lal', NULL, NULL, '2026-03-20 18:36:43', NULL, 1, '2026-03-20 13:06:44', '2026-03-20 13:06:44', NULL),
(25, NULL, 1, 1, 100.00, 1, 'Tea bag', NULL, NULL, '2026-03-19 00:00:00', NULL, 0, '2026-03-20 13:20:48', '2026-03-20 13:20:48', NULL),
(26, NULL, 1, 1, 50.00, 9, 'Settlement: Just Test - paid to Vikas Bansalaa', NULL, NULL, '2026-03-20 19:10:19', NULL, 1, '2026-03-20 13:40:19', '2026-03-20 13:40:19', NULL),
(27, 6, 1, 3, 300.00, 5, 'Watch moutkuwa', NULL, NULL, '2026-03-18 00:00:00', 'equal', 1, '2026-03-20 13:41:36', '2026-03-20 13:55:34', NULL),
(28, NULL, 1, 1, 150.00, 9, 'Settlement: Just Test - paid to Vikas Bansalaa', NULL, NULL, '2026-03-20 19:25:34', NULL, 1, '2026-03-20 13:55:34', '2026-03-20 13:55:34', NULL),
(29, NULL, 3, 3, 1825.00, 9, 'Settlement: Goa Trip - paid to Laddy', NULL, NULL, '2026-03-21 12:46:19', NULL, 1, '2026-03-21 07:16:19', '2026-03-21 07:16:19', NULL),
(30, NULL, 2, 2, 1050.00, 9, 'Settlement: Goa Trip - paid to Laddy', NULL, NULL, '2026-03-21 12:46:19', NULL, 1, '2026-03-21 07:16:19', '2026-03-21 07:16:19', NULL),
(31, NULL, 2, 2, 375.00, 9, 'Settlement: Goa Trip - paid to Jasvinder Kumar', NULL, NULL, '2026-03-21 12:46:19', NULL, 1, '2026-03-21 07:16:19', '2026-03-21 07:16:19', NULL),
(32, 6, 1, 1, 5300.00, 5, 'Party', NULL, NULL, '2026-03-20 00:00:00', 'equal', 0, '2026-03-21 07:21:06', '2026-03-21 07:21:06', NULL),
(33, 6, 1, 1, 200.00, 10, 'Shyam bhajan', NULL, NULL, '2026-03-22 00:00:00', 'equal', 0, '2026-03-23 13:51:56', '2026-03-23 13:51:56', NULL),
(34, 6, 1, 1, 1257.00, 1, 'Dinner in dhaba', NULL, NULL, '2026-03-23 00:00:00', 'equal', 0, '2026-03-23 14:13:37', '2026-03-23 14:13:37', NULL),
(35, 6, 1, 1, 935.00, 2, 'petrol', NULL, NULL, '2026-03-23 00:00:00', 'equal', 0, '2026-03-23 14:15:08', '2026-03-23 14:15:08', NULL),
(36, 6, 1, 1, 215.00, 1, 'coffee', NULL, NULL, '2026-03-23 00:00:00', 'equal', 0, '2026-03-23 14:16:12', '2026-03-23 14:16:12', NULL),
(37, 6, 1, 1, 67.00, 6, 'Disprin', NULL, NULL, '2026-03-23 00:00:00', 'equal', 0, '2026-03-23 16:08:23', '2026-03-23 16:08:23', NULL),
(38, NULL, 19, 19, 1000.00, 1, 'Lunch', NULL, NULL, '2026-03-28 12:39:00', NULL, 0, '2026-03-28 12:39:37', '2026-03-28 12:39:37', NULL),
(39, NULL, 28, 28, 5000.00, 4, 'Home', NULL, NULL, '2026-04-02 00:00:00', NULL, 0, '2026-04-05 06:33:56', '2026-04-05 06:33:56', NULL),
(40, NULL, 28, 28, 300.00, 7, NULL, NULL, NULL, '2026-04-05 00:00:00', NULL, 0, '2026-04-05 06:45:50', '2026-04-05 06:45:50', NULL),
(41, 8, 28, 28, 1000.00, 1, 'For food', NULL, NULL, '2026-04-05 00:00:00', 'equal', 0, '2026-04-05 06:47:36', '2026-04-05 06:47:36', NULL),
(42, NULL, 29, 29, 100.00, 4, 'Tt', 'expenses/vCDdTGUvveJFvbsyQ0gUhQuoxBsfay8Ox88sD7OU.jpg', NULL, '2026-04-05 00:00:00', NULL, 0, '2026-04-05 07:00:00', '2026-04-05 07:00:00', NULL),
(43, NULL, 29, 29, 300.00, 10, 'Wjdj', NULL, NULL, '2026-04-05 00:00:00', NULL, 0, '2026-04-05 07:00:10', '2026-04-05 07:00:10', NULL),
(44, 8, 28, 28, 500.00, 5, 'Cinema', NULL, NULL, '2026-04-10 00:00:00', 'equal', 0, '2026-04-10 05:48:47', '2026-04-10 05:48:47', NULL),
(45, NULL, 28, 28, 1500.00, 3, NULL, NULL, NULL, '2026-04-10 00:00:00', NULL, 0, '2026-04-10 05:50:41', '2026-04-10 05:50:41', NULL),
(46, NULL, 28, 28, 2500.00, 8, NULL, NULL, NULL, '2026-04-15 00:00:00', NULL, 0, '2026-04-15 09:05:30', '2026-04-15 09:05:30', NULL),
(47, NULL, 28, 28, 500.00, 2, NULL, NULL, NULL, '2026-04-15 00:00:00', NULL, 0, '2026-04-15 09:07:09', '2026-04-15 09:07:09', NULL),
(48, NULL, 28, 28, 450.00, 5, NULL, NULL, NULL, '2026-04-15 00:00:00', NULL, 0, '2026-04-15 09:08:12', '2026-04-15 09:08:12', NULL),
(49, NULL, 28, 28, 1570.00, 9, NULL, NULL, NULL, '2026-04-15 00:00:00', NULL, 0, '2026-04-15 09:08:29', '2026-04-15 09:08:29', NULL),
(50, NULL, 28, 28, 750.00, 1, NULL, NULL, NULL, '2026-04-15 00:00:00', NULL, 0, '2026-04-15 09:08:53', '2026-04-15 09:08:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_splits`
--

DROP TABLE IF EXISTS `expense_splits`;
CREATE TABLE IF NOT EXISTS `expense_splits` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `expense_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `share_amount` decimal(12,2) NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `is_settled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_splits_expense_id_user_id_unique` (`expense_id`,`user_id`),
  KEY `expense_splits_is_settled_index` (`is_settled`),
  KEY `expense_splits_user_id_index` (`user_id`),
  KEY `splits_expense_user_idx` (`expense_id`,`user_id`),
  KEY `splits_user_settled_idx` (`user_id`,`is_settled`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_splits`
--

INSERT INTO `expense_splits` (`id`, `expense_id`, `user_id`, `share_amount`, `percentage`, `is_settled`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1125.00, NULL, 1, '2026-03-11 01:18:44', '2026-03-21 07:16:19'),
(2, 1, 2, 1125.00, NULL, 1, '2026-03-11 01:18:44', '2026-03-21 07:16:19'),
(3, 1, 5, 1125.00, NULL, 1, '2026-03-11 01:18:44', '2026-03-21 07:16:19'),
(4, 1, 3, 1125.00, NULL, 1, '2026-03-11 01:18:44', '2026-03-21 07:16:19'),
(5, 2, 1, 175.00, NULL, 1, '2026-03-11 01:20:12', '2026-03-21 07:16:19'),
(6, 2, 2, 175.00, NULL, 1, '2026-03-11 01:20:13', '2026-03-21 07:16:19'),
(7, 2, 3, 175.00, NULL, 1, '2026-03-11 01:20:13', '2026-03-21 07:16:19'),
(8, 2, 5, 175.00, NULL, 1, '2026-03-11 01:20:13', '2026-03-21 07:16:19'),
(9, 3, 1, 75.00, NULL, 1, '2026-03-11 01:21:07', '2026-03-21 07:16:19'),
(10, 3, 2, 75.00, NULL, 1, '2026-03-11 01:21:07', '2026-03-21 07:16:19'),
(11, 3, 3, 75.00, NULL, 1, '2026-03-11 01:21:07', '2026-03-21 07:16:19'),
(12, 3, 5, 75.00, NULL, 1, '2026-03-11 01:21:07', '2026-03-21 07:16:19'),
(13, 4, 1, 50.00, NULL, 1, '2026-03-11 01:21:47', '2026-03-21 07:16:19'),
(14, 4, 2, 50.00, NULL, 1, '2026-03-11 01:21:47', '2026-03-21 07:16:19'),
(15, 4, 3, 50.00, NULL, 1, '2026-03-11 01:21:47', '2026-03-21 07:16:19'),
(16, 4, 5, 50.00, NULL, 1, '2026-03-11 01:21:47', '2026-03-21 07:16:19'),
(23, 5, 5, 900.00, NULL, 1, '2026-03-18 12:31:52', '2026-03-21 07:16:19'),
(22, 5, 3, 900.00, NULL, 1, '2026-03-18 12:31:52', '2026-03-21 07:16:19'),
(21, 5, 1, 900.00, NULL, 1, '2026-03-18 12:31:52', '2026-03-21 07:16:19'),
(24, 17, 1, 220.00, NULL, 1, '2026-03-20 08:04:55', '2026-03-20 13:06:44'),
(25, 17, 2, 220.00, NULL, 1, '2026-03-20 08:04:55', '2026-03-20 13:06:44'),
(26, 17, 3, 220.00, NULL, 1, '2026-03-20 08:04:55', '2026-03-20 13:06:44'),
(27, 17, 4, 220.00, NULL, 1, '2026-03-20 08:04:55', '2026-03-20 13:06:44'),
(28, 17, 5, 220.00, NULL, 1, '2026-03-20 08:04:55', '2026-03-20 13:06:44'),
(29, 18, 1, 540.00, NULL, 1, '2026-03-20 08:09:48', '2026-03-20 13:06:44'),
(30, 18, 2, 540.00, NULL, 1, '2026-03-20 08:09:48', '2026-03-20 13:06:44'),
(31, 18, 3, 540.00, NULL, 1, '2026-03-20 08:09:48', '2026-03-20 13:06:44'),
(32, 18, 4, 540.00, NULL, 1, '2026-03-20 08:09:48', '2026-03-20 13:06:44'),
(33, 18, 5, 540.00, NULL, 1, '2026-03-20 08:09:48', '2026-03-20 13:06:44'),
(34, 19, 1, 160.00, NULL, 1, '2026-03-20 11:52:13', '2026-03-20 13:06:44'),
(35, 19, 2, 160.00, NULL, 1, '2026-03-20 11:52:13', '2026-03-20 13:06:44'),
(36, 19, 3, 160.00, NULL, 1, '2026-03-20 11:52:13', '2026-03-20 13:06:44'),
(37, 19, 4, 160.00, NULL, 1, '2026-03-20 11:52:13', '2026-03-20 13:06:44'),
(38, 19, 5, 160.00, NULL, 1, '2026-03-20 11:52:13', '2026-03-20 13:06:44'),
(39, 20, 3, 50.00, NULL, 1, '2026-03-20 13:01:38', '2026-03-20 13:40:19'),
(40, 20, 1, 50.00, NULL, 1, '2026-03-20 13:01:38', '2026-03-20 13:40:19'),
(41, 27, 3, 150.00, NULL, 1, '2026-03-20 13:41:36', '2026-03-20 13:55:34'),
(42, 27, 1, 150.00, NULL, 1, '2026-03-20 13:41:36', '2026-03-20 13:55:34'),
(43, 32, 3, 2650.00, NULL, 0, '2026-03-21 07:21:06', '2026-03-21 07:21:06'),
(44, 32, 1, 2650.00, NULL, 0, '2026-03-21 07:21:06', '2026-03-21 07:21:06'),
(45, 33, 3, 100.00, NULL, 0, '2026-03-23 13:51:56', '2026-03-23 13:51:56'),
(46, 33, 1, 100.00, NULL, 0, '2026-03-23 13:51:56', '2026-03-23 13:51:56'),
(47, 34, 3, 628.50, NULL, 0, '2026-03-23 14:13:37', '2026-03-23 14:13:37'),
(48, 34, 1, 628.50, NULL, 0, '2026-03-23 14:13:37', '2026-03-23 14:13:37'),
(49, 35, 3, 467.50, NULL, 0, '2026-03-23 14:15:08', '2026-03-23 14:15:08'),
(50, 35, 1, 467.50, NULL, 0, '2026-03-23 14:15:08', '2026-03-23 14:15:08'),
(51, 36, 3, 107.50, NULL, 0, '2026-03-23 14:16:12', '2026-03-23 14:16:12'),
(52, 36, 1, 107.50, NULL, 0, '2026-03-23 14:16:12', '2026-03-23 14:16:12'),
(53, 37, 3, 22.34, NULL, 0, '2026-03-23 16:08:23', '2026-03-23 16:08:23'),
(54, 37, 1, 22.33, NULL, 0, '2026-03-23 16:08:23', '2026-03-23 16:08:23'),
(55, 37, 5, 22.33, NULL, 0, '2026-03-23 16:08:23', '2026-03-23 16:08:23'),
(56, 41, 28, 1000.00, NULL, 0, '2026-04-05 06:47:36', '2026-04-05 06:47:36'),
(57, 44, 28, 500.00, NULL, 0, '2026-04-10 05:48:47', '2026-04-10 05:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invite_code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `groups_invite_code_unique` (`invite_code`),
  KEY `groups_created_by_index` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `photo`, `invite_code`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Khattu Shyam', 'This group is for Khattu shyam yatra. in the car 5 peoples are in this group.', NULL, '0J7PR2BT', 2, '2026-03-09 09:02:11', '2026-03-09 23:21:12'),
(2, 'Goa Trip', 'This is a goa trip with 4 members', 'groups/fggMr9HRfdOtIbKmXlTqBfXHBC3C9l1VjawfdxvT.jpg', 'DQRB4A40', 1, '2026-03-10 08:35:46', '2026-03-20 05:43:56'),
(3, 'Mata Mansa Devi', 'we are 5 members in this group, this is one day trip. mata ke darshan ke liye.', NULL, 'VYUOMW1Z', 4, '2026-03-17 07:32:04', '2026-03-17 07:43:31'),
(6, 'Just Test', 'Just testing group for 2 members', 'groups/IKBcrGazoiQDVvXE5AnbFFe24mL5388N9IYw69MT.jpg', 'L42IF0TJ', 3, '2026-03-20 13:00:55', '2026-03-20 13:00:55'),
(5, 'Varindavan', '5 peoples trip for varindavan barsana', 'groups/EQSA5KOGV6vcSL4ERMsdNdFjnl9DA3yrEpgHezGk.jpg', 'JMVNCOCS', 1, '2026-03-20 07:58:28', '2026-03-20 07:58:28'),
(7, 'yoyo', 'yoyo', NULL, 'ZQ7BD7SS', 3, '2026-03-23 16:22:59', '2026-03-23 16:22:59'),
(8, 'Tripod', 'My college friends', NULL, 'VJVJVMV0', 28, '2026-04-05 06:34:31', '2026-04-05 06:46:39'),
(9, 'Pp', NULL, NULL, 'YFZAHYRG', 29, '2026-04-05 07:00:24', '2026-04-05 07:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

DROP TABLE IF EXISTS `group_members`;
CREATE TABLE IF NOT EXISTS `group_members` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` enum('admin','member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_members_group_id_user_id_unique` (`group_id`,`user_id`),
  KEY `group_members_user_id_foreign` (`user_id`),
  KEY `gm_group_user_approved_idx` (`group_id`,`user_id`,`is_approved`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `user_id`, `role`, `is_active`, `is_approved`, `joined_at`) VALUES
(1, 1, 2, 'admin', 1, 1, '2026-03-09 14:32:11'),
(2, 2, 1, 'admin', 1, 1, '2026-03-10 14:05:46'),
(3, 2, 2, 'member', 1, 1, '2026-03-10 14:06:16'),
(4, 2, 3, 'member', 1, 1, '2026-03-10 14:08:13'),
(5, 2, 5, 'member', 1, 1, '2026-03-10 14:08:24'),
(6, 3, 4, 'admin', 1, 1, '2026-03-17 13:02:04'),
(7, 3, 3, 'member', 1, 1, '2026-03-17 13:06:03'),
(9, 4, 3, 'admin', 1, 1, '2026-03-20 05:48:43'),
(10, 5, 1, 'admin', 1, 1, '2026-03-20 07:58:28'),
(11, 5, 2, 'member', 1, 1, '2026-03-20 07:58:53'),
(12, 5, 3, 'member', 1, 1, '2026-03-20 07:58:57'),
(13, 5, 4, 'member', 1, 1, '2026-03-20 07:58:58'),
(14, 5, 5, 'member', 1, 1, '2026-03-20 07:58:58'),
(15, 6, 3, 'admin', 1, 1, '2026-03-20 13:00:55'),
(16, 6, 1, 'member', 1, 1, '2026-03-20 13:01:07'),
(17, 6, 5, 'member', 1, 1, '2026-03-23 16:06:22'),
(18, 7, 3, 'admin', 1, 1, '2026-03-23 16:22:59'),
(20, 8, 28, 'admin', 1, 1, '2026-04-05 06:34:31'),
(21, 9, 29, 'admin', 1, 1, '2026-04-05 07:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `incomes`
--

DROP TABLE IF EXISTS `incomes`;
CREATE TABLE IF NOT EXISTS `incomes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `source` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incomes_user_id_income_date_index` (`user_id`,`income_date`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `incomes`
--

INSERT INTO `incomes` (`id`, `user_id`, `amount`, `source`, `description`, `income_date`, `created_at`, `updated_at`) VALUES
(1, 4, 16000.00, 'freelance', 'some projects', '2026-03-02', '2026-03-17 06:31:16', '2026-03-18 09:14:43'),
(2, 1, 5000.00, 'Freelance', NULL, '2026-03-06', '2026-03-20 13:22:19', '2026-03-20 13:22:19'),
(3, 3, 10000.00, 'Investment', 'Just back', '2026-03-23', '2026-03-23 06:27:08', '2026-03-23 06:27:08'),
(4, 28, 75300.00, 'Salary', 'Tttyyuud', '2026-04-01', '2026-04-05 06:32:57', '2026-04-05 06:32:57'),
(5, 28, 75000.00, 'Salary', '.', '2026-02-27', '2026-04-05 06:35:37', '2026-04-05 06:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM AUTO_INCREMENT=198 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_02_000001_create_categories_table', 1),
(5, '2024_01_02_000002_create_expenses_table', 1),
(6, '2026_03_09_121504_add_phone_to_users_table', 2),
(7, '2026_03_09_121505_create_otp_codes_table', 2),
(8, '2026_03_09_123833_add_phone_verified_at_to_users_table', 3),
(9, '2026_03_09_132055_create_groups_table', 4),
(10, '2026_03_09_132056_create_group_members_table', 4),
(11, '2026_03_10_100000_create_expense_splits_table', 5),
(12, '2026_03_10_200000_create_settlements_table', 6),
(14, '2026_03_10_300000_add_role_to_users_table', 7),
(15, '2026_03_10_400000_create_blog_posts_table', 8),
(16, '2026_03_10_090827_create_notifications_table', 9),
(17, '2026_03_10_500000_create_pages_table', 10),
(18, '2026_03_11_073204_change_expense_date_to_datetime', 11),
(19, '2026_03_12_072711_add_performance_indexes_to_tables', 12),
(20, '2026_03_16_063249_create_incomes_table', 12),
(21, '2026_03_16_063249_create_todos_table', 12),
(22, '2026_03_18_062049_create_personal_access_tokens_table', 13),
(23, '2026_03_18_150132_add_reminder_to_todos_table', 14),
(24, '2026_03_18_153209_create_todo_categories_table', 15),
(25, '2026_03_18_153210_add_category_to_todos_table', 15),
(26, '2026_03_18_163250_create_contacts_table', 16),
(27, '2026_03_18_163251_add_assigned_to_to_todos_table', 16),
(28, '2026_03_18_165937_add_banned_at_to_users_table', 17),
(29, '2026_03_18_170241_create_settings_table', 18),
(30, '2026_03_18_171114_add_images_to_expenses_table', 19),
(31, '2026_03_19_144828_add_is_active_to_group_members_table', 20),
(32, '2026_03_20_101802_add_is_approved_to_group_members_table', 21),
(33, '2026_03_20_104344_add_photo_to_groups_table', 22),
(34, '2026_03_20_124234_add_performance_indexes', 23),
(35, '2026_03_21_154915_create_device_tokens_table', 24);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('5685b7ea-1151-42c6-9c42-e951918bac14', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 2, '{\"type\":\"added_to_group\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"added_by\":\"Admin User\",\"message\":\"Admin User added you to \\\"Goa Trip\\\".\"}', NULL, '2026-03-10 08:36:20', '2026-03-10 08:36:20'),
('a251af43-c566-41b4-8d7d-908a3eb19556', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 3, '{\"type\":\"added_to_group\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"added_by\":\"Admin User\",\"message\":\"Admin User added you to \\\"Goa Trip\\\".\"}', '2026-03-19 08:31:19', '2026-03-10 08:38:13', '2026-03-19 08:31:19'),
('4146495f-dc1e-4755-8b15-49eff8942068', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 5, '{\"type\":\"added_to_group\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"added_by\":\"Admin User\",\"message\":\"Admin User added you to \\\"Goa Trip\\\".\"}', '2026-03-11 01:07:39', '2026-03-10 08:38:24', '2026-03-11 01:07:39'),
('1624f57e-27b7-4f48-b389-a0f8134b7ce8', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 1, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":1,\"expense_description\":\"Disel\",\"expense_amount\":\"4500.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b94500.00 for \\\"Disel\\\" in Goa Trip.\"}', '2026-03-19 13:55:06', '2026-03-11 01:18:44', '2026-03-19 13:55:06'),
('2a35ea27-c162-4a6b-bb0f-83551cf7a823', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 2, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":1,\"expense_description\":\"Disel\",\"expense_amount\":\"4500.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b94500.00 for \\\"Disel\\\" in Goa Trip.\"}', NULL, '2026-03-11 01:18:45', '2026-03-11 01:18:45'),
('0457b714-4c33-4758-940f-eed0dee75d8c', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":1,\"expense_description\":\"Disel\",\"expense_amount\":\"4500.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b94500.00 for \\\"Disel\\\" in Goa Trip.\"}', '2026-03-17 10:13:42', '2026-03-11 01:18:45', '2026-03-17 10:13:42'),
('b2e099fc-c4c1-45b2-b82c-4fabcafcc67d', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 1, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":2,\"expense_description\":\"Dinner in dhaba\",\"expense_amount\":\"700.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b9700.00 for \\\"Dinner in dhaba\\\" in Goa Trip.\"}', '2026-03-19 13:52:14', '2026-03-11 01:20:13', '2026-03-19 13:52:14'),
('bb4bb17e-9968-4c0d-829c-46c00bc4cdcf', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 2, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":2,\"expense_description\":\"Dinner in dhaba\",\"expense_amount\":\"700.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b9700.00 for \\\"Dinner in dhaba\\\" in Goa Trip.\"}', NULL, '2026-03-11 01:20:13', '2026-03-11 01:20:13'),
('6c748140-6c2c-4bf6-87db-590fe54f3301', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":2,\"expense_description\":\"Dinner in dhaba\",\"expense_amount\":\"700.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b9700.00 for \\\"Dinner in dhaba\\\" in Goa Trip.\"}', '2026-03-19 08:31:19', '2026-03-11 01:20:13', '2026-03-19 08:31:19'),
('6cd930cd-a59f-4d7a-9492-3b3f0343a595', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 2, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":3,\"expense_description\":\"Tea nasta\",\"expense_amount\":\"300.00\",\"payer_name\":\"Admin User\",\"message\":\"Admin User added \\u20b9300.00 for \\\"Tea nasta\\\" in Goa Trip.\"}', NULL, '2026-03-11 01:21:07', '2026-03-11 01:21:07'),
('8a38e232-825e-4c56-96f2-e304bc1b4af8', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":3,\"expense_description\":\"Tea nasta\",\"expense_amount\":\"300.00\",\"payer_name\":\"Admin User\",\"message\":\"Admin User added \\u20b9300.00 for \\\"Tea nasta\\\" in Goa Trip.\"}', '2026-03-19 08:31:19', '2026-03-11 01:21:07', '2026-03-19 08:31:19'),
('a314992d-b09f-4e25-ac54-338b89840e7e', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 5, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":3,\"expense_description\":\"Tea nasta\",\"expense_amount\":\"300.00\",\"payer_name\":\"Admin User\",\"message\":\"Admin User added \\u20b9300.00 for \\\"Tea nasta\\\" in Goa Trip.\"}', NULL, '2026-03-11 01:21:08', '2026-03-11 01:21:08'),
('04887a24-db43-4a73-8b29-e7c53faec5cf', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 1, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":4,\"expense_description\":\"Water bottles\",\"expense_amount\":\"200.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b9200.00 for \\\"Water bottles\\\" in Goa Trip.\"}', '2026-03-19 13:51:50', '2026-03-11 01:21:47', '2026-03-19 13:51:50'),
('db0035dc-ceaa-4ea4-ae59-311ebdef931a', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 2, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":4,\"expense_description\":\"Water bottles\",\"expense_amount\":\"200.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b9200.00 for \\\"Water bottles\\\" in Goa Trip.\"}', NULL, '2026-03-11 01:21:47', '2026-03-11 01:21:47'),
('795903e6-ca23-4550-a0de-6871e96a5c0c', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":4,\"expense_description\":\"Water bottles\",\"expense_amount\":\"200.00\",\"payer_name\":\"Neha Gupta\",\"message\":\"Neha Gupta added \\u20b9200.00 for \\\"Water bottles\\\" in Goa Trip.\"}', '2026-03-19 08:31:19', '2026-03-11 01:21:48', '2026-03-19 08:31:19'),
('efb9aa38-0e32-4c88-b104-e93c2c6e0a8a', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 2, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":5,\"expense_description\":\"Room Rent\",\"expense_amount\":\"2700.00\",\"payer_name\":\"Admin User\",\"message\":\"Admin User added \\u20b92700.00 for \\\"Room Rent\\\" in Goa Trip.\"}', NULL, '2026-03-11 01:22:24', '2026-03-11 01:22:24'),
('99f822a8-a65f-4b90-b404-93065d80aa24', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":5,\"expense_description\":\"Room Rent\",\"expense_amount\":\"2700.00\",\"payer_name\":\"Admin User\",\"message\":\"Admin User added \\u20b92700.00 for \\\"Room Rent\\\" in Goa Trip.\"}', '2026-03-19 08:31:19', '2026-03-11 01:22:25', '2026-03-19 08:31:19'),
('3e0feb14-b263-425a-93bc-cc2b3040b29c', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 5, '{\"type\":\"group_expense_added\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"expense_id\":5,\"expense_description\":\"Room Rent\",\"expense_amount\":\"2700.00\",\"payer_name\":\"Admin User\",\"message\":\"Admin User added \\u20b92700.00 for \\\"Room Rent\\\" in Goa Trip.\"}', NULL, '2026-03-11 01:22:25', '2026-03-11 01:22:25'),
('8c48b157-4200-44d3-9d83-4714ef2635dd', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 3, '{\"type\":\"todo_assigned\",\"todo_id\":4,\"title\":\"gold shopping\",\"priority\":\"high\",\"assigned_by\":\"Raja\",\"message\":\"Raja assigned you: gold shopping\"}', '2026-03-18 12:21:18', '2026-03-18 06:31:25', '2026-03-18 12:21:18'),
('7eb779c3-ff76-48d9-b96b-b2016fffe29b', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 3, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1825,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91825 to Laddy in Goa Trip.\"}', '2026-03-19 08:31:19', '2026-03-19 02:37:23', '2026-03-19 08:31:19'),
('df37b206-58a8-48f3-b94b-08b83d364406', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1050,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91050 to Laddy in Goa Trip.\"}', NULL, '2026-03-19 02:37:34', '2026-03-19 02:37:34'),
('13425ab4-0e29-4707-b6a2-60046abb36f1', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":375,\"to_user_name\":\"Jasvinder Kumar\",\"message\":\"You need to pay \\u20b9375 to Jasvinder Kumar in Goa Trip.\"}', NULL, '2026-03-19 02:37:36', '2026-03-19 02:37:36'),
('0b5346b6-4495-45b5-8985-4e6dfbade1d3', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 3, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1825,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91825 to Laddy in Goa Trip.\"}', '2026-03-19 08:10:15', '2026-03-19 02:39:25', '2026-03-19 08:10:15'),
('cf68b72f-a13a-4bdb-9991-5ba8f63c0f0d', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1050,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91050 to Laddy in Goa Trip.\"}', NULL, '2026-03-19 02:39:28', '2026-03-19 02:39:28'),
('83bf746e-dc77-4eb5-b333-888ec096aeb0', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":375,\"to_user_name\":\"Jasvinder Kumar\",\"message\":\"You need to pay \\u20b9375 to Jasvinder Kumar in Goa Trip.\"}', NULL, '2026-03-19 02:39:31', '2026-03-19 02:39:31'),
('68285593-a582-4b8a-8163-9af93382bb7d', 'App\\Notifications\\SettlementCompleted', 'App\\Models\\User', 5, '{\"type\":\"settlement_completed\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1825,\"from_user_name\":\"Vikas Bansal\",\"message\":\"Vikas Bansal paid you \\u20b91825 in Goa Trip.\"}', NULL, '2026-03-19 02:40:28', '2026-03-19 02:40:28'),
('e40d6598-ea89-4159-8cfc-d7a4b69f8dd3', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 3, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1825,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91825 to Laddy in Goa Trip.\"}', '2026-03-19 08:31:19', '2026-03-19 02:42:32', '2026-03-19 08:31:19'),
('08db559f-74e8-4768-a362-18f300db4d49', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1050,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91050 to Laddy in Goa Trip.\"}', NULL, '2026-03-19 02:42:35', '2026-03-19 02:42:35'),
('41998cd7-60b7-4d36-87de-00db8557f8bb', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":375,\"to_user_name\":\"Jasvinder Kumar\",\"message\":\"You need to pay \\u20b9375 to Jasvinder Kumar in Goa Trip.\"}', NULL, '2026-03-19 02:42:37', '2026-03-19 02:42:37'),
('82836be9-fe55-459f-9f0d-38ad5764874f', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 3, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1825,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91825 to Laddy in Goa Trip.\"}', '2026-03-19 08:31:08', '2026-03-19 02:47:06', '2026-03-19 08:31:08'),
('0eed792a-b6d5-428e-85d9-2186ab6beec5', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1050,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91050 to Laddy in Goa Trip.\"}', NULL, '2026-03-19 02:47:31', '2026-03-19 02:47:31'),
('5abee418-c15c-4d1e-9d2d-4696e037b72a', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":375,\"to_user_name\":\"Jasvinder Kumar\",\"message\":\"You need to pay \\u20b9375 to Jasvinder Kumar in Goa Trip.\"}', NULL, '2026-03-19 02:47:33', '2026-03-19 02:47:33'),
('abd12148-1537-4804-a8d6-14e96848e4e4', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 3, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1825,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91825 to Laddy in Goa Trip.\"}', '2026-03-19 08:32:01', '2026-03-19 03:01:28', '2026-03-19 08:32:01'),
('052511c3-4524-4036-b1b5-b95c1308278a', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1050,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91050 to Laddy in Goa Trip.\"}', NULL, '2026-03-19 03:01:33', '2026-03-19 03:01:33'),
('2766a512-1684-4337-bb5c-3a5cef517bfe', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":375,\"to_user_name\":\"Jasvinder Kumar\",\"message\":\"You need to pay \\u20b9375 to Jasvinder Kumar in Goa Trip.\"}', NULL, '2026-03-19 03:01:36', '2026-03-19 03:01:36'),
('4165d225-d1c4-44d8-a380-e8961e773166', 'App\\Notifications\\SettlementCompleted', 'App\\Models\\User', 5, '{\"type\":\"settlement_completed\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1825,\"from_user_name\":\"Vikas Bansal\",\"message\":\"Vikas Bansal paid you \\u20b91825 in Goa Trip.\"}', NULL, '2026-03-19 03:02:12', '2026-03-19 03:02:12'),
('381302ff-d5c1-4eab-abb1-6b1a665e979b', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 1, '{\"type\":\"todo_assigned\",\"todo_id\":8,\"title\":\"Need to take medicine\",\"priority\":\"high\",\"assigned_by\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa assigned you: Need to take medicine\"}', '2026-03-19 13:51:44', '2026-03-19 13:51:13', '2026-03-19 13:51:44'),
('6eecebc5-ef24-4d00-a8e8-81e72da0b980', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 2, '{\"type\":\"added_to_group\",\"group_id\":5,\"group_name\":\"Varindavan\",\"added_by\":\"Jasvinder Kumar k\",\"message\":\"Jasvinder Kumar k added you to \\\"Varindavan\\\".\"}', NULL, '2026-03-20 10:16:30', '2026-03-20 10:16:30'),
('386f1a32-8933-418e-904c-3580c02201c8', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 3, '{\"type\":\"added_to_group\",\"group_id\":5,\"group_name\":\"Varindavan\",\"added_by\":\"Jasvinder Kumar k\",\"message\":\"Jasvinder Kumar k added you to \\\"Varindavan\\\".\"}', '2026-03-23 10:03:33', '2026-03-20 10:16:31', '2026-03-23 10:03:33'),
('f9087c48-6362-4501-9545-242a8ab8ef76', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 4, '{\"type\":\"added_to_group\",\"group_id\":5,\"group_name\":\"Varindavan\",\"added_by\":\"Jasvinder Kumar k\",\"message\":\"Jasvinder Kumar k added you to \\\"Varindavan\\\".\"}', NULL, '2026-03-20 10:16:31', '2026-03-20 10:16:31'),
('46f96c94-44cc-45fb-ad19-3f2e7fcf633c', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 5, '{\"type\":\"added_to_group\",\"group_id\":5,\"group_name\":\"Varindavan\",\"added_by\":\"Jasvinder Kumar k\",\"message\":\"Jasvinder Kumar k added you to \\\"Varindavan\\\".\"}', NULL, '2026-03-20 10:16:31', '2026-03-20 10:16:31'),
('9b426031-a827-4ff1-aed8-7eb77603d30f', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 2, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":17,\"expense_description\":\"Have dinner\",\"expense_amount\":\"1100.00\",\"payer_name\":\"Jasvinder Kumar k\",\"message\":\"Jasvinder Kumar k added \\u20b91100.00 for \\\"Have dinner\\\" in Varindavan.\"}', NULL, '2026-03-20 10:16:31', '2026-03-20 10:16:31'),
('3b1a9985-2cfd-4730-be17-17659f19e122', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":17,\"expense_description\":\"Have dinner\",\"expense_amount\":\"1100.00\",\"payer_name\":\"Jasvinder Kumar k\",\"message\":\"Jasvinder Kumar k added \\u20b91100.00 for \\\"Have dinner\\\" in Varindavan.\"}', '2026-03-23 10:03:28', '2026-03-20 10:16:32', '2026-03-23 10:03:28'),
('7ca11e3b-c447-4984-978f-9425c0a0d3b9', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 4, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":17,\"expense_description\":\"Have dinner\",\"expense_amount\":\"1100.00\",\"payer_name\":\"Jasvinder Kumar k\",\"message\":\"Jasvinder Kumar k added \\u20b91100.00 for \\\"Have dinner\\\" in Varindavan.\"}', NULL, '2026-03-20 10:16:32', '2026-03-20 10:16:32'),
('93aae252-9832-4473-b981-63c65963955a', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 5, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":17,\"expense_description\":\"Have dinner\",\"expense_amount\":\"1100.00\",\"payer_name\":\"Jasvinder Kumar k\",\"message\":\"Jasvinder Kumar k added \\u20b91100.00 for \\\"Have dinner\\\" in Varindavan.\"}', NULL, '2026-03-20 10:16:32', '2026-03-20 10:16:32'),
('8c79fd2d-68da-493b-b850-f993e3b6b5cf', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 1, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":18,\"expense_description\":\"Room Rent\",\"expense_amount\":\"2700.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b92700.00 for \\\"Room Rent\\\" in Varindavan.\"}', '2026-03-23 13:45:17', '2026-03-20 10:16:32', '2026-03-23 13:45:17'),
('9a527a06-f3d0-4ef2-b2d2-0189b7917e32', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 2, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":18,\"expense_description\":\"Room Rent\",\"expense_amount\":\"2700.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b92700.00 for \\\"Room Rent\\\" in Varindavan.\"}', NULL, '2026-03-20 10:16:33', '2026-03-20 10:16:33'),
('ab616729-8007-4e38-95af-bd5170a2764d', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 4, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":18,\"expense_description\":\"Room Rent\",\"expense_amount\":\"2700.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b92700.00 for \\\"Room Rent\\\" in Varindavan.\"}', NULL, '2026-03-20 10:16:33', '2026-03-20 10:16:33'),
('f9368df0-41ca-4b88-a730-5935c533a7be', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 5, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":18,\"expense_description\":\"Room Rent\",\"expense_amount\":\"2700.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b92700.00 for \\\"Room Rent\\\" in Varindavan.\"}', NULL, '2026-03-20 10:16:33', '2026-03-20 10:16:33'),
('45edceaa-0502-45a5-931e-c0f1a05978b0', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 1, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":19,\"expense_description\":\"Nasta\",\"expense_amount\":\"800.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b9800.00 for \\\"Nasta\\\" in Varindavan.\"}', '2026-03-20 12:42:36', '2026-03-20 11:52:16', '2026-03-20 12:42:36'),
('c318f1ad-ab8e-4ac7-a05d-4005df09525f', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 2, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":19,\"expense_description\":\"Nasta\",\"expense_amount\":\"800.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b9800.00 for \\\"Nasta\\\" in Varindavan.\"}', NULL, '2026-03-20 11:52:22', '2026-03-20 11:52:22'),
('e1b5ddf2-148c-40ad-93c6-24acbe78c3e9', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 4, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":19,\"expense_description\":\"Nasta\",\"expense_amount\":\"800.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b9800.00 for \\\"Nasta\\\" in Varindavan.\"}', NULL, '2026-03-20 11:52:25', '2026-03-20 11:52:25'),
('1ccea0fc-cfa2-4697-a31f-1ca92909edfa', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 5, '{\"type\":\"group_expense_added\",\"group_id\":5,\"group_name\":\"Varindavan\",\"expense_id\":19,\"expense_description\":\"Nasta\",\"expense_amount\":\"800.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b9800.00 for \\\"Nasta\\\" in Varindavan.\"}', NULL, '2026-03-20 11:52:28', '2026-03-20 11:52:28'),
('aa9c7351-041e-4fa7-8ed5-8f11c2ef517a', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 1, '{\"type\":\"added_to_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"added_by\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added you to \\\"Just Test\\\".\"}', '2026-03-23 13:45:17', '2026-03-23 13:41:37', '2026-03-23 13:45:17'),
('cffa2f7e-2274-4e8b-99a7-ae0ba2f03e23', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 1, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":20,\"expense_description\":\"Nasta\",\"expense_amount\":\"100.00\",\"payer_name\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added \\u20b9100.00 for \\\"Nasta\\\" in Just Test.\"}', '2026-03-23 13:45:17', '2026-03-23 13:42:00', '2026-03-23 13:45:17'),
('c93c1411-1d64-4619-b7c7-06261572f6d0', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 1, '{\"type\":\"settlement_requested\",\"group_id\":5,\"group_name\":\"Varindavan\",\"amount\":920,\"to_user_name\":\"Vikas Bansalaa\",\"message\":\"You need to pay \\u20b9920 to Vikas Bansalaa in Varindavan.\"}', '2026-03-23 13:45:17', '2026-03-23 13:42:02', '2026-03-23 13:45:17'),
('6be1949e-aba1-4663-95dd-526ab78f4214', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 4, '{\"type\":\"settlement_requested\",\"group_id\":5,\"group_name\":\"Varindavan\",\"amount\":920,\"to_user_name\":\"Vikas Bansalaa\",\"message\":\"You need to pay \\u20b9920 to Vikas Bansalaa in Varindavan.\"}', NULL, '2026-03-23 13:42:04', '2026-03-23 13:42:04'),
('a5f6c4de-8fd9-429c-b206-82d2f852a43f', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 5, '{\"type\":\"settlement_requested\",\"group_id\":5,\"group_name\":\"Varindavan\",\"amount\":740,\"to_user_name\":\"Vikas Bansalaa\",\"message\":\"You need to pay \\u20b9740 to Vikas Bansalaa in Varindavan.\"}', NULL, '2026-03-23 13:42:06', '2026-03-23 13:42:06'),
('db86c0a2-365a-4c40-a5a9-5a089da8980c', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 5, '{\"type\":\"settlement_requested\",\"group_id\":5,\"group_name\":\"Varindavan\",\"amount\":180,\"to_user_name\":\"Mohan Lal\",\"message\":\"You need to pay \\u20b9180 to Mohan Lal in Varindavan.\"}', NULL, '2026-03-23 13:42:08', '2026-03-23 13:42:08'),
('f982b0f9-bf5f-46e1-8f2b-06147bf111db', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 1, '{\"type\":\"settlement_requested\",\"group_id\":6,\"group_name\":\"Just Test\",\"amount\":50,\"to_user_name\":\"Vikas Bansalaa\",\"message\":\"You need to pay \\u20b950 to Vikas Bansalaa in Just Test.\"}', '2026-03-23 13:45:17', '2026-03-23 13:42:10', '2026-03-23 13:45:17'),
('81688d6c-efd2-4832-89fe-29a689b83aa5', 'App\\Notifications\\SettlementCompleted', 'App\\Models\\User', 3, '{\"type\":\"settlement_completed\",\"group_id\":6,\"group_name\":\"Just Test\",\"amount\":50,\"from_user_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar paid you \\u20b950 in Just Test.\"}', '2026-03-23 13:45:44', '2026-03-23 13:42:13', '2026-03-23 13:45:44'),
('e8c151e5-5671-4218-a62d-9a8432cde502', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":27,\"expense_description\":\"Watch moutkuwa\",\"expense_amount\":\"300.00\",\"payer_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar added \\u20b9300.00 for \\\"Watch moutkuwa\\\" in Just Test.\"}', '2026-03-23 13:45:44', '2026-03-23 13:42:15', '2026-03-23 13:45:44'),
('ae9eb2a2-0ba2-4561-93b0-2c5432c18b83', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 1, '{\"type\":\"settlement_requested\",\"group_id\":6,\"group_name\":\"Just Test\",\"amount\":150,\"to_user_name\":\"Vikas Bansalaa\",\"message\":\"You need to pay \\u20b9150 to Vikas Bansalaa in Just Test.\"}', '2026-03-23 13:45:17', '2026-03-23 13:42:17', '2026-03-23 13:45:17'),
('88517270-e23f-4464-a2fc-3d999f80d111', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 3, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1825,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91825 to Laddy in Goa Trip.\"}', '2026-03-23 13:45:44', '2026-03-23 13:42:19', '2026-03-23 13:45:44'),
('d6da7f6a-5184-40d4-93c4-b27567418cec', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":1050,\"to_user_name\":\"Laddy\",\"message\":\"You need to pay \\u20b91050 to Laddy in Goa Trip.\"}', NULL, '2026-03-23 13:42:21', '2026-03-23 13:42:21'),
('649f19c7-80d2-4bff-847e-03fb69c25fcb', 'App\\Notifications\\SettlementRequested', 'App\\Models\\User', 2, '{\"type\":\"settlement_requested\",\"group_id\":2,\"group_name\":\"Goa Trip\",\"amount\":375,\"to_user_name\":\"Jasvinder Kumar\",\"message\":\"You need to pay \\u20b9375 to Jasvinder Kumar in Goa Trip.\"}', NULL, '2026-03-23 13:42:23', '2026-03-23 13:42:23'),
('e947489c-9c6e-4b43-afd3-a40d02e7c0dc', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":32,\"expense_description\":\"Party\",\"expense_amount\":\"5300.00\",\"payer_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar added \\u20b95300.00 for \\\"Party\\\" in Just Test.\"}', '2026-03-23 13:45:44', '2026-03-23 13:42:25', '2026-03-23 13:45:44'),
('07cc43ac-ae63-4fba-a6dd-359bbc57b6da', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 3, '{\"type\":\"todo_assigned\",\"todo_id\":9,\"title\":\"Chain and mangalsutra size\",\"priority\":\"high\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: Chain and mangalsutra size\"}', '2026-03-23 13:45:44', '2026-03-23 13:42:27', '2026-03-23 13:45:44'),
('a78555d1-e0ed-48b0-9646-ca2372f2de7d', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 3, '{\"type\":\"todo_assigned\",\"todo_id\":11,\"title\":\"1 kg chini le aana aate hue.\",\"priority\":\"medium\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: 1 kg chini le aana aate hue.\"}', '2026-03-23 14:15:49', '2026-03-23 13:49:57', '2026-03-23 14:15:49'),
('14748850-f134-4389-8bdf-cd5881f7f7a5', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":33,\"expense_description\":\"Shyam bhajan\",\"expense_amount\":\"200.00\",\"payer_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar added \\u20b9200.00 for \\\"Shyam bhajan\\\" in Just Test.\"}', '2026-03-23 14:15:49', '2026-03-23 13:51:58', '2026-03-23 14:15:49'),
('3cab996e-f502-48a1-b80e-80ca944c00d2', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":34,\"expense_description\":\"Dinner in dhaba\",\"expense_amount\":\"1257.00\",\"payer_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar added \\u20b91257.00 for \\\"Dinner in dhaba\\\" in Just Test.\"}', '2026-03-23 14:15:49', '2026-03-23 14:13:39', '2026-03-23 14:15:49'),
('32282de7-28d2-4912-b9f3-d3319ec51bf9', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":35,\"expense_description\":\"petrol\",\"expense_amount\":\"935.00\",\"payer_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar added \\u20b9935.00 for \\\"petrol\\\" in Just Test.\"}', '2026-03-23 14:15:49', '2026-03-23 14:15:09', '2026-03-23 14:15:49'),
('e402d83b-a88d-4034-9a66-8592cc372fb6', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":36,\"expense_description\":\"coffee\",\"expense_amount\":\"215.00\",\"payer_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar added \\u20b9215.00 for \\\"coffee\\\" in Just Test.\"}', NULL, '2026-03-23 14:16:13', '2026-03-23 14:16:13'),
('ea2fb906-afa2-4e40-a71a-a475a8533668', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 3, '{\"type\":\"todo_assigned\",\"todo_id\":12,\"title\":\"chal re chhore 1 killo gud leya\",\"priority\":\"high\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: chal re chhore 1 killo gud leya\"}', NULL, '2026-03-23 14:24:17', '2026-03-23 14:24:17'),
('a547da00-e8bf-4046-8b67-ff63be269801', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 3, '{\"type\":\"todo_assigned\",\"todo_id\":13,\"title\":\"re shakka bhi le aana\",\"priority\":\"high\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: re shakka bhi le aana\"}', NULL, '2026-03-23 14:57:30', '2026-03-23 14:57:30'),
('3834edbc-ae08-4c6f-bfcf-4516a3be312c', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 3, '{\"type\":\"todo_assigned\",\"todo_id\":14,\"title\":\"Tayar ke pata kar liye sharma tyres par jake kal lene hain.\",\"priority\":\"high\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: Tayar ke pata kar liye sharma tyres par jake kal lene hain.\"}', NULL, '2026-03-23 15:20:47', '2026-03-23 15:20:47'),
('639d616c-d1c9-498e-b3cd-846c8432152f', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 5, '{\"type\":\"todo_assigned\",\"todo_id\":15,\"title\":\"kunda lagana kalu\",\"priority\":\"medium\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: kunda lagana kalu\"}', NULL, '2026-03-23 15:51:50', '2026-03-23 15:51:50'),
('e6660695-743a-4bf1-b610-498614147952', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 5, '{\"type\":\"todo_assigned\",\"todo_id\":16,\"title\":\"Hello Laddy yaar kuchh samaan leke aana hai aane se pehle phone kariyo mujhe\",\"priority\":\"medium\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: Hello Laddy yaar kuchh samaan leke aana hai aane se pehle phone kariyo mujhe\"}', NULL, '2026-03-23 15:57:44', '2026-03-23 15:57:44'),
('d2427b05-763d-460e-9f43-c5945973cf58', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 5, '{\"type\":\"todo_assigned\",\"todo_id\":17,\"title\":\"Hi laddy\",\"priority\":\"medium\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: Hi laddy\"}', NULL, '2026-03-23 16:00:35', '2026-03-23 16:00:35'),
('bd5d9f59-7aab-4dcd-bf69-461c91158f35', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 3, '{\"type\":\"todo_assigned\",\"todo_id\":18,\"title\":\"Hi vikas\",\"priority\":\"high\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: Hi vikas\"}', NULL, '2026-03-23 16:00:51', '2026-03-23 16:00:51'),
('9849f97c-47b3-4c26-a99b-68322e9f8cee', 'App\\Notifications\\TodoAssigned', 'App\\Models\\User', 5, '{\"type\":\"todo_assigned\",\"todo_id\":19,\"title\":\"hi\",\"priority\":\"high\",\"assigned_by\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar assigned you: hi\"}', NULL, '2026-03-23 16:05:20', '2026-03-23 16:05:20'),
('66295ebd-8370-43e4-b40f-8e9bebe564be', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 5, '{\"type\":\"added_to_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"added_by\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added you to \\\"Just Test\\\".\"}', NULL, '2026-03-23 16:06:23', '2026-03-23 16:06:23'),
('ac6bd736-be87-40ec-9896-40432961903f', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 3, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":37,\"expense_description\":\"Disprin\",\"expense_amount\":\"67.00\",\"payer_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar added \\u20b967.00 for \\\"Disprin\\\" in Just Test.\"}', NULL, '2026-03-23 16:08:24', '2026-03-23 16:08:24'),
('a6054818-26ef-4cfd-b99e-c38e332d022f', 'App\\Notifications\\GroupExpenseAdded', 'App\\Models\\User', 5, '{\"type\":\"group_expense_added\",\"group_id\":6,\"group_name\":\"Just Test\",\"expense_id\":37,\"expense_description\":\"Disprin\",\"expense_amount\":\"67.00\",\"payer_name\":\"Jasvinder Kumar\",\"message\":\"Jasvinder Kumar added \\u20b967.00 for \\\"Disprin\\\" in Just Test.\"}', NULL, '2026-03-23 16:08:26', '2026-03-23 16:08:26'),
('c25fee13-9c13-4ed0-b218-8345bc8467d5', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 5, '{\"type\":\"added_to_group\",\"group_id\":1,\"group_name\":\"Khattu Shyam\",\"added_by\":\"Test Admin\",\"message\":\"Test Admin added you to \\\"Khattu Shyam\\\".\"}', NULL, '2026-03-23 16:20:10', '2026-03-23 16:20:10'),
('6197f5dd-c993-4011-90d7-4676b6a7d595', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 5, '{\"type\":\"added_to_group\",\"group_id\":1,\"group_name\":\"Khattu Shyam\",\"added_by\":\"Test Admin\",\"message\":\"Test Admin added you to \\\"Khattu Shyam\\\".\"}', NULL, '2026-03-23 16:20:51', '2026-03-23 16:20:51'),
('b85fd9d9-b33f-4e1a-a40f-1d397a28b1c6', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 5, '{\"type\":\"added_to_group\",\"group_id\":1,\"group_name\":\"Khattu Shyam\",\"added_by\":\"Debug Test\",\"message\":\"Debug Test added you to \\\"Khattu Shyam\\\".\"}', NULL, '2026-03-23 16:21:39', '2026-03-23 16:21:39'),
('a37133b2-d568-4434-abf9-d5ee91cb216a', 'App\\Notifications\\RemovedFromGroup', 'App\\Models\\User', 5, '{\"type\":\"removed_from_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"removed_by\":\"Vikas Bansalaa\",\"deactivated\":true,\"message\":\"Vikas Bansalaa deactivated from \\\"Just Test\\\".\"}', NULL, '2026-03-23 16:22:34', '2026-03-23 16:22:34'),
('8c785275-2539-48d2-be9b-7ec81647d7f7', 'App\\Notifications\\AddedToGroup', 'App\\Models\\User', 5, '{\"type\":\"added_to_group\",\"group_id\":7,\"group_name\":\"yoyo\",\"added_by\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa added you to \\\"yoyo\\\".\"}', NULL, '2026-03-23 16:23:03', '2026-03-23 16:23:03'),
('20e5df40-672b-4d33-9405-20025bd3ee2c', 'App\\Notifications\\RemovedFromGroup', 'App\\Models\\User', 5, '{\"type\":\"removed_from_group\",\"group_id\":7,\"group_name\":\"yoyo\",\"removed_by\":\"Vikas Bansalaa\",\"deactivated\":false,\"message\":\"Vikas Bansalaa removed from \\\"yoyo\\\".\"}', NULL, '2026-03-23 16:23:13', '2026-03-23 16:23:13'),
('af35d36b-b164-4fef-8adf-74ddf876260f', 'App\\Notifications\\RemovedFromGroup', 'App\\Models\\User', 5, '{\"type\":\"removed_from_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"removed_by\":\"Vikas Bansalaa\",\"deactivated\":true,\"message\":\"Vikas Bansalaa deactivated from \\\"Just Test\\\".\"}', NULL, '2026-03-23 16:27:03', '2026-03-23 16:27:03'),
('c1918ce6-24ed-4e2a-9421-7d89da4ed134', 'App\\Notifications\\ReactivatedInGroup', 'App\\Models\\User', 5, '{\"type\":\"reactivated_in_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"reactivated_by\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa reactivated you in \\\"Just Test\\\".\"}', NULL, '2026-03-23 16:27:25', '2026-03-23 16:27:25'),
('6c8391a7-4c81-4ac1-bc85-6302d2c82166', 'App\\Notifications\\RemovedFromGroup', 'App\\Models\\User', 5, '{\"type\":\"removed_from_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"removed_by\":\"Vikas Bansalaa\",\"deactivated\":true,\"message\":\"Vikas Bansalaa deactivated from \\\"Just Test\\\".\"}', NULL, '2026-03-23 16:28:59', '2026-03-23 16:28:59'),
('bed06ef6-bbc2-4d43-aa29-2803570e2f27', 'App\\Notifications\\ReactivatedInGroup', 'App\\Models\\User', 5, '{\"type\":\"reactivated_in_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"reactivated_by\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa reactivated you in \\\"Just Test\\\".\"}', NULL, '2026-03-23 16:29:09', '2026-03-23 16:29:09'),
('08bb96fc-703c-412a-ac20-53d6e518dfa6', 'App\\Notifications\\RemovedFromGroup', 'App\\Models\\User', 5, '{\"type\":\"removed_from_group\",\"group_id\":1,\"group_name\":\"Khattu Shyam\",\"removed_by\":\"Admin\",\"deactivated\":true,\"message\":\"Admin deactivated from \\\"Khattu Shyam\\\".\"}', NULL, '2026-03-23 16:30:37', '2026-03-23 16:30:37'),
('fc831b0a-5e47-4c4d-afe5-80d71eb2843b', 'App\\Notifications\\RemovedFromGroup', 'App\\Models\\User', 5, '{\"type\":\"removed_from_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"removed_by\":\"Vikas Bansalaa\",\"deactivated\":true,\"message\":\"Vikas Bansalaa deactivated from \\\"Just Test\\\".\"}', NULL, '2026-03-23 16:34:36', '2026-03-23 16:34:36'),
('b596697a-f3c8-467e-9506-7aed06f75122', 'App\\Notifications\\ReactivatedInGroup', 'App\\Models\\User', 5, '{\"type\":\"reactivated_in_group\",\"group_id\":6,\"group_name\":\"Just Test\",\"reactivated_by\":\"Vikas Bansalaa\",\"message\":\"Vikas Bansalaa reactivated you in \\\"Just Test\\\".\"}', NULL, '2026-03-23 16:34:53', '2026-03-23 16:34:53');

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
CREATE TABLE IF NOT EXISTS `otp_codes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_phone_code_index` (`phone`,`code`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `phone`, `code`, `expires_at`, `used`, `created_at`, `updated_at`) VALUES
(1, '9416716775', '424550', '2026-03-09 07:18:19', 1, '2026-03-09 07:13:19', '2026-03-09 07:13:34'),
(2, '9416716775', '251868', '2026-03-09 23:28:35', 1, '2026-03-09 23:23:35', '2026-03-09 23:23:41'),
(3, '9416716775', '415312', '2026-03-09 23:54:07', 1, '2026-03-09 23:49:07', '2026-03-09 23:49:10'),
(4, '9416716775', '818689', '2026-03-09 23:54:10', 1, '2026-03-09 23:49:10', '2026-03-09 23:49:14'),
(5, '7015457850', '639767', '2026-03-10 08:39:31', 1, '2026-03-10 08:34:31', '2026-03-10 08:34:37'),
(6, '7015457850', '110338', '2026-03-11 01:10:40', 1, '2026-03-11 01:05:40', '2026-03-11 01:05:46'),
(7, '9000000003', '760513', '2026-03-11 01:12:14', 1, '2026-03-11 01:07:14', '2026-03-11 01:07:19'),
(8, '9416716775', '002541', '2026-03-11 09:11:05', 1, '2026-03-11 09:06:05', '2026-03-11 09:06:16'),
(9, '7015457850', '311264', '2026-03-11 09:12:44', 1, '2026-03-11 09:07:44', '2026-03-11 09:07:51'),
(10, '7015457850', '545497', '2026-03-16 06:00:13', 1, '2026-03-16 05:55:13', '2026-03-16 05:55:17'),
(11, '7015457850', '119724', '2026-03-16 06:09:26', 1, '2026-03-16 06:04:26', '2026-03-16 06:04:30'),
(12, '7015457850', '663963', '2026-03-16 06:25:27', 1, '2026-03-16 06:20:27', '2026-03-16 06:22:24'),
(13, '9000000001', '806750', '2026-03-17 07:40:40', 1, '2026-03-17 07:35:40', '2026-03-17 07:35:43'),
(14, '9000000002', '989378', '2026-03-17 07:48:12', 1, '2026-03-17 07:43:12', '2026-03-17 07:43:17'),
(15, '9000000003', '994191', '2026-03-18 01:03:13', 1, '2026-03-18 00:58:13', '2026-03-18 00:58:22'),
(16, '9000000001', '271194', '2026-03-18 02:30:32', 1, '2026-03-18 02:25:32', '2026-03-18 02:25:58'),
(17, '9000000001', '624973', '2026-03-18 02:38:08', 1, '2026-03-18 02:33:08', '2026-03-18 02:33:18'),
(18, '9000000002', '722365', '2026-03-18 09:24:25', 1, '2026-03-18 09:19:25', '2026-03-18 09:19:29'),
(19, '9000000001', '641577', '2026-03-18 12:08:05', 1, '2026-03-18 12:03:05', '2026-03-18 12:03:10'),
(20, '7015457850', '353099', '2026-03-18 12:35:35', 1, '2026-03-18 12:30:35', '2026-03-18 12:30:39'),
(21, '7015457850', '765238', '2026-03-19 13:49:22', 1, '2026-03-19 13:44:22', '2026-03-19 13:44:36'),
(22, '9000000001', '028224', '2026-03-19 13:55:02', 1, '2026-03-19 13:50:02', '2026-03-19 13:50:06'),
(23, '9000000003', '384018', '2026-03-19 14:52:07', 1, '2026-03-19 14:47:07', '2026-03-19 14:47:57'),
(24, '7015457850', '010551', '2026-03-20 04:25:29', 0, '2026-03-20 04:20:29', '2026-03-20 04:20:29'),
(25, '9000000001', '525948', '2026-03-21 14:41:09', 1, '2026-03-21 14:36:09', '2026-03-21 14:36:21'),
(26, '9000000002', '248962', '2026-03-21 14:53:37', 1, '2026-03-21 14:48:37', '2026-03-21 14:48:53'),
(27, '9000000001', '645892', '2026-03-23 05:30:41', 1, '2026-03-23 05:25:41', '2026-03-23 05:25:53'),
(28, '9000000001', '351310', '2026-03-23 08:00:16', 1, '2026-03-23 07:55:16', '2026-03-23 07:55:40'),
(29, '9000000001', '237732', '2026-03-23 10:07:37', 1, '2026-03-23 10:02:37', '2026-03-23 10:02:44'),
(30, '9000000001', '199398', '2026-03-23 14:26:53', 1, '2026-03-23 14:21:53', '2026-03-23 14:22:00'),
(31, '9000000001', '673765', '2026-03-23 15:01:08', 1, '2026-03-23 14:56:08', '2026-03-23 14:56:20'),
(32, '9000000001', '865570', '2026-03-23 15:18:56', 1, '2026-03-23 15:13:56', '2026-03-23 15:14:04'),
(33, '9000000003', '604953', '2026-03-23 15:55:40', 1, '2026-03-23 15:50:40', '2026-03-23 15:50:49');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'about', '<h2>About JodTod</h2><p>JodTod is a smart expense tracker and splitter app that helps you manage personal expenses and split group expenses with friends, family, and roommates.</p><h3>Our Mission</h3><p>We believe managing money with friends shouldn\'t be awkward. JodTod makes it simple to track who owes what, settle up fairly, and keep friendships strong.</p><h3>What We Offer</h3><ul><li><p><strong>Personal Expense Tracking</strong> - Track your daily spending with categories and charts</p></li><li><p><strong>Group Expense Splitting</strong> - Split bills equally, by custom amounts, or by percentage</p></li><li><p><strong>Smart Settlement</strong> - Our algorithm minimizes the number of transactions needed</p></li><li><p><strong>Real-time Notifications</strong> - Stay updated on group activities</p></li></ul><p></p>', 'About JodTod - Expense Tracker & Splitter', 'Learn about JodTod, a smart expense tracker and splitter app for managing personal and group expenses easily.', 1, '2026-03-10 06:08:20', '2026-03-10 08:32:26'),
(2, 'Contact Us', 'contact', '<h2>Get in Touch</h2><p>Have questions, feedback, or need help? We\'d love to hear from you!</p><h3>Email</h3><p>support@jodtod.com</p><h3>Social Media</h3><p>Follow us on social media for updates and tips:</p><ul><li>Twitter: @jodtod</li><li>Instagram: @jodtod.app</li></ul><h3>Business Inquiries</h3><p>For partnerships and business inquiries, please email: hello@jodtod.com</p>', 'Contact JodTod - Get in Touch', 'Contact the JodTod team for support, feedback, or business inquiries.', 1, '2026-03-10 06:08:20', '2026-03-10 06:08:20'),
(3, 'Privacy Policy', 'privacy', '<h2>Privacy Policy</h2><p><strong>Last updated:</strong> March 2026</p><h3>Information We Collect</h3><p>We collect information you provide directly, including your name, email, phone number, and expense data.</p><h3>How We Use Your Information</h3><ul><li>To provide and maintain our service</li><li>To notify you about changes to our service</li><li>To provide customer support</li><li>To gather analysis so we can improve our service</li></ul><h3>Data Security</h3><p>We implement appropriate security measures to protect your personal information. Your data is encrypted in transit and at rest.</p><h3>Contact Us</h3><p>If you have questions about this Privacy Policy, please contact us at support@jodtod.com.</p>', 'Privacy Policy - JodTod', 'Read JodTod\'s privacy policy to understand how we collect, use, and protect your data.', 1, '2026-03-10 06:08:20', '2026-03-10 06:08:20'),
(4, 'Terms of Service', 'terms', '<h2>Terms of Service</h2><p><strong>Last updated:</strong> March 2026</p><h3>Acceptance of Terms</h3><p>By accessing or using JodTod, you agree to be bound by these Terms of Service.</p><h3>Use of Service</h3><p>You may use JodTod for lawful purposes only. You are responsible for maintaining the security of your account.</p><h3>User Content</h3><p>You retain ownership of all data you submit. We do not sell your personal data to third parties.</p><h3>Limitation of Liability</h3><p>JodTod is provided \"as is\" without warranties. We are not liable for any financial decisions made based on the app\'s calculations.</p><h3>Changes to Terms</h3><p>We may update these terms from time to time. Continued use of the service constitutes acceptance of the updated terms.</p>', 'Terms of Service - JodTod', 'Read the Terms of Service for using JodTod expense tracker and splitter app.', 1, '2026-03-10 06:08:20', '2026-03-10 06:08:20'),
(5, 'Features', 'features', '<h2>Features</h2><h3>Personal Expense Tracking</h3><p>Track every rupee with categories, date filters, and visual charts. Know exactly where your money goes each month.</p><h3>Group Expense Splitting</h3><p>Split bills with friends using equal, custom, or percentage splits. Perfect for trips, roommates, and dining out.</p><h3>Smart Settlement</h3><p>Our algorithm calculates the minimum number of transactions needed to settle all debts. No more confusing IOUs.</p><h3>Real-time Notifications</h3><p>Get notified instantly when expenses are added, settlements are requested, or you\'re added to a group.</p><h3>Dashboard Overview</h3><p>See your financial summary at a glance — monthly spending, group balances, and recent activity all in one place.</p><h3>Works Everywhere</h3><p>JodTod is a Progressive Web App. Install it on your phone, tablet, or use it on desktop. Works offline too.</p>', 'Features - JodTod Expense Tracker', 'Explore JodTod features: personal expense tracking, group splitting, smart settlements, notifications, and more.', 1, '2026-03-10 06:08:20', '2026-03-10 06:08:20');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('radhe@yopmail.com', '$2y$12$BSSbSXQZhpY8RcHs2gYak.9Ui1SCmmbddMM4fZoOw.hzgVCkvvZNy', '2026-03-18 00:58:59'),
('mohan@example.com', '$2y$12$JQRiil5nUeSf52vx1rKi/OlzQ1TEi1zpIfv9wQMcbYbQM9LbH2P/q', '2026-03-18 01:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(5, 'App\\Models\\User', 12, 'I2407', '4be10d17f30b3c28400077ab2ff4353e170c3c0961996ffa572e395f8f3b2edc', '[\"*\"]', NULL, NULL, '2026-03-18 01:27:03', '2026-03-18 01:27:03'),
(6, 'App\\Models\\User', 14, 'I2407', '2873cd34182d955b2ade58b972aff59a01ac70ba985c3535ee062f12410d9047', '[\"*\"]', '2026-03-18 02:03:37', NULL, '2026-03-18 02:02:58', '2026-03-18 02:03:37'),
(13, 'App\\Models\\User', 5, 'I2407', 'faca7f908196afaf575dbbad75544784923290421fa1fff74f2742193256907c', '[\"*\"]', '2026-03-19 15:08:36', NULL, '2026-03-19 14:47:57', '2026-03-19 15:08:36'),
(14, 'App\\Models\\User', 1, 'I2407', 'f117c12dc8a30b163704e2fc891ed8c3e32cd3c1a543f4ad5f09986181f9dbfe', '[\"*\"]', '2026-03-21 10:22:33', NULL, '2026-03-20 04:20:54', '2026-03-21 10:22:33'),
(16, 'App\\Models\\User', 4, 'I2407', '8c620fd02ac3b9c1531d9eeef9f1a70ba7b8df91569ca060e00939d2dbb61020', '[\"*\"]', '2026-03-21 17:04:54', NULL, '2026-03-21 14:48:53', '2026-03-21 17:04:54'),
(24, 'App\\Models\\User', 1, 'I2407', 'da1af9cc1ad28c499bc981208370dff9927fdae7debd8a40baefc2ddf62e7888', '[\"*\"]', '2026-04-01 14:08:44', NULL, '2026-04-01 14:08:30', '2026-04-01 14:08:44'),
(18, 'App\\Models\\User', 3, 'I2407', 'a91e55f8e8f92e5cc38c09cd0b07f4204a1b1a21ac4d157e0ac818910f0bb7cb', '[\"*\"]', '2026-03-23 14:18:17', NULL, '2026-03-23 10:02:44', '2026-03-23 14:18:17'),
(19, 'App\\Models\\User', 3, 'I2407', '0a5c193324c435f0804994ff006b8fdc91021b5b66c253dd7fb3c20fd2540187', '[\"*\"]', '2026-03-23 14:25:18', NULL, '2026-03-23 14:22:00', '2026-03-23 14:25:18'),
(20, 'App\\Models\\User', 3, 'I2407', 'e29e208145b51ba8ba2fb3b678e4d53324a4c4362a49364576dcc741970bd1c8', '[\"*\"]', '2026-03-23 14:56:26', NULL, '2026-03-23 14:56:20', '2026-03-23 14:56:26'),
(21, 'App\\Models\\User', 3, 'I2407', 'fdec65bf5bc23a40d01842ccc1a29149bee29cb653c37f305e88abd633eff88c', '[\"*\"]', '2026-03-23 15:21:31', NULL, '2026-03-23 15:14:04', '2026-03-23 15:21:31'),
(22, 'App\\Models\\User', 5, 'I2407', 'd2ce99b844b4b49bcc5f779b7743c26dd0ec3006f2c5c4f4d2ab340fc959ab03', '[\"*\"]', '2026-03-23 16:28:31', NULL, '2026-03-23 15:50:49', '2026-03-23 16:28:31'),
(27, 'App\\Models\\User', 1, 'I2407', '914e211be43ebeccb934964d3cc58f68502a15053747cb7c290d95ef608ab336', '[\"*\"]', '2026-04-02 01:00:27', NULL, '2026-04-02 01:00:26', '2026-04-02 01:00:27'),
(28, 'App\\Models\\User', 1, 'I2407', '3d098ef80ac4fb51eae488b26aac9ad45931b41a1c3934239a9700b1e09d55bc', '[\"*\"]', '2026-04-05 06:28:46', NULL, '2026-04-03 14:56:31', '2026-04-05 06:28:46'),
(30, 'App\\Models\\User', 19, 'SM-S908E', '57353e210dc316365ab1ac148eb73d44b76dd2887d9e9ceab2f1553ea0816ecd', '[\"*\"]', '2026-04-03 15:11:18', NULL, '2026-04-03 15:09:45', '2026-04-03 15:11:18'),
(31, 'App\\Models\\User', 28, 'SM-A346E', 'eb2e8a36591e379e2d7367a002f40bf0b92d70780553acec50098b2fa22e7b6b', '[\"*\"]', '2026-04-05 06:52:42', NULL, '2026-04-05 06:26:53', '2026-04-05 06:52:42'),
(32, 'App\\Models\\User', 29, 'I2220', 'df952e52c84ef3815d08fd341d76e07be05bc0d8dfed21b0b8447b278db600e2', '[\"*\"]', '2026-04-05 07:07:08', NULL, '2026-04-05 06:58:38', '2026-04-05 07:07:08'),
(33, 'App\\Models\\User', 1, 'I2407', '92c88d9a12b28132082fab132b87158344a2c630d7b60b9670f0202f28bf026a', '[\"*\"]', '2026-04-10 06:32:12', NULL, '2026-04-05 09:37:05', '2026-04-10 06:32:12'),
(35, 'App\\Models\\User', 28, 'SM-A346E', '4673368f38db841fffa90cb811fc815de80ab8452ed29409759a6e4719e87362', '[\"*\"]', '2026-04-10 17:37:56', NULL, '2026-04-10 17:36:59', '2026-04-10 17:37:56'),
(36, 'App\\Models\\User', 28, 'SM-A346E', 'ea4ec08d4a6e19e8543e85985ba567a034e379b180489e7dec3d48f5e431bcb9', '[\"*\"]', '2026-04-15 09:13:41', NULL, '2026-04-15 07:27:22', '2026-04-15 09:13:41');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Obo0noce3NGjqX3I1LDqkSGmceebAQusE92fiti5', NULL, '205.210.31.94', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNHZMUWVRdTFJeUlYRUgzUU5ocmhNVW10OWhBZGU2bGFDMjhVdk1CTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775640733),
('ZwXerFcvgspJ8k19mjuhw1UfQ7WpJTKTPyRWtPJs', NULL, '35.197.26.59', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSTYxTGY4UXM1S0tpU2c4Z1JWa1Z0bzZodU1nUmgwQ3FXQVpVT2RhWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9qb2R0b2QuY28uaW4iO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775644007),
('Ah4kVNahX7GiOsfQplG7ZbgXMSfJRHMAqMcfFIWa', NULL, '2a10:4640:0:fc72::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWldnTXNvdzNiOGFNV294MHI1MFZta2gyaUFLNmVPYkVFVGJ5bFRyYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776072515),
('SuMOnLWJ6dxFBcPiaxstz7wf3qQlkGvaXYUPkUo8', NULL, '103.108.58.16', 'FaviconHash-API/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXBYR2EyaHpuSVdIdDd6clNCcFROcGpWb2o0bjdRWnZQVXFERk43aiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776095559),
('XEurUQ44eALUFG76ik4jdHkPrbJAQg5rL2rPJ6TQ', NULL, '45.148.10.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSmFFUEFGTkk0cEh6dDE0RmF0SE05R0FubGxKTHJGWlpxelFsc3JJYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775804368),
('orzyN0jAtIvluL4tgOKXKedhH2SS6wgm3JtPsTmk', NULL, '103.108.58.16', 'FaviconHash-API/1.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWhQaEVhVFBad2FkYmRtWm1nSEtTZVNMZExjVXYxcUxha29ZajBSaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776064455),
('D5hyzbXHAZwfOeFFxrO1gSVlKu0w85AoomQTPRQG', NULL, '185.213.174.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzlYM05QSG1TRWcyR2dPZXNieVNVNU5LSkVielhvN0h0SEh6WjkwVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9qb2R0b2QuY28uaW4iO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776243432),
('z3SreCua3tu9wzw1LCr0IPiIUWhUYI2ggeTKcECp', NULL, '2400:6180:100:d0::9857:9001', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTWxlQWlJN3J1UVZjSlkyaDBncGZZcXdoZ0lPWWdVeXhWcFBxU3ZxTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9qb2R0b2QuY28uaW4iO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776241716),
('rlVAUYhMQUspntivtF5XENCY4qaKsSMgGfWtNKP9', NULL, '66.249.64.225', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.7680.177 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia0dIMUZERFNqYk56cmpsOE45V1dIdkdhbFA3Wk04aldJOFNjdWFkZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775648800),
('pTqSCu0gDycAYMZ5WF9xtssLM0WmDWfeZDkmTftP', NULL, '66.249.64.234', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiamtndEswWEVKTGFUQ3AxY0xGUHZKYTZhYmc2Y281V1ZKZEZNOERpWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775648815),
('5ApWeIZFedvY956ltnHlkIFgbml82F5xywkhBNNQ', NULL, '66.249.64.234', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibG1DcUR5am9waVZFYTlyNlllY3FDcndCd3p4ZDNSTll1Wk44eUx4UCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775648816),
('w6GtrphQVYcTucUV376SqEKE3Q7csBgYftbZxeyw', NULL, '147.185.132.60', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWJyUXpBWlJFdEJweUpCdnhqYkhaeEx3WklUbjJtR2JERzhMNjF4SyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775655608),
('LbSk6u6Z1u4zHE5Gc2QzYegTDjI8rDp04OQBXtni', NULL, '34.122.236.155', 'Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTHZrdVJaRVJmVGt2VjVIOXFjUDNLUWw2bU9EN0xuUXl3V3JWbmxUdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9qb2R0b2QuY28uaW4iO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775662117),
('V33JynD7vadxzxa7AivpWZxlUeF0EDiUYdV84Sma', NULL, '198.235.24.33', 'Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialdiMlRkWVpKZVFHWlUzSmZxdWttUDhLT0UxMlE0VjJxeFFPSDlyZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775718599),
('0ZyATuFeqJSmrzj7O3cG2Ens4j3P0yI5mvLIapVR', NULL, '2409:40d0:1011:8ea1:75d1:85ae:f2fe:75ef', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZUJQTW14RnBCQzF2WDdkTkExSkxrU01tNEUwcHVsN3lYWVE1Rk5sRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775756398),
('QjqOhyUzE7bIuoBMmGgwGh4CJ8w4sDBYaStjQpi9', NULL, '2405:9800:b661:a2ca:51c0:5e46:d5fe:13e6', 'Python/3.14 aiohttp/3.13.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicm9Pb1lhQXM3bWNlYnRoRWt3SkJJSW1qazBoVDRZcGtlbHZ0YnVqQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775801995),
('WESSSFz0S2IrjR8LjrUj0RPejtZeddGlsEGbs8vo', NULL, '54.180.24.19', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQTY4MzRQMWt5SVNPNm9HbWNmMUNrUmtmcU9WUFlsb3RWQ2ZsNVhmayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHBzOi8vam9kdG9kLmNvLmluIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1775646475),
('wbFx821tsdPGnAijajRra5P2OXAxiVfwXQxPPAnl', NULL, '51.39.130.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidGk2aENzc0QwVm8yZUxKazVaNnd5aWlGNFczZ1Y0WjB3WEZCTmg0RiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTk6Imh0dHA6Ly9qb2R0b2QuY28uaW4iO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775647750);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`) VALUES
('site_name', 'JodTod'),
('default_currency', 'INR'),
('maintenance_mode', '0');

-- --------------------------------------------------------

--
-- Table structure for table `settlements`
--

DROP TABLE IF EXISTS `settlements`;
CREATE TABLE IF NOT EXISTS `settlements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` bigint UNSIGNED NOT NULL,
  `from_user` bigint UNSIGNED NOT NULL,
  `to_user` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settlements_group_id_index` (`group_id`),
  KEY `settlements_from_user_index` (`from_user`),
  KEY `settlements_to_user_index` (`to_user`),
  KEY `settlements_status_index` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settlements`
--

INSERT INTO `settlements` (`id`, `group_id`, `from_user`, `to_user`, `amount`, `status`, `note`, `settled_at`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 3, 920.00, 'completed', NULL, '2026-03-20 13:06:43', '2026-03-20 13:05:41', '2026-03-20 13:06:43'),
(2, 5, 4, 3, 920.00, 'completed', NULL, '2026-03-20 13:06:43', '2026-03-20 13:05:41', '2026-03-20 13:06:43'),
(3, 5, 5, 3, 740.00, 'completed', NULL, '2026-03-20 13:06:43', '2026-03-20 13:05:41', '2026-03-20 13:06:43'),
(4, 5, 5, 2, 180.00, 'completed', NULL, '2026-03-20 13:06:43', '2026-03-20 13:05:41', '2026-03-20 13:06:43'),
(5, 6, 1, 3, 50.00, 'completed', NULL, '2026-03-20 13:40:19', '2026-03-20 13:09:27', '2026-03-20 13:40:19'),
(6, 6, 1, 3, 150.00, 'completed', NULL, '2026-03-20 13:55:34', '2026-03-20 13:55:20', '2026-03-20 13:55:34'),
(7, 2, 3, 5, 1825.00, 'completed', NULL, '2026-03-21 07:16:19', '2026-03-21 07:16:10', '2026-03-21 07:16:19'),
(8, 2, 2, 5, 1050.00, 'completed', NULL, '2026-03-21 07:16:19', '2026-03-21 07:16:11', '2026-03-21 07:16:19'),
(9, 2, 2, 1, 375.00, 'completed', NULL, '2026-03-21 07:16:19', '2026-03-21 07:16:11', '2026-03-21 07:16:19');

-- --------------------------------------------------------

--
-- Table structure for table `todos`
--

DROP TABLE IF EXISTS `todos`;
CREATE TABLE IF NOT EXISTS `todos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `todo_category_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `reminder_at` datetime DEFAULT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `todos_user_id_is_completed_index` (`user_id`,`is_completed`),
  KEY `todos_due_date_index` (`due_date`),
  KEY `todos_reminder_at_index` (`reminder_at`),
  KEY `todos_todo_category_id_foreign` (`todo_category_id`),
  KEY `todos_assigned_to_foreign` (`assigned_to`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `todos`
--

INSERT INTO `todos` (`id`, `user_id`, `assigned_to`, `todo_category_id`, `title`, `priority`, `due_date`, `reminder_at`, `reminder_sent`, `is_completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, NULL, 'Go to mansa devi', 'high', '2026-03-20', NULL, 0, 0, NULL, '2026-03-17 06:32:29', '2026-03-18 12:11:02'),
(2, 4, NULL, NULL, 'Fromo sabzi mandi, nibu, tomato, papaya, tarbooz', 'medium', NULL, NULL, 0, 1, '2026-03-21 15:30:16', '2026-03-17 07:30:10', '2026-03-21 15:30:16'),
(3, 3, NULL, NULL, 'Car service', 'medium', '2026-03-23', NULL, 0, 0, NULL, '2026-03-18 00:12:27', '2026-03-23 06:28:43'),
(4, 4, 3, 1, 'gold shopping', 'high', '2026-03-21', NULL, 0, 0, NULL, '2026-03-18 10:30:44', '2026-03-18 12:11:09'),
(5, 4, NULL, 1, 'Cloths shopping', 'medium', '2026-03-23', NULL, 0, 0, NULL, '2026-03-18 10:31:42', '2026-03-18 12:11:03'),
(6, 4, NULL, 2, 'Revenue booking', 'medium', NULL, '2026-03-18 10:33:00', 0, 0, NULL, '2026-03-18 10:32:28', '2026-03-18 12:11:04'),
(7, 4, NULL, NULL, 'purchase shoes', 'low', NULL, NULL, 0, 0, NULL, '2026-03-18 12:13:26', '2026-03-18 12:13:26'),
(8, 3, 1, NULL, 'Need to take medicine', 'high', '2026-03-23', NULL, 0, 0, NULL, '2026-03-19 13:51:09', '2026-03-23 06:30:11'),
(9, 1, 3, NULL, 'Chain and mangalsutra size', 'high', '2026-03-24', NULL, 0, 0, NULL, '2026-03-21 09:57:53', '2026-03-23 10:04:08'),
(10, 1, NULL, NULL, 'Under garments shop', 'medium', NULL, NULL, 0, 0, NULL, '2026-03-21 10:09:37', '2026-03-21 10:19:46'),
(11, 1, 3, NULL, '1 kg chini le aana aate hue.', 'medium', '2026-03-23', '2026-03-23 08:00:00', 0, 0, NULL, '2026-03-23 13:49:56', '2026-03-23 13:49:56'),
(12, 1, 3, NULL, 'chal re chhore 1 killo gud leya', 'high', '2026-03-24', NULL, 0, 0, NULL, '2026-03-23 14:24:16', '2026-03-23 14:24:16'),
(13, 1, 3, NULL, 're shakka bhi le aana', 'high', '2026-03-24', NULL, 0, 0, NULL, '2026-03-23 14:57:28', '2026-03-23 14:57:28'),
(14, 1, 3, NULL, 'Tayar ke pata kar liye sharma tyres par jake kal lene hain.', 'high', '2026-03-24', NULL, 0, 0, NULL, '2026-03-23 15:20:44', '2026-03-23 15:20:44'),
(15, 1, 5, NULL, 'kunda lagana kalu', 'medium', '2026-03-24', NULL, 0, 0, NULL, '2026-03-23 15:51:47', '2026-03-23 15:51:47'),
(16, 1, 5, NULL, 'Hello Laddy yaar kuchh samaan leke aana hai aane se pehle phone kariyo mujhe', 'medium', '2026-03-24', NULL, 0, 0, NULL, '2026-03-23 15:57:43', '2026-03-23 15:57:43'),
(17, 1, 5, NULL, 'Hi laddy', 'medium', '2026-03-23', NULL, 0, 0, NULL, '2026-03-23 16:00:34', '2026-03-23 16:00:34'),
(18, 1, 3, NULL, 'Hi vikas', 'high', '2026-03-24', NULL, 0, 0, NULL, '2026-03-23 16:00:50', '2026-03-23 16:00:50'),
(19, 1, 5, NULL, 'hi', 'high', '2026-03-22', NULL, 0, 0, NULL, '2026-03-23 16:05:10', '2026-03-23 16:05:18'),
(20, 28, NULL, NULL, 'Get 100000 earning in a month', 'medium', '2026-12-01', NULL, 0, 0, NULL, '2026-04-10 05:46:36', '2026-04-15 07:28:06'),
(21, 28, NULL, NULL, 'Complete syllabua', 'medium', '2026-05-10', NULL, 0, 0, NULL, '2026-04-15 09:11:01', '2026-04-15 09:11:01');

-- --------------------------------------------------------

--
-- Table structure for table `todo_categories`
--

DROP TABLE IF EXISTS `todo_categories`;
CREATE TABLE IF NOT EXISTS `todo_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6366f1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `todo_categories_user_id_name_unique` (`user_id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `todo_categories`
--

INSERT INTO `todo_categories` (`id`, `user_id`, `name`, `color`, `created_at`, `updated_at`) VALUES
(1, 4, 'Marriege', '#ef4444', '2026-03-18 10:25:34', '2026-03-18 10:25:34'),
(2, 4, 'Engage', '#3b82f6', '2026-03-18 10:39:40', '2026-03-18 10:39:40'),
(3, 1, 'Jwellery', '#ef4444', '2026-03-21 10:02:51', '2026-03-21 10:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `banned_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INR',
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hi',
  `notification_email` tinyint(1) NOT NULL DEFAULT '1',
  `notification_push` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_google_id_unique` (`google_id`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `phone_verified_at`, `email_verified_at`, `role`, `banned_at`, `password`, `avatar`, `google_id`, `currency`, `language`, `notification_email`, `notification_push`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Jasvinder Kumar', 'admin@yopmail.com', '7015457850', '2026-03-10 08:34:37', '2026-03-09 06:02:35', 'admin', NULL, '$2y$12$jyp/ZvGc99ucuzZApukB6.SPIgw16E.bVtg0nYtLRebssnbRISG0C', 'avatars/1.webp', NULL, 'INR', 'hi', 1, 1, 'wde7oeTdziMw4V0a9pQCxQlu4yJHDPjOutCN9nQEBN9BPkIk0ypt2U8ktPdw', '2026-03-09 06:02:36', '2026-03-20 12:47:26'),
(2, 'Mohan Lal', 'mohan@yopmail.com', '9416716775', '2026-03-09 07:13:34', '2026-03-09 06:02:35', 'user', NULL, '$2y$12$59Rg01YKXws/BBMESAI7E.QyMgP5Co7qQ/p6aMxzPL8WEIIaAzNJm', 'https://lh3.googleusercontent.com/a/ACg8ocJyKGRnHCzuJfs9c_StzCrAjjVFP0VJeqRnyCnVOLbEuPZYt5o=s96-c', NULL, 'INR', 'hi', 1, 1, '3UPL2XLJg92kGQ0ahSaq3DFqp4uLEma261oigAMNdSs3N9yffsgQHWkBpyUU', '2026-03-09 06:02:36', '2026-03-28 18:50:27'),
(3, 'Vikas Bansalaa', 'vikas@yopmail.com', '9000000001', '2026-03-23 07:55:40', '2026-03-09 07:13:34', 'user', NULL, '$2y$12$bHruEiHOoWUFYqrT7o/jcewlqb9iCqo48x9SeTppQc23cAkqVcOn2', 'avatars/3.webp', NULL, 'INR', 'hi', 1, 1, '2Xpxb3Xb1nfUoIrVXORMMdL4tmblOaVoFPfzVlvXbXwZ5GO5YokF9LuIzTnB', '2026-03-09 06:02:36', '2026-03-23 07:55:40'),
(4, 'Raja', 'raja@yopmail.com', '9000000002', '2026-03-09 07:13:34', '2026-03-09 06:02:35', 'user', NULL, '$2y$12$ELD5n4TLkzUH90TH8/AZAuPTdOfkdEUDEs7ByGe7IgFqjuAwoEchi', NULL, NULL, 'INR', 'hi', 1, 1, 'kOTGN1KSAcKjIDyDvmlf86TBELPVBAoNrHFDaYggsHQ2ZtemCFQQPKqhOq7C', '2026-03-09 06:02:36', '2026-03-09 06:02:36'),
(5, 'Laddy', 'laddy@yopmail.com', '9000000003', '2026-03-09 07:13:34', '2026-03-09 06:02:36', 'user', NULL, '$2y$12$3t1ccHdg4exSdnLGkITeIesXe6N1DdMTt.xZlbovC2mK0srGkKpfW', NULL, NULL, 'INR', 'hi', 1, 1, 'fOEUwY9jP4ueQcyuJ41NBLsab4UGmls4AuQ1w7lzRUqLPqA4yH1SelyFNZnk', '2026-03-09 06:02:36', '2026-03-09 06:02:36'),
(16, 'Radhe ji', 'radhye@yopmail.com', '9898989898', NULL, '2026-03-18 02:21:03', 'user', NULL, '$2y$12$uQtk.AKpvwettUMltFB3h.x.DCQMPhSFDRYeg7KIKX47QeDtMd1PW', NULL, NULL, 'INR', 'hi', 1, 1, NULL, '2026-03-18 02:13:31', '2026-03-19 10:13:09'),
(18, 'Jass', 'teqjasvinder@gmail.com', NULL, NULL, '2026-03-28 12:26:42', 'user', NULL, '$2y$12$txVY754Ck9tqGw6q6ZPpyu/eAtHUZ.1/R7QmwGw6emyDj2A3RDSqO', NULL, NULL, 'INR', 'hi', 1, 1, NULL, '2026-03-28 12:10:54', '2026-03-28 12:26:42'),
(19, 'Manisha', 'manishamisha66@gmail.com', NULL, NULL, '2026-03-28 12:33:25', 'user', NULL, '$2y$12$VnKj8uCLA3pAJXfHCXCSsu0AAIQAiUBO1T17ika3loXoUIVgJhUxG', NULL, NULL, 'INR', 'hi', 1, 1, NULL, '2026-03-28 12:33:02', '2026-03-28 12:33:25'),
(25, 'Jack', 'jack@yopmail.com', NULL, NULL, '2026-04-01 16:15:28', 'user', NULL, '$2y$12$181uzcnOOtdGp/2CRsJVgux8L59GiWLXEp4lVUQ4hryVEWn2569OO', NULL, NULL, 'INR', 'hi', 1, 1, NULL, '2026-04-01 16:14:05', '2026-04-01 16:15:28'),
(24, 'Jass Kashyap', 'jass.code.tools@gmail.com', NULL, NULL, '2026-03-29 04:20:22', 'user', NULL, '$2y$12$neUhC/ycIO19HcM2gkoWFesaQq1HowV.2KYiv8xjqK3NQotvnbpt6', 'https://lh3.googleusercontent.com/a/ACg8ocJyKGRnHCzuJfs9c_StzCrAjjVFP0VJeqRnyCnVOLbEuPZYt5o=s96-c', '104201146537003072811', 'INR', 'hi', 1, 1, '08z3eWXuPHLsOLLeixvsCvcITzmyM8qPekjoOhPYUEZm6gh7g6JMpZLTWFAZ', '2026-03-29 04:13:42', '2026-03-29 07:46:09'),
(26, 'Shivam Diwan', 'shivdiwan73@gmail.com', NULL, NULL, '2026-04-02 17:40:28', 'user', NULL, '$2y$12$SMd9NAkxoDNfKoF5u6w.ruZYcA8Ma1rTYqKazqMUv7GsZlfye/C/e', 'avatars/26.webp', NULL, 'INR', 'hi', 1, 1, NULL, '2026-04-02 17:39:46', '2026-04-02 17:42:11'),
(27, 'Lakshay', 'baitakashyap1@gmail.com', NULL, NULL, '2026-04-03 15:01:24', 'user', NULL, '$2y$12$J5YLH1A5bR/s/m4EDzicYOrlu9ql926269IRlDBdixbEBYuPScpYC', NULL, NULL, 'INR', 'hi', 1, 1, NULL, '2026-04-03 15:00:33', '2026-04-03 15:01:24'),
(28, 'Tushar', 'tusharreactjs@gmail.com', NULL, NULL, '2026-04-05 06:28:53', 'user', NULL, '$2y$12$q/.wCGdvxBpo57OaVQZbN.xkUcwaC0j3uT4um/6jTae2qUzT3qXEC', NULL, NULL, 'INR', 'hi', 1, 1, NULL, '2026-04-05 06:26:53', '2026-04-05 06:28:53'),
(29, 'Gurpreet Kait', 'gurpreetkait.codes@gmail.com', NULL, NULL, '2026-04-05 06:58:52', 'user', NULL, '$2y$12$bk3lYBDjApHu.GJ8iv3pu.PlFGlb.lezC8fNL39Bi1jARhVTN9KW6', NULL, NULL, 'INR', 'hi', 1, 1, NULL, '2026-04-05 06:58:38', '2026-04-05 06:58:52');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
