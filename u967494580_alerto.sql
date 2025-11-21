-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 21, 2025 at 06:48 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u967494580_alerto`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `region` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `level` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'Public',
  `posted_by` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `title`, `content`, `region`, `date`, `location`, `level`, `type`, `posted_by`, `created_at`, `updated_at`, `updated_by`) VALUES
(16, 'Admin2 Edit - Title Test', 'Content Test', 'Metro Manila', '2025-10-14', 'Manila', 'All Levels', 'Both', 9, '2025-10-13 10:09:14', '2025-10-27 06:14:22', 18),
(18, 'udtest', 'udtest', 'Metro Manila', '2025-10-26', 'udtest', 'All Levels', 'Public', 9, '2025-10-26 15:07:12', '2025-10-26 15:14:56', 9);

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contacts`
--

CREATE TABLE `emergency_contacts` (
  `contact_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `relation` varchar(100) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_contacts`
--

INSERT INTO `emergency_contacts` (`contact_id`, `user_id`, `name`, `relation`, `phone_number`, `address`, `created_at`, `updated_at`) VALUES
(2, 5, 'pj shele', 'classmate', '1111 111 1111', 'dsdsd', '2025-10-12 16:00:55', '2025-10-12 18:19:30'),
(3, 5, 'KARL HOWARD', 'brother', '123 123 123', 'dsds', '2025-10-12 18:17:22', '2025-10-12 18:19:34'),
(7, 13, 'Christian Garcia', 'Friend', '0919 911 9119', 'Makati', '2025-10-24 04:25:32', '2025-10-24 04:25:32'),
(8, 20, 'Karl CASCAYO', 'dog', '093824841', 'Bacoor Cavite', '2025-10-28 02:49:01', '2025-10-28 02:49:01'),
(9, 13, 'ASDASDA', 'ASD', 'ASD', 'ASD', '2025-10-29 02:58:12', '2025-10-29 02:58:12'),
(10, 23, 'howard the great', 'my nigga', '935793583', 'q343ioy4gr6', '2025-10-29 04:55:16', '2025-10-29 04:55:16'),
(11, 13, 'test', 'MOTHER', '099', 'CAVITE', '2025-10-29 06:58:07', '2025-10-29 06:58:07'),
(12, 13, 'ERIKA', 'Girlfriend', '0912 345 6789', '', '2025-11-21 06:47:43', '2025-11-21 06:47:43');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_kit`
--

CREATE TABLE `emergency_kit` (
  `item_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_kit`
--

INSERT INTO `emergency_kit` (`item_id`, `user_id`, `item_name`, `created_at`, `updated_at`, `category`, `quantity`) VALUES
(3, 5, 'Shortsd', '2025-10-12 16:00:03', '2025-10-12 16:13:57', 'CLOTHES', 2),
(4, 5, 'Tshirt', '2025-10-12 18:18:11', '2025-10-12 18:18:11', 'CLOTHES', 4),
(5, 5, 'Canned goods', '2025-10-12 18:18:11', '2025-10-12 18:18:11', 'Food & Water', 5),
(6, 5, 'water', '2025-10-12 18:18:11', '2025-10-12 18:18:11', 'Food & Water', 5),
(9, 13, 'Shorts', '2025-10-13 07:33:17', '2025-10-13 07:33:17', 'Clothes', 3),
(10, 13, 'T-shirt', '2025-10-13 07:33:17', '2025-10-13 07:33:17', 'Clothes', 5),
(13, 13, 'Guitar', '2025-10-24 04:25:53', '2025-10-24 04:25:53', 'Entertainment', 1),
(14, 20, 'Coke', '2025-10-28 02:49:34', '2025-10-28 02:49:34', 'FOOD', 50),
(15, 20, 'Buldak', '2025-10-28 02:49:34', '2025-10-28 02:49:34', 'FOOD', 100),
(18, 13, 'Rice', '2025-10-29 06:59:06', '2025-10-29 06:59:06', 'foods', 7);

-- --------------------------------------------------------

--
-- Table structure for table `hotlines`
--

CREATE TABLE `hotlines` (
  `hotline_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `agency_name` varchar(150) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `added_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotlines`
--

INSERT INTO `hotlines` (`hotline_id`, `name`, `contact_number`, `agency_name`, `location`, `added_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(6, 'Fire Emergency', '(02) 426-0219', 'Bureau of Fire Protection', 'NCR', 9, '2025-10-27 04:28:08', NULL, NULL),
(7, 'Police', '(2) 722-0650', 'Philippine National Police', 'NCR', 9, '2025-10-27 04:29:03', NULL, NULL),
(8, 'Red Cross', '143 / (02) 527-0000', 'Philippine Red Cross', 'NCR', 9, '2025-10-27 04:30:16', 9, '2025-10-27 04:41:54'),
(9, 'Earthquake and Volcano Monitoring', '(02) 426-1468', 'PHIVOLCS', 'NCR', 9, '2025-10-27 04:31:34', NULL, NULL),
(10, 'Maritime Rescue / Coast Guard', '(02) 527-8481', 'Philippine Coast Guard', 'NCR', 9, '2025-10-27 04:32:23', 9, '2025-10-27 04:37:45'),
(11, 'Weather and Flood Forecast', '(02) 8284-0800', 'PAGASA', 'Quezon City', 9, '2025-10-27 04:33:58', NULL, NULL),
(12, 'Power Supply / Electrical Emergency', '(02) 8922-5555', 'Meralco', 'Metro Manila', 9, '2025-10-27 04:35:01', NULL, NULL),
(13, 'Water Supply', '(02) 1627', 'Maynilad', 'NCR', 9, '2025-10-27 04:35:35', NULL, NULL),
(14, 'Disaster Response / Relief', '(02) 8911-5061', 'NDRRMC', 'Quezon City', 9, '2025-10-27 04:37:13', NULL, NULL),
(17, 'test', 'test', 'test', 'test', 9, '2025-10-29 07:03:31', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(14, 'cascayo12ssi@gmail.com', 'c7fa25e9a58292bb94d6b2320fe7bc918245c678c718c68e1df5ea9d53edbbda', '2025-10-19 17:44:09', '2025-10-19 09:39:09'),
(20, 'cascayok@gmail.com', '547d49bf6b35bbbb4cc31c18f66adc6e83224ba49b2f16f64574fb88fa7d6f19', '2025-10-28 11:00:51', '2025-10-28 02:55:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `birthdate` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `profile_picture` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `birthdate`, `address`, `phone_number`, `profile_picture`) VALUES
(5, 'Karl PJ Ramos', 'alerto@sample.com', '$2y$10$3pBWJXTkjMmr8aXufPvWfOyj4rG5.Q.LptSRla4OK1htkmCEsuaku', 'user', '2025-09-21 13:26:55', '2025-09-30 16:54:00', NULL, NULL, NULL, NULL),
(7, 'Jan Karl Busayong', 'alert@sample.com', '$2y$10$GkkpKjEtBA7GH.o4YiheKOlE5rEVXAH2OqUvjnm0H5qAIhvy6Ga4e', 'user', '2025-09-21 14:10:08', '2025-09-30 17:26:55', NULL, NULL, NULL, NULL),
(8, 'Shele PJ Howard', 'alert1@sample.com', '$2y$10$zOWBk1Zg3EvaUukeKFJIMOBW/fjpj.jBjM5./Jbx3Re4JXpI2kidi', 'user', '2025-09-21 14:18:51', '2025-09-30 17:26:35', NULL, NULL, NULL, NULL),
(9, 'Admin1', 'admin1@alerto.com', '$2y$10$V3hXx5FMCLWJLQTuNvyaxOpOoRZUKIMSF2NkhsmlTrAZCHmu8eA1O', 'admin', '2025-09-29 15:38:39', '2025-10-19 11:18:33', '2025-10-19', 'CAVITE', '0919191919', 'uploads/profile_pictures/profile_9_1760872680.jpg'),
(10, 'james', 'mjr@gmail.com', '$2y$10$k6XO7BSKgotC5/Ze2MTEruwoVOEGMTB4NQknzSrfzjZYjOzdpSVZ.', 'user', '2025-10-01 01:52:22', '2025-10-01 01:52:22', NULL, NULL, NULL, NULL),
(11, 'Francheska Diaz', 'cheska@gmail.com', '$2y$10$IdhCCxMyfew.R6EZKOcggeJMHayHgxT1PUcUHn0T6gqrkBsSXIf0O', 'user', '2025-10-01 02:12:23', '2025-10-01 02:12:23', NULL, NULL, NULL, NULL),
(12, 'PJ Shele Cascayo', 'alerto3@gmail.com', '$2y$10$NtJ58PEvcnGgsQ1CScMJlue1YHj.ABULpz0AjKrunCFipZzTgWt/u', 'user', '2025-10-01 03:27:17', '2025-10-01 03:28:30', NULL, NULL, NULL, NULL),
(13, 'Cascayo Karl', 'cascayok@gmail.com', '$2y$10$dKYWGs.OM4ZZOfqJFPDhleJpyWFPAcpPKaUR2EgvpSaiydboJvqN.', 'user', '2025-10-13 06:44:03', '2025-10-21 16:18:57', '2025-10-22', 'etivacsummerhills molino', '0999999999', 'uploads/profile_pictures/profile_13_1761063518.png'),
(17, 'Ward Karl', 'cascayo12ssi@gmail.com', '$2y$10$5KFP326RTPw0xPZmO9swnuKUwBJoP6X5llGEFSteEwRfGzOp6P2Hm', 'user', '2025-10-26 14:11:51', '2025-10-26 14:11:51', NULL, NULL, NULL, NULL),
(18, 'Admin2', 'admin2@alerto.com', '$2y$10$xdtQhxUmqInvFoFZdUxjguZmZUkkDPpok8TnNYJKnwGQp60UVuwu2', 'admin', '2025-10-27 06:12:35', '2025-10-27 06:12:35', NULL, NULL, NULL, NULL),
(20, 'Erika Ablola', 'erikadrys@gmail.com', '$2y$10$akifFsYeBeUPr0LmXa2Va.Az3r/L.MSEGc9cBWjj/m37e.FlDnLCC', 'user', '2025-10-28 02:44:05', '2025-10-28 02:47:14', '2005-01-20', 'Navotas', '09614929303', 'uploads/profile_pictures/profile_20_1761619630.png'),
(21, 'Mobile Test', 'chowardkarl6125@gmail.com', '$2y$10$2BjOYxL16g7brx2q2cD2/e2I/bPNKsO6PMoOOVau145MXns193nnC', 'user', '2025-10-28 03:01:47', '2025-10-28 03:01:47', NULL, NULL, NULL, NULL),
(22, 'Rahowardo Otsodiretso', 'howtabs@gmail.com', '$2y$10$VvABVhzEKf5KPQ5hm/ALWeZ/dgndb3uFAc4bXkCkb84I9dQ9kKS.C', 'user', '2025-10-28 03:09:53', '2025-10-28 03:09:53', NULL, NULL, NULL, NULL),
(23, 'Christian Pogi', 'cj1009garcia@gmail.com', '$2y$10$oYveSi/rYVhtasmbxoKd0O4nD.6OTFo5u4EipvzjBlkJiJyVMUbdC', 'user', '2025-10-29 04:51:06', '2025-10-29 04:51:06', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `posted_by` (`posted_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `emergency_kit`
--
ALTER TABLE `emergency_kit`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hotlines`
--
ALTER TABLE `hotlines`
  ADD PRIMARY KEY (`hotline_id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `contact_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `emergency_kit`
--
ALTER TABLE `emergency_kit`
  MODIFY `item_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `hotlines`
--
ALTER TABLE `hotlines`
  MODIFY `hotline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `emergency_kit`
--
ALTER TABLE `emergency_kit`
  ADD CONSTRAINT `emergency_kit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hotlines`
--
ALTER TABLE `hotlines`
  ADD CONSTRAINT `hotlines_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `hotlines_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
