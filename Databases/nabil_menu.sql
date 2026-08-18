-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 18, 2026 at 09:23 PM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u104370626_nabilmenu`
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

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `cat_picture`, `cat_icon`, `Order`, `cat_footer`, `cat_footer_bottom`) VALUES
(1, 'WRAPS', '', '', 4, 'Wrapped in pita!', ''),
(2, 'OUR FAMOUS PIES', '', '', 6, '', ''),
(3, 'DIPS', '', '', 1, '', ''),
(4, 'FAMILY COMBOS', '', '', 8, 'YOU MUST ORDER 1 HOUR EARLIER', ''),
(5, 'SIDES', '', '', 2, '', ''),
(6, 'SALAD & SPECIALTIES', '', '', 3, '', ''),
(7, 'SKEWERS', '', '', 5, '', ''),
(9, 'BOWLS', '', '', 7, '', 'INCLUDES DRINK');

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

--
-- Dumping data for table `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `name`, `phonenumber`, `subject`, `message`, `submission_date`) VALUES
(10, 'David Williams', '3507007668', 'Is your website ready for Search?', 'Hi,\r\n\r\nI checked your website and found a few SEO improvements that can help increase your Google rankings and generate more quality leads.\r\n\r\nI can share a quick SEO proposal with pricing and a strategy tailored for your business growth.\r\n\r\nLet me know if you\'re interested.\r\n\r\nRegards,\r\n\r\nDavid', '2026-06-08 12:23:50'),
(11, 'Ananya Singh', '5185894802', 'Get Your Website on Google’s First Page', 'Hello http://nabilmediterraneanfood.com,\r\n\r\nIf you’re looking to boost your website’s visibility, I can help you achieve top Google rankings.\r\n\r\nI’ll prepare a complete SEO plan with actionable steps and potential growth insights for your products or services.\r\n\r\nOnce you share your target keywords and target market, I’ll send a full proposal.\r\n\r\nBest Regards,\r\nAnanya', '2026-06-09 05:56:03'),
(12, 'Sharma', 'Vszscm Jtycuj', 'Proposal for Website Redesign to Enhance Your Online Presence', 'Hi,\r\nI recently reviewed your website (nabilmediterraneanfood.com) and would love to help enhance it with a modern redesign.\r\n \r\nKey benefits:\r\n• Responsive, mobile-friendly design\r\n• Improved UX and navigation\r\n• Faster load times\r\n• SEO optimization\r\n• Accessibility compliance\r\n \r\nWe specialize in Shopify, Magento, WordPress, Drupal, and custom development, along with mobile apps.\r\n \r\nYou can view our work here:\r\n• Website Portfolio: https://webstore.vgroup.net/portfolio/\r\n• Mobile App Portfolio: https://vgroupdigital.com/portfolio\r\n \r\nWould you be open to a quick call to discuss your goals?\r\n\r\nBest regards, \r\nManshi Sharma', '2026-06-09 12:32:58'),
(13, 'Daniel Thompson', '775 326 7211', 'Re: Issues with SEO and traffic', 'Hi nabilmediterraneanfood.com,\r\n\r\nWhile reviewing your website www.nabilmediterraneanfood.com, I noticed an SEO and AIO issue that might be holding it back from appearing more prominently in Google, AI-generated answers, and voice-enabled search.\r\n\r\nI can send a short proposal with recommendations, expected benefits, and pricing.\r\n\r\nInterested?\r\n\r\nRegards,\r\nDaniel Thompson\r\nWebsite : https://digital-care-services.us\r\nWhatsApp : wa.link/u2vgm8', '2026-06-10 05:49:57'),
(14, 'Mark Collins', '845 447 9454', 'Question About Your Website', 'Hello,\r\n\r\nWe recently ran a backend analysis of your website, and the results show that several important SEO (Search Engine Optimization) steps are incomplete. Due to this, your website is currently not appearing on Google, Bing, and other search engines when searched with keywords related to your business and services.\r\n\r\nWe understand that your website was created to attract more clients and generate more business, and we want you to have the best website experience ever. Our team would be happy to assist you with improving the SEO and overall online visibility of your website and business.\r\n\r\nPlease send us your preferred time availability for a quick phone call, along with your updated contact number, and we will get in touch with you to explain how we can help fix and improve your website.\r\n\r\nLooking forward to hearing from you\r\n\r\nThanks,\r\nMark Collins', '2026-06-10 15:23:53'),
(15, 'Test Uzer', '5556410684', 'Testing', 'This is a test message. Please disregard this submission.', '2026-06-10 17:52:25'),
(16, 'Janelle Feierabend', '2175212188', 'Results for nabilmediterraneanfood.com', 'Hello\r\n\r\nEnlist nabilmediterraneanfood.com in GoogleSearchIndex and have it be visible in web search results!\r\n\r\nEnlist nabilmediterraneanfood.com now: https://searchregister.live', '2026-06-11 17:44:08'),
(17, 'Sharyl Brazenor', '42504582', 'Results for nabilmediterraneanfood.com', 'Hi\r\n\r\nPlace nabilmediterraneanfood.com in GoogleSearchIndex to be displayed in online search results!\r\n\r\nInclude nabilmediterraneanfood.com now: https://searchregister.info', '2026-06-12 13:53:21'),
(18, 'Lucy Gordon', '1201201200', 'lucygordon.mkt@gmail.com', '\"Hello team,\r\n\r\nI was going through your website & I personally see a lot of potential in your website & business.\r\n\r\nWe can increase targeted traffic to your website so that it appears on Google\'s first page. Bing, Yahoo, etc.\r\n\r\nPlease provide your name, contact information, and email.\r\n\r\nWell wishes,\r\nLucy Gordon\r\n\r\n\r\n\r\n\r\nSkilled across major platforms including Squarespace, Shopify, Wix, WordPress, GoDaddy etc.\"', '2026-06-17 05:31:55'),
(19, 'Anaya Mishra', '7013104086', 'Improve Your Google Rankings & Get More Leads.', 'Hi,\r\n\r\nI hope you’re doing well.\r\n\r\nI wanted to reach out to see if improving your website’s visibility (http://nabilmediterraneanfood.com) on Google is something you’re considering. We provide SEO services focused on increasing organic traffic, improving rankings, and generating more business inquiries.\r\n\r\nIf interested, please let me know, and I can send SEO plans and pricing details.\r\n\r\nBest regards,\r\nAnaya', '2026-06-17 11:47:31'),
(20, 'Mishra', 'Tjhnbta bsl i', 'Re: Improve Your Website\'s Google Rankings !!', 'Hi,\r\n\r\nI came across your website http://nabilmediterraneanfood.com and noticed there may be opportunities to improve its visibility in Google search results.\r\n\r\nI help businesses increase organic traffic through SEO strategies including keyword research, on-page optimization, technical SEO, content improvements, and local search optimization.\r\n\r\nWith better rankings, your website can attract more qualified visitors and generate additional leads without relying solely on paid advertising.\r\n\r\nI\'d be happy to provide a free SEO review and share a few recommendations tailored to your website.\r\n\r\nBest regards,\r\nAnaya', '2026-06-18 05:45:33'),
(21, 'Diana Cruz', '1201201200', 'dianacruz.mkt@gmail.com', '\"Hello team,\r\n\r\n\r\nI was going through your website & I personally see a lot of potential in your website & business.\r\n\r\nWe can increase targeted traffic to your website so that it appears on Google\'s first page. Bing, Yahoo, etc.\r\n\r\nPlease provide your name, contact information, and email.\r\n\r\nWell wishes,\r\nDiana Cruz\r\n\r\nWeb platform expertise across Squarespace, Shopify, Wix, WordPress, GoDaddy etc.\"', '2026-06-23 05:33:15'),
(22, 'Diana Cruz', '1201201200', 'dianacruz.mkt@gmail.com', '\"Hello team,\r\n\r\n\r\nI was going through your website & I personally see a lot of potential in your website & business.\r\n\r\nWe can increase targeted traffic to your website so that it appears on Google\'s first page. Bing, Yahoo, etc.\r\n\r\nPlease provide your name, contact information, and email.\r\n\r\nWell wishes,\r\nDiana Cruz\r\n\r\nWeb platform expertise across Squarespace, Shopify, Wix, WordPress, GoDaddy etc.\"', '2026-06-23 11:34:01'),
(23, 'AK Hz', '6883709545', 'Congrats on Your Website Launch', 'Hi [nabilmediterraneanfood.com],\r\n\r\nI visited your website online and discovered that it was not showing up in any search results for the majority of keywords related to your company on Google, Yahoo, or Bing. \r\n\r\nWe can place your website on Google\'s 1st page. Yahoo, Facebook, LinkedIn, YouTube, Instagram, Pinterest etc.\r\n\r\nI would be pleased to provide you with \"charges,\" \"Proposals,\" details of past work!\r\n\r\nCheers,\r\nAkhz | Founder & Project Head\r\nWhatsApp - http://wa.me/919654429975\r\nwww.Rankyounow.com', '2026-06-23 14:25:47'),
(24, 'DavidFum', '87698285663', 'Are you looking for a cost-effective and innovative advertising solution for a minimal cost?', 'Hey! nabilmediterraneanfood.com, \r\nI found your website while looking for sites open to proposals. \r\nOur platform helps companies introduce their services to website operators. \r\nThe platform enables efficient communication with websites. \r\n  \r\nA free test is available so you can see how the platform works. \r\nYou can reply if you would like more details. \r\n \r\nHave a wonderful day. \r\nContact us. \r\nTelegram - https://t.me/FeedbackFormEU \r\nWhatsApp - +375259112693 \r\nWhatsApp  https://wa.me/+375259112693', '2026-06-28 04:47:59'),
(25, 'AK Hz', '1 208-827 5732', 'Congrats — A Great Time to Strengthen SEO', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI would like to discuss SEO!\r\n\r\nI can help your website to get on first page of Google and increase the number of leads and sales you are getting from your website.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nAkhz | Founder & Project Head\r\nContact - 1 208-827 5732\r\nwww.Rankyounow.com', '2026-06-30 11:17:10'),
(26, 'Earnestine Brito', '9035611049', 'Daily seo backlinks for nabilmediterraneanfood.com', 'DailySeoLinks.com - we deliver daily backlinks and increase google rank every day:\r\n\r\n+ 1000+ backlinks daily\r\n+ Real google clicks\r\n+ Price cheap as $1\r\n+ Up to 85% off:\r\n\r\nhttps://dailyseolinks.com/sale\r\n\r\nDailySeoLinks.com - daily backlinks to grow your seo ranking everyday', '2026-07-04 11:39:23'),
(27, 'AK Hz', '1 208-827 5732', 'Quick SEO Idea for Your New Website', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI would like to discuss SEO!\r\n\r\nI can help your website to get on first page of Google and increase the number of leads and sales you are getting from your website.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nAkhz | Founder & Project Head\r\nContact - 1 208-827 5732\r\nwww.Rankyounow.com', '2026-07-05 12:39:10'),
(28, 'Jaunita Feakes', '263321725', 'Results for nabilmediterraneanfood.com', 'Hey\r\n\r\nInclude nabilmediterraneanfood.com in GoogleSearchIndex so it can appear in google search results!\r\n\r\nAdd nabilmediterraneanfood.com now: https://searchregister.org', '2026-07-06 19:12:59'),
(29, 'Barney Tenney', '41522208', 'Plans for nabilmediterraneanfood.com?', 'Greetings\r\n\r\nHi,\r\n\r\nCongrats on your new domain, nabilmediterraneanfood.com.\r\n\r\nIf you’re looking for someone to build your website, we’d be happy to help.\r\n\r\nWe design clean, modern websites that help businesses grow online.\r\n\r\nWhether you need a brand-new website or a modern redesign, we’re here to help.\r\n\r\nHave a look at WebLaunched.net to view our portfolio and pricing.', '2026-07-07 06:12:25'),
(30, 'Daniel Edwards', '8454479454', 'Question About Your Website', 'Hello,\r\n\r\nWe recently ran a backend analysis of your website, and the results show that several important SEO (Search Engine Optimization) steps are incomplete. Due to this, your website is currently not appearing on Google, Bing, and other search engines when searched with keywords related to your business and services.\r\n\r\nWe understand that your website was created to attract more clients and generate more business, and we want you to have the best website experience ever. Our team would be happy to assist you with improving the SEO and overall online visibility of your website and business.\r\n\r\nPlease send us your preferred time availability for a quick phone call, along with your updated contact number, and we will get in touch with you to explain how we can help fix and improve your website.\r\n\r\nLooking forward to hearing from you\r\n\r\nThanks,\r\nDaniel Edwards', '2026-07-07 22:03:11'),
(31, 'Rosabel Edney', '1201201200', 'Helping New Teams Grow Organically', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI\'d love to discuss how we can grow your online visibility.\r\n\r\nWe help businesses rank higher on Google while also increasing their presence on AI-powered search platforms like ChatGPT, Gemini, Claude, and Perplexity.\r\n\r\nOur SEO + AEO + GEO strategies help brands get discovered wherever customers search for answers.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nRosabel Edney | Founder & Project Head', '2026-07-21 17:14:58'),
(34, 'DavidFum', '84784936834', 'Do you wish to draw in more customers for your business?', 'Hey there! nabilmediterraneanfood.com, \r\nWhile browsing the web I discovered nabilmediterraneanfood.com. \r\nOur service is built to simplify online outreach to websites. \r\nOur platform is designed to support communication with websites. \r\nThe service is available with different pricing options depending on needs. \r\n  \r\nIf this topic might interest you, feel free to get in touch. \r\n \r\nThanks for taking a moment to read this. \r\nContact us. \r\nTelegram - https://t.me/FeedbackFormEU \r\nWhatsApp - +375259112693 \r\nWhatsApp  https://wa.me/+375259112693 \r\nWe only use chat for communication.', '2026-07-22 12:16:26'),
(35, 'Lucy Gordon', '1201201200', 'Re: nabilmediterraneanfood.com - SEO', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI\'d love to discuss your online visibility!\r\n\r\nWe help businesses rank not only on Google Search but also become discoverable on AI platforms like ChatGPT, Gemini, Claude, and Perplexity through SEO, AEO (Answer Engine Optimization), and GEO (Generative Engine Optimization).\r\n\r\nThis helps you generate more qualified traffic, leads, and brand visibility across both search engines and AI assistants.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nLucy Gordon | Founder & Project Head', '2026-07-23 13:57:05'),
(36, 'Anaya Mishra', '176181755', 'Re: A Few Ideas to Improve http://nabilmediterraneanfood.com', 'Hi, \r\n\r\nI hope you’re doing well.\r\n\r\nI wanted to reach out and see if you are interested in improving your website’s http://nabilmediterraneanfood.com visibility on Google and attracting more potential customers through SEO. We offer SEO solutions designed to help businesses increase organic traffic, improve keyword rankings, and generate quality inquiries.\r\n\r\nIf this sounds interesting, I’d be happy to share our complete SEO packages, activities, timeline, and pricing details for your review.\r\n\r\nLooking forward to your response.\r\n\r\nRegards,\r\nAnaya', '2026-07-27 15:52:06'),
(37, 'Rosabel Edney', '1201201200', 'Quick SEO Idea for Your New Website', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI\'d love to discuss how we can grow your online visibility.\r\n\r\nWe help businesses rank higher on Google while also increasing their presence on AI-powered search platforms like ChatGPT, Gemini, Claude, and Perplexity.\r\n\r\nOur SEO + AEO + GEO strategies help brands get discovered wherever customers search for answers.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nRosabel Edney | Founder & Project Head', '2026-07-30 05:13:20'),
(38, 'Tommy Zapes', '1201201200', 'Congrats — A Great Time to Strengthen SEO', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI\'d love to discuss how we can grow your online visibility.\r\n\r\nWe help businesses rank higher on Google while also increasing their presence on AI-powered search platforms like ChatGPT, Gemini, Claude, and Perplexity.\r\n\r\nOur SEO + AEO + GEO strategies help brands get discovered wherever customers search for answers.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nTommy  Zapes | Founder & Project Head', '2026-08-03 05:17:40'),
(39, 'Rosabel Edney', '474419105', 'Quick SEO Idea for Your New Website', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI\'d love to discuss how we can grow your online visibility.\r\n\r\nWe help businesses rank higher on Google while also increasing their presence on AI-powered search platforms like ChatGPT, Gemini, Claude, and Perplexity.\r\n\r\nOur SEO + AEO + GEO strategies help brands get discovered wherever customers search for answers.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nRosabel Edney | Founder & Project Head', '2026-08-10 11:02:11'),
(40, 'Rosabel Edney', '50558931', 'Helping New Teams Grow Organically', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI\'d love to discuss how we can grow your online visibility.\r\n\r\nWe help businesses rank higher on Google while also increasing their presence on AI-powered search platforms like ChatGPT, Gemini, Claude, and Perplexity.\r\n\r\nOur SEO + AEO + GEO strategies help brands get discovered wherever customers search for answers.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nRosabel Edney | Founder & Project Head', '2026-08-11 10:33:57'),
(41, 'Rosabel Edney', '493702435', 'Congrats — A Great Time to Strengthen SEO', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI was going through your website & I personally see a lot of potential in your website & business.\r\n\r\nWe help businesses rank higher on Google while also increasing their presence on AI-powered search platforms like ChatGPT, Gemini, Claude, and Perplexity.\r\n\r\nOur SEO + AEO + GEO strategies help brands get discovered wherever customers search for answers.\r\n\r\nMay I send you a quote & price list?\r\n\r\nCheers,\r\nRosabel Edney | Founder & Project Head', '2026-08-13 14:01:30'),
(42, 'Rosabel Edney', '1201201200', 'Re: nabilmediterraneanfood.com - SEO', 'Hey team nabilmediterraneanfood.com,\r\n\r\nI would like to discuss SEO!\r\n\r\nWe help businesses rank higher on Google while also increasing their presence on AI-powered search platforms like ChatGPT, Gemini, Claude, and Perplexity.\r\n\r\nOur SEO + AEO + GEO strategies help brands get discovered wherever customers search for answers.\r\n\r\nWould you like me to send you our proposal and pricing?\r\n\r\nCheers,\r\nRosabel Edney | Founder & Project Head', '2026-08-16 10:01:52'),
(43, 'TimothySpoky', '89272112549', 'Gfdhwuwfeeu fhefuwhdwijduwfeu huwhfduewfhuwijdwuhfwu', 'Egjnjmfnefjwdifj fkmdkdwdwkdwjj fkmfkengjkfmsdnfejfk mkfmkdmwjefnejfem nabilmediterraneanfood.com', '2026-08-18 16:29:22');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `photo_path`, `created_at`) VALUES
(21, 'assets/images/admin/pics/VIBE-6a26ac24516665.16286878.webp', '2026-06-08 11:48:52'),
(22, 'assets/images/admin/pics/VIBE-6a26ac249c26a1.29896991.webp', '2026-06-08 11:48:52'),
(23, 'assets/images/admin/pics/VIBE-6a26ac24e98163.21259607.webp', '2026-06-08 11:48:53'),
(25, 'assets/images/admin/pics/VIBE-6a26ac25925ea9.44198835.webp', '2026-06-08 11:48:53');

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

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `item_category`, `Ingredients`, `item_pic`, `Order`, `item_priceusd`, `price_suffix`) VALUES
(1, 'Chicken Shawarma', 'WRAPS', 'Marinated chicken, lettuce, tomatoes, pickles, turnips and garlic sauce', '', 0, 12.99, ''),
(2, 'Beef Shawarma', 'WRAPS', 'Marinated beef, parsley, onions, tomatoes, pickles,\r\nturnips and tahini sauce', '', 0, 12.99, ''),
(3, 'Falafel Wrap', 'WRAPS', 'Falafel, lettuce, tomatoes, pickles,turnips,parsley\r\nand tahini sauce', '', 0, 8.99, ''),
(4, 'Kafta Wrap', 'WRAPS', 'Grilled kafta, tomatoes, pickles, turnips,parsley,onion\r\nand tahini sauce', '', 0, 11.99, ''),
(5, 'Gyro Wrap', 'WRAPS', 'Gyro meat, lettuce, tomatoes, onions\r\nand tzatziki sauce', '', 0, 9.99, ''),
(6, 'Shish Tawook Wrap', 'WRAPS', 'Marinated chicken cubes, lettuce, tomatoes, pickles, turnips and garlic sauce', '', 0, 11.99, ''),
(7, 'Shish Kabob Wrap', 'WRAPS', 'Marinated beef kabob, tomatoes, onions, parsley,\r\npickles, turnips and tahini sauce', '', 0, 12.99, ''),
(8, 'Lamb Kabob Wrap', 'WRAPS', 'Marinated lamb kabob, tomatoes, onions, parsley,\r\npickles, turnips and tahini sauce', '', 0, 13.99, ''),
(9, 'Hummus Wrap', 'WRAPS', 'Fresh hummus, lettuce, tomatoes and pickles,turnips', '', 0, 8.99, ''),
(10, 'Baba Ghanouj Wrap', 'WRAPS', 'Fresh baba ghanouj, lettuce, tomatoes,turnips\r\nand pickles', '', 0, 8.99, ''),
(11, 'French Fries Wrap', 'WRAPS', 'French fries, pickles and garlic sauce', '', 0, 8.99, ''),
(12, 'Zaatar Pie', 'OUR FAMOUS PIES', '', '', 0, 3.5, ''),
(13, 'Spinach Pie', 'OUR FAMOUS PIES', '', '', 0, 4, ''),
(14, 'Cheese Pie', 'OUR FAMOUS PIES', '', '', 0, 5, ''),
(15, 'Meat Pie', 'OUR FAMOUS PIES', '', '', 0, 5, ''),
(16, 'Spinach & Feta Pie', 'OUR FAMOUS PIES', '', '', 0, 4.5, ''),
(17, 'Kishek Pie', 'OUR FAMOUS PIES', '', '', 0, 5, ''),
(18, '1/2 Zaatar / 1/2 Cheese', 'OUR FAMOUS PIES', '', '', 0, 5, ''),
(19, 'Hummus', 'DIPS', '', '', 0, 10, '/lb'),
(20, 'Baba Ghanouj', 'DIPS', '', '', 0, 10, '/lb'),
(21, 'Tabouli', 'Salad & Specialties', '', '', 0, 10, '/lb'),
(22, 'Fattoush', 'SALAD & SPECIALTIES', '', '', 0, 10, '1 Person'),
(23, 'Grape Leaves', 'Salad & Specialties', '', '', 0, 10, '/lb'),
(24, 'Moujadara', 'Salad & Specialties', '', '', 0, 10, '/lb'),
(25, 'Mousaka', 'DIPS', '', '', 0, 10, '/lb'),
(26, 'Labneh', 'DIPS', '', '', 0, 8, '/lb'),
(27, 'Labneh with Garlic', 'DIPS', '', '', 0, 10, '/lb'),
(28, '4 PEOPLE COMBO', 'FAMILY COMBOS', '2 beef kabobs, 2 chicken kabobs, 2 kafta kabobs, 6 falafel, 4 kibbeh, hummus, salad, rice and pita.', '', 0, 95, ''),
(29, '6 PEOPLE COMBO', 'FAMILY COMBOS', '4 beef kabobs, 4 chicken kabobs, 4 kafta kabobs, 12 falafel, 6 kibbeh, hummus, salad, rice and pita.', '', 0, 130, ''),
(30, '10 PEOPLE COMBO', 'FAMILY COMBOS', '6 beef kabobs, 6 chicken kabobs, 6 kafta kabobs, 24 falafel, 10 kibbeh, hummus, salad, rice and pita.', '', 0, 190, ''),
(31, 'Falafel (6 pcs)', 'SIDES', '', '', 0, 5.5, ''),
(32, 'Falafel (12 pcs)', 'SIDES', '', '', 0, 10, ''),
(33, 'Kibbeh', 'SIDES', '', '', 0, 3.5, '/pcs'),
(34, 'French Fries Small', 'SIDES', '', '', 0, 5, ''),
(35, 'French Fries Large', 'SIDES', '', '', 0, 9, ''),
(36, 'Shankleesh', 'DIPS', '', '', 0, 11, '/lb'),
(37, 'Rice with Lamb', 'SALAD & SPECIALTIES', '', '', 0, 12, '/lb'),
(38, 'Chicken Tarouk Skewer', 'Skewers', '', '', 0, 4.5, 'ea'),
(39, 'Kafta Kabob Skewer', 'Skewers', '', '', 0, 4.5, 'ea'),
(40, 'Beef Kabob Skewer', 'Skewers', '', '', 0, 5.5, 'ea'),
(42, 'Lamb Kabob Skewer', 'Skewers', '', '', 0, 5.5, 'ea'),
(43, 'Chicken Skewer Bowl', 'BOWLS', 'Fluffy Rice, Chicken Skewer, Hummus or Baba Ghanouj, Fresh Salad', '', 1, 20, ''),
(44, 'Beef Skewer Bowl', 'BOWLS', 'Fluffy Rice, Beef Skewer, Hummus or Baba Ghanouj, Fresh Salad', '', 2, 20, ''),
(45, 'Lamb Skewer Bowl', 'BOWLS', 'Fluffy Rice, Lamb Skewer, Hummus or Baba Ghanouj, Fresh Salad', '', 3, 20, ''),
(46, 'Kafta Bowl', 'BOWLS', 'Fluffy Rice, Kafta, Hummus or Baba Ghanouj, Fresh Salad', '', 4, 20, ''),
(47, 'Chicken Shawarma Bowl', 'BOWLS', 'Fluffy Rice, Chicken Shawarma, Hummus or Baba Ghanouj, Fresh Salad', '', 5, 20, ''),
(48, 'Beef Shawarma Bowl', 'BOWLS', 'Fluffy Rice, Beef Shawarma, Hummus or Baba Ghanouj, Fresh Salad', '', 6, 20, ''),
(49, 'Falafel Bowl', 'BOWLS', 'Fluffy Rice, 4 Falafel Pieces, Hummus or Baba Ghanouj, Fresh Salad', '', 7, 16, '');

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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `total_usd`, `whatsapp_number`, `status`, `created_at`, `completed_at`, `notes`, `requested_time`, `items`) VALUES
(10, 'Nabeel', 63.48, '2489533731', 'completed', '2026-07-20 16:04:21', '2026-07-22 07:39:09', 'Dairy allergy', NULL, NULL),
(11, 'Lauren Mientkiewicz', 28.48, '3303070820', 'completed', '2026-07-20 19:09:44', '2026-07-21 10:53:49', 'Will pickup at 3:30 PM', NULL, NULL),
(12, 'Lauren Mientkiewicz', 28.48, '3303070820', 'completed', '2026-07-20 19:11:08', '2026-07-21 10:48:53', 'Pickup 330', NULL, NULL),
(17, 'Nikki Rhoades', 25.00, '4409756018', 'completed', '2026-07-21 14:26:05', '2026-07-22 06:53:26', 'I\'d love an extra side of pita if that\'s an option you offer', '11:20', '[{\"name\":\"Cheese Pie\",\"quantity\":1,\"price\":5},{\"name\":\"Hummus\",\"quantity\":1,\"price\":10},{\"name\":\"Tabouli\",\"quantity\":1,\"price\":10}]'),
(18, 'Gary Bilchik', 25.98, '2167898701', 'completed', '2026-07-21 15:45:16', '2026-07-22 06:53:13', 'No lettuce or garlic sauce on one chicken shawarma', NULL, '[{\"name\":\"Chicken Shawarma\",\"quantity\":2,\"price\":12.99}]'),
(19, 'Mostafa', 12.99, '70535819', 'completed', '2026-07-22 07:42:59', '2026-07-22 07:51:32', 'this is a test', NULL, '[{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99}]'),
(20, 'Lauren Mientkiewicz', 26.48, '3303070820', 'completed', '2026-07-23 14:31:44', '2026-07-26 09:10:53', 'Please add 2 falafel. Side of garlic sauce.', NULL, '[{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99},{\"name\":\"Zaatar Pie\",\"quantity\":1,\"price\":3.5}]'),
(21, 'Adam Bankhurst', 56.98, '2165437215', 'completed', '2026-07-23 21:24:47', '2026-07-26 09:11:17', NULL, NULL, '[{\"name\":\"Hummus\",\"quantity\":1,\"price\":10},{\"name\":\"French Fries Large\",\"quantity\":1,\"price\":9},{\"name\":\"Grape Leaves\",\"quantity\":1,\"price\":10},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99},{\"name\":\"1\\/2 Zaatar \\/ 1\\/2 Cheese\",\"quantity\":1,\"price\":5}]'),
(22, 'C.C.', 53.49, '2163961520', 'completed', '2026-07-24 17:07:22', '2026-07-26 09:11:07', NULL, NULL, '[{\"name\":\"Grape Leaves\",\"quantity\":1,\"price\":10},{\"name\":\"Rice with Lamb\",\"quantity\":1,\"price\":12},{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Spinach & Feta Pie\",\"quantity\":3,\"price\":4.5},{\"name\":\"Meat Pie\",\"quantity\":1,\"price\":5}]'),
(23, 'alisha clark', 46.98, '2169906850', 'pending', '2026-07-27 16:00:40', NULL, NULL, NULL, '[{\"name\":\"Kibbeh\",\"quantity\":2,\"price\":3.5},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Cheese Pie\",\"quantity\":1,\"price\":5},{\"name\":\"Grape Leaves\",\"quantity\":1,\"price\":10},{\"name\":\"Shish Tawook Wrap\",\"quantity\":1,\"price\":11.99}]'),
(24, 'Gary Bilchik', 9.99, '2167898701', 'pending', '2026-07-27 17:55:04', NULL, 'Light on the sauce', NULL, '[{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99}]'),
(25, 'Ian Verschuren', 39.98, '2169060535', 'pending', '2026-07-28 16:03:48', NULL, NULL, NULL, '[{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Baba Ghanouj\",\"quantity\":1,\"price\":10},{\"name\":\"Shish Tawook Wrap\",\"quantity\":1,\"price\":11.99},{\"name\":\"Meat Pie\",\"quantity\":1,\"price\":5}]'),
(26, 'Nicole schierbaum', 8.99, '2163130722', 'pending', '2026-07-28 17:32:29', NULL, NULL, NULL, '[{\"name\":\"Hummus Wrap\",\"quantity\":1,\"price\":8.99}]'),
(27, 'Dan Krivenki', 12.99, '4403910200', 'sent', '2026-07-29 15:23:37', NULL, NULL, NULL, '[{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99}]'),
(28, 'Jesse Flanagan', 13.49, '4405509153', 'pending', '2026-07-29 16:14:24', NULL, NULL, NULL, '[{\"name\":\"Chicken Tarouk Skewer\",\"quantity\":1,\"price\":4.5},{\"name\":\"Baba Ghanouj Wrap\",\"quantity\":1,\"price\":8.99}]'),
(29, 'Sam Peters', 32.48, '2347163832', 'pending', '2026-07-29 19:17:08', NULL, NULL, NULL, '[{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Lamb Kabob Wrap\",\"quantity\":1,\"price\":13.99},{\"name\":\"Falafel (6 pcs)\",\"quantity\":1,\"price\":5.5}]'),
(30, 'Lauren Mientkiewicz', 38.48, '3303070820', 'pending', '2026-08-01 19:17:25', NULL, NULL, '16:15', '[{\"name\":\"Falafel (6 pcs)\",\"quantity\":1,\"price\":5.5},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Zaatar Pie\",\"quantity\":2,\"price\":3.5}]'),
(31, 'Santana', 15.49, '2163740869', 'pending', '2026-08-03 18:33:52', NULL, NULL, NULL, '[{\"name\":\"Kafta Wrap\",\"quantity\":1,\"price\":11.99},{\"name\":\"Kibbeh\",\"quantity\":1,\"price\":3.5}]'),
(32, 'salem', 21.00, '6146018287', 'pending', '2026-08-03 21:04:57', NULL, NULL, NULL, '[{\"name\":\"Lamb Kabob Skewer\",\"quantity\":2,\"price\":5.5},{\"name\":\"Tabouli\",\"quantity\":1,\"price\":10}]'),
(33, 'Michael', 262.00, '2166401510', 'pending', '2026-08-03 23:41:34', NULL, '8 adults and 8 kids plus 2 toddlers', '17:00', '[{\"name\":\"10 PEOPLE COMBO\",\"quantity\":1,\"price\":190},{\"name\":\"Lamb Kabob Skewer\",\"quantity\":6,\"price\":5.5},{\"name\":\"Moujadara\",\"quantity\":1,\"price\":10},{\"name\":\"Chicken Tarouk Skewer\",\"quantity\":2,\"price\":4.5},{\"name\":\"Kafta Kabob Skewer\",\"quantity\":2,\"price\":4.5},{\"name\":\"Beef Kabob Skewer\",\"quantity\":2,\"price\":5.5}]'),
(34, 'Nabil', 99.00, '9495003155', 'pending', '2026-08-04 13:09:29', NULL, NULL, NULL, '[{\"name\":\"Falafel (12 pcs)\",\"quantity\":1,\"price\":10},{\"name\":\"Hummus\",\"quantity\":1,\"price\":10},{\"name\":\"Tabouli\",\"quantity\":1,\"price\":10},{\"name\":\"Grape Leaves\",\"quantity\":1,\"price\":10},{\"name\":\"Chicken Tarouk Skewer\",\"quantity\":6,\"price\":4.5},{\"name\":\"Beef Kabob Skewer\",\"quantity\":4,\"price\":5.5},{\"name\":\"Baba Ghanouj\",\"quantity\":1,\"price\":10}]'),
(35, 'Gary Bilchik', 9.99, '2167898701', 'pending', '2026-08-04 16:20:20', NULL, NULL, NULL, '[{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99}]'),
(36, 'Mya Eady', 115.95, '2163390935', 'pending', '2026-08-04 16:39:43', NULL, 'Please no lettuce', NULL, '[{\"name\":\"Gyro Wrap\",\"quantity\":3,\"price\":9.99},{\"name\":\"Spinach & Feta Pie\",\"quantity\":2,\"price\":4.5},{\"name\":\"French Fries Large\",\"quantity\":3,\"price\":9},{\"name\":\"Lamb Kabob Skewer\",\"quantity\":1,\"price\":5.5},{\"name\":\"Meat Pie\",\"quantity\":1,\"price\":5},{\"name\":\"Spinach Pie\",\"quantity\":1,\"price\":4},{\"name\":\"Lamb Kabob Wrap\",\"quantity\":1,\"price\":13.99},{\"name\":\"French Fries Small\",\"quantity\":1,\"price\":5},{\"name\":\"Zaatar Pie\",\"quantity\":1,\"price\":3.5},{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99}]'),
(37, 'Lauren Mientkiewicz', 31.48, '3303070820', 'pending', '2026-08-05 20:03:50', NULL, 'Please include garlic sauce and hot sauce. Will also get a small side of the eggplant. Thanks!!', NULL, '[{\"name\":\"Falafel (6 pcs)\",\"quantity\":1,\"price\":5.5},{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99}]'),
(38, 'Santana', 11.99, '2163740869', 'pending', '2026-08-05 21:37:35', NULL, NULL, NULL, '[{\"name\":\"Shish Tawook Wrap\",\"quantity\":1,\"price\":11.99}]'),
(39, 'Aron', 41.00, '4403762550', 'pending', '2026-08-06 21:10:11', NULL, NULL, '17:35', '[{\"name\":\"Kafta Kabob Skewer\",\"quantity\":2,\"price\":4.5},{\"name\":\"Beef Kabob Skewer\",\"quantity\":2,\"price\":5.5},{\"name\":\"Lamb Kabob Skewer\",\"quantity\":2,\"price\":5.5},{\"name\":\"Hummus\",\"quantity\":1,\"price\":10}]'),
(40, 'Jeff Bennett', 51.48, '2163901120', 'pending', '2026-08-07 20:59:47', NULL, NULL, '17:45', '[{\"name\":\"Falafel (6 pcs)\",\"quantity\":1,\"price\":5.5},{\"name\":\"Fattoush\",\"quantity\":1,\"price\":10},{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Lamb Kabob Skewer\",\"quantity\":1,\"price\":5.5},{\"name\":\"Spinach & Feta Pie\",\"quantity\":1,\"price\":4.5}]'),
(41, 'Shaza', 79.99, '2343807164', 'pending', '2026-08-08 16:16:34', NULL, NULL, NULL, '[{\"name\":\"Kishek Pie\",\"quantity\":1,\"price\":5},{\"name\":\"Spinach & Feta Pie\",\"quantity\":2,\"price\":4.5},{\"name\":\"Meat Pie\",\"quantity\":4,\"price\":5},{\"name\":\"Spinach Pie\",\"quantity\":1,\"price\":4},{\"name\":\"Zaatar Pie\",\"quantity\":2,\"price\":3.5},{\"name\":\"Falafel Wrap\",\"quantity\":1,\"price\":8.99},{\"name\":\"Cheese Pie\",\"quantity\":2,\"price\":5},{\"name\":\"1\\/2 Zaatar \\/ 1\\/2 Cheese\",\"quantity\":1,\"price\":5},{\"name\":\"Shankleesh\",\"quantity\":1,\"price\":11}]'),
(42, 'Susan Raphaely', 28.50, '2162874998', 'pending', '2026-08-08 21:11:24', NULL, NULL, NULL, '[{\"name\":\"Spinach Pie\",\"quantity\":1,\"price\":4},{\"name\":\"Lamb Kabob Skewer\",\"quantity\":2,\"price\":5.5},{\"name\":\"Moujadara\",\"quantity\":1,\"price\":10},{\"name\":\"Zaatar Pie\",\"quantity\":1,\"price\":3.5}]'),
(43, 'Musa', 13.99, '3472257899', 'pending', '2026-08-10 16:40:47', NULL, NULL, NULL, '[{\"name\":\"Lamb Kabob Wrap\",\"quantity\":1,\"price\":13.99}]'),
(44, 'Musa Abdul-Basser', 43.47, '3472257899', 'pending', '2026-08-10 21:11:19', NULL, NULL, NULL, '[{\"name\":\"Kafta Kabob Skewer\",\"quantity\":1,\"price\":4.5},{\"name\":\"Chicken Shawarma\",\"quantity\":2,\"price\":12.99},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99}]'),
(45, 'Dennis', 8.99, '2162720447', 'pending', '2026-08-11 16:49:13', NULL, 'No tomato’s please', NULL, '[{\"name\":\"Falafel Wrap\",\"quantity\":1,\"price\":8.99}]'),
(46, 'Gary Bilchik', 9.99, '2167898701', 'pending', '2026-08-11 16:54:32', NULL, NULL, NULL, '[{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99}]'),
(47, 'Musa Abdul-Basser', 53.46, '3472257899', 'pending', '2026-08-11 22:37:36', NULL, NULL, NULL, '[{\"name\":\"Kafta Kabob Skewer\",\"quantity\":1,\"price\":4.5},{\"name\":\"Chicken Shawarma\",\"quantity\":3,\"price\":12.99},{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99}]'),
(48, 'Musa Abdul-Basser', 53.46, '3472257899', 'pending', '2026-08-11 22:37:37', NULL, NULL, NULL, '[{\"name\":\"Kafta Kabob Skewer\",\"quantity\":1,\"price\":4.5},{\"name\":\"Chicken Shawarma\",\"quantity\":3,\"price\":12.99},{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99}]'),
(49, 'Katie Pestak', 50.98, '2164098415', 'pending', '2026-08-12 16:31:40', NULL, NULL, '15:30', '[{\"name\":\"Hummus\",\"quantity\":1,\"price\":10},{\"name\":\"Cheese Pie\",\"quantity\":1,\"price\":5},{\"name\":\"Beef Kabob Skewer\",\"quantity\":1,\"price\":5.5},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Kafta Kabob Skewer\",\"quantity\":1,\"price\":4.5}]'),
(50, 'Santana', 16.99, '2163740869', 'pending', '2026-08-12 18:56:05', NULL, NULL, NULL, '[{\"name\":\"Shish Tawook Wrap\",\"quantity\":1,\"price\":11.99},{\"name\":\"Cheese Pie\",\"quantity\":1,\"price\":5}]'),
(51, 'Yvonne Weatherspoon', 9.99, '2165095357', 'pending', '2026-08-13 15:15:52', NULL, NULL, NULL, '[{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99}]'),
(52, 'Musa', 50.96, '3472257899', 'pending', '2026-08-14 21:42:10', NULL, NULL, NULL, '[{\"name\":\"Chicken Shawarma\",\"quantity\":2,\"price\":12.99},{\"name\":\"Shish Tawook Wrap\",\"quantity\":1,\"price\":11.99},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99}]'),
(53, 'Nadia Mansour', 75.45, '2163727459', 'pending', '2026-08-15 15:56:06', NULL, NULL, NULL, '[{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Beef Shawarma\",\"quantity\":2,\"price\":12.99},{\"name\":\"Falafel Wrap\",\"quantity\":1,\"price\":8.99},{\"name\":\"Shish Tawook Wrap\",\"quantity\":1,\"price\":11.99},{\"name\":\"Falafel (6 pcs)\",\"quantity\":1,\"price\":5.5},{\"name\":\"1\\/2 Zaatar \\/ 1\\/2 Cheese\",\"quantity\":2,\"price\":5}]'),
(54, 'Michelle Gerstenhaber', 34.50, '2164090875', 'pending', '2026-08-15 19:17:19', NULL, NULL, NULL, '[{\"name\":\"Grape Leaves\",\"quantity\":2,\"price\":10},{\"name\":\"1\\/2 Zaatar \\/ 1\\/2 Cheese\",\"quantity\":1,\"price\":5},{\"name\":\"Kishek Pie\",\"quantity\":1,\"price\":5},{\"name\":\"Spinach & Feta Pie\",\"quantity\":1,\"price\":4.5}]'),
(55, 'Amel Ahmed', 85.96, '2165716964', 'pending', '2026-08-15 21:37:57', NULL, NULL, NULL, '[{\"name\":\"Kafta Wrap\",\"quantity\":1,\"price\":11.99},{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Cheese Pie\",\"quantity\":1,\"price\":5},{\"name\":\"Rice with Lamb\",\"quantity\":2,\"price\":12},{\"name\":\"Lamb Kabob Wrap\",\"quantity\":1,\"price\":13.99},{\"name\":\"French Fries Small\",\"quantity\":1,\"price\":5},{\"name\":\"Beef Shawarma\",\"quantity\":1,\"price\":12.99}]'),
(56, 'Omar Elghanam', 173.89, '2164571812', 'pending', '2026-08-15 21:50:39', NULL, NULL, NULL, '[{\"name\":\"Shish Kabob Wrap\",\"quantity\":2,\"price\":12.99},{\"name\":\"Lamb Kabob Wrap\",\"quantity\":1,\"price\":13.99},{\"name\":\"Kafta Wrap\",\"quantity\":2,\"price\":11.99},{\"name\":\"Chicken Shawarma\",\"quantity\":3,\"price\":12.99},{\"name\":\"Beef Shawarma\",\"quantity\":3,\"price\":12.99},{\"name\":\"French Fries Large\",\"quantity\":3,\"price\":9},{\"name\":\"Cheese Pie\",\"quantity\":1,\"price\":5}]'),
(57, 'Courtenay', 25.48, '2164772738', 'pending', '2026-08-17 14:04:09', NULL, NULL, NULL, '[{\"name\":\"Kibbeh\",\"quantity\":1,\"price\":3.5},{\"name\":\"Gyro Wrap\",\"quantity\":1,\"price\":9.99},{\"name\":\"Kafta Wrap\",\"quantity\":1,\"price\":11.99}]'),
(58, 'Jesse Flanagan', 11.99, '4405509153', 'pending', '2026-08-17 15:47:11', NULL, 'No pickles please', NULL, '[{\"name\":\"Shish Tawook Wrap\",\"quantity\":1,\"price\":11.99}]'),
(59, 'Santana', 21.98, '2163740869', 'pending', '2026-08-17 18:38:08', NULL, 'tzatziki sauce on side please', NULL, '[{\"name\":\"Shish Kabob Wrap\",\"quantity\":1,\"price\":12.99},{\"name\":\"French Fries Wrap\",\"quantity\":1,\"price\":8.99}]'),
(60, 'Lisa Small', 19.49, '2168545731', 'pending', '2026-08-18 16:29:38', NULL, NULL, NULL, '[{\"name\":\"Lamb Kabob Wrap\",\"quantity\":1,\"price\":13.99},{\"name\":\"Falafel (6 pcs)\",\"quantity\":1,\"price\":5.5}]'),
(61, 'Tom G', 15.50, '4405208330', 'pending', '2026-08-18 17:34:38', NULL, 'Lemon wedges on the side please :)', NULL, '[{\"name\":\"Beef Kabob Skewer\",\"quantity\":1,\"price\":5.5},{\"name\":\"Tabouli\",\"quantity\":1,\"price\":10}]'),
(62, 'Cornell Blake', 41.99, '2166189239', 'pending', '2026-08-18 20:59:54', NULL, NULL, NULL, '[{\"name\":\"Zaatar Pie\",\"quantity\":1,\"price\":3.5},{\"name\":\"Beef Shawarma Bowl\",\"quantity\":1,\"price\":20},{\"name\":\"Chicken Shawarma\",\"quantity\":1,\"price\":12.99},{\"name\":\"Falafel (6 pcs)\",\"quantity\":1,\"price\":5.5}]');

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

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `restaurant_name`, `restaurant_logo`, `restaurant_email`, `restaurant_phone`, `restaurant_address`, `restaurant_maps`, `restaurant_description`, `opening_hours`, `opening_title`, `home_bg`, `menu_bg`, `contact_bg`, `whatsapp_number`, `instagram_url`, `facebook_url`, `chat_id`, `bot_token`, `country_code`, `order_method`, `banner1_visible`, `banner2_visible`, `banner1_t1`, `banner1_t2`, `banner1_t3`, `banner2_t1`, `banner2_t2`, `banner2_t3`, `banner2_t4`, `about_title`, `about_subtitle`, `about_desc1`, `about_desc2`, `about_image`, `about_chef_image`, `about_chef_title`, `about_chef_subtitle`, `about_chef_name`, `about_chef_bio1`, `about_chef_bio2`, `about_years`, `about_years_label`, `about_bg`, `values_title`, `values_subtitle`, `values_desc`, `value1_icon`, `value1_title`, `value1_desc`, `value2_icon`, `value2_title`, `value2_desc`, `value3_icon`, `value3_title`, `value3_desc`, `value4_icon`, `value4_title`, `value4_desc`, `show_cart`, `deepseek_api_key`, `notify_contact_telegram`) VALUES
(1, 'Nabil Mediterranean Food', 'assets/images/admin/bgs/logo-6a26ab4c72dac9.22160252.webp', 'nabilskitchen@outlook.com', '216-245-6110', '4640 Richmond Road - 200 Warrensville, OH 44128', 'https://maps.app.goo.gl/DWDCtNb15gPvEGdB9?g_st=aw', '', '9:00AM - 6:00PM', 'Monday - Saturday', 'assets/images/admin/bgs/home_1779008244_WhatsApp Image 2026-05-16 at 5.21.26 PM.jpeg', 'assets/images/admin/bgs/menu_1779008244_WhatsApp Image 2026-05-16 at 5.16.51 PMmm.jpeg', 'assets/images/admin/bgs/contact_1779008244_WhatsApp Image 2026-05-16 at 5.16.51 PMm.jpeg', '216-245-6110', '', '', 7099289966, 'YOUR_TELEGRAM_BOT_TOKEN', '+1', 'telegram', 1, 1, 'THANK YOU FOR SUPPORTING LOCAL BUSINESS', 'Made with fresh ingredients & lots of love', 'AUTHENTIC MEDITERRANEAN FLAVOR', 'FRESH INGREDIENTS', 'MADE DAILY', 'AUTHENTIC RECIPES', 'MADE WITH LOTS OF LOVE ❤️', 'Flavors Crafted With Heritage & Love', 'Our Legacy', '', '', 'assets/images/admin/bgs/about_story.png', 'assets/images/admin/bgs/about_chef.png', 'The Passion Behind the Plate', 'Handcrafted Culinary Artistry', 'Chef Nabil', '', '', '15+', 'Years of Tradition', 'assets/images/admin/bgs/about_bg_1779314421_VIBE-6a097bdeec25b5.78933111.jpeg', 'What We Stand For', 'Our Principles', 'Our commitment to authenticity and excellence shapes everything we do in our kitchen.', 'fas fa-seedling', '100% Fresh Daily', 'We source the freshest local vegetables, premium meats, and hand-picked herbs every morning to ensure quality you can taste.', 'fas fa-scroll', 'Authentic Recipes', 'Our dishes are prepared using traditional Lebanese and Mediterranean methods, honoring culinary secrets preserved for decades.', 'fas fa-heart', 'Prepared With Love', 'We believe that food should warm the soul. Every meal is cooked with the same passion and dedication as if it were for our own family.', 'fas fa-hands-helping', 'Warm Hospitality', 'To us, every guest is family. We welcome you with open arms and strive to make your dining experience comfortable and memorable.', 1, 'YOUR_DEEPSEEK_API_KEY', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
