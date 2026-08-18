-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2026 at 11:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nabil_menu`
--

-- --------------------------------------------------------

--
-- Table structure for table `bot_pending_actions`
--

CREATE TABLE `bot_pending_actions` (
  `id` int(11) NOT NULL,
  `chat_id` varchar(64) NOT NULL,
  `action_type` varchar(40) NOT NULL,
  `payload` text NOT NULL,
  `summary` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` text NOT NULL,
  `cat_picture` text NOT NULL,
  `cat_icon` text DEFAULT NULL,
  `Order` int(11) NOT NULL,
  `cat_footer` text DEFAULT NULL,
  `cat_footer_bottom` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phonenumber` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` text NOT NULL,
  `item_category` text NOT NULL,
  `Ingredients` text NOT NULL,
  `item_pic` text NOT NULL,
  `Order` int(11) NOT NULL DEFAULT 0,
  `item_priceusd` double NOT NULL,
  `price_suffix` varchar(20) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `total_usd` decimal(10,2) DEFAULT 0.00,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `requested_time` varchar(20) DEFAULT NULL,
  `items` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `restaurant_name` varchar(255) NOT NULL,
  `restaurant_logo` varchar(255) DEFAULT NULL,
  `restaurant_email` varchar(255) DEFAULT NULL,
  `restaurant_phone` varchar(50) DEFAULT NULL,
  `restaurant_address` text DEFAULT NULL,
  `restaurant_maps` text DEFAULT NULL,
  `restaurant_description` text DEFAULT NULL,
  `opening_hours` varchar(255) DEFAULT NULL,
  `opening_title` varchar(255) DEFAULT 'Open Daily',
  `home_bg` varchar(255) DEFAULT 'bgs/home-bg.jpg',
  `menu_bg` varchar(255) DEFAULT 'bgs/menu-bg.jpg',
  `contact_bg` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(50) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `chat_id` bigint(255) NOT NULL,
  `bot_token` text NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `order_method` varchar(50) DEFAULT 'whatsapp',
  `banner1_visible` tinyint(1) NOT NULL DEFAULT 1,
  `banner2_visible` tinyint(1) NOT NULL DEFAULT 1,
  `banner1_t1` varchar(255) DEFAULT 'THANK YOU FOR SUPPORTING LOCAL',
  `banner1_t2` varchar(255) DEFAULT 'Made with fresh ingredients & lots of love',
  `banner1_t3` varchar(255) DEFAULT 'AUTHENTIC MEDITERRANEAN FLAVOR',
  `banner2_t1` varchar(255) DEFAULT 'FRESH INGREDIENTS',
  `banner2_t2` varchar(255) DEFAULT 'MADE DAILY',
  `banner2_t3` varchar(255) DEFAULT 'AUTHENTIC RECIPES',
  `banner2_t4` varchar(255) DEFAULT 'MADE WITH LOVE',
  `about_title` varchar(255) DEFAULT 'Flavors Crafted With Heritage & Love',
  `about_subtitle` varchar(255) DEFAULT 'Our Legacy',
  `about_desc1` text DEFAULT NULL,
  `about_desc2` text DEFAULT NULL,
  `about_image` varchar(255) DEFAULT 'admin/bgs/about_story.png',
  `about_chef_image` varchar(255) DEFAULT 'admin/bgs/about_chef.png',
  `about_chef_title` varchar(255) DEFAULT 'The Passion Behind the Plate',
  `about_chef_subtitle` varchar(255) DEFAULT 'Handcrafted Culinary Artistry',
  `about_chef_name` varchar(255) DEFAULT 'Nabil',
  `about_chef_bio1` text DEFAULT NULL,
  `about_chef_bio2` text DEFAULT NULL,
  `about_years` varchar(50) DEFAULT '15+',
  `about_years_label` varchar(255) DEFAULT 'Years of Tradition',
  `about_bg` varchar(255) DEFAULT 'admin/bgs/hero-bg.jpg',
  `values_title` varchar(255) DEFAULT 'What We Stand For',
  `values_subtitle` varchar(255) DEFAULT 'Our Principles',
  `values_desc` text DEFAULT NULL,
  `value1_icon` varchar(100) DEFAULT 'fas fa-seedling',
  `value1_title` varchar(255) DEFAULT '100% Fresh Daily',
  `value1_desc` text DEFAULT NULL,
  `value2_icon` varchar(100) DEFAULT 'fas fa-scroll',
  `value2_title` varchar(255) DEFAULT 'Authentic Recipes',
  `value2_desc` text DEFAULT NULL,
  `value3_icon` varchar(100) DEFAULT 'fas fa-heart',
  `value3_title` varchar(255) DEFAULT 'Prepared With Love',
  `value3_desc` text DEFAULT NULL,
  `value4_icon` varchar(100) DEFAULT 'fas fa-hands-helping',
  `value4_title` varchar(255) DEFAULT 'Warm Hospitality',
  `value4_desc` text DEFAULT NULL,
  `show_cart` tinyint(1) NOT NULL DEFAULT 1,
  `deepseek_api_key` text DEFAULT NULL,
  `notify_contact_telegram` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `userpassword` varchar(20) NOT NULL,
  `isAdmin` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `userpassword`, `isAdmin`) VALUES
(1, 'admin', 'CHANGE_ME', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bot_pending_actions`
--
ALTER TABLE `bot_pending_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `idx_categories_order` (`Order`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_items_category` (`item_category`(768)),
  ADD KEY `idx_items_name` (`item_name`(768));

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bot_pending_actions`
--
ALTER TABLE `bot_pending_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
