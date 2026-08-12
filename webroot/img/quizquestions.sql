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
-- Table structure for table `quizquestions`
--

CREATE TABLE `quizquestions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_type` varchar(20) NOT NULL DEFAULT 'objective' COMMENT 'objective, theory, truefalse',
  `question` text NOT NULL,
  `op1` varchar(500) DEFAULT NULL COMMENT 'Option 1 (for objective/truefalse)',
  `op2` varchar(500) DEFAULT NULL COMMENT 'Option 2 (for objective/truefalse)',
  `op3` varchar(500) DEFAULT NULL COMMENT 'Option 3 (for objective only)',
  `op4` varchar(500) DEFAULT NULL COMMENT 'Option 4 (for objective only)',
  `correctans` varchar(10) DEFAULT NULL COMMENT 'Correct answer: op1/op2/op3/op4 or true/false',
  `mark` int(11) NOT NULL DEFAULT 1 COMMENT 'Marks for this question',
  `question_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizquestions`
--

INSERT INTO `quizquestions` (`id`, `quiz_id`, `question_type`, `question`, `op1`, `op2`, `op3`, `op4`, `correctans`, `mark`, `question_order`) VALUES
(1, 1, 'objective', 'Who is the Nigeria President', 'Mr A', 'Mr B', 'Mr C', 'Mr D', 'op2', 2, 1),
(2, 1, 'objective', 'Are you a student of FPNO ?', 'True', 'False', 'Yes', 'No', 'op3', 2, 2),
(3, 1, 'objective', 'Type of computers are except ___________', 'Phone', 'Calculator', 'Laptop', 'Book', 'op4', 2, 3),
(4, 1, 'truefalse', 'Are you a student ?', 'True', 'False', '', '', 'true', 2, 4),
(5, 1, 'theory', 'What is your favorite color ?', '', '', '', '', NULL, 2, 5),
(6, 6, 'truefalse', '4t345345', 'True', 'False', '', '', 'true', 1, 1),
(7, 6, 'theory', 'tr6rju6r6', '', '', '', '', NULL, 1, 1),
(9, 6, 'objective', 'what is  igbo', 'asusu', 'mba', 'two', '3r', 'op1', 1, 1),
(11, 8, 'objective', 'who is the president of nigeria', 'remi', 'tinubu', 'obj', 'jona', 'op2', 15, 1),
(12, 8, 'truefalse', 'Nigeria got independde in 1960 ', 'True', 'False', '', '', 'true', 10, 1),
(13, 8, 'theory', 'Democracy in nigeria explain?', '', '', '', '', NULL, 10, 1),
(14, 8, 'objective', 'owerri is in whic state', 'abuja', 'ekiti', 'oyo', 'imo', 'op4', 10, 1),
(17, 8, 'truefalse', 'NIgeria is europe?', 'True', 'False', '', '', 'false', 10, 1),
(18, 8, 'theory', 'explain nigeria', '', '', '', '', NULL, 10, 1),
(19, 10, 'objective', 'WHICH IS A NIGERIAN LANGUAGE?', 'Igbo', 'english', 'spanish', 'finish', 'op1', 2, 1),
(20, 11, 'objective', 'these state is the northern nigeria?', 'kaduna', 'oyo', 'lagos', 'ekiti', 'op1', 2, 1),
(21, 12, 'truefalse', 'nigeria has only two languages?', 'True', 'False', '', '', 'true', 40, 1),
(22, 13, 'objective', 'what is HTML', 'programming language', 'graphic app', 'scripting language', 'hypertext markup language', 'op4', 1, 1),
(23, 13, 'objective', 'what is the name of your school', 'TSS', 'ACA', 'LIVING TEMPLE', 'DAD', 'op2', 1, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quizquestions`
--
ALTER TABLE `quizquestions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quizquestions`
--
ALTER TABLE `quizquestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
