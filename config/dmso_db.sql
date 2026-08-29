-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2026 at 08:43 AM
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
-- Database: `dmso_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `created_at`) VALUES
(1, 'admin', '$2y$12$z4KrywskF3BDOkOx0Ae7Vuyjo/hh3wqgb82/s98oBe5M/Hyv3tD.O', 'System Administrator', 'admin@dms.local', '0000000000', '2026-07-22 17:03:31');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `division_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `division_id`, `name`, `created_at`) VALUES
(1, 1, 'Dhaka District', '2026-08-22 05:54:01'),
(2, 1, 'Faridpur District', '2026-08-22 05:54:01'),
(3, 1, 'Gazipur District', '2026-08-22 05:54:01'),
(4, 1, 'Gopalganj District', '2026-08-22 05:54:01'),
(5, 1, 'Kishoreganj District', '2026-08-22 05:54:01'),
(6, 1, 'Madaripur District', '2026-08-22 05:54:01'),
(7, 1, 'Manikganj District', '2026-08-22 05:54:01'),
(8, 1, 'Munshiganj District', '2026-08-22 05:54:01'),
(9, 1, 'Narayanganj District', '2026-08-22 05:54:01'),
(10, 1, 'Narsingdi District', '2026-08-22 05:54:01'),
(11, 1, 'Rajbari District', '2026-08-22 05:54:01'),
(12, 1, 'Shariatpur District', '2026-08-22 05:54:01'),
(13, 1, 'Tangail District', '2026-08-22 05:54:01'),
(14, 2, 'Bagerhat District', '2026-08-22 05:54:01'),
(15, 2, 'Chuadanga District', '2026-08-22 05:54:01'),
(16, 2, 'Jashore District', '2026-08-22 05:54:01'),
(17, 2, 'Jhenaidah District', '2026-08-22 05:54:01'),
(18, 2, 'Khulna District', '2026-08-22 05:54:01'),
(19, 2, 'Kushtia District', '2026-08-22 05:54:01'),
(20, 2, 'Magura District', '2026-08-22 05:54:01'),
(21, 2, 'Meherpur District', '2026-08-22 05:54:01'),
(22, 2, 'Narail District', '2026-08-22 05:54:01'),
(23, 2, 'Satkhira District', '2026-08-22 05:54:01'),
(24, 3, 'Bandarban District', '2026-08-22 05:54:01'),
(25, 3, 'Brahmanbaria District', '2026-08-22 05:54:01'),
(26, 3, 'Chandpur District', '2026-08-22 05:54:01'),
(27, 3, 'Chattogram District', '2026-08-22 05:54:01'),
(28, 3, 'Cumilla District', '2026-08-22 05:54:01'),
(29, 3, 'Coxsbazar District', '2026-08-22 05:54:01'),
(30, 3, 'Feni District', '2026-08-22 05:54:01'),
(31, 3, 'Khagrachari District', '2026-08-22 05:54:01'),
(32, 3, 'Lakshmipur District', '2026-08-22 05:54:01'),
(33, 3, 'Noakhali District', '2026-08-22 05:54:01'),
(34, 3, 'Rangamati District', '2026-08-22 05:54:01'),
(35, 4, 'Bogura District', '2026-08-22 05:54:01'),
(36, 4, 'Joypurhat District', '2026-08-22 05:54:01'),
(37, 4, 'Naogaon District', '2026-08-22 05:54:01'),
(38, 4, 'Natore District', '2026-08-22 05:54:01'),
(39, 4, 'Chapainawabganj District', '2026-08-22 05:54:01'),
(40, 4, 'Pabna District', '2026-08-22 05:54:01'),
(41, 4, 'Rajshahi District', '2026-08-22 05:54:01'),
(42, 4, 'Sirajganj District', '2026-08-22 05:54:01'),
(43, 5, 'Habiganj District', '2026-08-22 05:54:01'),
(44, 5, 'Moulvibazar District', '2026-08-22 05:54:01'),
(45, 5, 'Sunamganj District', '2026-08-22 05:54:01'),
(46, 5, 'Sylhet District', '2026-08-22 05:54:01'),
(47, 6, 'Dinajpur District', '2026-08-22 05:54:01'),
(48, 6, 'Gaibandha District', '2026-08-22 05:54:01'),
(49, 6, 'Kurigram District', '2026-08-22 05:54:01'),
(50, 6, 'Lalmonirhat District', '2026-08-22 05:54:01'),
(51, 6, 'Nilphamari District', '2026-08-22 05:54:01'),
(52, 6, 'Panchagarh District', '2026-08-22 05:54:01'),
(53, 6, 'Rangpur District', '2026-08-22 05:54:01'),
(54, 6, 'Thakurgaon District', '2026-08-22 05:54:01'),
(55, 7, 'Jamalpur District', '2026-08-22 05:54:01'),
(56, 7, 'Mymensingh District', '2026-08-22 05:54:01'),
(57, 7, 'Netrokona District', '2026-08-22 05:54:01'),
(58, 7, 'Sherpur District', '2026-08-22 05:54:01'),
(59, 8, 'Barguna District', '2026-08-22 05:54:01'),
(60, 8, 'Barishal District', '2026-08-22 05:54:01'),
(61, 8, 'Bhola District', '2026-08-22 05:54:01'),
(62, 8, 'Jhalakathi District', '2026-08-22 05:54:01'),
(63, 8, 'Patuakhali District', '2026-08-22 05:54:01'),
(64, 8, 'Pirojpur District', '2026-08-22 05:54:01');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `name`, `created_at`) VALUES
(1, 'Dhaka Division', '2026-08-22 05:53:49'),
(2, 'Khulna Division', '2026-08-22 05:53:49'),
(3, 'Chattogram Division', '2026-08-22 05:53:49'),
(4, 'Rajshahi Division', '2026-08-22 05:53:49'),
(5, 'Sylhet Division', '2026-08-22 05:53:49'),
(6, 'Rangpur Division', '2026-08-22 05:53:49'),
(7, 'Mymensingh Division', '2026-08-22 05:53:49'),
(8, 'Barishal Division', '2026-08-22 05:53:49');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `document_name` varchar(200) NOT NULL,
  `ministry_id` int(11) NOT NULL,
  `division_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `upazila_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `doc_date` date NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(20) DEFAULT NULL,
  `uploaded_by` varchar(50) NOT NULL,
  `uploader_role` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ministries`
--

CREATE TABLE `ministries` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ministries`
--

INSERT INTO `ministries` (`id`, `name`, `created_at`) VALUES
(1, 'Public Administration', '2026-08-22 05:53:49'),
(2, 'Finance', '2026-08-22 05:53:49'),
(3, 'Law, Justice and Parliamentary Affairs', '2026-08-22 05:53:49'),
(4, 'Agriculture', '2026-08-22 05:53:49'),
(5, 'Food', '2026-08-22 05:53:49'),
(6, 'Posts, Telecommunications and Information Technology', '2026-08-22 05:53:49'),
(7, 'Information and Broadcasting', '2026-08-22 05:53:49'),
(8, 'Religious Affairs', '2026-08-22 05:53:49'),
(9, 'Shipping', '2026-08-22 05:53:49'),
(10, 'Foreign Affairs', '2026-08-22 05:53:49'),
(11, 'Planning', '2026-08-22 05:53:49'),
(12, 'Environment, Forest and Climate Change', '2026-08-22 05:53:49'),
(13, 'Defense', '2026-08-22 05:53:49'),
(14, 'Textiles and Jute', '2026-08-22 05:53:49'),
(15, 'Housing and Public Works', '2026-08-22 05:53:49'),
(16, 'Commerce', '2026-08-22 05:53:49'),
(17, 'Power, Energy and Mineral Resources', '2026-08-22 05:53:49'),
(18, 'Chittagong Hill Tracts Affairs', '2026-08-22 05:53:49'),
(19, 'Civil Aviation and Tourism', '2026-08-22 05:53:49'),
(20, 'Land', '2026-08-22 05:53:49'),
(21, 'Women and Children Affairs', '2026-08-22 05:53:49'),
(22, 'Fisheries and Livestock', '2026-08-22 05:53:49'),
(23, 'Youth and Sports', '2026-08-22 05:53:49'),
(24, 'Industries', '2026-08-22 05:53:49'),
(25, 'Education', '2026-08-22 05:53:49'),
(26, 'Primary and Mass Education', '2026-08-22 05:53:49'),
(27, 'Science and Technology', '2026-08-22 05:53:49'),
(28, 'Labour & Employment', '2026-08-22 05:53:49'),
(29, 'Social Welfare', '2026-08-22 05:53:49'),
(30, 'Water Resources', '2026-08-22 05:53:49'),
(31, 'Cultural Affairs', '2026-08-22 05:53:49'),
(32, 'Home Affairs', '2026-08-22 05:53:49'),
(33, 'Health and Family Welfare', '2026-08-22 05:53:49'),
(34, 'Local Government, Rural Development and Co-operatives', '2026-08-22 05:53:49'),
(35, 'Liberation War Affairs', '2026-08-22 05:53:49'),
(36, 'Expatriates Welfare and Overseas Employment', '2026-08-22 05:53:49'),
(37, 'Disaster Management and Relief', '2026-08-22 05:53:49'),
(38, 'Railways', '2026-08-22 05:53:49'),
(39, 'Roads Transport and Bridges', '2026-08-22 05:53:49');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `created_at`) VALUES
(1, 'General', '2026-08-22 05:53:49'),
(2, 'Infrastructure Development', '2026-08-22 05:53:49'),
(3, 'Rural Development', '2026-08-22 05:53:49'),
(4, 'Urban Planning', '2026-08-22 05:53:49'),
(5, 'Water Supply', '2026-08-22 05:53:49'),
(6, 'Road Construction', '2026-08-22 05:53:49'),
(7, 'Education Project', '2026-08-22 05:53:49'),
(8, 'Health Project', '2026-08-22 05:53:49'),
(9, 'Agriculture Project', '2026-08-22 05:53:49'),
(10, 'Disaster Management', '2026-08-22 05:53:49');

-- --------------------------------------------------------

--
-- Table structure for table `pwd_benchmark`
--

CREATE TABLE `pwd_benchmark` (
  `id` int(11) NOT NULL,
  `level_type` enum('district','upazila') NOT NULL,
  `district_id` int(11) NOT NULL,
  `upazila_id` int(11) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(30) DEFAULT 'pdf',
  `uploaded_by` varchar(50) NOT NULL,
  `uploader_role` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subadmins`
--

CREATE TABLE `subadmins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subadmins`
--

INSERT INTO `subadmins` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `created_at`) VALUES
(1, 'subadmin', '$2y$10$J58IQmOcXVd/2.9oFF3CS.TGA9RG1mACDzIDQyNeHeE.VVK7rvHq.', 'Sub Administrator', 'subadmin@dms.local', '01845787878', '2026-07-23 09:45:48');

-- --------------------------------------------------------

--
-- Table structure for table `upazilas`
--

CREATE TABLE `upazilas` (
  `id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `upazilas`
--

INSERT INTO `upazilas` (`id`, `district_id`, `name`, `created_at`) VALUES
(1, 1, 'Dhamrai', '2026-08-27 08:30:00'),
(2, 1, 'Dohar', '2026-08-27 08:30:00'),
(3, 1, 'Keraniganj', '2026-08-27 08:30:00'),
(4, 1, 'Nawabganj', '2026-08-27 08:30:00'),
(5, 1, 'Dhaka', '2026-08-27 08:30:00'),
(6, 1, 'Savar', '2026-08-27 08:30:00'),
(7, 2, 'Alfadanga', '2026-08-27 08:30:00'),
(8, 2, 'Bhanga', '2026-08-27 08:30:00'),
(9, 2, 'Boalmari', '2026-08-27 08:30:00'),
(10, 2, 'Charbhadrasan', '2026-08-27 08:30:00'),
(11, 2, 'Faridpur Sadar', '2026-08-27 08:30:00'),
(12, 2, 'Madhukhali', '2026-08-27 08:30:00'),
(13, 2, 'Nagarkanda', '2026-08-27 08:30:00'),
(14, 2, 'Sadarpur', '2026-08-27 08:30:00'),
(15, 2, 'Saltha', '2026-08-27 08:30:00'),
(16, 3, 'Gazipur Sadar', '2026-08-27 08:30:00'),
(17, 3, 'Kaliakair', '2026-08-27 08:30:00'),
(18, 3, 'Kaliganj', '2026-08-27 08:30:00'),
(19, 3, 'Kapasia', '2026-08-27 08:30:00'),
(20, 3, 'Sreepur', '2026-08-27 08:30:00'),
(21, 4, 'Gopalganj Sadar', '2026-08-27 08:30:00'),
(22, 4, 'Kashiani', '2026-08-27 08:30:00'),
(23, 4, 'Kotalipara', '2026-08-27 08:30:00'),
(24, 4, 'Muksudpur', '2026-08-27 08:30:00'),
(25, 4, 'Tungipara', '2026-08-27 08:30:00'),
(26, 5, 'Austagram', '2026-08-27 08:30:00'),
(27, 5, 'Bajitpur', '2026-08-27 08:30:00'),
(28, 5, 'Bhairab', '2026-08-27 08:30:00'),
(29, 5, 'Hossainpur', '2026-08-27 08:30:00'),
(30, 5, 'Itna', '2026-08-27 08:30:00'),
(31, 5, 'Karimgonj', '2026-08-27 08:30:00'),
(32, 5, 'Katiadi', '2026-08-27 08:30:00'),
(33, 5, 'Kishoreganj Sadar', '2026-08-27 08:30:00'),
(34, 5, 'Kuliarchar', '2026-08-27 08:30:00'),
(35, 5, 'Mithamoin', '2026-08-27 08:30:00'),
(36, 5, 'Nikli', '2026-08-27 08:30:00'),
(37, 5, 'Pakundia', '2026-08-27 08:30:00'),
(38, 5, 'Tarail', '2026-08-27 08:30:00'),
(39, 6, 'Kalkini', '2026-08-27 08:30:00'),
(40, 6, 'Madaripur Sadar', '2026-08-27 08:30:00'),
(41, 6, 'Rajoir', '2026-08-27 08:30:00'),
(42, 6, 'Shibchar', '2026-08-27 08:30:00'),
(43, 6, 'Dasar', '2026-08-27 08:30:00'),
(44, 7, 'Daulatpur', '2026-08-27 08:30:00'),
(45, 7, 'Gior', '2026-08-27 08:30:00'),
(46, 7, 'Harirampur', '2026-08-27 08:30:00'),
(47, 7, 'Manikganj Sadar', '2026-08-27 08:30:00'),
(48, 7, 'Saturia', '2026-08-27 08:30:00'),
(49, 7, 'Shibaloy', '2026-08-27 08:30:00'),
(50, 7, 'Singiar', '2026-08-27 08:30:00'),
(51, 8, 'Gajaria', '2026-08-27 08:30:00'),
(52, 8, 'Louhajanj', '2026-08-27 08:30:00'),
(53, 8, 'Munshiganj Sadar', '2026-08-27 08:30:00'),
(54, 8, 'Sirajdikhan', '2026-08-27 08:30:00'),
(55, 8, 'Sreenagar', '2026-08-27 08:30:00'),
(56, 8, 'Tongibari', '2026-08-27 08:30:00'),
(57, 9, 'Araihazar', '2026-08-27 08:30:00'),
(58, 9, 'Sonargaon', '2026-08-27 08:30:00'),
(59, 9, 'Narayanganj Sadar', '2026-08-27 08:30:00'),
(60, 9, 'Rupganj', '2026-08-27 08:30:00'),
(61, 9, 'Bandar', '2026-08-27 08:30:00'),
(62, 10, 'Belabo', '2026-08-27 08:30:00'),
(63, 10, 'Monohardi', '2026-08-27 08:30:00'),
(64, 10, 'Narsingdi Sadar', '2026-08-27 08:30:00'),
(65, 10, 'Palash', '2026-08-27 08:30:00'),
(66, 10, 'Raipura', '2026-08-27 08:30:00'),
(67, 10, 'Shibpur', '2026-08-27 08:30:00'),
(68, 11, 'Baliakandi', '2026-08-27 08:30:00'),
(69, 11, 'Goalanda', '2026-08-27 08:30:00'),
(70, 11, 'Kalukhali', '2026-08-27 08:30:00'),
(71, 11, 'Pangsa', '2026-08-27 08:30:00'),
(72, 11, 'Rajbari Sadar', '2026-08-27 08:30:00'),
(73, 12, 'Bhedarganj', '2026-08-27 08:30:00'),
(74, 12, 'Damudya', '2026-08-27 08:30:00'),
(75, 12, 'Gosairhat', '2026-08-27 08:30:00'),
(76, 12, 'Naria', '2026-08-27 08:30:00'),
(77, 12, 'Shariatpur Sadar', '2026-08-27 08:30:00'),
(78, 12, 'Zajira', '2026-08-27 08:30:00'),
(79, 13, 'Basail', '2026-08-27 08:30:00'),
(80, 13, 'Bhuapur', '2026-08-27 08:30:00'),
(81, 13, 'Delduar', '2026-08-27 08:30:00'),
(82, 13, 'Dhanbari', '2026-08-27 08:30:00'),
(83, 13, 'Ghatail', '2026-08-27 08:30:00'),
(84, 13, 'Gopalpur', '2026-08-27 08:30:00'),
(85, 13, 'Kalihati', '2026-08-27 08:30:00'),
(86, 13, 'Madhupur', '2026-08-27 08:30:00'),
(87, 13, 'Mirzapur', '2026-08-27 08:30:00'),
(88, 13, 'Nagarpur', '2026-08-27 08:30:00'),
(89, 13, 'Sakhipur', '2026-08-27 08:30:00'),
(90, 13, 'Tangail Sadar', '2026-08-27 08:30:00'),
(91, 14, 'Chitalmari', '2026-08-27 08:30:00'),
(92, 14, 'Fakirhat', '2026-08-27 08:30:00'),
(93, 14, 'Kachua', '2026-08-27 08:30:00'),
(94, 14, 'Mollahat', '2026-08-27 08:30:00'),
(95, 14, 'Mongla', '2026-08-27 08:30:00'),
(96, 14, 'Morrelganj', '2026-08-27 08:30:00'),
(97, 14, 'Rampal', '2026-08-27 08:30:00'),
(98, 14, 'Sarankhola', '2026-08-27 08:30:00'),
(99, 14, 'Bagerhat Sadar', '2026-08-27 08:30:00'),
(100, 15, 'Alamdanga', '2026-08-27 08:30:00'),
(101, 15, 'Chuadanga Sadar', '2026-08-27 08:30:00'),
(102, 15, 'Damurhuda', '2026-08-27 08:30:00'),
(103, 15, 'Jibannagar', '2026-08-27 08:30:00'),
(104, 16, 'Abhaynagar', '2026-08-27 08:30:00'),
(105, 16, 'Bagherpara', '2026-08-27 08:30:00'),
(106, 16, 'Chougachha', '2026-08-27 08:30:00'),
(107, 16, 'Jhikargacha', '2026-08-27 08:30:00'),
(108, 16, 'Keshabpur', '2026-08-27 08:30:00'),
(109, 16, 'Jessore Sadar', '2026-08-27 08:30:00'),
(110, 16, 'Manirampur', '2026-08-27 08:30:00'),
(111, 16, 'Sharsha', '2026-08-27 08:30:00'),
(112, 17, 'Harinakundu', '2026-08-27 08:30:00'),
(113, 17, 'Jhenaidah Sadar', '2026-08-27 08:30:00'),
(114, 17, 'Kaliganj', '2026-08-27 08:30:00'),
(115, 17, 'Kotchandpur', '2026-08-27 08:30:00'),
(116, 17, 'Moheshpur', '2026-08-27 08:30:00'),
(117, 18, 'Botiaghata', '2026-08-27 08:30:00'),
(118, 18, 'Dakop', '2026-08-27 08:30:00'),
(119, 18, 'Dumuria', '2026-08-27 08:30:00'),
(120, 18, 'Koyra', '2026-08-27 08:30:00'),
(121, 18, 'Paikgasa', '2026-08-27 08:30:00'),
(122, 18, 'Fultola', '2026-08-27 08:30:00'),
(123, 18, 'Rupsha', '2026-08-27 08:30:00'),
(124, 18, 'Terokhada', '2026-08-27 08:30:00'),
(125, 18, 'Digholia', '2026-08-27 08:30:00'),
(126, 19, 'Bheramara', '2026-08-27 08:30:00'),
(127, 19, 'Daulatpur', '2026-08-27 08:30:00'),
(128, 19, 'Khoksa', '2026-08-27 08:30:00'),
(129, 19, 'Kumarkhali', '2026-08-27 08:30:00'),
(130, 19, 'Kushtia Sadar', '2026-08-27 08:30:00'),
(131, 19, 'Mirpur', '2026-08-27 08:30:00'),
(132, 20, 'Magura Sadar', '2026-08-27 08:30:00'),
(133, 20, 'Mohammadpur', '2026-08-27 08:30:00'),
(134, 20, 'Shalikha', '2026-08-27 08:30:00'),
(135, 20, 'Sreepur', '2026-08-27 08:30:00'),
(136, 21, 'Gangni', '2026-08-27 08:30:00'),
(137, 21, 'Mujibnagar', '2026-08-27 08:30:00'),
(138, 21, 'Meherpur Sadar', '2026-08-27 08:30:00'),
(139, 22, 'Kalia', '2026-08-27 08:30:00'),
(140, 22, 'Lohagara', '2026-08-27 08:30:00'),
(141, 22, 'Narail Sadar', '2026-08-27 08:30:00'),
(142, 23, 'Assasuni', '2026-08-27 08:30:00'),
(143, 23, 'Debhata', '2026-08-27 08:30:00'),
(144, 23, 'Kalaroa', '2026-08-27 08:30:00'),
(145, 23, 'Kaliganj', '2026-08-27 08:30:00'),
(146, 23, 'Satkhira Sadar', '2026-08-27 08:30:00'),
(147, 23, 'Shyamnagar', '2026-08-27 08:30:00'),
(148, 23, 'Tala', '2026-08-27 08:30:00'),
(149, 24, 'Alikadam', '2026-08-27 08:30:00'),
(150, 24, 'Bandarban Sadar', '2026-08-27 08:30:00'),
(151, 24, 'Lama', '2026-08-27 08:30:00'),
(152, 24, 'Naikhongchhari', '2026-08-27 08:30:00'),
(153, 24, 'Rowangchhari', '2026-08-27 08:30:00'),
(154, 24, 'Ruma', '2026-08-27 08:30:00'),
(155, 24, 'Thanchi', '2026-08-27 08:30:00'),
(156, 25, 'Akhaura', '2026-08-27 08:30:00'),
(157, 25, 'Bancharampur', '2026-08-27 08:30:00'),
(158, 25, 'Bijoynagar', '2026-08-27 08:30:00'),
(159, 25, 'Brahmanbaria Sadar', '2026-08-27 08:30:00'),
(160, 25, 'Ashuganj', '2026-08-27 08:30:00'),
(161, 25, 'Kasba', '2026-08-27 08:30:00'),
(162, 25, 'Nabinagar', '2026-08-27 08:30:00'),
(163, 25, 'Nasirnagar', '2026-08-27 08:30:00'),
(164, 25, 'Sarail', '2026-08-27 08:30:00'),
(165, 26, 'Chandpur Sadar', '2026-08-27 08:30:00'),
(166, 26, 'Faridgonj', '2026-08-27 08:30:00'),
(167, 26, 'Haimchar', '2026-08-27 08:30:00'),
(168, 26, 'Hajiganj', '2026-08-27 08:30:00'),
(169, 26, 'Kachua', '2026-08-27 08:30:00'),
(170, 26, 'Matlab South', '2026-08-27 08:30:00'),
(171, 26, 'Matlab North', '2026-08-27 08:30:00'),
(172, 26, 'Shahrasti', '2026-08-27 08:30:00'),
(173, 27, 'Anwara', '2026-08-27 08:30:00'),
(174, 27, 'Banshkhali', '2026-08-27 08:30:00'),
(175, 27, 'Boalkhali', '2026-08-27 08:30:00'),
(176, 27, 'Chandanaish', '2026-08-27 08:30:00'),
(177, 27, 'Fatikchhari', '2026-08-27 08:30:00'),
(178, 27, 'Hathazari', '2026-08-27 08:30:00'),
(179, 27, 'Lohagara', '2026-08-27 08:30:00'),
(180, 27, 'Mirsharai', '2026-08-27 08:30:00'),
(181, 27, 'Patiya', '2026-08-27 08:30:00'),
(182, 27, 'Rangunia', '2026-08-27 08:30:00'),
(183, 27, 'Raozan', '2026-08-27 08:30:00'),
(184, 27, 'Sandwip', '2026-08-27 08:30:00'),
(185, 27, 'Satkania', '2026-08-27 08:30:00'),
(186, 27, 'Sitakunda', '2026-08-27 08:30:00'),
(187, 27, 'Karnafuli', '2026-08-27 08:30:00'),
(188, 28, 'Barura', '2026-08-27 08:30:00'),
(189, 28, 'Brahmanpara', '2026-08-27 08:30:00'),
(190, 28, 'Burichang', '2026-08-27 08:30:00'),
(191, 28, 'Chandina', '2026-08-27 08:30:00'),
(192, 28, 'Chauddagram', '2026-08-27 08:30:00'),
(193, 28, 'Adarsha Sadar', '2026-08-27 08:30:00'),
(194, 28, 'Sadar South', '2026-08-27 08:30:00'),
(195, 28, 'Daudkandi', '2026-08-27 08:30:00'),
(196, 28, 'Debidwar', '2026-08-27 08:30:00'),
(197, 28, 'Homna', '2026-08-27 08:30:00'),
(198, 28, 'Laksam', '2026-08-27 08:30:00'),
(199, 28, 'Monohargonj', '2026-08-27 08:30:00'),
(200, 28, 'Meghna', '2026-08-27 08:30:00'),
(201, 28, 'Muradnagar', '2026-08-27 08:30:00'),
(202, 28, 'Nangalkot', '2026-08-27 08:30:00'),
(203, 28, 'Titas', '2026-08-27 08:30:00'),
(204, 28, 'Lalmai', '2026-08-27 08:30:00'),
(205, 29, 'Chakaria', '2026-08-27 08:30:00'),
(206, 29, 'Coxsbazar Sadar', '2026-08-27 08:30:00'),
(207, 29, 'Kutubdia', '2026-08-27 08:30:00'),
(208, 29, 'Moheshkhali', '2026-08-27 08:30:00'),
(209, 29, 'Pekua', '2026-08-27 08:30:00'),
(210, 29, 'Ramu', '2026-08-27 08:30:00'),
(211, 29, 'Teknaf', '2026-08-27 08:30:00'),
(212, 29, 'Ukhiya', '2026-08-27 08:30:00'),
(213, 29, 'Eidgaon', '2026-08-27 08:30:00'),
(214, 30, 'Chhagalnaiya', '2026-08-27 08:30:00'),
(215, 30, 'Daganbhuiyan', '2026-08-27 08:30:00'),
(216, 30, 'Feni Sadar', '2026-08-27 08:30:00'),
(217, 30, 'Fulgazi', '2026-08-27 08:30:00'),
(218, 30, 'Parshuram', '2026-08-27 08:30:00'),
(219, 30, 'Sonagazi', '2026-08-27 08:30:00'),
(220, 31, 'Dighinala', '2026-08-27 08:30:00'),
(221, 31, 'Manikchari', '2026-08-27 08:30:00'),
(222, 31, 'Khagrachhari Sadar', '2026-08-27 08:30:00'),
(223, 31, 'Laxmichhari', '2026-08-27 08:30:00'),
(224, 31, 'Mohalchari', '2026-08-27 08:30:00'),
(225, 31, 'Matiranga', '2026-08-27 08:30:00'),
(226, 31, 'Panchari', '2026-08-27 08:30:00'),
(227, 31, 'Ramgarh', '2026-08-27 08:30:00'),
(228, 31, 'Guimara', '2026-08-27 08:30:00'),
(229, 32, 'Kamalnagar', '2026-08-27 08:30:00'),
(230, 32, 'Lakshmipur Sadar', '2026-08-27 08:30:00'),
(231, 32, 'Raipur', '2026-08-27 08:30:00'),
(232, 32, 'Ramganj', '2026-08-27 08:30:00'),
(233, 32, 'Ramgati', '2026-08-27 08:30:00'),
(234, 32, 'Chandraganj', '2026-08-27 08:30:00'),
(235, 33, 'Begumganj', '2026-08-27 08:30:00'),
(236, 33, 'Chatkhil', '2026-08-27 08:30:00'),
(237, 33, 'Companiganj', '2026-08-27 08:30:00'),
(238, 33, 'Hatia', '2026-08-27 08:30:00'),
(239, 33, 'Senbug', '2026-08-27 08:30:00'),
(240, 33, 'Sonaimuri', '2026-08-27 08:30:00'),
(241, 33, 'Subarnachar', '2026-08-27 08:30:00'),
(242, 33, 'Noakhali Sadar', '2026-08-27 08:30:00'),
(243, 33, 'Kabirhat', '2026-08-27 08:30:00'),
(244, 34, 'Baghaichari', '2026-08-27 08:30:00'),
(245, 34, 'Barkal', '2026-08-27 08:30:00'),
(246, 34, 'Kawkhali', '2026-08-27 08:30:00'),
(247, 34, 'Kaptai', '2026-08-27 08:30:00'),
(248, 34, 'Juraichari', '2026-08-27 08:30:00'),
(249, 34, 'Langadu', '2026-08-27 08:30:00'),
(250, 34, 'Naniarchar', '2026-08-27 08:30:00'),
(251, 34, 'Rangamati Sadar', '2026-08-27 08:30:00'),
(252, 34, 'Rajasthali', '2026-08-27 08:30:00'),
(253, 34, 'Belaichari', '2026-08-27 08:30:00'),
(254, 35, 'Adamdighi', '2026-08-27 08:30:00'),
(255, 35, 'Bogura Sadar', '2026-08-27 08:30:00'),
(256, 35, 'Dhunot', '2026-08-27 08:30:00'),
(257, 35, 'Dupchanchia', '2026-08-27 08:30:00'),
(258, 35, 'Gabtali', '2026-08-27 08:30:00'),
(259, 35, 'Kahaloo', '2026-08-27 08:30:00'),
(260, 35, 'Nondigram', '2026-08-27 08:30:00'),
(261, 35, 'Shariakandi', '2026-08-27 08:30:00'),
(262, 35, 'Shajahanpur', '2026-08-27 08:30:00'),
(263, 35, 'Sherpur', '2026-08-27 08:30:00'),
(264, 35, 'Shibganj', '2026-08-27 08:30:00'),
(265, 35, 'Sonatala', '2026-08-27 08:30:00'),
(266, 35, 'Mokamtala', '2026-08-27 08:30:00'),
(267, 36, 'Akkelpur', '2026-08-27 08:30:00'),
(268, 36, 'Joypurhat Sadar', '2026-08-27 08:30:00'),
(269, 36, 'Kalai', '2026-08-27 08:30:00'),
(270, 36, 'Panchbibi', '2026-08-27 08:30:00'),
(271, 36, 'Khetlal', '2026-08-27 08:30:00'),
(272, 37, 'Atrai', '2026-08-27 08:30:00'),
(273, 37, 'Dhamoirhat', '2026-08-27 08:30:00'),
(274, 37, 'Manda', '2026-08-27 08:30:00'),
(275, 37, 'Mohadevpur', '2026-08-27 08:30:00'),
(276, 37, 'Naogaon Sadar', '2026-08-27 08:30:00'),
(277, 37, 'Niamatpur', '2026-08-27 08:30:00'),
(278, 37, 'Patnitala', '2026-08-27 08:30:00'),
(279, 37, 'Raninagar', '2026-08-27 08:30:00'),
(280, 37, 'Sapahar', '2026-08-27 08:30:00'),
(281, 37, 'Badalgachi', '2026-08-27 08:30:00'),
(282, 37, 'Porsha', '2026-08-27 08:30:00'),
(283, 38, 'Bagatipara', '2026-08-27 08:30:00'),
(284, 38, 'Baraigram', '2026-08-27 08:30:00'),
(285, 38, 'Gurudaspur', '2026-08-27 08:30:00'),
(286, 38, 'Lalpur', '2026-08-27 08:30:00'),
(287, 38, 'Natore Sadar', '2026-08-27 08:30:00'),
(288, 38, 'Singra', '2026-08-27 08:30:00'),
(289, 38, 'Naldanga', '2026-08-27 08:30:00'),
(290, 39, 'Shibganj', '2026-08-27 08:30:00'),
(291, 39, 'Bholahat', '2026-08-27 08:30:00'),
(292, 39, 'Gomostapur', '2026-08-27 08:30:00'),
(293, 39, 'Nachol', '2026-08-27 08:30:00'),
(294, 39, 'Chapainawabganj Sadar', '2026-08-27 08:30:00'),
(295, 40, 'Atghoria', '2026-08-27 08:30:00'),
(296, 40, 'Bera', '2026-08-27 08:30:00'),
(297, 40, 'Bhangura', '2026-08-27 08:30:00'),
(298, 40, 'Chatmohar', '2026-08-27 08:30:00'),
(299, 40, 'Faridpur', '2026-08-27 08:30:00'),
(300, 40, 'Ishurdi', '2026-08-27 08:30:00'),
(301, 40, 'Pabna Sadar', '2026-08-27 08:30:00'),
(302, 40, 'Santhia', '2026-08-27 08:30:00'),
(303, 40, 'Sujanagar', '2026-08-27 08:30:00'),
(304, 41, 'Bagha', '2026-08-27 08:30:00'),
(305, 41, 'Bagmara', '2026-08-27 08:30:00'),
(306, 41, 'Charghat', '2026-08-27 08:30:00'),
(307, 41, 'Durgapur', '2026-08-27 08:30:00'),
(308, 41, 'Godagari', '2026-08-27 08:30:00'),
(309, 41, 'Mohonpur', '2026-08-27 08:30:00'),
(310, 41, 'Paba', '2026-08-27 08:30:00'),
(311, 41, 'Puthia', '2026-08-27 08:30:00'),
(312, 41, 'Tanore', '2026-08-27 08:30:00'),
(313, 42, 'Belkuchi', '2026-08-27 08:30:00'),
(314, 42, 'Chauhali', '2026-08-27 08:30:00'),
(315, 42, 'Kamarkhand', '2026-08-27 08:30:00'),
(316, 42, 'Kazipur', '2026-08-27 08:30:00'),
(317, 42, 'Raigonj', '2026-08-27 08:30:00'),
(318, 42, 'Shahjadpur', '2026-08-27 08:30:00'),
(319, 42, 'Sirajganj Sadar', '2026-08-27 08:30:00'),
(320, 42, 'Tarash', '2026-08-27 08:30:00'),
(321, 42, 'Ullapara', '2026-08-27 08:30:00'),
(322, 43, 'Ajmiriganj', '2026-08-27 08:30:00'),
(323, 43, 'Bahubal', '2026-08-27 08:30:00'),
(324, 43, 'Baniachong', '2026-08-27 08:30:00'),
(325, 43, 'Chunarughat', '2026-08-27 08:30:00'),
(326, 43, 'Habiganj Sadar', '2026-08-27 08:30:00'),
(327, 43, 'Lakhai', '2026-08-27 08:30:00'),
(328, 43, 'Madhabpur', '2026-08-27 08:30:00'),
(329, 43, 'Nabiganj', '2026-08-27 08:30:00'),
(330, 43, 'Shayestaganj', '2026-08-27 08:30:00'),
(331, 44, 'Barlekha', '2026-08-27 08:30:00'),
(332, 44, 'Juri', '2026-08-27 08:30:00'),
(333, 44, 'Kamolganj', '2026-08-27 08:30:00'),
(334, 44, 'Kulaura', '2026-08-27 08:30:00'),
(335, 44, 'Moulvibazar Sadar', '2026-08-27 08:30:00'),
(336, 44, 'Rajnagar', '2026-08-27 08:30:00'),
(337, 44, 'Sreemangal', '2026-08-27 08:30:00'),
(338, 45, 'Bishwambarpur', '2026-08-27 08:30:00'),
(339, 45, 'Chhatak', '2026-08-27 08:30:00'),
(340, 45, 'Derai', '2026-08-27 08:30:00'),
(341, 45, 'Dharmapasha', '2026-08-27 08:30:00'),
(342, 45, 'Dowarabazar', '2026-08-27 08:30:00'),
(343, 45, 'Jagannathpur', '2026-08-27 08:30:00'),
(344, 45, 'Jamalganj', '2026-08-27 08:30:00'),
(345, 45, 'Shalla', '2026-08-27 08:30:00'),
(346, 45, 'Sunamganj Sadar', '2026-08-27 08:30:00'),
(347, 45, 'Tahirpur', '2026-08-27 08:30:00'),
(348, 45, 'Shantiganj', '2026-08-27 08:30:00'),
(349, 45, 'Madhyanagar', '2026-08-27 08:30:00'),
(350, 46, 'Balaganj', '2026-08-27 08:30:00'),
(351, 46, 'Beanibazar', '2026-08-27 08:30:00'),
(352, 46, 'Bishwanath', '2026-08-27 08:30:00'),
(353, 46, 'Companiganj', '2026-08-27 08:30:00'),
(354, 46, 'Dakshin Surma', '2026-08-27 08:30:00'),
(355, 46, 'Fenchuganj', '2026-08-27 08:30:00'),
(356, 46, 'Golapganj', '2026-08-27 08:30:00'),
(357, 46, 'Gowainghat', '2026-08-27 08:30:00'),
(358, 46, 'Jaintiapur', '2026-08-27 08:30:00'),
(359, 46, 'Kanaighat', '2026-08-27 08:30:00'),
(360, 46, 'Sylhet Sadar', '2026-08-27 08:30:00'),
(361, 46, 'Zakiganj', '2026-08-27 08:30:00'),
(362, 46, 'Osmani Nagar', '2026-08-27 08:30:00'),
(363, 47, 'Birampur', '2026-08-27 08:30:00'),
(364, 47, 'Birganj', '2026-08-27 08:30:00'),
(365, 47, 'Birol', '2026-08-27 08:30:00'),
(366, 47, 'Bochaganj', '2026-08-27 08:30:00'),
(367, 47, 'Chirirbandar', '2026-08-27 08:30:00'),
(368, 47, 'Fulbari', '2026-08-27 08:30:00'),
(369, 47, 'Ghoraghat', '2026-08-27 08:30:00'),
(370, 47, 'Hakimpur', '2026-08-27 08:30:00'),
(371, 47, 'Kaharol', '2026-08-27 08:30:00'),
(372, 47, 'Khansama', '2026-08-27 08:30:00'),
(373, 47, 'Nawabganj', '2026-08-27 08:30:00'),
(374, 47, 'Parbatipur', '2026-08-27 08:30:00'),
(375, 47, 'Dinajpur Sadar', '2026-08-27 08:30:00'),
(376, 48, 'Phulchari', '2026-08-27 08:30:00'),
(377, 48, 'Gaibandha Sadar', '2026-08-27 08:30:00'),
(378, 48, 'Gobindaganj', '2026-08-27 08:30:00'),
(379, 48, 'Palashbari', '2026-08-27 08:30:00'),
(380, 48, 'Sadullapur', '2026-08-27 08:30:00'),
(381, 48, 'Saghata', '2026-08-27 08:30:00'),
(382, 48, 'Sundarganj', '2026-08-27 08:30:00'),
(383, 49, 'Phulbari', '2026-08-27 08:30:00'),
(384, 49, 'Bhurungamari', '2026-08-27 08:30:00'),
(385, 49, 'Charrajibpur', '2026-08-27 08:30:00'),
(386, 49, 'Chilmari', '2026-08-27 08:30:00'),
(387, 49, 'Kurigram Sadar', '2026-08-27 08:30:00'),
(388, 49, 'Nageshwari', '2026-08-27 08:30:00'),
(389, 49, 'Rajarhat', '2026-08-27 08:30:00'),
(390, 49, 'Rowmari', '2026-08-27 08:30:00'),
(391, 49, 'Ulipur', '2026-08-27 08:30:00'),
(392, 50, 'Aditmari', '2026-08-27 08:30:00'),
(393, 50, 'Hatibandha', '2026-08-27 08:30:00'),
(394, 50, 'Kaliganj', '2026-08-27 08:30:00'),
(395, 50, 'Lalmonirhat Sadar', '2026-08-27 08:30:00'),
(396, 50, 'Patgram', '2026-08-27 08:30:00'),
(397, 51, 'Domar', '2026-08-27 08:30:00'),
(398, 51, 'Jaldhaka', '2026-08-27 08:30:00'),
(399, 51, 'Kishorganj', '2026-08-27 08:30:00'),
(400, 51, 'Nilphamari Sadar', '2026-08-27 08:30:00'),
(401, 51, 'Syedpur', '2026-08-27 08:30:00'),
(402, 51, 'Dimla', '2026-08-27 08:30:00'),
(403, 52, 'Atwari', '2026-08-27 08:30:00'),
(404, 52, 'Boda', '2026-08-27 08:30:00'),
(405, 52, 'Debiganj', '2026-08-27 08:30:00'),
(406, 52, 'Panchagarh Sadar', '2026-08-27 08:30:00'),
(407, 52, 'Tetulia', '2026-08-27 08:30:00'),
(408, 53, 'Badargonj', '2026-08-27 08:30:00'),
(409, 53, 'Kaunia', '2026-08-27 08:30:00'),
(410, 53, 'Rangpur Sadar', '2026-08-27 08:30:00'),
(411, 53, 'Mithapukur', '2026-08-27 08:30:00'),
(412, 53, 'Pirgacha', '2026-08-27 08:30:00'),
(413, 53, 'Pirgonj', '2026-08-27 08:30:00'),
(414, 53, 'Taragonj', '2026-08-27 08:30:00'),
(415, 53, 'Gangachara', '2026-08-27 08:30:00'),
(416, 54, 'Pirganj', '2026-08-27 08:30:00'),
(417, 54, 'Baliadangi', '2026-08-27 08:30:00'),
(418, 54, 'Haripur', '2026-08-27 08:30:00'),
(419, 54, 'Ranisankail', '2026-08-27 08:30:00'),
(420, 54, 'Thakurgaon Sadar', '2026-08-27 08:30:00'),
(421, 54, 'Bhulli', '2026-08-27 08:30:00'),
(422, 54, 'Ruhia', '2026-08-27 08:30:00'),
(423, 55, 'Bokshiganj', '2026-08-27 08:30:00'),
(424, 55, 'Dewangonj', '2026-08-27 08:30:00'),
(425, 55, 'Islampur', '2026-08-27 08:30:00'),
(426, 55, 'Jamalpur', '2026-08-27 08:30:00'),
(427, 55, 'Madarganj', '2026-08-27 08:30:00'),
(428, 55, 'Melandah', '2026-08-27 08:30:00'),
(429, 55, 'Sarishabari', '2026-08-27 08:30:00'),
(430, 56, 'Bhaluka', '2026-08-27 08:30:00'),
(431, 56, 'Dhobaura', '2026-08-27 08:30:00'),
(432, 56, 'Fulbaria', '2026-08-27 08:30:00'),
(433, 56, 'Gafargaon', '2026-08-27 08:30:00'),
(434, 56, 'Gouripur', '2026-08-27 08:30:00'),
(435, 56, 'Haluaghat', '2026-08-27 08:30:00'),
(436, 56, 'Iswarganj', '2026-08-27 08:30:00'),
(437, 56, 'Mymensingh Sadar', '2026-08-27 08:30:00'),
(438, 56, 'Muktagacha', '2026-08-27 08:30:00'),
(439, 56, 'Nandail', '2026-08-27 08:30:00'),
(440, 56, 'Phulpur', '2026-08-27 08:30:00'),
(441, 56, 'Tarakanda', '2026-08-27 08:30:00'),
(442, 56, 'Trishal', '2026-08-27 08:30:00'),
(443, 57, 'Atpara', '2026-08-27 08:30:00'),
(444, 57, 'Barhatta', '2026-08-27 08:30:00'),
(445, 57, 'Durgapur', '2026-08-27 08:30:00'),
(446, 57, 'Khaliajuri', '2026-08-27 08:30:00'),
(447, 57, 'Kalmakanda', '2026-08-27 08:30:00'),
(448, 57, 'Kendua', '2026-08-27 08:30:00'),
(449, 57, 'Madan', '2026-08-27 08:30:00'),
(450, 57, 'Mohongonj', '2026-08-27 08:30:00'),
(451, 57, 'Netrokona Sadar', '2026-08-27 08:30:00'),
(452, 57, 'Purbadhala', '2026-08-27 08:30:00'),
(453, 58, 'Jhenaigati', '2026-08-27 08:30:00'),
(454, 58, 'Nokla', '2026-08-27 08:30:00'),
(455, 58, 'Nalitabari', '2026-08-27 08:30:00'),
(456, 58, 'Sherpur Sadar', '2026-08-27 08:30:00'),
(457, 58, 'Sreebordi', '2026-08-27 08:30:00'),
(458, 59, 'Amtali', '2026-08-27 08:30:00'),
(459, 59, 'Bamna', '2026-08-27 08:30:00'),
(460, 59, 'Barguna Sadar', '2026-08-27 08:30:00'),
(461, 59, 'Betagi', '2026-08-27 08:30:00'),
(462, 59, 'Pathorghata', '2026-08-27 08:30:00'),
(463, 59, 'Taltali', '2026-08-27 08:30:00'),
(464, 60, 'Agailjhara', '2026-08-27 08:30:00'),
(465, 60, 'Babuganj', '2026-08-27 08:30:00'),
(466, 60, 'Bakerganj', '2026-08-27 08:30:00'),
(467, 60, 'Banaripara', '2026-08-27 08:30:00'),
(468, 60, 'Gournadi', '2026-08-27 08:30:00'),
(469, 60, 'Hizla', '2026-08-27 08:30:00'),
(470, 60, 'Barishal Sadar', '2026-08-27 08:30:00'),
(471, 60, 'Mehendiganj', '2026-08-27 08:30:00'),
(472, 60, 'Muladi', '2026-08-27 08:30:00'),
(473, 60, 'Wazirpur', '2026-08-27 08:30:00'),
(474, 61, 'Bhola Sadar', '2026-08-27 08:30:00'),
(475, 61, 'Borhanuddin', '2026-08-27 08:30:00'),
(476, 61, 'Doulatkhan', '2026-08-27 08:30:00'),
(477, 61, 'Lalmohan', '2026-08-27 08:30:00'),
(478, 61, 'Monpura', '2026-08-27 08:30:00'),
(479, 61, 'Tazumuddin', '2026-08-27 08:30:00'),
(480, 61, 'Charfesson', '2026-08-27 08:30:00'),
(481, 62, 'Jhalakathi Sadar', '2026-08-27 08:30:00'),
(482, 62, 'Nalchity', '2026-08-27 08:30:00'),
(483, 62, 'Kathalia', '2026-08-27 08:30:00'),
(484, 62, 'Rajapur', '2026-08-27 08:30:00'),
(485, 63, 'Bauphal', '2026-08-27 08:30:00'),
(486, 63, 'Dashmina', '2026-08-27 08:30:00'),
(487, 63, 'Dumki', '2026-08-27 08:30:00'),
(488, 63, 'Kalapara', '2026-08-27 08:30:00'),
(489, 63, 'Mirzaganj', '2026-08-27 08:30:00'),
(490, 63, 'Patuakhali Sadar', '2026-08-27 08:30:00'),
(491, 63, 'Rangabali', '2026-08-27 08:30:00'),
(492, 63, 'Galachipa', '2026-08-27 08:30:00'),
(493, 64, 'Bhandaria', '2026-08-27 08:30:00'),
(494, 64, 'Kawkhali', '2026-08-27 08:30:00'),
(495, 64, 'Mathbaria', '2026-08-27 08:30:00'),
(496, 64, 'Nazirpur', '2026-08-27 08:30:00'),
(497, 64, 'Pirojpur Sadar', '2026-08-27 08:30:00'),
(498, 64, 'Nesarabad', '2026-08-27 08:30:00'),
(499, 64, 'Zianagar', '2026-08-27 08:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `access_type` enum('all','ministrie','division','district','upazila','project') NOT NULL DEFAULT 'all',
  `access_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`access_ids`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `access_type`, `access_ids`, `created_at`) VALUES
(1, 'user-1', '$2y$10$ctfkbkh2R02HA/QmeGergOfkU65JI8TpmAf.HwcDFgT54yH0cCHzO', 'User-1', 'user-1@dms.com', '01515655830', 'all', NULL, '2026-08-27 09:28:41'),
(2, 'user-2', '$2y$10$a/lySoOFabdmRenMg0hz1O7Gh5lwKAFona13SB.pVvebbndS41QKK', 'User-2', 'user-2@dms.com', '01515655830', 'division', '[1,7]', '2026-08-27 09:29:32');

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
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_district_div` (`division_id`,`name`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doc_ministry` (`ministry_id`),
  ADD KEY `fk_doc_division` (`division_id`),
  ADD KEY `fk_doc_district` (`district_id`),
  ADD KEY `fk_doc_upazila` (`upazila_id`),
  ADD KEY `fk_doc_project` (`project_id`);

--
-- Indexes for table `ministries`
--
ALTER TABLE `ministries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `pwd_benchmark`
--
ALTER TABLE `pwd_benchmark`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_district_level` (`level_type`,`district_id`,`upazila_id`),
  ADD KEY `fk_bm_district` (`district_id`),
  ADD KEY `fk_bm_upazila` (`upazila_id`);

--
-- Indexes for table `subadmins`
--
ALTER TABLE `subadmins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `upazilas`
--
ALTER TABLE `upazilas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_upazila_dist` (`district_id`,`name`);

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
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ministries`
--
ALTER TABLE `ministries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pwd_benchmark`
--
ALTER TABLE `pwd_benchmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `subadmins`
--
ALTER TABLE `subadmins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `upazilas`
--
ALTER TABLE `upazilas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=500;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `fk_district_division` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `fk_doc_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `fk_doc_division` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `fk_doc_ministry` FOREIGN KEY (`ministry_id`) REFERENCES `ministries` (`id`),
  ADD CONSTRAINT `fk_doc_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `fk_doc_upazila` FOREIGN KEY (`upazila_id`) REFERENCES `upazilas` (`id`);

--
-- Constraints for table `pwd_benchmark`
--
ALTER TABLE `pwd_benchmark`
  ADD CONSTRAINT `fk_bm_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bm_upazila` FOREIGN KEY (`upazila_id`) REFERENCES `upazilas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `upazilas`
--
ALTER TABLE `upazilas`
  ADD CONSTRAINT `fk_upazila_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
