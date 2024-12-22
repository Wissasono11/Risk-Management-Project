-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 22, 2024 at 01:20 PM
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
-- Database: `bbp_risk_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `fakultas`
--

CREATE TABLE `fakultas` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fakultas`
--

INSERT INTO `fakultas` (`id`, `nama`, `kode`, `created_at`) VALUES
(1, 'Fakultas Adab dan Ilmu Budaya', 'FAIB', '2024-12-21 11:40:20'),
(2, 'Fakultas Dakwah dan Komunikasi', 'FDK', '2024-12-21 11:40:20'),
(3, 'Fakultas Ekonomi dan Bisnis Islam', 'FEBI', '2024-12-21 11:40:20'),
(4, 'Fakultas Ilmu Sosial dan Humaniora', 'FISHUM', '2024-12-21 11:40:20'),
(5, 'Fakultas Ilmu Tarbiyah dan Keguruan', 'FITK', '2024-12-21 11:40:20'),
(6, 'Fakultas Syariah dan Hukum', 'FSH', '2024-12-21 11:40:20'),
(7, 'Fakultas Sains dan Teknologi', 'FST', '2024-12-21 11:40:20'),
(8, 'Fakultas Ushuluddin dan Pemikiran Islam', 'FUPI', '2024-12-21 11:40:20');

-- --------------------------------------------------------

--
-- Table structure for table `mitigation_timeline`
--

CREATE TABLE `mitigation_timeline` (
  `id` int NOT NULL,
  `treatment_id` int NOT NULL,
  `tahun` int NOT NULL,
  `triwulan` int NOT NULL,
  `rencana` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `realisasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mitigation_timeline`
--

INSERT INTO `mitigation_timeline` (`id`, `treatment_id`, `tahun`, `triwulan`, `rencana`, `realisasi`, `created_at`) VALUES
(1, 1, 2024, 4, '1', '1', '2024-12-22 05:15:00'),
(2, 2, 2024, 4, '1', '1', '2024-12-22 05:45:00'),
(3, 1, 2024, 4, 'Plan 1', 'Realization 1', '2024-12-22 03:45:00'),
(4, 2, 2024, 4, 'Plan 2', 'Realization 2', '2024-12-22 04:45:00'),
(5, 1, 2024, 4, 'Plan 1', 'Realization 1', '2024-12-22 03:45:00'),
(6, 2, 2024, 4, 'Plan 2', 'Realization 2', '2024-12-22 04:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `risk_categories`
--

CREATE TABLE `risk_categories` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risk_categories`
--

INSERT INTO `risk_categories` (`id`, `nama`) VALUES
(1, 'Strategic Risk'),
(2, 'Operational Risk'),
(3, 'Financial Risk'),
(4, 'Compliance Risk');

-- --------------------------------------------------------

--
-- Table structure for table `risk_registers`
--

CREATE TABLE `risk_registers` (
  `id` int NOT NULL,
  `fakultas_id` int DEFAULT NULL,
  `objective` text COLLATE utf8mb4_general_ci,
  `proses_bisnis` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kategori_id` int DEFAULT NULL,
  `risk_event` text COLLATE utf8mb4_general_ci,
  `risk_cause` text COLLATE utf8mb4_general_ci,
  `risk_source` enum('internal','external') COLLATE utf8mb4_general_ci DEFAULT 'internal',
  `risk_owner` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `likelihood_inherent` int DEFAULT NULL,
  `impact_inherent` int DEFAULT NULL,
  `risk_level_inherent` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risk_registers`
--

INSERT INTO `risk_registers` (`id`, `fakultas_id`, `objective`, `proses_bisnis`, `kategori_id`, `risk_event`, `risk_cause`, `risk_source`, `risk_owner`, `likelihood_inherent`, `impact_inherent`, `risk_level_inherent`, `created_at`, `updated_at`) VALUES
(3, NULL, 'tesrser', 'serser', 1, 'ser', 'ser', 'external', 'ser', 5, 5, 'VERYHIGH', '2024-12-22 06:04:40', '2024-12-22 11:12:45'),
(4, NULL, 'awdawd', 'awdawd', 1, 'awdawd', 'awdawd', 'external', 'awdawd', 5, 1, 'HIGH', '2024-12-22 11:03:48', '2024-12-22 11:12:45'),
(6, NULL, 'awdawd', 'awd', 1, 'awdaw', 'awdwad', 'internal', 'awd', 5, 4, 'Very-High', '2024-12-22 11:43:36', NULL),
(7, NULL, 'awdawd', 'awd', 1, 'awdawd', 'awdawd', 'internal', 'awdawd', 3, 4, 'High', '2024-12-22 11:44:17', NULL),
(8, NULL, 'asdas', 'asdasd', 1, 'asdasd', 'asd', 'external', 'asdasd', 5, 4, 'Very-High', '2024-12-22 11:46:21', NULL),
(9, NULL, 'asdasd', 'asdasd', 2, 'asdasd', 'asdasd', 'internal', 'asdd', 3, 3, 'Medium', '2024-12-22 11:46:30', NULL),
(10, NULL, 'asdasd', 'asd', 1, 'asdasd', 'asdasd', 'internal', 'asdasd', 4, 3, 'High', '2024-12-22 11:48:05', NULL),
(11, NULL, 'asdasd', 'asdasd', 1, 'asd', 'asdasd', 'internal', 'asd', 5, 5, 'Very-High', '2024-12-22 11:50:18', NULL),
(12, 1, 'Objective 1', 'Business Process 1', 1, 'Risk Event 1', 'Cause 1', 'internal', 'Owner 1', 3, 4, 'High', '2024-12-22 03:00:00', NULL),
(13, 1, 'Objective 2', 'Business Process 2', 2, 'Risk Event 2', 'Cause 2', 'external', 'Owner 2', 5, 5, 'Very-High', '2024-12-22 04:00:00', NULL),
(14, NULL, 'asdasd', 'dsa', 3, 'asd', 'asd', 'external', 'asd', 1, 1, 'Low', '2024-12-22 11:52:10', NULL),
(15, 1, 'Objective 1', 'Business Process 1', 1, 'Risk Event 1', 'Cause 1', 'internal', 'Owner 1', 3, 4, 'High', '2024-12-22 03:00:00', NULL),
(16, 1, 'Objective 2', 'Business Process 2', 2, 'Risk Event 2', 'Cause 2', 'external', 'Owner 2', 5, 5, 'Very-High', '2024-12-22 04:00:00', NULL),
(17, NULL, 'fawsd', 'awdawd', 1, 'awdawd', 'awdawd', 'internal', 'awdawd', 1, 1, 'Low', '2024-12-22 12:56:25', NULL),
(18, NULL, 'awdaw', 'dawdawd', 2, 'awdawd', 'wadawd', 'internal', 'adwawd', 5, 3, 'High', '2024-12-22 12:56:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `risk_treatments`
--

CREATE TABLE `risk_treatments` (
  `id` int NOT NULL,
  `risk_register_id` int NOT NULL,
  `rencana_mitigasi` text COLLATE utf8mb4_general_ci,
  `pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `evidence_type` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risk_treatments`
--

INSERT INTO `risk_treatments` (`id`, `risk_register_id`, `rencana_mitigasi`, `pic`, `evidence_type`, `created_at`) VALUES
(1, 3, 'Implementasi kontrol X untuk mengurangi risiko serangan', 'John Doe', 'Dokumen Kontrol', '2024-12-22 05:00:00'),
(2, 4, 'Pelatihan staf untuk meningkatkan kesadaran keamanan', 'Jane Smith', 'Catatan Pelatihan', '2024-12-22 05:30:00'),
(3, 1, 'Mitigation Plan 1', 'PIC 1', 'Document', '2024-12-22 03:30:00'),
(4, 2, 'Mitigation Plan 2', 'PIC 2', 'Training', '2024-12-22 04:30:00'),
(5, 1, 'Mitigation Plan 1', 'PIC 1', 'Document', '2024-12-22 03:30:00'),
(6, 2, 'Mitigation Plan 2', 'PIC 2', 'Training', '2024-12-22 04:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA256 encrypted',
  `role` enum('admin','fakultas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fakultas',
  `fakultas_id` int DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `fakultas_id`, `remember_token`, `created_at`, `last_login`, `profile_picture`) VALUES
(1, 'admin@uin-suka.ac.id', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin', NULL, NULL, '2024-12-21 11:40:20', '2024-12-22 13:09:08', 'default.jpg'),
(2, 'faib@uin-suka.ac.id', 'cd3cdf8dda64d6b1ec9fc722fe0130200d1a8c90609e0ef97e4e27dd25570f5a', 'fakultas', 1, NULL, '2024-12-21 11:40:20', '2024-12-22 13:19:17', 'profile_2_1734873546.jpg'),
(3, 'fdk@uin-suka.ac.id', 'a9b35dc6e4d3c22db9a3bebc6cb6dc50d7202afb903f92e65d5351f91089b1b3', 'fakultas', 2, NULL, '2024-12-21 11:40:20', '2024-12-22 12:46:48', 'default.jpg'),
(4, 'febi@uin-suka.ac.id', 'c81010e27ca75c11dc50fe0befca3b6c06c77490367425ff3dfd7cfe494f9ba6', 'fakultas', 3, NULL, '2024-12-21 11:40:20', NULL, 'default.jpg'),
(5, 'fishum@uin-suka.ac.id', '9b1a29f6900f418c00bb833e8a2b764add0beb3f63b876cd6b82b0bd5fe84c45', 'fakultas', 4, NULL, '2024-12-21 11:40:20', NULL, 'default.jpg'),
(6, 'fitk@uin-suka.ac.id', 'c9c6a57a60d748872ffb6068618f44052b176376d968d2fa083abe6325ca3580', 'fakultas', 5, NULL, '2024-12-21 11:40:20', NULL, 'default.jpg'),
(7, 'fsh@uin-suka.ac.id', 'edc5a898e8993aed2f3d550d4728179edbe12e5f45536f95dcdf5d52feb0e622', 'fakultas', 6, NULL, '2024-12-21 11:40:20', NULL, 'default.jpg'),
(8, 'fst@uin-suka.ac.id', '023e78827d24d122dd2a043ca1d9cf19b36538b78fbc5a553898481cbb35cf2b', 'fakultas', 7, NULL, '2024-12-21 11:40:20', NULL, 'default.jpg'),
(9, 'fupi@uin-suka.ac.id', '1ef71fb491a714e5491e3ce9a593b37325b0a63913521e77e3312577bab749c0', 'fakultas', 8, NULL, '2024-12-21 11:40:20', NULL, 'default.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fakultas`
--
ALTER TABLE `fakultas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `idx_kode` (`kode`);

--
-- Indexes for table `mitigation_timeline`
--
ALTER TABLE `mitigation_timeline`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `risk_categories`
--
ALTER TABLE `risk_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `risk_registers`
--
ALTER TABLE `risk_registers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `risk_treatments`
--
ALTER TABLE `risk_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fakultas`
--
ALTER TABLE `fakultas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mitigation_timeline`
--
ALTER TABLE `mitigation_timeline`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `risk_categories`
--
ALTER TABLE `risk_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `risk_registers`
--
ALTER TABLE `risk_registers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `risk_treatments`
--
ALTER TABLE `risk_treatments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
