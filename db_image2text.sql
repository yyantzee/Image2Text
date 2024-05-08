-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 08, 2024 at 05:53 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_image2text`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_response`
--

CREATE TABLE `user_response` (
  `id_ur` int NOT NULL,
  `ip_address` varchar(55) NOT NULL,
  `file_name` varchar(55) NOT NULL,
  `extracted_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_response`
--

INSERT INTO `user_response` (`id_ur`, `ip_address`, `file_name`, `extracted_text`, `created_at`) VALUES
(15, '::1', 'Screenshot 2024-04-10 003831.png', 'US-021 10/10/2024\n', '2024-05-08 17:41:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_response`
--
ALTER TABLE `user_response`
  ADD PRIMARY KEY (`id_ur`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_response`
--
ALTER TABLE `user_response`
  MODIFY `id_ur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
