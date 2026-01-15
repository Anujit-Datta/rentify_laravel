-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 05:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

-- DB Credentials:
-- $servername = "mysql-206984-0.cloudclusters.net:10010";
-- $username = "admin"; 
-- $password = "3rqVWFjR";      
-- $dbname = "house_renting";

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `house_renting`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_logs`
--

CREATE TABLE `action_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `security_question` varchar(255) NOT NULL,
  `security_answer` varchar(255) NOT NULL,
  `is_blocked` tinyint(1) DEFAULT 0,
  `failed_attempts` int(11) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `is_otp_verified` tinyint(1) DEFAULT 0,
  `role` varchar(20) NOT NULL DEFAULT 'admin',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `security_question`, `security_answer`, `is_blocked`, `failed_attempts`, `last_login`, `otp_code`, `otp_expires_at`, `is_otp_verified`, `role`, `user_id`) VALUES
(8, 'Admin1', 'admin1@rentify.com', '$2y$10$F5FjVCYifignEOVH0NSHDOn5fbXssy3F/c7ZsXMJknn.jBdxY6AH.', 'What is your secret code?', 'shadowfox', 0, 0, '2026-01-12 23:45:08', NULL, NULL, 0, 'admin', 38),
(9, 'Admin2', 'admin2@rentify.com', '$2y$10$lgMPoZ5.GXqjeIzikyUxSefQc4UxT9ubAuqkG79kWJICEmI2tkFK.', 'What was your first school’s name?', 'sunrise', 0, 0, NULL, NULL, NULL, 0, 'admin', 38),
(10, 'Admin3', 'admin3@rentify.com', '$2y$10$1j8BDg7b/IbaUsYXwsGZheMg1G5vxucJ1OLqUvQjXAPIvhiLQ4JGG', 'What is your favorite city?', 'kyotodream', 0, 0, NULL, NULL, NULL, 0, 'admin', 38);

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(255) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `target_user_name` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `admin_id`, `admin_name`, `action`, `description`, `target_user_id`, `target_user_name`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 38, 'Admin1', 'accessed_admin_chat', 'Admin opened chat interface', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 14:01:28'),
(2, 38, 'Admin1', 'accessed_admin_chat', 'Admin opened chat interface', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 14:02:20'),
(3, 38, 'Admin1', 'accessed_admin_chat', 'Admin opened chat interface', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 14:04:28'),
(4, 8, 'Admin1', 'viewed_user_chat', 'Opened chat conversation with Tanvir (tenant)', 23, 'Tanvir', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 14:04:29'),
(5, 8, 'Admin1', 'sent_message', 'Sent message to Tanvir', 23, 'Tanvir', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 14:04:34'),
(6, 38, 'Admin1', 'accessed_admin_chat', 'Admin opened chat interface', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-12 21:44:22'),
(7, 38, 'Admin1', 'accessed_admin_chat', 'Admin opened chat interface', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-12 23:45:11'),
(8, 8, 'Admin1', 'viewed_user_chat', 'Opened chat conversation with Tanvir (tenant)', 23, 'Tanvir', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-12 23:45:14'),
(9, 8, 'Admin1', 'sent_message', 'Sent message to Tanvir', 23, 'Tanvir', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-12 23:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `admin_allowed_ips`
--

CREATE TABLE `admin_allowed_ips` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `added_by` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_used` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_allowed_ips`
--

INSERT INTO `admin_allowed_ips` (`id`, `ip_address`, `description`, `admin_id`, `added_by`, `is_active`, `created_at`, `last_used`) VALUES
(1, '127.0.0.1', 'Localhost (Development)', NULL, 'System', 0, '2026-01-11 14:52:34', NULL),
(2, '::1', 'Localhost IPv6', NULL, 'System', 0, '2026-01-11 14:52:34', '2026-01-11 15:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_users`
--

CREATE TABLE `blocked_users` (
  `id` int(11) NOT NULL,
  `blocker_id` int(11) NOT NULL,
  `blocked_id` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `blocked_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` int(11) NOT NULL,
  `contract_id` varchar(100) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `landlord_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `rental_request_id` int(11) DEFAULT NULL,
  `contract_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`contract_data`)),
  `contract_hash` varchar(64) NOT NULL,
  `pdf_filename` varchar(255) NOT NULL,
  `pdf_filepath` varchar(500) NOT NULL,
  `tenant_signature` text DEFAULT NULL,
  `tenant_signed_at` timestamp NULL DEFAULT NULL,
  `landlord_signature` text DEFAULT NULL,
  `landlord_signed_at` timestamp NULL DEFAULT NULL,
  `verification_url` varchar(500) NOT NULL,
  `qr_code_data` text DEFAULT NULL,
  `status` enum('draft','pending_signatures','partially_signed','fully_signed','active','expired','terminated') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activated_at` timestamp NULL DEFAULT NULL,
  `expires_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_terms`
--

CREATE TABLE `contract_terms` (
  `id` int(11) NOT NULL,
  `contract_id` varchar(100) NOT NULL,
  `monthly_rent` decimal(10,2) NOT NULL,
  `security_deposit` decimal(10,2) NOT NULL,
  `advance_payment` decimal(10,2) DEFAULT 0.00,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `duration_months` int(11) DEFAULT NULL,
  `payment_day` int(11) DEFAULT 1,
  `late_fee_per_day` decimal(10,2) DEFAULT 0.00,
  `utilities_included` enum('all','partial','none') DEFAULT 'none',
  `electricity_included` tinyint(1) DEFAULT 0,
  `water_included` tinyint(1) DEFAULT 0,
  `gas_included` tinyint(1) DEFAULT 0,
  `internet_included` tinyint(1) DEFAULT 0,
  `maintenance_by` enum('landlord','tenant','shared') DEFAULT 'landlord',
  `major_repairs_by` enum('landlord','tenant') DEFAULT 'landlord',
  `pets_allowed` tinyint(1) DEFAULT 0,
  `smoking_allowed` tinyint(1) DEFAULT 0,
  `subletting_allowed` tinyint(1) DEFAULT 0,
  `guests_allowed` tinyint(1) DEFAULT 1,
  `max_occupants` int(11) DEFAULT 2,
  `tenant_notice_days` int(11) DEFAULT 30,
  `landlord_notice_days` int(11) DEFAULT 60,
  `special_terms` text DEFAULT NULL,
  `additional_clauses` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_verifications`
--

CREATE TABLE `contract_verifications` (
  `id` int(11) NOT NULL,
  `contract_id` varchar(100) NOT NULL,
  `verified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_result` enum('valid','tampered','not_found','signature_invalid') NOT NULL,
  `verification_type` enum('qr_scan','manual','api') DEFAULT 'qr_scan',
  `hash_match` tinyint(1) NOT NULL,
  `tenant_signature_valid` tinyint(1) DEFAULT NULL,
  `landlord_signature_valid` tinyint(1) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `verified_by_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favourites`
--

INSERT INTO `favourites` (`id`, `tenant_id`, `property_id`, `created_at`) VALUES
(1, 1, 1, '2025-10-09 04:59:48'),
(3, 32, 22, '2026-01-06 15:48:06');

-- --------------------------------------------------------

--
-- Table structure for table `landlords`
--

CREATE TABLE `landlords` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `experience` varchar(100) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `hobby` varchar(100) DEFAULT NULL,
  `pet` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landlords`
--

INSERT INTO `landlords` (`id`, `full_name`, `gender`, `age`, `occupation`, `experience`, `about`, `profile_pic`, `hobby`, `pet`, `user_id`, `name`) VALUES
(2, NULL, NULL, NULL, NULL, NULL, NULL, '../php/uploads/default.png', NULL, NULL, 32, 'Rakib'),
(4, 'Afreen2', NULL, NULL, NULL, NULL, NULL, 'uploads/default.png', NULL, NULL, 35, 'Afreen2'),
(5, 'Mu', NULL, NULL, NULL, NULL, NULL, 'uploads/default.png', NULL, NULL, 40, 'Mu');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `success` tinyint(1) DEFAULT 0,
  `attempt_time` datetime DEFAULT current_timestamp(),
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `success`, `attempt_time`, `user_agent`) VALUES
(23, 'admin1@rentify.com', '::1', 1, '2026-01-12 21:37:30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(24, 'admin1@rentify.com', '::1', 1, '2026-01-12 21:37:50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(25, 'mrk243719@gmail.com', '::1', 1, '2026-01-12 21:38:03', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(26, 'admin1@rentify.com', '::1', 1, '2026-01-12 21:43:38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(27, 'mrk243719@gmail.com', '::1', 1, '2026-01-12 21:56:05', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(28, 'admin1@rentify.com', '::1', 1, '2026-01-12 23:45:03', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(29, 'rakib12@gmail.com', '::1', 1, '2026-01-13 09:03:44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `receiver_role` varchar(20) NOT NULL DEFAULT 'tenant',
  `priority` enum('normal','high') DEFAULT 'normal',
  `sender_role` varchar(20) NOT NULL DEFAULT 'tenant',
  `status` enum('sent','delivered','seen') DEFAULT 'sent',
  `seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `file_path`, `timestamp`, `is_active`, `receiver_role`, `priority`, `sender_role`, `status`, `seen`) VALUES
(73, 23, 38, 'h', NULL, '2026-01-11 14:03:10', 1, 'admin', 'normal', 'tenant', 'sent', 0),
(74, 23, 38, 'k', NULL, '2026-01-11 14:03:15', 1, 'admin', 'normal', 'tenant', 'sent', 0),
(75, 23, 38, 'huyishdiua', NULL, '2026-01-11 14:03:17', 1, 'admin', 'normal', 'tenant', 'sent', 0),
(76, 38, 23, 'hellllllllo', NULL, '2026-01-11 14:04:34', 1, 'tenant', 'normal', 'admin', 'seen', 1),
(77, 23, 32, 'hi', NULL, '2026-01-11 14:31:13', 1, 'landlord', 'normal', 'tenant', 'seen', 1),
(78, 23, 38, 'hi', NULL, '2026-01-12 23:44:35', 1, 'admin', 'normal', 'tenant', 'sent', 0),
(79, 38, 23, 'ho', NULL, '2026-01-12 23:45:18', 1, 'tenant', 'normal', 'admin', 'sent', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('payment_pending','payment_confirmed','receipt_generated','due_reminder''contract','payment','request','general') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `link` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `related_id`, `is_read`, `created_at`, `link`) VALUES
(26, 32, '', 'New Rental Request', 'New rental request from Tanvir for your property.', 22, 0, '2026-01-12 17:38:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pending_users`
--

CREATE TABLE `pending_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('tenant','landlord') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(15) DEFAULT NULL,
  `phoneVerified` tinyint(1) NOT NULL DEFAULT 0,
  `emailVerified` tinyint(1) NOT NULL DEFAULT 0,
  `nid_number` varchar(50) DEFAULT NULL,
  `nid_front` varchar(255) DEFAULT NULL,
  `nid_back` varchar(255) DEFAULT NULL,
  `is_landlord_verified` tinyint(1) NOT NULL DEFAULT 0,
  `otp` int(6) DEFAULT NULL,
  `otp_time` datetime DEFAULT NULL,
  `admin_review_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `otp_expires_at` datetime DEFAULT NULL,
  `verify_type` varchar(50) DEFAULT 'email'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `rent` int(11) NOT NULL,
  `landlord` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT 1,
  `property_type` varchar(50) DEFAULT 'Apartment',
  `description` text DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `posted_date` date NOT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `size` varchar(50) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT 1,
  `floor` varchar(50) DEFAULT NULL,
  `parking` tinyint(1) DEFAULT 0,
  `furnished` tinyint(1) DEFAULT 0,
  `map_embed` text DEFAULT NULL,
  `floor_plan` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `status` enum('Rent','Sell') NOT NULL DEFAULT 'Rent',
  `rental_type` enum('sublet','commercial','family','bachelor','roommate','all') NOT NULL,
  `is_building` tinyint(1) DEFAULT 0,
  `total_floors` int(11) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `thumbnail` varchar(255) DEFAULT NULL,
  `landlord_id` int(11) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `property_name`, `location`, `rent`, `landlord`, `image`, `bedrooms`, `property_type`, `description`, `available`, `posted_date`, `featured`, `size`, `bathrooms`, `floor`, `parking`, `furnished`, `map_embed`, `floor_plan`, `latitude`, `longitude`, `status`, `rental_type`, `is_building`, `total_floors`, `created_at`, `thumbnail`, `landlord_id`, `is_verified`) VALUES
(20, 'Green View Apartment', 'Dhanmondi, Dhaka, Bangladesh', 45000, 'Rakib', 'uploads/Copilot_20251205_192529.png', 3, 'Apartment', 'A spacious 3-bedroom apartment located in the heart of Dhanmondi. Features include modern tiled flooring, large balconies with city views, and a semi-furnished interior with wardrobes and kitchen cabinets. The building has 24/7 security, lift service, and generator backup. Ideal for families seeking a peaceful yet central location.', 1, '2025-12-05', 1, '1,650 sq ft', 3, '6th', 1, 1, 'Latitude: 23.746466, Longitude: 90.376015', NULL, 23.746466, 90.376015, 'Rent', 'family', 0, 1, '2025-12-05 19:32:49', NULL, 32, 1),
(22, 'Green Haven 5-Level Residence', 'House 22, Road 8, Bashundhara R/A, Dhaka', 20000, 'Rakib', 'uploads/WhatsApp Image 2026-01-03 at 07.57.25.jpeg', 1, 'Apartment', 'This 5-level residential building offers comfortable living with well-planned units on each floor. Floors 1–4 are suitable for families, bachelors, or roommates, featuring 2 bedrooms, 2 bathrooms, and a bright living–dining setup. Units are well-ventilated with reliable utility services and optional parking.\r\n\r\nThe 5th floor is dedicated for sublet, ideal for students or working individuals. It includes a bedroom, attached bath, small kitchenette, and access to the rooftop. Clean, private, and budget-friendly.\r\n\r\nThe house is located in a safe neighborhood with grocery shops, restaurants, pharmacies, bus stops, and universities nearby. Perfect for tenants seeking convenience, flexibility, and an organized living environment.', 1, '2026-01-06', 0, '', 1, 'Multiple Floors', 0, 0, NULL, 'uploads/WhatsApp Image 2026-01-03 at 07.57.34 (1).jpeg', NULL, NULL, 'Rent', 'all', 1, 3, '2026-01-06 18:15:00', NULL, 32, 0),
(24, 'City Nest Residence', 'House 14, Block C, Mirpur DOHS, Dhaka', 25000, 'Rakib', 'uploads/WhatsApp Image 2026-01-03 at 07.57.30 (1).jpeg', 1, '0', 'City Nest Five-Story Residence is a well-maintained building located in the quiet and secure area of Mirpur DOHS. Floors 1–4 offer spacious family-friendly units with ample sunlight, tiled interiors, and reliable utilities. Each floor has a clean layout suitable for both families and shared living setups.\r\n\r\nThe top floor sublet unit is perfect for students or young professionals seeking a low-cost, private living space. The room includes an attached bathroom, balcony, and access to a small shared kitchen area.\r\n\r\nThe property is close to shopping malls, parks, mosques, schools, hospitals, and public transport, making it an ideal choice for everyday convenience.', 1, '2026-01-06', 0, '', 1, '0', 0, 0, NULL, 'uploads/WhatsApp Image 2026-01-03 at 07.57.28.jpeg', NULL, NULL, 'Rent', 'all', 1, 2, '2026-01-06 21:11:11', NULL, 32, 0);

-- --------------------------------------------------------

--
-- Table structure for table `property_amenities`
--

CREATE TABLE `property_amenities` (
  `id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `amenity` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_floors`
--

CREATE TABLE `property_floors` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `floor_number` int(11) NOT NULL,
  `total_units` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_floors`
--

INSERT INTO `property_floors` (`id`, `property_id`, `floor_number`, `total_units`, `created_at`) VALUES
(1, 22, 1, 2, '2026-01-06 12:15:00'),
(2, 22, 2, 4, '2026-01-06 12:15:00'),
(3, 22, 3, 2, '2026-01-06 12:15:00'),
(7, 24, 1, 1, '2026-01-06 15:11:11'),
(8, 24, 2, 1, '2026-01-06 15:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `property_gallery`
--

CREATE TABLE `property_gallery` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_gallery`
--

INSERT INTO `property_gallery` (`id`, `property_id`, `image_path`, `uploaded_at`) VALUES
(7, 24, 'uploads/WhatsApp Image 2026-01-03 at 07.57.30.jpeg', '2026-01-06 15:11:11'),
(8, 24, 'uploads/WhatsApp Image 2026-01-03 at 07.57.31 (1).jpeg', '2026-01-06 15:11:11'),
(9, 24, 'uploads/WhatsApp Image 2026-01-03 at 07.57.31.jpeg', '2026-01-06 15:11:11'),
(10, 24, 'uploads/WhatsApp Image 2026-01-03 at 07.57.32.jpeg', '2026-01-06 15:11:11'),
(11, 24, 'uploads/WhatsApp Image 2026-01-03 at 07.57.33 (1).jpeg', '2026-01-06 15:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `property_reports`
--

CREATE TABLE `property_reports` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `report_reason` varchar(100) NOT NULL,
  `report_description` text NOT NULL,
  `screenshots` text DEFAULT NULL,
  `status` enum('pending','reviewed','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_reviews`
--

CREATE TABLE `property_reviews` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_units`
--

CREATE TABLE `property_units` (
  `id` int(11) NOT NULL,
  `floor_id` int(11) NOT NULL DEFAULT 0,
  `property_id` int(11) NOT NULL,
  `unit_name` varchar(100) DEFAULT 'Unit 1',
  `unit_number` int(11) NOT NULL,
  `rent` decimal(10,2) NOT NULL,
  `bedrooms` int(11) DEFAULT 1,
  `bathrooms` int(11) DEFAULT 1,
  `size` varchar(50) DEFAULT NULL,
  `rental_type` varchar(255) DEFAULT 'family',
  `is_sublet` tinyint(1) DEFAULT 0,
  `max_tenants` int(11) DEFAULT 1,
  `current_tenants` int(11) DEFAULT 0,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_units`
--

INSERT INTO `property_units` (`id`, `floor_id`, `property_id`, `unit_name`, `unit_number`, `rent`, `bedrooms`, `bathrooms`, `size`, `rental_type`, `is_sublet`, `max_tenants`, `current_tenants`, `is_available`, `created_at`) VALUES
(1, 1, 22, 'Flat 1', 1, 30000.00, 3, 2, '950', 'family', 0, 1, 0, 1, '2026-01-06 12:15:00'),
(2, 1, 22, 'Flat 2', 2, 30000.00, 3, 2, '950', 'family', 0, 1, 0, 1, '2026-01-06 12:15:00'),
(3, 2, 22, 'Flat 1', 1, 20000.00, 2, 2, '950', 'family', 0, 1, 0, 1, '2026-01-06 12:15:00'),
(4, 2, 22, 'Flat 2', 2, 20000.00, 2, 2, '950', 'family', 0, 1, 0, 1, '2026-01-06 12:15:00'),
(5, 2, 22, 'Flat 3', 3, 20000.00, 2, 2, '950', 'family', 0, 1, 0, 1, '2026-01-06 12:15:00'),
(6, 2, 22, 'Flat 4', 4, 20000.00, 2, 2, '950', 'family', 0, 1, 0, 1, '2026-01-06 12:15:00'),
(7, 3, 22, 'Flat 1', 1, 15000.00, 1, 1, '950', 'bachelor,family,roommate,sublet', 1, 4, 0, 1, '2026-01-06 12:15:00'),
(8, 3, 22, 'Flat 2', 2, 15000.00, 1, 1, '950', 'bachelor,family,roommate,sublet', 1, 4, 0, 1, '2026-01-06 12:15:00'),
(15, 7, 24, 'Unit 1', 1, 25000.00, 1, 1, '0', 'family', 0, 1, 0, 1, '2026-01-06 15:11:11'),
(16, 8, 24, 'Unit 1', 1, 25000.00, 1, 1, '0', 'family', 0, 1, 0, 1, '2026-01-06 15:11:11');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `landlord_id` int(11) DEFAULT NULL,
  `tenant_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','completed') DEFAULT 'active',
  `contract_id` varchar(100) DEFAULT NULL,
  `contract_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tenant_signed` tinyint(1) DEFAULT 0,
  `landlord_signed` tinyint(1) DEFAULT 0,
  `tenant_signature` text DEFAULT NULL,
  `landlord_signature` text DEFAULT NULL,
  `verified` tinyint(4) DEFAULT 0,
  `contract_hash` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rental_requests`
--

CREATE TABLE `rental_requests` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_name` varchar(255) DEFAULT NULL,
  `tenant_name` varchar(255) NOT NULL,
  `tenant_email` varchar(255) NOT NULL,
  `tenant_phone` varchar(20) NOT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `move_in_date` date NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `has_pets` varchar(10) DEFAULT 'no',
  `current_address` text DEFAULT NULL,
  `num_occupants` int(11) DEFAULT 1,
  `occupation` varchar(100) DEFAULT NULL,
  `emergency_contact` text NOT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `terms` tinyint(1) NOT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `contract_file` varchar(255) DEFAULT NULL,
  `contract_id` varchar(100) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rental_requests`
--

INSERT INTO `rental_requests` (`id`, `property_id`, `unit_id`, `tenant_id`, `property_name`, `tenant_name`, `tenant_email`, `tenant_phone`, `national_id`, `move_in_date`, `payment_method`, `has_pets`, `current_address`, `num_occupants`, `occupation`, `emergency_contact`, `emergency_phone`, `notes`, `documents`, `document_path`, `status`, `terms`, `pdf_file`, `request_date`, `contract_file`, `contract_id`, `approved_at`) VALUES
(14, 22, 8, 23, NULL, 'Tanvir', 'mrk243719@gmail.com', '01923456789', '0191028816329', '2026-01-15', 'bank_transfer', 'no', 'gghg, hgjnhkmj, fghnjjgfjnh, fgdfhgn, dgnjhj - 1234', 1, 'student', 'sdsdfd', '1812345670', 'xcvcv', '[\"rental_documents\\/doc_696531856c3af_1768239493.pdf\"]', NULL, 'pending', 1, NULL, '2026-01-12 17:38:13', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rent_payments`
--

CREATE TABLE `rent_payments` (
  `id` int(11) NOT NULL,
  `rental_id` int(11) DEFAULT NULL,
  `tenant_id` int(11) NOT NULL,
  `landlord_id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_month` date NOT NULL,
  `payment_date` date DEFAULT NULL,
  `status` enum('pending','confirmed','rejected') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'hand_cash',
  `is_advance` tinyint(1) DEFAULT 0,
  `note` text DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rent_receipts`
--

CREATE TABLE `rent_receipts` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `tenant_id` int(11) NOT NULL,
  `landlord_id` int(11) NOT NULL,
  `rent_amount` decimal(10,2) NOT NULL,
  `late_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_month` date NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pdf_path` varchar(255) DEFAULT NULL,
  `email_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rent_settings`
--

CREATE TABLE `rent_settings` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `base_rent` decimal(10,2) DEFAULT NULL,
  `include_electricity` tinyint(1) DEFAULT 1,
  `electricity_per_unit` decimal(10,2) DEFAULT 0.00,
  `electricity_meter_rent` decimal(10,2) DEFAULT 0.00,
  `include_water` tinyint(1) DEFAULT 0,
  `water_bill` decimal(10,2) DEFAULT 0.00,
  `include_gas` tinyint(1) DEFAULT 0,
  `gas_bill` decimal(10,2) DEFAULT 0.00,
  `include_service` tinyint(1) DEFAULT 0,
  `service_charge` decimal(10,2) DEFAULT 0.00,
  `include_other` tinyint(1) DEFAULT 0,
  `other_charges` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rent_settings`
--

INSERT INTO `rent_settings` (`id`, `property_id`, `base_rent`, `include_electricity`, `electricity_per_unit`, `electricity_meter_rent`, `include_water`, `water_bill`, `include_gas`, `gas_bill`, `include_service`, `service_charge`, `include_other`, `other_charges`) VALUES
(1, 0, 0.00, 1, 0.00, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00),
(2, 0, 0.00, 1, 0.00, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00),
(3, 12, 1000.00, 1, 0.00, 0.00, 1, 100.00, 1, 500.00, 1, 100.00, 1, 50.00),
(4, 13, 1000.00, 1, 0.00, 0.00, 1, 100.00, 1, 500.00, 1, 100.00, 1, 50.00),
(5, 14, 1000.00, 1, 0.00, 0.00, 0, 100.00, 0, 500.00, 0, 100.00, 0, 50.00),
(6, 15, 1000.00, 1, 0.00, 0.00, 1, 100.00, 1, 500.00, 1, 100.00, 1, 50.00),
(7, 16, 1000.00, 1, 0.00, 0.00, 1, 100.00, 0, 500.00, 1, 100.00, 1, 50.00),
(8, 17, 1000.00, 1, 0.00, 0.00, 1, 100.00, 1, 500.00, 1, 100.00, 1, 50.00),
(9, 18, 1000.00, 1, 0.00, 0.00, 0, 100.00, 0, 500.00, 0, 100.00, 0, 50.00),
(10, 19, 1000.00, 1, 0.00, 0.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00),
(11, 20, 45000.00, 1, 0.00, 0.00, 1, 800.00, 1, 975.00, 1, 3000.00, 0, 0.00),
(12, 21, 45000.00, 1, 0.00, 0.00, 1, 800.00, 1, 975.00, 1, 3000.00, 0, 0.00),
(13, 23, 20000.00, 1, 7.50, 50.00, 1, 100.00, 1, 1000.00, 1, 1000.00, 1, 100.00),
(14, 24, 25000.00, 1, 7.50, 50.00, 1, 100.00, 1, 1000.00, 1, 1000.00, 1, 100.00),
(15, 25, 50000.00, 1, 7.50, 50.00, 0, 0.00, 0, 0.00, 0, 0.00, 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `roommates`
--

CREATE TABLE `roommates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `budget` int(11) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `smoking` enum('Yes','No') DEFAULT NULL,
  `pets` enum('Yes','No') DEFAULT NULL,
  `cleanliness` enum('High','Medium','Low') DEFAULT NULL,
  `about` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_logs`
--

INSERT INTO `security_logs` (`id`, `user_id`, `action`, `ip_address`, `user_agent`, `details`, `created_at`) VALUES
(1, 23, 'password_changed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL, '2026-01-06 05:40:34');

-- --------------------------------------------------------

--
-- Table structure for table `signature_logs`
--

CREATE TABLE `signature_logs` (
  `id` int(11) NOT NULL,
  `contract_id` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_role` enum('tenant','landlord') NOT NULL,
  `signature_data` text NOT NULL,
  `public_key_used` text NOT NULL,
  `signed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` text DEFAULT NULL,
  `signature_valid` tinyint(1) DEFAULT 1,
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `message` text NOT NULL,
  `status` enum('open','in-progress','resolved','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `ticket_number`, `subject`, `category`, `priority`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 23, 'TKT-695CE55E00CC4', 'a thsha sjjdhalknb', 'account', 'medium', 'aaaaaaaaaaaaaaaaaaa udhfushsdjiugdawddsieg  scsufhhx hsudslld d eiendw;', 'open', '2026-01-06 10:35:10', '2026-01-06 10:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_replies`
--

CREATE TABLE `support_ticket_replies` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_staff` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gender` varchar(10) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `preferred_location` varchar(255) DEFAULT NULL,
  `budget_min` int(11) DEFAULT 0,
  `budget_max` int(11) DEFAULT 0,
  `preferred_property_type` varchar(100) DEFAULT NULL,
  `move_in_date` date DEFAULT NULL,
  `family_size` int(11) DEFAULT 1,
  `occupation` varchar(100) DEFAULT NULL,
  `hobby` varchar(100) DEFAULT NULL,
  `pet` varchar(50) DEFAULT NULL,
  `smoker` enum('yes','no') DEFAULT 'no',
  `employed` enum('yes','no') DEFAULT 'yes',
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `full_name`, `email`, `phone`, `location`, `budget`, `bio`, `profile_pic`, `created_at`, `updated_at`, `gender`, `age`, `address`, `preferred_location`, `budget_min`, `budget_max`, `preferred_property_type`, `move_in_date`, `family_size`, `occupation`, `hobby`, `pet`, `smoker`, `employed`, `emergency_contact_name`, `emergency_contact_phone`, `user_id`, `name`) VALUES
(9, 'Tanvir n', '', '', NULL, NULL, NULL, 'uploads/download (1).jpg', '2025-11-21 15:23:23', '2025-12-03 15:09:50', 'Male', 30, NULL, NULL, 0, 0, NULL, NULL, 1, 'student', 'sleeping', 'dog', 'no', 'yes', NULL, NULL, 23, 'Tanvir'),
(11, '', '', '', NULL, NULL, NULL, 'uploads/default.png', '2025-12-29 11:51:32', '2025-12-29 11:51:32', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 1, NULL, NULL, NULL, 'no', 'yes', NULL, NULL, 36, 'Afreen'),
(13, '', '', '', NULL, NULL, NULL, 'uploads/default.png', '2026-01-10 16:17:34', '2026-01-10 16:17:34', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 1, NULL, NULL, NULL, 'no', 'yes', NULL, NULL, 39, 'M');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_reviews`
--

CREATE TABLE `tenant_reviews` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `landlord_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `phoneVerified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `role` varchar(20) NOT NULL,
  `nid_number` varchar(50) DEFAULT NULL,
  `nid_front` varchar(255) DEFAULT NULL,
  `nid_back` varchar(255) DEFAULT NULL,
  `is_landlord_verified` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) DEFAULT 'pending',
  `verification_code` varchar(6) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `public_key` text DEFAULT NULL,
  `private_key` text DEFAULT NULL,
  `key_created` tinyint(1) DEFAULT 0,
  `last_seen` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `phoneVerified`, `created_at`, `is_verified`, `role`, `nid_number`, `nid_front`, `nid_back`, `is_landlord_verified`, `status`, `verification_code`, `reset_token`, `reset_expiry`, `public_key`, `private_key`, `key_created`, `last_seen`) VALUES
(23, 'Tanvir', 'mrk243719@gmail.com', '$2y$10$jzHHf2jBXJFXvGYFGNziOuw1va8gK18yHq1b.ymoYnL9TzPh0MOSe', '+88001234567891', 0, '2025-11-21 15:23:23', 1, 'tenant', NULL, NULL, NULL, 1, 'active', NULL, NULL, NULL, '-----BEGIN RSA PUBLIC KEY-----\nMIIBCgKCAQEA68gAOPBFoVwGDEkBfhBN3/Xp9f38XONtaictYwhT/XHXHAnelqcR\nFqAsU0KGmJV7nEsIdpr4VR9aOf8G/5+AKmtoXzmsiWDR3s57/qJmc5ibqCmoRQkz\nhF+bX5/HgJFax5D33gewel59xEcLU8YatlHomdk2WAKvXLaoSA+ZlTYc4oX7Xnuq\nTYTUuLZ1TN6dYg6i533DkvC/1TuxNJ7/CyfB4+kVE98Oiq+km6uBr4Apr3qg5tKF\n2Cn/mFnOOvI/3w3YYkhHMpSy0y3PiiWfcxjZ47+P4MGX3hm8RhELkGPyVPpaGpQy\n1USmuhB3d+OLwwDUVkaAGJfEOxswT0eANwIDAQAB\n-----END RSA PUBLIC KEY-----\n', '-----BEGIN RSA PRIVATE KEY-----\nMIIEowIBAAKCAQEA68gAOPBFoVwGDEkBfhBN3/Xp9f38XONtaictYwhT/XHXHAne\nlqcRFqAsU0KGmJV7nEsIdpr4VR9aOf8G/5+AKmtoXzmsiWDR3s57/qJmc5ibqCmo\nRQkzhF+bX5/HgJFax5D33gewel59xEcLU8YatlHomdk2WAKvXLaoSA+ZlTYc4oX7\nXnuqTYTUuLZ1TN6dYg6i533DkvC/1TuxNJ7/CyfB4+kVE98Oiq+km6uBr4Apr3qg\n5tKF2Cn/mFnOOvI/3w3YYkhHMpSy0y3PiiWfcxjZ47+P4MGX3hm8RhELkGPyVPpa\nGpQy1USmuhB3d+OLwwDUVkaAGJfEOxswT0eANwIDAQABAoIBAA1tv61+g2JUUASr\ngWOnrar5LsRG6fR0Zr5Q0U6+buIuQkOJ85AX0p5VeXc1TV5e+fWF3hghoJrG68Gf\n2v5i6QIBfT+z+wKvOmTHoXV4Os0pcbwbKBOxsxBxmlmdsddXVmWrxdVy3kQQVUNn\njg3NNUCrDswsYq7482BP1ISXw6lYZqjB7+J2s4IjyslzxEI8wf7xnRZ921LeZIsw\nLakIH9tYN9pSk7RGv7c+TmanI/RkFcURy53FisUbGQaVtQjAeA8540zd4lAgaxgg\nAJl80PcEgAeBoSwTG4VFuljhXSFCuyebpP8wrwCSJZA2pO05of8niqfU+0xR+KK5\nbIiWH1ECgYEA9quFZoSEqOE5IdEQAINH4vQ5GEzfmG80r952OYo4+vriRl60ANgM\n4m44INRR2daaHkdQ76onPSf24LLmbbHk2lLw17vF0BcbkI4FeNFrcmaviKEDswhD\nWkFfsvetmVif1KM2A4Dtw+eRbxsNEoxakTsNSTLePXpSOD01lTrfpr8CgYEA9LML\nkNwXi02lI3ojFMEt3BHf9tLf/sizZHK5LHfryqA2VtFZJyHL1eoKrmLupiMo3NdC\nUnf4bqdR9g4gSsMxCFj+ZCuUQfCHr6eqkqweyVLe/f+HwpyV6qA4oQd2zNSdlX0t\nFLAO433eeK76s6aYp9iR/NcVXA5nOZ1hULSdvIkCgYAj7+FDilL5d7anIpo+OOIk\nQoh+7dQ1lR4L8VEwGN8sUZjdv7Zh2RgndP9gaQ5zujxB5Qn5oMSOuF/TZL0FhkFd\nPXAxPf4XzwYJDWfmrjwFMsioEy1CzBVj9NpIbKUyDiNbC1w3LnV1cStTEJHa/e/D\n8OKCo5KVSljoMKK51xX2aQKBgHIxfBxGGdawjXhY7zYoULZ6wQH2twG3t8AQqcnO\nzQLqcXFikTBp3S4e4o0vLh/qGI+3BQRnaFB7VQx22ysInEFBBrbIf4+mKCwZGskH\n+fYJ7naqYTi4yl2MF+FOEmrUbhXtLLSS1N/lRYUcY3KjNGpf7wEHfrhPIG0KvPdk\n0pfBAoGBAJA50iYUn/De1aF0KiM7Z8kGkNNA0pDo/Fr0BGrag5OhN0PAZ5GgAfnY\nj3zeQmW5qiyKhdGssEDOZjKq34brh5s72J/5mC8WwhSH/VF8ZHKsLGpcZUuCvyo3\n+4oZzGO6j37VHhSmimrntUvsF6xEcsCGVZQnGLDl4jpuiHCOjtZi\n-----END RSA PRIVATE KEY-----\n', 1, '2026-01-12 23:44:29'),
(32, 'Rakib', 'rakib12@gmail.com', '$2y$10$ojdcnU9J9te1XSTQowS/sO8JtKJDnnXe/Y9YDuO81AdebZsVS7VI.', '+88001234567890', 0, '2025-12-05 13:09:16', 1, 'landlord', '89901527897', 'uploads/1764861500_download__1_.jpg', 'uploads/1764861500_download__1_.jpg', 1, 'active', NULL, NULL, NULL, '-----BEGIN RSA PUBLIC KEY-----\nMIIBCgKCAQEA3ySG+KxH7u8IcgjUYbn7DKsE+f7n+/NH117vw5HZqEfTe+yq5zpH\ni5OsyEd0h7P7uu0SlOsRQ+0wzyrNN5GiNStr9bo+ZQcCvdpZ8VMUKfPwIoV5sdGI\nm2sKh6/rmLfaAHGZ1szsABZKL2/JtWFCM8p5wUV5F0xNPhgrLt6YtIoU3A97ckoM\n8iBEZkemAKiJWA5TAJYZhncVqkx6i1AguodaJQ0gaN2WLWUKh6tbSPMwJJQLSahV\nX7emOf6Mg2yYgX8qDLgwr9nfsOxWBFlHhAr2cvi8ON7zsQ5TZsSyseRp9FBpD86T\npPyamXaViD7tUfZMJGODm9q247nOF1LTuwIDAQAB\n-----END RSA PUBLIC KEY-----\n', '-----BEGIN RSA PRIVATE KEY-----\nMIIEowIBAAKCAQEA3ySG+KxH7u8IcgjUYbn7DKsE+f7n+/NH117vw5HZqEfTe+yq\n5zpHi5OsyEd0h7P7uu0SlOsRQ+0wzyrNN5GiNStr9bo+ZQcCvdpZ8VMUKfPwIoV5\nsdGIm2sKh6/rmLfaAHGZ1szsABZKL2/JtWFCM8p5wUV5F0xNPhgrLt6YtIoU3A97\nckoM8iBEZkemAKiJWA5TAJYZhncVqkx6i1AguodaJQ0gaN2WLWUKh6tbSPMwJJQL\nSahVX7emOf6Mg2yYgX8qDLgwr9nfsOxWBFlHhAr2cvi8ON7zsQ5TZsSyseRp9FBp\nD86TpPyamXaViD7tUfZMJGODm9q247nOF1LTuwIDAQABAoIBAGv15EeUMwlrJQ7n\nLrpJnSk35SR/LAY+YlfVWvOoMUK13aBrlRRiohLp1ALib4LolbaO1Rqv0J6ot6DD\n+J4WHADVjVpKTb3bcnwglLupkmvp1hkdsw9RbWKkm2eiub4d1Z+5VtESPGyeXC4v\nS8wxzjH4mIfl8PxQsl6EmwAGOqO986TyG391iIpQzCR+KrKj9LJtsKFohwPrZyd2\nGNbtpWU+pp2DfIdHREGEFMVCgumjkfIZaeJrqAT7uzpuU+IBmxboAvwCW6c+josv\neaSArROcMPEg/drk6L8LhYdnJoGCjRk4iVow5ommzOJsVUtK3N5W7eJGVPEhH9MQ\nKyj7oSECgYEA8npLgDefBF0EEHs/w8EaZNEH9sCBdSYg8GcLqhKlyk4x4N/yf+CU\netKuOEMxWeq2bAA468HzZqeC4Rem7EmckEy6leQHFVAyfzZIRdxJcgokBUvkMwVq\naJyBlsGfPGbPVfUegVcq71HvSul4OC/6tMJlpuB+XL0sapN8GiY+8T8CgYEA65Yy\nrxkSw5orbRG7xZBmVCSSRL8/bdv5LTdkE+T2MfivrIUxJvwxX7+JEtiV+J1zSHCb\n38gfSqAE+OKUe5GT27MtMsIMaCAMN//v4mUlCeuiPyp842am/ZC4fdcylzI9O00k\nIHkpdxdcUN9cLtftFqyI9ScN4bs0YMEksFkDAoUCgYAUkFaS2yS7RAAyPaGcwmWw\noyGfxZe1DbTBbpvIoqg4zgTg4103hx5QhEmOL/ShgYxTAHnTVLGFxTMOT+kyJNQI\nUNmKGLqZDiIaM8pRUmtQJpgDcvo5cX4ivRMoa+dLQhTXX2Bk67WSckpu+fneGgOT\nDOF+E4/j4Y9Awp1FwnSYFQKBgQCIopI+z7H8VFw/Y/uduARBZPyYdrtZAi3gtbGL\ngE3x6aQp0Q4S6E3SlInxrkA880S7jOQ6xByx8WEw4GjxyhsRXzglqjn74ip0CDKf\nAb6bpFhzfjb0xMKxadA23CrxWif9Tt5XHy6XbasTFJioan+NmP/N/5qjgyn7u8mF\npfqC6QKBgHBpriCaOsmWFsM2yOzJWmil1rCygZmkluTO8YsbbWetF8PySo5QYFbh\nmkrF6QVzlz+6kqZJnDZn/ha8kumCS4tCLEggBmUVDmYU7BKO7CkJWlI1UmYyH/ve\ng+jHmlLI3o8KFiUzUxIzN8Qbv2E6HlaDxJKdYY/7t9yGp2LiMw35\n-----END RSA PRIVATE KEY-----\n', 1, '2026-01-10 21:43:59'),
(36, 'Afreen', 'afreen@gmail.com', '$2y$10$iMF8Xs2TqMTHMLZvV9m/ve4X4AT.yT7lzR0NQ/cztfGQmOVqItCTe', '+88001234567890', 0, '2025-12-29 11:51:32', 1, 'tenant', NULL, NULL, NULL, 0, 'pending', NULL, NULL, NULL, NULL, NULL, 0, '2026-01-01 23:24:38'),
(38, 'Admin1', 'admin1@rentify.com', '$2y$10$DT0CcIkQKH1GTsFOEtD4q.1Gv99BQ2./aluUCAWI/WPkwWcfL8t2y', NULL, 0, '2026-01-02 05:58:24', 0, 'admin', NULL, NULL, NULL, 0, 'pending', NULL, NULL, NULL, NULL, NULL, 0, '2026-01-12 23:45:11'),
(39, 'M', 'muttakin12@gmail.com', '$2y$10$XpK0TON4QIeDuyK93B2ZkuWhmi32.ThG70Tfqa6G57H9jG1vEic1C', '+880+8800123456', 0, '2026-01-10 16:17:34', 1, 'tenant', NULL, NULL, NULL, 0, 'pending', NULL, NULL, NULL, NULL, NULL, 0, '2026-01-10 22:17:34'),
(40, 'Mu', 'muttakin123@gmail.com', '$2y$10$pYimHTVvqT9BsCcCuKMQ9uPx5mM5o.qhzvVd9dp37hI68yBHs6RNO', '+880+8800123456', 0, '2026-01-10 16:23:22', 0, 'landlord', '1028816329', 'uploads/1768062104_download.jpeg', 'uploads/1768062104_download__1_.jpeg', 1, 'active', NULL, NULL, NULL, NULL, NULL, 0, '2026-01-10 22:23:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_reports`
--

CREATE TABLE `user_reports` (
  `id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reported_user_id` int(11) NOT NULL,
  `reason` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `screenshot_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','reviewing','resolved','dismissed') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_balances`
--

CREATE TABLE `wallet_balances` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `monthly_added` decimal(10,2) DEFAULT 0.00,
  `last_reset_date` date DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet_balances`
--

INSERT INTO `wallet_balances` (`id`, `user_id`, `balance`, `monthly_added`, `last_reset_date`, `updated_at`) VALUES
(3, 23, 1000.00, 1000.00, '2026-01-09', '2026-01-10 07:37:27');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_type` enum('add_money','rent_payment','refund') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `user_id`, `transaction_type`, `amount`, `payment_method`, `balance_after`, `description`, `transaction_id`, `created_at`) VALUES
(11, 23, 'add_money', 1000.00, 'bkash', 0.00, 'Money added via Bkash', 'TXN17680306476981', '2026-01-10 07:37:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_logs`
--
ALTER TABLE `action_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_admin_user` (`user_id`);

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_target_user` (`target_user_id`);

--
-- Indexes for table `admin_allowed_ips`
--
ALTER TABLE `admin_allowed_ips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `blocked_users`
--
ALTER TABLE `blocked_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_block` (`blocker_id`,`blocked_id`),
  ADD KEY `idx_blocker` (`blocker_id`),
  ADD KEY `idx_blocked` (`blocked_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contract_id` (`contract_id`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_landlord` (`landlord_id`),
  ADD KEY `idx_property` (`property_id`),
  ADD KEY `idx_rental_request` (`rental_request_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_signature_status` (`status`,`tenant_signature`(10),`landlord_signature`(10));

--
-- Indexes for table `contract_terms`
--
ALTER TABLE `contract_terms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contract_id` (`contract_id`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `contract_verifications`
--
ALTER TABLE `contract_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contract` (`contract_id`),
  ADD KEY `idx_verified_at` (`verified_at`),
  ADD KEY `idx_result` (`verification_result`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landlords`
--
ALTER TABLE `landlords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_time` (`email`,`attempt_time`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempt_time`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender_receiver` (`sender_id`,`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`);

--
-- Indexes for table `pending_users`
--
ALTER TABLE `pending_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_properties_created_at` (`created_at`);

--
-- Indexes for table `property_amenities`
--
ALTER TABLE `property_amenities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_floors`
--
ALTER TABLE `property_floors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_gallery`
--
ALTER TABLE `property_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_reports`
--
ALTER TABLE `property_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_reviews`
--
ALTER TABLE `property_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `property_units`
--
ALTER TABLE `property_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `floor_id` (`floor_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `idx_property` (`property_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_property_status` (`property_id`,`status`),
  ADD KEY `idx_contract_id` (`contract_id`);

--
-- Indexes for table `rent_payments`
--
ALTER TABLE `rent_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `landlord_id` (`landlord_id`),
  ADD KEY `idx_rental` (`rental_id`);

--
-- Indexes for table `rent_receipts`
--
ALTER TABLE `rent_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `landlord_id` (`landlord_id`);

--
-- Indexes for table `rent_settings`
--
ALTER TABLE `rent_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roommates`
--
ALTER TABLE `roommates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `signature_logs`
--
ALTER TABLE `signature_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contract` (`contract_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_signed_at` (`signed_at`),
  ADD KEY `idx_user_contract` (`user_id`,`contract_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `idx_ticket_number` (`ticket_number`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_priority` (`priority`);

--
-- Indexes for table `support_ticket_replies`
--
ALTER TABLE `support_ticket_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `tenant_reviews`
--
ALTER TABLE `tenant_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `landlord_id` (`landlord_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `user_reports`
--
ALTER TABLE `user_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reporter` (`reporter_id`),
  ADD KEY `idx_reported` (`reported_user_id`);

--
-- Indexes for table `wallet_balances`
--
ALTER TABLE `wallet_balances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_logs`
--
ALTER TABLE `action_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_allowed_ips`
--
ALTER TABLE `admin_allowed_ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blocked_users`
--
ALTER TABLE `blocked_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `contract_terms`
--
ALTER TABLE `contract_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_verifications`
--
ALTER TABLE `contract_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favourites`
--
ALTER TABLE `favourites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `landlords`
--
ALTER TABLE `landlords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `pending_users`
--
ALTER TABLE `pending_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `property_amenities`
--
ALTER TABLE `property_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_floors`
--
ALTER TABLE `property_floors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `property_gallery`
--
ALTER TABLE `property_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `property_reports`
--
ALTER TABLE `property_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_reviews`
--
ALTER TABLE `property_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_units`
--
ALTER TABLE `property_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rental_requests`
--
ALTER TABLE `rental_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rent_payments`
--
ALTER TABLE `rent_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rent_receipts`
--
ALTER TABLE `rent_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rent_settings`
--
ALTER TABLE `rent_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roommates`
--
ALTER TABLE `roommates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `signature_logs`
--
ALTER TABLE `signature_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `support_ticket_replies`
--
ALTER TABLE `support_ticket_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tenant_reviews`
--
ALTER TABLE `tenant_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `user_reports`
--
ALTER TABLE `user_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_balances`
--
ALTER TABLE `wallet_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `action_logs`
--
ALTER TABLE `action_logs`
  ADD CONSTRAINT `action_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blocked_users`
--
ALTER TABLE `blocked_users`
  ADD CONSTRAINT `blocked_users_ibfk_1` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blocked_users_ibfk_2` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `contracts_ibfk_2` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `contracts_ibfk_3` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `contracts_ibfk_4` FOREIGN KEY (`rental_request_id`) REFERENCES `rental_requests` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_terms`
--
ALTER TABLE `contract_terms`
  ADD CONSTRAINT `contract_terms_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_verifications`
--
ALTER TABLE `contract_verifications`
  ADD CONSTRAINT `contract_verifications_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `property_amenities`
--
ALTER TABLE `property_amenities`
  ADD CONSTRAINT `property_amenities_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_floors`
--
ALTER TABLE `property_floors`
  ADD CONSTRAINT `fk_floors_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_gallery`
--
ALTER TABLE `property_gallery`
  ADD CONSTRAINT `property_gallery_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_reports`
--
ALTER TABLE `property_reports`
  ADD CONSTRAINT `property_reports_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_units`
--
ALTER TABLE `property_units`
  ADD CONSTRAINT `fk_units_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD CONSTRAINT `fk_tenant_user` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rental_requests_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rent_payments`
--
ALTER TABLE `rent_payments`
  ADD CONSTRAINT `rent_payments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rent_payments_ibfk_2` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rent_receipts`
--
ALTER TABLE `rent_receipts`
  ADD CONSTRAINT `rent_receipts_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `rent_payments` (`id`),
  ADD CONSTRAINT `rent_receipts_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rent_receipts_ibfk_3` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD CONSTRAINT `fk_security_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `signature_logs`
--
ALTER TABLE `signature_logs`
  ADD CONSTRAINT `signature_logs_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `signature_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `fk_support_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_ticket_replies`
--
ALTER TABLE `support_ticket_replies`
  ADD CONSTRAINT `fk_reply_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reply_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_reports`
--
ALTER TABLE `user_reports`
  ADD CONSTRAINT `user_reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_reports_ibfk_2` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_balances`
--
ALTER TABLE `wallet_balances`
  ADD CONSTRAINT `wallet_balances_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
