-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jan 09, 2026 at 01:54 PM
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
-- Database: `claire_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `pathologist_table`
--

CREATE TABLE `pathologist_table` (
  `pathologist_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pathologist_table`
--

INSERT INTO `pathologist_table` (`pathologist_id`, `full_name`, `email`, `password`, `created_at`, `updated_at`) VALUES
('MD-2025-0042', 'Dr. Maria Santos, FPSP', 'msantos@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0043', 'Dr. Agnes Tachyon, MD', 'tachyonagnes@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0044', 'Dr. Daiwa Scarlet, FPSP', 'daiwascarlet@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0045', 'Dr. Ricardo Gomez, MD', 'rgomez@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0046', 'Dr. Sofia Ramos, FPSP', 'sramos@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0047', 'Dr. Ross Geller, MD', 'rossgeller@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0048', 'Dr. Beatrice Lim, FPSP', 'blim@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0049', 'Dr. Manuel Roxas, MD', 'mroxas@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0050', 'Dr. Clara Zobel, FPSP', 'czobel@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2025-0051', 'Dr. Gabriel Silva, MD', 'gsilva@gmail.com', 'admin123', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('MD-2026-6207', 'Dr. Stacey Ballares, MD', 'sdballares@gmail.com', '$2y$10$1kUSgWpg1zfoBVP3j6K/4uyrG6NQ/MFS0knFtRFHA9/eo5sqLjndq', '2026-01-06 20:59:19', '2026-01-06 20:59:19');

-- --------------------------------------------------------

--
-- Table structure for table `patients_table`
--

CREATE TABLE `patients_table` (
  `patient_id` varchar(20) NOT NULL,
  `pathologist_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('M','F') NOT NULL DEFAULT 'F',
  `contact_number` varchar(15) DEFAULT NULL,
  `diagnosis_history` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients_table`
--

INSERT INTO `patients_table` (`patient_id`, `pathologist_id`, `full_name`, `dob`, `gender`, `contact_number`, `diagnosis_history`, `created_at`, `updated_at`) VALUES
('PT-00895', 'MD-2025-0042', 'Juana A. Dela Cruz', '1988-06-12', 'F', '0917-123-4567', 'History of HPV infection (2021).', '2026-01-06 19:33:46', '2026-01-06 22:35:09'),
('PT-00896', 'MD-2025-0042', 'Annie Batumbakal', '1960-02-12', 'F', '0917-123-4567', 'HPV Positive', '2026-01-09 17:33:42', '2026-01-09 17:33:42'),
('PT-00900', 'MD-2025-0043', 'Elena Reyes', '1995-03-25', 'F', '0918-999-8888', 'Routine checkup.', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
('PT-00905', 'MD-2025-0042', 'Liza Soberano', '1990-01-10', 'F', '0922-333-4444', 'No prior complications.', '2026-01-06 19:33:46', '2026-01-06 19:33:46');

-- --------------------------------------------------------

--
-- Table structure for table `patient_records_table`
--

CREATE TABLE `patient_records_table` (
  `record_id` int(11) NOT NULL,
  `patient_id` varchar(20) NOT NULL,
  `report_date` date NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `ai_prediction` enum('Dyskeratotic','Koilocytotic','Metaplastic','Parabasal','Superficial','Intermediate') NOT NULL,
  `findings` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_records_table`
--

INSERT INTO `patient_records_table` (`record_id`, `patient_id`, `report_date`, `image_path`, `ai_prediction`, `findings`, `created_at`, `updated_at`) VALUES
(1, 'PT-00895', '2025-02-10', '/uploads/PT00895_slide3_crop.jpg', 'Dyskeratotic', 'Cells show dense cytoplasm and abnormal keratinization. Suggestive of LSIL.', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
(2, 'PT-00900', '2025-02-15', '/uploads/PT00900_slide1.jpg', 'Parabasal', 'Normal cell distribution observed.', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
(3, 'PT-00905', '2025-02-20', '/uploads/PT00905_slide2.jpg', 'Intermediate', 'Healthy cell patterns identified.', '2026-01-06 19:33:46', '2026-01-06 19:33:46'),
(15, 'PT-00896', '2026-01-09', 'PT-00896_1767951281.jpg', 'Parabasal', 'Complications are succeeding', '2026-01-09 17:34:41', '2026-01-09 17:34:41'),
(17, 'PT-00895', '2026-01-09', 'PT-00895_1767962223.jpg', 'Koilocytotic', '', '2026-01-09 20:37:03', '2026-01-09 20:37:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pathologist_table`
--
ALTER TABLE `pathologist_table`
  ADD PRIMARY KEY (`pathologist_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `patients_table`
--
ALTER TABLE `patients_table`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `pathologist_id` (`pathologist_id`),
  ADD KEY `idx_patient_name` (`full_name`);

--
-- Indexes for table `patient_records_table`
--
ALTER TABLE `patient_records_table`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `patient_records_table`
--
ALTER TABLE `patient_records_table`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `patients_table`
--
ALTER TABLE `patients_table`
  ADD CONSTRAINT `patients_table_ibfk_1` FOREIGN KEY (`pathologist_id`) REFERENCES `pathologist_table` (`pathologist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patient_records_table`
--
ALTER TABLE `patient_records_table`
  ADD CONSTRAINT `patient_records_table_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients_table` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
