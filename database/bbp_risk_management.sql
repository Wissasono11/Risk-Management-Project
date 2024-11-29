-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 30, 2024 at 01:46 AM
-- Server version: 11.3.2-MariaDB-log
-- PHP Version: 7.4.33

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
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `fakultas`
--

INSERT INTO `fakultas` (`id`, `nama`, `kode`, `created_at`) VALUES
(1, 'Fakultas Adab dan Ilmu Budaya', 'FAIB', '2024-11-29 06:01:52'),
(2, 'Fakultas Dakwah dan Komunikasi', 'FDK', '2024-11-29 06:01:52'),
(3, 'Fakultas Ekonomi dan Bisnis Islam', 'FEBI', '2024-11-29 06:01:52'),
(4, 'Fakultas Ilmu Sosial dan Humaniora', 'FISHUM', '2024-11-29 06:01:52'),
(5, 'Fakultas Ilmu Tarbiyah dan Keguruan', 'FITK', '2024-11-29 06:01:52'),
(6, 'Fakultas Syariah dan Hukum', 'FSH', '2024-11-29 06:01:52'),
(7, 'Fakultas Sains dan Teknologi', 'FST', '2024-11-29 06:01:52'),
(8, 'Fakultas Ushuluddin dan Pemikiran Islam', 'FUPI', '2024-11-29 06:01:52');

-- --------------------------------------------------------

--
-- Table structure for table `mitigation_timeline`
--

CREATE TABLE `mitigation_timeline` (
  `id` int(11) NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `triwulan` int(11) NOT NULL COMMENT '1-4',
  `rencana` tinyint(1) DEFAULT 0,
  `realisasi` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `risk_categories`
--

CREATE TABLE `risk_categories` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `risk_categories`
--

INSERT INTO `risk_categories` (`id`, `nama`, `kode`, `description`, `created_at`) VALUES
(1, 'Risiko Strategik', 'RS', 'Risiko terkait strategi organisasi', '2024-11-29 06:17:44'),
(2, 'Risiko Operasional', 'RO', 'Risiko terkait operasional', '2024-11-29 06:17:44'),
(3, 'Risiko Finansial', 'RF', 'Risiko terkait keuangan', '2024-11-29 06:17:44');

-- --------------------------------------------------------

--
-- Table structure for table `risk_registers`
--

CREATE TABLE `risk_registers` (
  `id` int(11) NOT NULL,
  `fakultas_id` int(11) NOT NULL,
  `objective` text NOT NULL,
  `proses_bisnis` varchar(255) DEFAULT NULL,
  `kategori_id` int(11) NOT NULL,
  `kode_risiko` varchar(10) NOT NULL,
  `risk_event` text NOT NULL,
  `risk_cause` text DEFAULT NULL,
  `risk_source` enum('internal','external') NOT NULL,
  `severity_qualitative` text DEFAULT NULL,
  `severity_amount` decimal(20,2) DEFAULT NULL,
  `risk_owner` varchar(255) DEFAULT NULL,
  `unit_terkait` varchar(255) DEFAULT NULL,
  `likelihood_inherent` int(11) NOT NULL COMMENT '1-5',
  `impact_inherent` int(11) NOT NULL COMMENT '1-5',
  `risk_level_inherent` varchar(20) DEFAULT NULL,
  `existing_control_ada` enum('ada','tidak_ada') DEFAULT NULL,
  `existing_control_memadai` enum('memadai','belum_memadai') DEFAULT NULL,
  `existing_control_dijalankan` varchar(50) DEFAULT NULL,
  `likelihood_residual` int(11) NOT NULL COMMENT '1-5',
  `impact_residual` int(11) NOT NULL COMMENT '1-5',
  `risk_level_residual` varchar(20) DEFAULT NULL,
  `treatment_option` enum('accept','reduce','avoid','transfer') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `risk_treatments`
--

CREATE TABLE `risk_treatments` (
  `id` int(11) NOT NULL,
  `risk_register_id` int(11) NOT NULL,
  `rencana_mitigasi` text NOT NULL,
  `pic` varchar(255) NOT NULL,
  `evidence_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(64) NOT NULL COMMENT 'SHA256 encrypted',
  `role` enum('admin','fakultas') NOT NULL DEFAULT 'fakultas',
  `fakultas_id` int(11) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `fakultas_id`, `remember_token`, `created_at`, `last_login`) VALUES
(1, 'admin@uin-suka.ac.id', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin', NULL, 'ffcae4915e57add23dbace9f3921346981bf1ccea70b399d786cb564ccb3a799', '2024-11-29 06:01:52', '2024-11-29 18:40:54'),
(2, 'faib@uin-suka.ac.id', 'cd3cdf8dda64d6b1ec9fc722fe0130200d1a8c90609e0ef97e4e27dd25570f5a', 'fakultas', 1, NULL, '2024-11-29 06:01:52', NULL),
(3, 'fdk@uin-suka.ac.id', 'a9b35dc6e4d3c22db9a3bebc6cb6dc50d7202afb903f92e65d5351f91089b1b3', 'fakultas', 2, NULL, '2024-11-29 06:01:52', NULL),
(4, 'febi@uin-suka.ac.id', 'c81010e27ca75c11dc50fe0befca3b6c06c77490367425ff3dfd7cfe494f9ba6', 'fakultas', 3, NULL, '2024-11-29 06:01:52', NULL),
(5, 'fishum@uin-suka.ac.id', '9b1a29f6900f418c00bb833e8a2b764add0beb3f63b876cd6b82b0bd5fe84c45', 'fakultas', 4, NULL, '2024-11-29 06:01:52', NULL),
(6, 'fitk@uin-suka.ac.id', 'c9c6a57a60d748872ffb6068618f44052b176376d968d2fa083abe6325ca3580', 'fakultas', 5, NULL, '2024-11-29 06:01:52', NULL),
(7, 'fsh@uin-suka.ac.id', 'edc5a898e8993aed2f3d550d4728179edbe12e5f45536f95dcdf5d52feb0e622', 'fakultas', 6, NULL, '2024-11-29 06:01:52', NULL),
(8, 'fst@uin-suka.ac.id', '023e78827d24d122dd2a043ca1d9cf19b36538b78fbc5a553898481cbb35cf2b', 'fakultas', 7, NULL, '2024-11-29 06:01:52', NULL),
(9, 'fupi@uin-suka.ac.id', '1ef71fb491a714e5491e3ce9a593b37325b0a63913521e77e3312577bab749c0', 'fakultas', 8, NULL, '2024-11-29 06:01:52', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fakultas`
--
ALTER TABLE `fakultas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indexes for table `mitigation_timeline`
--
ALTER TABLE `mitigation_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `treatment_id` (`treatment_id`);

--
-- Indexes for table `risk_categories`
--
ALTER TABLE `risk_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indexes for table `risk_registers`
--
ALTER TABLE `risk_registers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fakultas_id` (`fakultas_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `risk_treatments`
--
ALTER TABLE `risk_treatments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `risk_register_id` (`risk_register_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fakultas`
--
ALTER TABLE `fakultas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mitigation_timeline`
--
ALTER TABLE `mitigation_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `risk_categories`
--
ALTER TABLE `risk_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `risk_registers`
--
ALTER TABLE `risk_registers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `risk_treatments`
--
ALTER TABLE `risk_treatments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mitigation_timeline`
--
ALTER TABLE `mitigation_timeline`
  ADD CONSTRAINT `mitigation_timeline_ibfk_1` FOREIGN KEY (`treatment_id`) REFERENCES `risk_treatments` (`id`);

--
-- Constraints for table `risk_registers`
--
ALTER TABLE `risk_registers`
  ADD CONSTRAINT `risk_registers_ibfk_1` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas` (`id`),
  ADD CONSTRAINT `risk_registers_ibfk_2` FOREIGN KEY (`kategori_id`) REFERENCES `risk_categories` (`id`);

--
-- Constraints for table `risk_treatments`
--
ALTER TABLE `risk_treatments`
  ADD CONSTRAINT `risk_treatments_ibfk_1` FOREIGN KEY (`risk_register_id`) REFERENCES `risk_registers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
