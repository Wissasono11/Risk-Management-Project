-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 23, 2024 at 12:41 PM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fakultas`
--

INSERT INTO `fakultas` (`id`, `nama`, `kode`, `created_at`) VALUES
(1, 'Fakultas Adab dan Ilmu Budaya', 'FAIB', '2024-12-22 17:41:22'),
(2, 'Fakultas Dakwah dan Komunikasi', 'FDK', '2024-12-22 17:41:22'),
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
  `id` int(11) NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `triwulan` int(11) NOT NULL,
  `rencana` varchar(255) DEFAULT NULL,
  `realisasi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mitigation_timeline`
--

INSERT INTO `mitigation_timeline` (`id`, `treatment_id`, `tahun`, `triwulan`, `rencana`, `realisasi`, `created_at`) VALUES
(7, 7, 2024, 1, 'Pemeliharaan berkala tahap pertama', 'Selesai', '2024-12-22 08:00:00'),
(8, 8, 2024, 2, 'Pelatihan awal untuk pendakwah', 'Progres', '2024-12-22 08:30:00'),
(9, 9, 2024, 3, 'Penerapan metode baru investasi', 'Progres', '2024-12-22 09:00:00'),
(10, 10, 2024, 4, 'Publikasi ulang strategi komunikasi', 'Belum', '2024-12-22 09:30:00'),
(11, 11, 2024, 4, 'Pengumuman rekrutmen tambahan', 'Selesai', '2024-12-22 10:00:00'),
(12, 12, 2024, 1, 'Verifikasi dokumen awal kontrak', 'Progres', '2024-12-22 10:30:00'),
(13, 13, 2024, 2, 'Penggantian alat laboratorium tahap 1', 'Progres', '2024-12-22 10:45:00'),
(14, 14, 2024, 3, 'Kajian tahap awal dengan moderasi', 'Selesai', '2024-12-22 11:15:00'),
(31, 15, 2024, 4, 'Evaluasi artikel', '1', '2024-12-22 13:00:00'),
(32, 16, 2024, 4, 'Distribusi bahan ajar', '1', '2024-12-22 13:30:00'),
(33, 17, 2024, 4, 'Promosi partisipasi mahasiswa', '0', '2024-12-22 14:00:00'),
(34, 18, 2024, 4, 'SOP komunikasi dengan media', '1', '2024-12-22 14:30:00'),
(35, 19, 2024, 4, 'Monitoring anggaran', '1', '2024-12-22 15:00:00'),
(36, 20, 2024, 4, 'Jaringan sponsor', '0', '2024-12-22 15:30:00'),
(51, 21, 2024, 3, 'Pengujian sistem keamanan', '1', '2024-12-23 15:00:00'),
(52, 22, 2024, 4, 'Sosialisasi kebijakan baru', '0', '2024-12-23 16:00:00'),
(53, 31, 2024, 2, NULL, NULL, '2024-12-22 20:14:57'),
(54, 32, 2024, 2, NULL, NULL, '2024-12-22 20:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `risk_categories`
--

CREATE TABLE `risk_categories` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL
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
  `id` int(11) NOT NULL,
  `fakultas_id` int(11) DEFAULT NULL,
  `objective` text DEFAULT NULL,
  `proses_bisnis` varchar(255) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `risk_event` text DEFAULT NULL,
  `risk_cause` text DEFAULT NULL,
  `risk_source` enum('internal','external') DEFAULT 'internal',
  `risk_owner` varchar(255) DEFAULT NULL,
  `likelihood_inherent` int(11) DEFAULT NULL,
  `impact_inherent` int(11) DEFAULT NULL,
  `risk_level_inherent` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risk_registers`
--

INSERT INTO `risk_registers` (`id`, `fakultas_id`, `objective`, `proses_bisnis`, `kategori_id`, `risk_event`, `risk_cause`, `risk_source`, `risk_owner`, `likelihood_inherent`, `impact_inherent`, `risk_level_inherent`, `created_at`, `updated_at`) VALUES
(19, 1, 'Memastikan ketersediaan fasilitas belajar', 'Manajemen Fasilitas', 1, 'Kerusakan fasilitas ruang kelas', 'Pemeliharaan yang tidak memadai', 'internal', 'Dept. Fasilitas', 4, 5, 'Very-High', '2024-12-22 07:30:00', NULL),
(20, 2, 'Memastikan keberlanjutan program dakwah', 'Penyelenggaraan Acara', 6, 'Kritik terhadap metode dakwah', 'Kurangnya pelatihan pendakwah', 'external', 'Dept. Dakwah', 3, 4, 'High', '2024-12-22 08:00:00', NULL),
(21, 3, 'Mengelola investasi keuangan', 'Manajemen Keuangan', 3, 'Kerugian dalam investasi', 'Kesalahan pengambilan keputusan', 'internal', 'Dept. Keuangan', 5, 5, 'Very-High', '2024-12-22 08:30:00', NULL),
(22, 4, 'Menjaga reputasi fakultas di media sosial', 'Publikasi Media', 6, 'Kritik publik di media sosial', 'Komunikasi yang kurang efektif', 'external', 'Dept. Komunikasi', 2, 4, 'Medium', '2024-12-22 09:00:00', NULL),
(23, 5, 'Memastikan kualitas pengajaran', 'Proses Pengajaran', 1, 'Kekurangan tenaga pengajar', 'Kurangnya rekrutmen', 'internal', 'Dept. Akademik', 3, 3, 'Medium', '2024-12-22 09:30:00', NULL),
(24, 6, 'Memastikan kepatuhan terhadap hukum syariah', 'Audit Hukum', 4, 'Pelanggaran syariah dalam kontrak', 'Dokumen tidak diverifikasi', 'external', 'Dept. Hukum', 4, 4, 'High', '2024-12-22 10:00:00', NULL),
(25, 7, 'Memastikan keandalan laboratorium penelitian', 'Manajemen Laboratorium', 2, 'Kerusakan peralatan laboratorium', 'Kurangnya penggantian peralatan', 'internal', 'Dept. Penelitian', 5, 4, 'Very-High', '2024-12-22 10:30:00', NULL),
(26, 8, 'Menjaga standar kajian keagamaan', 'Kajian Keagamaan', 5, 'Kontroversi dalam kajian keagamaan', 'Kurangnya moderasi', 'external', 'Dept. Kajian', 3, 5, 'High', '2024-12-22 11:00:00', NULL),
(27, 1, 'Meningkatkan kualitas publikasi ilmiah', 'Manajemen Publikasi', 1, 'Kurangnya artikel yang diterbitkan', 'Kurangnya bimbingan penulisan', 'internal', 'Dept. Publikasi', 2, 2, 'Low', '2024-12-22 12:00:00', NULL),
(28, 1, 'Memastikan ketersediaan bahan ajar', 'Distribusi Bahan Ajar', 2, 'Keterlambatan pengiriman bahan ajar', 'Kurangnya koordinasi dengan distributor', 'external', 'Dept. Logistik', 3, 2, 'Medium', '2024-12-22 12:30:00', NULL),
(29, 2, 'Meningkatkan partisipasi mahasiswa dalam dakwah', 'Kegiatan Mahasiswa', 1, 'Kurangnya antusiasme mahasiswa', 'Minimnya insentif', 'internal', 'Dept. Mahasiswa', 2, 3, 'Medium', '2024-12-22 13:00:00', NULL),
(30, 2, 'Menjaga hubungan baik dengan media', 'Kerja Sama Media', 4, 'Kerusakan hubungan dengan media', 'Kesalahan komunikasi', 'external', 'Dept. Komunikasi', 1, 2, 'Low', '2024-12-22 13:30:00', NULL),
(31, 3, 'Meningkatkan efisiensi anggaran operasional', 'Pengelolaan Anggaran', 3, 'Pemborosan anggaran', 'Kurangnya monitoring', 'internal', 'Dept. Keuangan', 2, 2, 'Low', '2024-12-22 14:00:00', NULL),
(32, 3, 'Memastikan keberlanjutan program kewirausahaan', 'Program Kewirausahaan', 1, 'Minimnya dukungan sponsor', 'Kurangnya jaringan dengan sponsor', 'external', 'Dept. Kewirausahaan', 3, 3, 'Medium', '2024-12-22 14:30:00', NULL),
(33, 4, 'Memastikan keamanan acara fakultas', 'Penyelenggaraan Acara', 2, 'Insiden selama acara berlangsung', 'Kurangnya pengamanan', 'external', 'Dept. Acara', 2, 2, 'Low', '2024-12-22 15:00:00', NULL),
(34, 4, 'Meningkatkan kolaborasi penelitian', 'Kerja Sama Penelitian', 3, 'Kurangnya kolaborasi dengan universitas lain', 'Minimnya anggaran penelitian', 'internal', 'Dept. Penelitian', 3, 2, 'Medium', '2024-12-22 15:30:00', NULL),
(35, 5, 'Meningkatkan kualitas pelatihan guru', 'Pelatihan Guru', 1, 'Kekurangan peserta pelatihan', 'Kurangnya promosi program', 'internal', 'Dept. Pelatihan', 2, 3, 'Medium', '2024-12-22 16:00:00', NULL),
(36, 5, 'Memastikan keberlanjutan program beasiswa', 'Manajemen Beasiswa', 4, 'Minimnya dana beasiswa', 'Kurangnya dukungan sponsor', 'external', 'Dept. Keuangan', 1, 2, 'Low', '2024-12-22 16:30:00', NULL),
(37, 6, 'Memastikan akurasi penilaian', 'Evaluasi Hukum', 2, 'Kesalahan dalam evaluasi dokumen hukum', 'Kurangnya keahlian evaluasi', 'internal', 'Dept. Evaluasi', 2, 2, 'Low', '2024-12-22 17:00:00', NULL),
(38, 6, 'Meningkatkan aksesibilitas layanan hukum', 'Layanan Hukum', 4, 'Minimnya akses layanan bagi mahasiswa', 'Kurangnya sistem online', 'external', 'Dept. Layanan', 3, 2, 'Medium', '2024-12-22 17:30:00', NULL),
(39, 7, 'Memastikan stabilitas jaringan IT', 'Manajemen IT', 1, 'Gangguan jaringan selama ujian online', 'Kurangnya perawatan server', 'internal', 'Dept. IT', 2, 3, 'Medium', '2024-12-22 18:00:00', NULL),
(40, 7, 'Menjaga keberlanjutan penelitian sains', 'Manajemen Penelitian', 3, 'Minimnya hasil penelitian baru', 'Kurangnya dana penelitian', 'external', 'Dept. Penelitian', 1, 2, 'Low', '2024-12-22 18:30:00', NULL),
(41, 8, 'Meningkatkan partisipasi dalam diskusi keagamaan', 'Diskusi Keagamaan', 1, 'Kurangnya peserta diskusi', 'Minimnya promosi acara', 'internal', 'Dept. Kajian', 2, 2, 'Low', '2024-12-22 19:00:00', NULL),
(42, 8, 'Memastikan moderasi kajian agama', 'Moderasi Kajian', 4, 'Ketegangan dalam kajian agama', 'Kurangnya moderasi', 'external', 'Dept. Moderasi', 3, 3, 'Medium', '2024-12-22 19:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `risk_treatments`
--

CREATE TABLE `risk_treatments` (
  `id` int(11) NOT NULL,
  `risk_register_id` int(11) NOT NULL,
  `rencana_mitigasi` text DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `evidence_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risk_treatments`
--

INSERT INTO `risk_treatments` (`id`, `risk_register_id`, `rencana_mitigasi`, `pic`, `evidence_type`, `created_at`) VALUES
(7, 19, 'Pemeliharaan berkala fasilitas', 'Tim Fasilitas', 'Laporan Pemeliharaan', '2024-12-22 07:45:00'),
(8, 20, 'Pelatihan tambahan bagi pendakwah', 'Dept. Dakwah', 'Dokumen Training', '2024-12-22 08:15:00'),
(9, 21, 'Peningkatan analisis risiko investasi', 'Dept. Keuangan', 'Laporan Risiko', '2024-12-22 08:45:00'),
(10, 22, 'Peningkatan strategi komunikasi', 'Dept. Komunikasi', 'Dokumen Strategi', '2024-12-22 09:15:00'),
(11, 23, 'Rekrutmen tenaga pengajar tambahan', 'Dept. Akademik', 'Dokumen Rekrutmen', '2024-12-22 09:45:00'),
(12, 24, 'Verifikasi kontrak oleh tim ahli', 'Dept. Hukum', 'Audit Dokumen', '2024-12-22 10:15:00'),
(13, 25, 'Penggantian peralatan usang', 'Dept. Penelitian', 'Proposal Pengadaan', '2024-12-22 10:45:00'),
(14, 26, 'Moderasi ketat dalam kajian', 'Dept. Kajian', 'Laporan Moderasi', '2024-12-22 11:15:00'),
(15, 27, 'Penyediaan bimbingan penulisan artikel', 'Dept. Publikasi', 'Dokumen Bimbingan', '2024-12-22 12:15:00'),
(16, 28, 'Koordinasi rutin dengan distributor', 'Dept. Logistik', 'Laporan Koordinasi', '2024-12-22 12:45:00'),
(17, 29, 'Pemberian insentif untuk partisipasi mahasiswa', 'Dept. Mahasiswa', 'Dokumen Insentif', '2024-12-22 13:15:00'),
(18, 30, 'Pengembangan SOP komunikasi dengan media', 'Dept. Komunikasi', 'Dokumen SOP', '2024-12-22 13:45:00'),
(19, 31, 'Peningkatan monitoring anggaran', 'Dept. Keuangan', 'Laporan Monitoring', '2024-12-22 14:15:00'),
(20, 32, 'Peningkatan jaringan dengan sponsor', 'Dept. Kewirausahaan', 'Dokumen Kerja Sama', '2024-12-22 14:45:00'),
(21, 33, 'Penyediaan pengamanan acara fakultas', 'Dept. Acara', 'Dokumen Pengamanan', '2024-12-22 15:15:00'),
(22, 34, 'Alokasi tambahan anggaran penelitian', 'Dept. Penelitian', 'Laporan Anggaran', '2024-12-22 15:45:00'),
(23, 35, 'Promosi program pelatihan guru', 'Dept. Pelatihan', 'Dokumen Promosi', '2024-12-22 16:15:00'),
(24, 36, 'Pencarian sponsor tambahan', 'Dept. Keuangan', 'Proposal Sponsor', '2024-12-22 16:45:00'),
(25, 37, 'Pelatihan evaluasi untuk staf hukum', 'Dept. Evaluasi', 'Dokumen Pelatihan', '2024-12-22 17:15:00'),
(26, 38, 'Pengembangan sistem layanan online', 'Dept. Layanan', 'Dokumen Sistem', '2024-12-22 17:45:00'),
(27, 39, 'Pemeliharaan rutin server IT', 'Dept. IT', 'Dokumen Pemeliharaan', '2024-12-22 18:15:00'),
(28, 40, 'Peningkatan dana penelitian sains', 'Dept. Penelitian', 'Proposal Dana', '2024-12-22 18:45:00'),
(31, 0, 'asdasd', 'asd', 'sadas', '2024-12-22 20:14:57'),
(32, 28, 'Meningkatkan kesejahteraan rakyat hahaha', 'RBW', 'Dokumen Administratif', '2024-12-22 20:31:01');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `fakultas_id`, `remember_token`, `created_at`, `last_login`, `profile_picture`) VALUES
(1, 'admin@uin-suka.ac.id', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin', NULL, NULL, '2024-12-21 11:40:20', '2024-12-22 21:30:46', 'default.jpg'),
(2, 'faib@uin-suka.ac.id', 'cd3cdf8dda64d6b1ec9fc722fe0130200d1a8c90609e0ef97e4e27dd25570f5a', 'fakultas', 1, '596fa34f68882c62572691931801bfe6489e52ee72710c689db0bcaf0707afb4', '2024-12-21 11:40:20', '2024-12-23 05:21:12', 'default.jpg'),
(3, 'fdk@uin-suka.ac.id', 'a9b35dc6e4d3c22db9a3bebc6cb6dc50d7202afb903f92e65d5351f91089b1b3', 'fakultas', 2, NULL, '2024-12-21 11:40:20', '2024-12-22 12:46:48', 'default.jpg'),
(4, 'febi@uin-suka.ac.id', 'c81010e27ca75c11dc50fe0befca3b6c06c77490367425ff3dfd7cfe494f9ba6', 'fakultas', 3, NULL, '2024-12-21 11:40:20', NULL, 'default.jpg'),
(5, 'fishum@uin-suka.ac.id', '9b1a29f6900f418c00bb833e8a2b764add0beb3f63b876cd6b82b0bd5fe84c45', 'fakultas', 4, NULL, '2024-12-21 11:40:20', NULL, 'default.jpg'),
(6, 'fitk@uin-suka.ac.id', 'c9c6a57a60d748872ffb6068618f44052b176376d968d2fa083abe6325ca3580', 'fakultas', 5, NULL, '2024-12-21 11:40:20', '2024-12-22 16:20:53', 'default.jpg'),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mitigation_timeline`
--
ALTER TABLE `mitigation_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `risk_categories`
--
ALTER TABLE `risk_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `risk_registers`
--
ALTER TABLE `risk_registers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `risk_treatments`
--
ALTER TABLE `risk_treatments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
