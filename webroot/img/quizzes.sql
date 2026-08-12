-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2026 at 10:43 AM
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
-- Database: `newlmsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `quizname` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) DEFAULT 30 COMMENT 'Duration in minutes',
  `total_marks` int(11) DEFAULT 0,
  `pass_mark` int(11) DEFAULT 40,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `datecreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `teacher_id`, `subject_id`, `semester_id`, `session_id`, `quizname`, `description`, `duration`, `total_marks`, `pass_mark`, `start_date`, `end_date`, `status`, `datecreated`) VALUES
(1, 1, 1, 1, 8, 'Test Quiz', 'This is a test quiz', 10, 10, 80, '2025-10-30 08:30:00', '2025-11-01 22:30:00', 'active', '2025-10-31 03:27:35'),
(2, 2, 4, 1, 8, 'hi', 'hi', 30, 0, 40, '2025-11-05 15:14:00', '2025-11-05 15:14:00', 'active', '2025-11-04 14:14:28'),
(3, 2, 4, 1, 8, 'hi e r', 'res', 30, 0, 40, '2025-11-07 14:27:00', '2025-11-08 14:27:00', 'active', '2025-11-07 13:27:14'),
(4, 1, 4, 1, 8, 'hi', 'hi', 30, 0, 40, '2025-11-10 15:12:00', '2025-11-15 15:12:00', 'active', '2025-11-10 14:12:59'),
(6, 14, 23, 1, 8, 'what is igbo', 'hi', 30, 3, 40, '2025-11-20 11:15:00', '2025-11-21 12:28:00', 'active', '2025-11-20 11:28:41'),
(8, 14, 12, 1, 8, 'History in', 'history', 30, 65, 40, '2025-11-20 12:53:00', '2025-11-21 12:55:00', 'active', '2025-11-20 11:55:47'),
(9, 14, 23, 1, 8, '65r7', '535e655', 30, 0, 40, '2025-11-20 13:03:00', '2025-11-22 13:03:00', 'active', '2025-11-20 12:03:57'),
(10, 15, 24, 1, 8, 'nigeria', 'HISTORY OF NIGERIA', 30, 2, 40, '2025-11-26 15:06:00', '2025-11-28 15:06:00', 'active', '2025-11-26 14:06:32'),
(11, 15, 24, 1, 8, 'MAP', 'NIGERIA LOCATION', 30, 2, 40, '2025-11-26 15:16:00', '2025-11-29 15:16:00', 'active', '2025-11-26 14:16:32'),
(12, 15, 24, 1, 8, 'MAP', 'gygyg', 30, 40, 40, '2025-11-26 15:24:00', '2025-11-29 15:24:00', 'active', '2025-11-26 14:24:25'),
(13, 3, 4, 1, 8, 'Resumption Quiz', 'The quiz to force students return to campus', 30, 2, 40, '2025-12-05 13:40:00', '2025-12-12 13:40:00', 'active', '2025-12-05 10:39:11'),
(14, 14, 10, 1, 8, 'Resumption Quiz 1', 'the is the resumption quiz', 30, 0, 40, '2026-04-10 16:51:00', '2026-04-24 16:51:00', 'active', '2026-04-10 15:51:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
