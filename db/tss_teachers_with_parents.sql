-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2025 at 03:09 PM
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
-- Database: `imsced`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `surname` varchar(188) NOT NULL,
  `lastname` varchar(188) NOT NULL,
  `status` varchar(19) NOT NULL DEFAULT 'active',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `adminphoto` varchar(202) DEFAULT NULL,
  `gender` varchar(66) NOT NULL,
  `department_id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(280) NOT NULL,
  `dob` varchar(22) DEFAULT NULL,
  `profile` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `user_id`, `surname`, `lastname`, `status`, `date_created`, `adminphoto`, `gender`, `department_id`, `phone`, `address`, `dob`, `profile`) VALUES
(1, 1, 'Aniegboka', 'Chukwudi', 'active', '2019-08-09 14:13:00', '10_07_25_03_02_07686fd5efb4938_chukd.jpg', 'Male', 1, '08122260140', 'Owerri, imo state Nigeria', '07/06/2010', 'Netpro Staff'),
(9, 2, 'School', 'Administrator', 'active', '2025-10-05 08:00:18', '05_10_25_08_00_1868e2259290bd0_loginlogo.png', 'Male', 1, '080', 'Owerri, Imo State', NULL, 'School administrator account');

-- --------------------------------------------------------

--
-- Table structure for table `admins_privileges`
--

CREATE TABLE `admins_privileges` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `privilege_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins_privileges`
--

INSERT INTO `admins_privileges` (`id`, `admin_id`, `privilege_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(5, 1, 5),
(6, 1, 6),
(7, 1, 4),
(8, 1, 7),
(9, 1, 8),
(10, 1, 9),
(11, 3, 4),
(12, 3, 1),
(13, 3, 2),
(14, 3, 7),
(15, 4, 1),
(16, 4, 2),
(17, 4, 3),
(18, 4, 4),
(19, 4, 5),
(20, 4, 7),
(21, 4, 9),
(22, 4, 6),
(23, 5, 1),
(24, 5, 2),
(25, 5, 3),
(26, 5, 4),
(27, 5, 5),
(28, 5, 7),
(29, 5, 8),
(30, 5, 9),
(31, 6, 1),
(32, 1, 10),
(33, 1, 11),
(34, 7, 1),
(35, 7, 2),
(36, 7, 3),
(37, 7, 8),
(38, 7, 10),
(39, 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `admisionconditions`
--

CREATE TABLE `admisionconditions` (
  `id` int(11) NOT NULL,
  `conditiond` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admisionconditions`
--

INSERT INTO `admisionconditions` (`id`, `conditiond`, `user_id`, `lastupdate`) VALUES
(1, '<p><strong>You are required to meet&nbsp;the following conditions before(deadline) to be eligible to enrol in this programme.</strong></p>\r\n\r\n<p><strong>JAMB Result:</strong>&nbsp; &nbsp;Must meet the required cut-off mark.</p>\r\n\r\n<p><strong>O&#39;Level Result:</strong>&nbsp; Minimum of 5 credits including English and Mathematics as required by the course offered.</p>\r\n\r\n<p><strong>Statement of Result(Drect Entry Only):</strong> A minimum of second class lower(CGPA of 3.0) is required.</p>\r\n\r\n<p><strong>Additional Entry Requirement:</strong> Payment of all admission-related fees and other criteria must be met before this offer will be made valid.</p>\r\n\r\n<p><strong>Accepting Our Offer:&nbsp;</strong></p>\r\n\r\n<p>If you meet all the above criteria and wish to accept this offer and firmly reserve a place, you must pay the applicable acceptance fee at any of the designated banks nationwide. Be reminded that acceptance fees are none refundable. In the event that you are found to be lacking in any of the requirements, this offer will be withdrawn and any acceptance fee paid will not be refunded. for further instructions on how to successfully pay the acceptance fee and other related fees, please visit <a href=\"http://portal.claretianuniversity.edu.ng/students/howtopayfees\" target=\"_blank\">how to pay fees</a>.&nbsp;</p>\r\n\r\n<p>In case there are errors in your personal details or parts of your name that you wish to change, please notify the university through the ICT unit so that such changes can be effected and subsequently updated.</p>\r\n\r\n<p>Good luck with your preparation and we hope you will be able to join us at&nbsp;soon. If you have any further queries please do not hesitate to contact the Admissions Officer.</p>\r\n', 1, '2019-09-12 12:52:04'),
(2, '<p><strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Fee Payment Procedure</strong></p>\r\n\r\n<p><strong>Fee payment procedure - Applicants</strong></p>\r\n\r\n<p>1. All applicants to BENJAMIN UWAJUMOGU STATE COLLEGE OF EDUCATION, IHITTE/UBOMA are to apply online at&nbsp;:&nbsp;<a href=\"https://claretianuniversity.edu.ng/students/newapplicant\">https:/</a><a href=\"https://imopoly.xn--precisi-r0a.com/students/newapplicant\">/portal.busced.edu.ng/students/</a>applicationguide</p>\r\n\r\n<p>2. Fill the application form and click submit</p>\r\n\r\n<p>3. After submission, the system generates a payment invoice bearing a unique transaction ID for you, print the page, and go to any commercial bank to make payment(or instantly pay online using your ATM card). After successfully making the payment, you will get an email from the system acknowledging your application and payment. Please note that UNTIL you pay the application fee, your application is incomplete and would not be considered.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>How to obtain the university email address</strong></p>\r\n\r\n<p>Once the admission list is published on the school website, all affected candidates who wish to accept their admission are to proceed to the school ICT unit where their data will be updated and their school email address assigned to them.</p>\r\n\r\n<p>Please note that from this moment on, every other communication between the university and the student is via the assigned university email address.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>Fee Payment Procedures for every other fee</strong></p>\r\n\r\n<p>After you have been offered admission and you accepted it, the next step is to pay all required fees. Below are the steps&nbsp;</p>\r\n\r\n<p>1. log in to the school portal at:&nbsp;<a href=\"https://portal.claretianuniversity.edu.ng/\">https://portal.busced.edu.ng/,</a> using your username(your new school email address) and password(default password is student123 and please do remember to change your password )</p>\r\n\r\n<p>2. Once you log in and your profile is up to date, the system generates invoices for all your fees.</p>\r\n\r\n<p>3. On each fee head generated above, click on &quot;Get Invoice&quot; to generate a payment invoice</p>\r\n\r\n<p>4. Print the&nbsp;generated invoice and proceed to any bank of your choice to make an Interswitch payment</p>\r\n\r\n<p>5. Once payment is done successfully, you will get an acknowledgment email/receipt for the payment</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Alternatively, students can go to the ICT unit and ask that the payment invoice be generated for them with which they can go to any commercial bank and make payment.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>For further assistance or enquiries:</strong></p>\r\n\r\n<p>call or whatsApp: 07036614567</p>\r\n\r\n<p>email: support@netpro.africa</p>\r\n\r\n<p>skype:&nbsp;support@netpro.com.ng</p>\r\n\r\n<p>or visit the ICT unit</p>\r\n', 1, '2019-09-13 08:21:01'),
(3, '<p>With the completion of the first phase of the screening exercise, I am pleased to offer you provisional admission into the&nbsp;BENJAMIN UWAJUMOGU STATE COLLEGE OF EDUCATION</p>\r\n\r\n<p><strong>The University will resume on Wednesday, the 4th of November 2022.</strong></p>\r\n\r\n<p>You will receive a mail showing your&nbsp;<strong>Registration number</strong>, and<strong>&nbsp;Login details</strong>.&nbsp;<em>Please guard these details well, and do not share it with anyone else.&nbsp;</em></p>\r\n\r\n<p>You can now proceed with making payment of fees.<br />\r\nUse the link below:<br />\r\n<a href=\"http://www.portal.claretianuniversity.edu.ng\">www.portal.busced.edu.ng</a>&nbsp;(log in with your username and password)</p>\r\n\r\n<p>The second phase of the screening exercise will commence on the&nbsp;<strong>15th of November.</strong>&nbsp;You will be required to present the original copies of the credentials you sent online, as well as this letter to the registrar&rsquo;s office on this day.</p>\r\n\r\n<p><br />\r\n<strong>Congratulations and good luck.</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><strong>________________________________</strong></p>\r\n\r\n<p><strong>Dr. ...</strong><br />\r\n<em><strong>Registrar</strong></em></p>\r\n', 1, '2019-09-20 12:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `approvedresults`
--

CREATE TABLE `approvedresults` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `subject_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `details` varchar(4000) NOT NULL,
  `datecreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(16) NOT NULL DEFAULT 'unchecked',
  `session_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `setassignment_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_score` int(11) DEFAULT NULL,
  `teacher_comments` text DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `class_arm_id` int(11) DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(188) NOT NULL,
  `author` varchar(188) NOT NULL,
  `pubdate` varchar(44) NOT NULL,
  `isavailable` varchar(18) DEFAULT 'Available',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `isbn` varchar(60) NOT NULL,
  `coverphoto` varchar(188) DEFAULT NULL,
  `copies` int(11) DEFAULT NULL,
  `section` varchar(55) DEFAULT NULL,
  `callno` varchar(22) DEFAULT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `pubdate`, `isavailable`, `date_created`, `user_id`, `isbn`, `coverphoto`, `copies`, `section`, `callno`, `department_id`) VALUES
(1, 'Intro to Physics', 'PN Okeke', '19/12/2000', 'Available', '2019-05-13 15:37:38', 0, 'ISBN00987', '300e4504a4b022c757eab402abe4e24a1557777370.jpeg', NULL, NULL, NULL, 0),
(2, 'General Mathematics For Junior Secondary School', 'PN Nnadolue', '19/12/2000', 'Unavailable', '2019-05-13 20:06:45', 0, 'ISBN009878764', 'dcf8635494a7ba07654aac5949e66b9a1557778005.jpg', NULL, NULL, NULL, 0),
(3, 'Foundation of Nursing', 'Rev Dr Polycarp Ugwu', '2014-01-09', 'Available', '2025-07-23 11:33:51', 0, '978598463', NULL, 4, 'Section C', '6', 10),
(4, 'General Physics', 'Prof P.N. Okeke', '2017-05-02', 'Available', '2025-07-23 11:58:01', 1, '978598463', NULL, 7, 'Section B', '9', 1);

-- --------------------------------------------------------

--
-- Table structure for table `borrowedbooks`
--

CREATE TABLE `borrowedbooks` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `datetoreturn` varchar(44) NOT NULL,
  `status` varchar(44) NOT NULL DEFAULT 'not returned '
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cafcredit`
--

CREATE TABLE `cafcredit` (
  `matricnum` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `date1` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cafcredit`
--

INSERT INTO `cafcredit` (`matricnum`, `amount`, `date1`, `id`) VALUES
(' CUN2021/0008', 10000, '2021-11-25 07:12:42', 1),
('', 20500, '2023-01-10 12:44:57', 2),
('', 20500, '2023-01-10 14:36:07', 3),
('', 20500, '2023-01-10 19:51:48', 4),
('', 20500, '2023-01-11 13:37:37', 5),
('', 20500, '2023-01-11 13:39:28', 6),
('', 20500, '2023-01-11 13:45:36', 7),
('', 20500, '2023-01-11 20:56:56', 8),
('', 20500, '2023-01-12 11:27:25', 9),
('', 20500, '2023-01-12 11:29:08', 10),
('', 20500, '2023-01-12 14:40:42', 11),
('', 20500, '2023-01-19 10:26:55', 12),
('', 20500, '2023-01-19 14:11:04', 13),
('', 20500, '2023-01-21 15:53:22', 14);

-- --------------------------------------------------------

--
-- Table structure for table `cafmenu`
--

CREATE TABLE `cafmenu` (
  `items` varchar(200) NOT NULL,
  `price` int(11) NOT NULL,
  `available` tinyint(1) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cafmenu`
--

INSERT INTO `cafmenu` (`items`, `price`, `available`, `id`) VALUES
('EGUSI SOUP', 700, 1, 5),
('COKE', 150, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `cafsales`
--

CREATE TABLE `cafsales` (
  `matricnum` varchar(50) NOT NULL,
  `menuid` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `unitprice` int(11) NOT NULL,
  `date1` datetime NOT NULL,
  `id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cafsales`
--

INSERT INTO `cafsales` (`matricnum`, `menuid`, `qty`, `unitprice`, `date1`, `id`) VALUES
('cun2021/0008', 5, 1, 700, '2021-11-25 06:03:01', 21),
('cun2021/0008', 6, 2, 150, '2021-11-25 06:03:11', 22),
('', 6, 1, 150, '2022-08-30 10:04:12', 23),
('', 5, 1, 700, '2022-08-30 10:04:19', 24);

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `totalvotes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `student_id`, `position_id`, `session_id`, `totalvotes`) VALUES
(1, 16, 1, 8, 6),
(2, 8, 2, 8, 2),
(3, 16, 2, 1, 0),
(4, 134, 2, 8, 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(188) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Undergraduate'),
(2, 'Post Graduate'),
(3, 'Distance Learning');

-- --------------------------------------------------------

--
-- Table structure for table `character_developments`
--

CREATE TABLE `character_developments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `punctuality` enum('Excellent','Very Good','Good','Fair','Poor') DEFAULT 'Good',
  `neatness` enum('Excellent','Very Good','Good','Fair','Poor') DEFAULT 'Good',
  `honesty` enum('Excellent','Very Good','Good','Fair','Poor') DEFAULT 'Good',
  `cooperation` enum('Excellent','Very Good','Good','Fair','Poor') DEFAULT 'Good',
  `leadership` enum('Excellent','Very Good','Good','Fair','Poor') DEFAULT 'Good',
  `attitude` enum('Excellent','Very Good','Good','Fair','Poor') DEFAULT 'Good',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_arms`
--

CREATE TABLE `class_arms` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `arm_name` varchar(10) NOT NULL,
  `arm_description` varchar(255) DEFAULT NULL,
  `class_teacher_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created` timestamp NULL DEFAULT current_timestamp(),
  `modified` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_arms`
--

INSERT INTO `class_arms` (`id`, `department_id`, `arm_name`, `arm_description`, `class_teacher_id`, `status`, `created`, `modified`) VALUES
(1, 1, 'A', '', NULL, 'active', '2025-09-14 20:35:42', '2025-09-30 14:42:49'),
(2, 2, 'Science', '', NULL, 'active', '2025-09-14 21:59:59', '2025-09-14 22:39:49'),
(3, 1, 'B', '', 2, 'active', '2025-09-14 22:07:02', '2025-09-30 14:49:43'),
(4, 2, 'Arts', '', NULL, 'active', '2025-09-14 22:39:39', '2025-09-14 22:39:39'),
(5, 3, 'A', '', NULL, 'active', '2025-09-29 18:13:45', '2025-09-29 18:13:45'),
(6, 3, 'B', '', NULL, 'active', '2025-09-29 18:14:01', '2025-09-29 18:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `constants`
--

CREATE TABLE `constants` (
  `id` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `value` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `constants`
--

INSERT INTO `constants` (`id`, `name`, `value`) VALUES
(1, 'A', '5.00'),
(3, 'B', '4.00'),
(5, 'C', '3.00'),
(7, 'D', '2.00'),
(8, 'E', '1.00'),
(9, 'F', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `continents`
--

CREATE TABLE `continents` (
  `code` char(2) NOT NULL COMMENT 'Continent code',
  `name` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `cost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `continents`
--

INSERT INTO `continents` (`code`, `name`, `id`, `cost`) VALUES
('LC', 'Local(Within Nigeria)', 1, 10000),
('AF', 'Africa', 2, 30000),
('CA', 'Central America', 3, 45000),
('CA', 'North America', 4, 33500),
('SA', 'South America', 5, 45000),
('AS', 'Asia', 6, 36000),
('EU', 'Europe', 7, 30900),
('CA', 'Caribeans/West Indies', 8, 45000),
('OC', 'Oceania/Australia', 9, 36000);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `sortname` varchar(3) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phonecode` int(11) NOT NULL,
  `cost` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `sortname`, `name`, `phonecode`, `cost`) VALUES
(1, 'AF', 'Afghanistan', 93, 25000),
(2, 'AL', 'Albania', 355, 25000),
(3, 'DZ', 'Algeria', 213, 25000),
(4, 'AS', 'American Samoa', 1684, 25000),
(5, 'AD', 'Andorra', 376, 25000),
(6, 'AO', 'Angola', 244, 25000),
(7, 'AI', 'Anguilla', 1264, 25000),
(8, 'AQ', 'Antarctica', 0, 25000),
(9, 'AG', 'Antigua And Barbuda', 1268, 25000),
(10, 'AR', 'Argentina', 54, 25000),
(11, 'AM', 'Armenia', 374, 25000),
(12, 'AW', 'Aruba', 297, 25000),
(13, 'AU', 'Australia', 61, 25000),
(14, 'AT', 'Austria', 43, 25000),
(15, 'AZ', 'Azerbaijan', 994, 25000),
(16, 'BS', 'Bahamas The', 1242, 25000),
(17, 'BH', 'Bahrain', 973, 25000),
(18, 'BD', 'Bangladesh', 880, 25000),
(19, 'BB', 'Barbados', 1246, 25000),
(20, 'BY', 'Belarus', 375, 25000),
(21, 'BE', 'Belgium', 32, 25000),
(22, 'BZ', 'Belize', 501, 25000),
(23, 'BJ', 'Benin', 229, 25000),
(24, 'BM', 'Bermuda', 1441, 25000),
(25, 'BT', 'Bhutan', 975, 25000),
(26, 'BO', 'Bolivia', 591, 25000),
(27, 'BA', 'Bosnia and Herzegovina', 387, 25000),
(28, 'BW', 'Botswana', 267, 25000),
(29, 'BV', 'Bouvet Island', 0, 25000),
(30, 'BR', 'Brazil', 55, 25000),
(31, 'IO', 'British Indian Ocean Territory', 246, 25000),
(32, 'BN', 'Brunei', 673, 25000),
(33, 'BG', 'Bulgaria', 359, 25000),
(34, 'BF', 'Burkina Faso', 226, 25000),
(35, 'BI', 'Burundi', 257, 25000),
(36, 'KH', 'Cambodia', 855, 25000),
(37, 'CM', 'Cameroon', 237, 25000),
(38, 'CA', 'Canada', 1, 25000),
(39, 'CV', 'Cape Verde', 238, 25000),
(40, 'KY', 'Cayman Islands', 1345, 25000),
(41, 'CF', 'Central African Republic', 236, 25000),
(42, 'TD', 'Chad', 235, 25000),
(43, 'CL', 'Chile', 56, 25000),
(44, 'CN', 'China', 86, 25000),
(45, 'CX', 'Christmas Island', 61, 25000),
(46, 'CC', 'Cocos (Keeling) Islands', 672, 25000),
(47, 'CO', 'Colombia', 57, 25000),
(48, 'KM', 'Comoros', 269, 25000),
(49, 'CG', 'Congo', 242, 25000),
(50, 'CD', 'Congo The Democratic Republic Of The', 242, 25000),
(51, 'CK', 'Cook Islands', 682, 25000),
(52, 'CR', 'Costa Rica', 506, 25000),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', 225, 25000),
(54, 'HR', 'Croatia (Hrvatska)', 385, 25000),
(55, 'CU', 'Cuba', 53, 25000),
(56, 'CY', 'Cyprus', 357, 25000),
(57, 'CZ', 'Czech Republic', 420, 25000),
(58, 'DK', 'Denmark', 45, 25000),
(59, 'DJ', 'Djibouti', 253, 25000),
(60, 'DM', 'Dominica', 1767, 25000),
(61, 'DO', 'Dominican Republic', 1809, 25000),
(62, 'TP', 'East Timor', 670, 25000),
(63, 'EC', 'Ecuador', 593, 25000),
(64, 'EG', 'Egypt', 20, 25000),
(65, 'SV', 'El Salvador', 503, 25000),
(66, 'GQ', 'Equatorial Guinea', 240, 25000),
(67, 'ER', 'Eritrea', 291, 25000),
(68, 'EE', 'Estonia', 372, 25000),
(69, 'ET', 'Ethiopia', 251, 25000),
(70, 'XA', 'External Territories of Australia', 61, 25000),
(71, 'FK', 'Falkland Islands', 500, 25000),
(72, 'FO', 'Faroe Islands', 298, 25000),
(73, 'FJ', 'Fiji Islands', 679, 25000),
(74, 'FI', 'Finland', 358, 25000),
(75, 'FR', 'France', 33, 25000),
(76, 'GF', 'French Guiana', 594, 25000),
(77, 'PF', 'French Polynesia', 689, 25000),
(78, 'TF', 'French Southern Territories', 0, 25000),
(79, 'GA', 'Gabon', 241, 25000),
(80, 'GM', 'Gambia The', 220, 25000),
(81, 'GE', 'Georgia', 995, 25000),
(82, 'DE', 'Germany', 49, 25000),
(83, 'GH', 'Ghana', 233, 25000),
(84, 'GI', 'Gibraltar', 350, 25000),
(85, 'GR', 'Greece', 30, 25000),
(86, 'GL', 'Greenland', 299, 25000),
(87, 'GD', 'Grenada', 1473, 25000),
(88, 'GP', 'Guadeloupe', 590, 25000),
(89, 'GU', 'Guam', 1671, 25000),
(90, 'GT', 'Guatemala', 502, 25000),
(91, 'XU', 'Guernsey and Alderney', 44, 25000),
(92, 'GN', 'Guinea', 224, 25000),
(93, 'GW', 'Guinea-Bissau', 245, 25000),
(94, 'GY', 'Guyana', 592, 25000),
(95, 'HT', 'Haiti', 509, 25000),
(96, 'HM', 'Heard and McDonald Islands', 0, 25000),
(97, 'HN', 'Honduras', 504, 25000),
(98, 'HK', 'Hong Kong S.A.R.', 852, 25000),
(99, 'HU', 'Hungary', 36, 25000),
(100, 'IS', 'Iceland', 354, 25000),
(101, 'IN', 'India', 91, 25000),
(102, 'ID', 'Indonesia', 62, 25000),
(103, 'IR', 'Iran', 98, 25000),
(104, 'IQ', 'Iraq', 964, 25000),
(105, 'IE', 'Ireland', 353, 25000),
(106, 'IL', 'Israel', 972, 25000),
(107, 'IT', 'Italy', 39, 25000),
(108, 'JM', 'Jamaica', 1876, 25000),
(109, 'JP', 'Japan', 81, 25000),
(110, 'XJ', 'Jersey', 44, 25000),
(111, 'JO', 'Jordan', 962, 25000),
(112, 'KZ', 'Kazakhstan', 7, 25000),
(113, 'KE', 'Kenya', 254, 25000),
(114, 'KI', 'Kiribati', 686, 25000),
(115, 'KP', 'Korea North', 850, 25000),
(116, 'KR', 'Korea South', 82, 25000),
(117, 'KW', 'Kuwait', 965, 25000),
(118, 'KG', 'Kyrgyzstan', 996, 25000),
(119, 'LA', 'Laos', 856, 25000),
(120, 'LV', 'Latvia', 371, 25000),
(121, 'LB', 'Lebanon', 961, 25000),
(122, 'LS', 'Lesotho', 266, 25000),
(123, 'LR', 'Liberia', 231, 25000),
(124, 'LY', 'Libya', 218, 25000),
(125, 'LI', 'Liechtenstein', 423, 25000),
(126, 'LT', 'Lithuania', 370, 25000),
(127, 'LU', 'Luxembourg', 352, 25000),
(128, 'MO', 'Macau S.A.R.', 853, 25000),
(129, 'MK', 'Macedonia', 389, 25000),
(130, 'MG', 'Madagascar', 261, 25000),
(131, 'MW', 'Malawi', 265, 25000),
(132, 'MY', 'Malaysia', 60, 25000),
(133, 'MV', 'Maldives', 960, 25000),
(134, 'ML', 'Mali', 223, 25000),
(135, 'MT', 'Malta', 356, 25000),
(136, 'XM', 'Man (Isle of)', 44, 25000),
(137, 'MH', 'Marshall Islands', 692, 25000),
(138, 'MQ', 'Martinique', 596, 25000),
(139, 'MR', 'Mauritania', 222, 25000),
(140, 'MU', 'Mauritius', 230, 25000),
(141, 'YT', 'Mayotte', 269, 25000),
(142, 'MX', 'Mexico', 52, 25000),
(143, 'FM', 'Micronesia', 691, 25000),
(144, 'MD', 'Moldova', 373, 25000),
(145, 'MC', 'Monaco', 377, 25000),
(146, 'MN', 'Mongolia', 976, 25000),
(147, 'MS', 'Montserrat', 1664, 25000),
(148, 'MA', 'Morocco', 212, 25000),
(149, 'MZ', 'Mozambique', 258, 25000),
(150, 'MM', 'Myanmar', 95, 25000),
(151, 'NA', 'Namibia', 264, 25000),
(152, 'NR', 'Nauru', 674, 25000),
(153, 'NP', 'Nepal', 977, 25000),
(154, 'AN', 'Netherlands Antilles', 599, 25000),
(155, 'NL', 'Netherlands The', 31, 25000),
(156, 'NC', 'New Caledonia', 687, 25000),
(157, 'NZ', 'New Zealand', 64, 25000),
(158, 'NI', 'Nicaragua', 505, 25000),
(159, 'NE', 'Niger', 227, 25000),
(160, 'NG', 'Nigeria', 234, 25000),
(161, 'NU', 'Niue', 683, 25000),
(162, 'NF', 'Norfolk Island', 672, 25000),
(163, 'MP', 'Northern Mariana Islands', 1670, 25000),
(164, 'NO', 'Norway', 47, 25000),
(165, 'OM', 'Oman', 968, 25000),
(166, 'PK', 'Pakistan', 92, 25000),
(167, 'PW', 'Palau', 680, 25000),
(168, 'PS', 'Palestinian Territory Occupied', 970, 25000),
(169, 'PA', 'Panama', 507, 25000),
(170, 'PG', 'Papua new Guinea', 675, 25000),
(171, 'PY', 'Paraguay', 595, 25000),
(172, 'PE', 'Peru', 51, 25000),
(173, 'PH', 'Philippines', 63, 25000),
(174, 'PN', 'Pitcairn Island', 0, 25000),
(175, 'PL', 'Poland', 48, 25000),
(176, 'PT', 'Portugal', 351, 25000),
(177, 'PR', 'Puerto Rico', 1787, 25000),
(178, 'QA', 'Qatar', 974, 25000),
(179, 'RE', 'Reunion', 262, 25000),
(180, 'RO', 'Romania', 40, 25000),
(181, 'RU', 'Russia', 70, 25000),
(182, 'RW', 'Rwanda', 250, 25000),
(183, 'SH', 'Saint Helena', 290, 25000),
(184, 'KN', 'Saint Kitts And Nevis', 1869, 25000),
(185, 'LC', 'Saint Lucia', 1758, 25000),
(186, 'PM', 'Saint Pierre and Miquelon', 508, 25000),
(187, 'VC', 'Saint Vincent And The Grenadines', 1784, 25000),
(188, 'WS', 'Samoa', 684, 25000),
(189, 'SM', 'San Marino', 378, 25000),
(190, 'ST', 'Sao Tome and Principe', 239, 25000),
(191, 'SA', 'Saudi Arabia', 966, 25000),
(192, 'SN', 'Senegal', 221, 25000),
(193, 'RS', 'Serbia', 381, 25000),
(194, 'SC', 'Seychelles', 248, 25000),
(195, 'SL', 'Sierra Leone', 232, 25000),
(196, 'SG', 'Singapore', 65, 25000),
(197, 'SK', 'Slovakia', 421, 25000),
(198, 'SI', 'Slovenia', 386, 25000),
(199, 'XG', 'Smaller Territories of the UK', 44, 25000),
(200, 'SB', 'Solomon Islands', 677, 25000),
(201, 'SO', 'Somalia', 252, 25000),
(202, 'ZA', 'South Africa', 27, 25000),
(203, 'GS', 'South Georgia', 0, 25000),
(204, 'SS', 'South Sudan', 211, 25000),
(205, 'ES', 'Spain', 34, 25000),
(206, 'LK', 'Sri Lanka', 94, 25000),
(207, 'SD', 'Sudan', 249, 25000),
(208, 'SR', 'Suriname', 597, 25000),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', 47, 25000),
(210, 'SZ', 'Swaziland', 268, 25000),
(211, 'SE', 'Sweden', 46, 25000),
(212, 'CH', 'Switzerland', 41, 25000),
(213, 'SY', 'Syria', 963, 25000),
(214, 'TW', 'Taiwan', 886, 25000),
(215, 'TJ', 'Tajikistan', 992, 25000),
(216, 'TZ', 'Tanzania', 255, 25000),
(217, 'TH', 'Thailand', 66, 25000),
(218, 'TG', 'Togo', 228, 25000),
(219, 'TK', 'Tokelau', 690, 25000),
(220, 'TO', 'Tonga', 676, 25000),
(221, 'TT', 'Trinidad And Tobago', 1868, 25000),
(222, 'TN', 'Tunisia', 216, 25000),
(223, 'TR', 'Turkey', 90, 25000),
(224, 'TM', 'Turkmenistan', 7370, 25000),
(225, 'TC', 'Turks And Caicos Islands', 1649, 25000),
(226, 'TV', 'Tuvalu', 688, 25000),
(227, 'UG', 'Uganda', 256, 25000),
(228, 'UA', 'Ukraine', 380, 25000),
(229, 'AE', 'United Arab Emirates', 971, 25000),
(230, 'GB', 'United Kingdom', 44, 25000),
(231, 'US', 'United States', 1, 25000),
(232, 'UM', 'United States Minor Outlying Islands', 1, 25000),
(233, 'UY', 'Uruguay', 598, 25000),
(234, 'UZ', 'Uzbekistan', 998, 25000),
(235, 'VU', 'Vanuatu', 678, 25000),
(236, 'VA', 'Vatican City State (Holy See)', 39, 25000),
(237, 'VE', 'Venezuela', 58, 25000),
(238, 'VN', 'Vietnam', 84, 25000),
(239, 'VG', 'Virgin Islands (British)', 1284, 25000),
(240, 'VI', 'Virgin Islands (US)', 1340, 25000),
(241, 'WF', 'Wallis And Futuna Islands', 681, 25000),
(242, 'EH', 'Western Sahara', 212, 25000),
(243, 'YE', 'Yemen', 967, 25000),
(244, 'YU', 'Yugoslavia', 38, 25000),
(245, 'ZM', 'Zambia', 260, 25000),
(246, 'ZW', 'Zimbabwe', 263, 25000),
(247, 'OT', 'Others', 0, 20000);

-- --------------------------------------------------------

--
-- Table structure for table `couriers`
--

CREATE TABLE `couriers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `couriers`
--

INSERT INTO `couriers` (`id`, `name`) VALUES
(1, 'DHL');

-- --------------------------------------------------------

--
-- Table structure for table `courseassignments`
--

CREATE TABLE `courseassignments` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `assignedon` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedon` varchar(44) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courseassignments_subjects`
--

CREATE TABLE `courseassignments_subjects` (
  `id` int(11) NOT NULL,
  `courseassignment_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coursematerials`
--

CREATE TABLE `coursematerials` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(188) NOT NULL,
  `fileurl` varchar(188) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `department_id` int(11) NOT NULL,
  `comment` varchar(202) DEFAULT NULL,
  `updatedon` varchar(44) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courseregistrations`
--

CREATE TABLE `courseregistrations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courseregistrations_subjects`
--

CREATE TABLE `courseregistrations_subjects` (
  `id` int(11) NOT NULL,
  `courseregistration_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `coursename` varchar(64) NOT NULL,
  `description` varchar(64) NOT NULL,
  `units` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `deptcode` varchar(44) NOT NULL,
  `iscdl` varchar(4) DEFAULT 'No',
  `maxunit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `faculty_id`, `name`, `deptcode`, `iscdl`, `maxunit`) VALUES
(1, 0, 'JSS 1', 'JSS 1', 'No', 0),
(2, 0, 'SSS 1', 'SSS 1', 'No', 0),
(3, 0, 'JSS 2', 'JSS 2', 'No', 0),
(5, 0, 'JSS 3', 'JSS 3', 'No', 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments_fees`
--

CREATE TABLE `departments_fees` (
  `id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `departments_fees`
--

INSERT INTO `departments_fees` (`id`, `fee_id`, `department_id`) VALUES
(1, 3, 1),
(2, 4, 1),
(3, 5, 1),
(4, 4, 2),
(5, 5, 2),
(6, 6, 2),
(7, 3, 3),
(8, 4, 3),
(9, 5, 3),
(13, 3, 5),
(14, 4, 5),
(15, 5, 5),
(16, 7, 5);

-- --------------------------------------------------------

--
-- Table structure for table `departments_levels`
--

CREATE TABLE `departments_levels` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments_programes`
--

CREATE TABLE `departments_programes` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `programe_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments_programmes`
--

CREATE TABLE `departments_programmes` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `programme_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments_semesters`
--

CREATE TABLE `departments_semesters` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments_subjects`
--

CREATE TABLE `departments_subjects` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donator` varchar(208) NOT NULL,
  `donationdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(15) NOT NULL,
  `email` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `amount` varchar(18) NOT NULL,
  `rrr` varchar(38) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'initiated'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `durations`
--

CREATE TABLE `durations` (
  `id` int(11) NOT NULL,
  `period` varchar(22) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eresources`
--

CREATE TABLE `eresources` (
  `id` int(11) NOT NULL,
  `title` varchar(222) NOT NULL,
  `pubdate` varchar(40) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `author` varchar(144) NOT NULL,
  `department_id` int(11) NOT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp(),
  `viewcount` int(11) DEFAULT NULL,
  `filenameurl` varchar(222) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eresources`
--

INSERT INTO `eresources` (`id`, `title`, `pubdate`, `isbn`, `author`, `department_id`, `dateadded`, `viewcount`, `filenameurl`) VALUES
(1, 'Solar Energy Management', '2025-05-27', '123456789', 'Dr. Chukwuele Emmanuel', 16, '2025-06-21 13:48:12', NULL, '21_06_25_01_48_126856b81c0ada7_Alusi.pdf'),
(2, 'Solar Energy Management', '2025-05-27', '123456789', 'Dr. Chukwuele Emmanuel', 16, '2025-06-21 13:51:26', NULL, '21_06_25_01_51_266856b8de26d49_Alusi.pdf'),
(3, 'Data science and Application', '2025-05-27', '978598463', 'Dr. Chukwuele Emmanuel', 13, '2025-06-22 06:39:08', NULL, '22_06_25_06_39_086857a50ca2ef8_CHUKD_NOV_2023.pdf'),
(4, 'Data science and Application', '2025-05-27', '978598463', 'Dr. Chukwuele Emmanuel', 13, '2025-06-22 06:40:35', NULL, '22_06_25_06_40_356857a56383330_CHUKD_NOV_2023.pdf'),
(5, 'Test Material for students', '2025-06-30', '978598463', 'Dr Aniegboka ', 17, '2025-07-10 15:22:04', NULL, '10_07_25_03_22_04686fda9c2ea1c_ddy.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `id` int(11) NOT NULL,
  `name` varchar(156) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `name`) VALUES
(1, 'SCHOOL OF GENERAL EDUCATION'),
(2, 'SCHOOL OF EARLYCHILDHOOD CARE AND PRIMARY EDUCATION'),
(3, 'SCHOOL OF ARTS AND SOCIAL SCIENCES'),
(4, 'SCHOOL OF SCIENCES'),
(5, 'SCHOOL OF LANGUAGES'),
(6, 'SCHOOL OF VOCATIONAL AND TECHNICAL EDUCATION');

-- --------------------------------------------------------

--
-- Table structure for table `feeallocations`
--

CREATE TABLE `feeallocations` (
  `id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `startdate` varchar(44) DEFAULT NULL,
  `enddate` varchar(44) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `name` varchar(98) NOT NULL,
  `amount` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `startdate` varchar(34) DEFAULT NULL,
  `enddate` varchar(34) DEFAULT NULL,
  `feetype` varchar(40) NOT NULL DEFAULT 'enrolled',
  `itemcode` varchar(22) DEFAULT '10001001'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `name`, `amount`, `user_id`, `status`, `startdate`, `enddate`, `feetype`, `itemcode`) VALUES
(1, 'Tuition Fee (Junior)', 30000, 1, 1, NULL, NULL, 'enrolled', '003352N4BB9H'),
(2, 'Registration Fee', 20000, 1, 1, NULL, NULL, 'enrolled', '003352N4BB9H'),
(3, 'Tuition Fee (Junior Class)', 35500, 1, 1, NULL, NULL, 'enrolled', '10001001'),
(4, 'e-Learning Fee', 15500, 1, 1, NULL, NULL, 'enrolled', '10001001'),
(5, 'Feeding Fee(Secondary)', 10000, 1, 1, NULL, NULL, 'enrolled', '10001001'),
(6, 'Tuition Fee (Senior Class)', 65000, 1, 1, NULL, NULL, 'enrolled', '10001001'),
(7, 'Junior WAEC', 15000, 1, 1, NULL, NULL, 'enrolled', '10001001');

-- --------------------------------------------------------

--
-- Table structure for table `fees_levels`
--

CREATE TABLE `fees_levels` (
  `id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_students`
--

CREATE TABLE `fees_students` (
  `id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hostelrooms`
--

CREATE TABLE `hostelrooms` (
  `id` int(11) NOT NULL,
  `hostel_id` int(11) NOT NULL,
  `floor` varchar(100) NOT NULL,
  `room_number` varchar(12) NOT NULL,
  `available_beds` int(11) DEFAULT 4,
  `occupiedbeds` int(11) DEFAULT NULL,
  `description` varchar(180) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hostelrooms`
--

INSERT INTO `hostelrooms` (`id`, `hostel_id`, `floor`, `room_number`, `available_beds`, `occupiedbeds`, `description`) VALUES
(1, 5, 'Last Floor', '001', 4, 2, 'Junior Section');

-- --------------------------------------------------------

--
-- Table structure for table `hostelrooms_students`
--

CREATE TABLE `hostelrooms_students` (
  `id` int(11) NOT NULL,
  `hostelroom_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hostelrooms_students`
--

INSERT INTO `hostelrooms_students` (`id`, `hostelroom_id`, `student_id`) VALUES
(2, 1, 1),
(3, 1, 13);

-- --------------------------------------------------------

--
-- Table structure for table `hostels`
--

CREATE TABLE `hostels` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(30) NOT NULL,
  `address` varchar(200) NOT NULL,
  `phone` varchar(18) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hostels`
--

INSERT INTO `hostels` (`id`, `name`, `type`, `address`, `phone`) VALUES
(5, 'Hostel A', 'Female Hostel', 'CAMPUS - UNIVERSITY PREMISES', ''),
(6, 'Hostel B', 'Female Hostel', 'CAMPUS - UNIVERSITY PREMISES', ''),
(7, 'Hostel C', 'Male Hotel', 'CAMPUS - UNIVERSITY PREMISES', '');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` varchar(44) NOT NULL,
  `paystatus` varchar(44) NOT NULL DEFAULT 'Unpaid',
  `invoiceid` varchar(44) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `payday` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lastregs`
--

CREATE TABLE `lastregs` (
  `id` int(11) NOT NULL,
  `lastreg` int(11) NOT NULL,
  `dategenerated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `student_id` int(11) NOT NULL,
  `lastappno` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `lastregs`
--

INSERT INTO `lastregs` (`id`, `lastreg`, `dategenerated`, `student_id`, `lastappno`) VALUES
(1, 163, '2023-01-21 15:35:19', 340, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `letters`
--

CREATE TABLE `letters` (
  `id` int(11) NOT NULL,
  `mode_id` int(11) NOT NULL,
  `letterbody` text NOT NULL,
  `title` varchar(222) NOT NULL,
  `datecreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `letters`
--

INSERT INTO `letters` (`id`, `mode_id`, `letterbody`, `title`, `datecreated`) VALUES
(1, 3, '<p>Congratulations! I am delighted to offer you admission to the Directorate of Distance Learning and Continuing Education of the Claretian University of Nigeria Nekede, Imo State Nigeria.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Your certificate programme will be taught and examined via online.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>The following are the fees for your programme:</p>\r\n\r\n<p>Acceptance Fee (CDL) &ndash; 10,000</p>\r\n\r\n<p>Caution Fee &ndash; 20,000</p>\r\n\r\n<p>E-Portal &ndash; 10,000</p>\r\n\r\n<p>Examination Fee &ndash; 10,000</p>\r\n\r\n<p>ICT &ndash; 10,000</p>\r\n\r\n<p>ID Card &ndash; 1,000</p>\r\n\r\n<p>Library &ndash; 10,000</p>\r\n\r\n<p>Student Handbook/Prospectus &ndash; 5000</p>\r\n\r\n<p>Tuition (CERTIFICATE) &ndash; 49,000</p>\r\n\r\n<p><strong>Total: N125,000</strong></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>The non-refundable fees of N20,500 will be deducted from the above.</p>\r\n\r\n<p>We are impressed by your desire and tenacity to pursue your programme with us.</p>\r\n\r\n<p>Welcome to the Claretian University Family!</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Regards</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Dr Uchenna B. Amadi-Ihunwo</p>\r\n\r\n<p>Director: Distance learning and Continuing Education</p>\r\n', 'Admission Letter for Distance Learning and Continuing Education', '2022-07-28 14:44:47');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `name` varchar(44) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`id`, `name`) VALUES
(1, '100 Level'),
(2, '200 Level'),
(3, '300 Level'),
(4, '400 Level'),
(5, 'Alumni'),
(6, '500 Level'),
(7, 'IJMB');

-- --------------------------------------------------------

--
-- Table structure for table `lgas`
--

CREATE TABLE `lgas` (
  `id` int(11) NOT NULL,
  `name` varchar(110) NOT NULL,
  `state_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `lgas`
--

INSERT INTO `lgas` (`id`, `name`, `state_id`) VALUES
(1, 'Aba North', 2647),
(2, 'Aba South', 2647),
(3, 'Arochukwu', 2647),
(4, 'Bende', 2647),
(5, 'Ikwuano', 2647),
(6, 'Isiala Ngwa North', 2647),
(7, 'Isiala Ngwa South', 2647),
(8, 'Isuikwuato', 2647),
(9, 'Obi Ngwa', 2647),
(10, 'Ohafia', 2647),
(11, 'Osisioma Ngwa', 2647),
(12, 'Ugwunagbo', 2647),
(13, 'Ukwa East', 2647),
(14, 'Ukwa West', 2647),
(15, 'Umuahia North', 2647),
(16, 'Umuahia South', 2647),
(17, 'Demsa', 2649),
(18, 'Fufore', 2649),
(19, 'Ganye', 2649),
(20, 'Girei', 2649),
(21, 'Gombi', 2649),
(22, 'Guyuk', 2649),
(23, 'Hong', 2649),
(24, 'Jada', 2649),
(25, 'Lamurde', 2649),
(26, 'Madagali', 2649),
(27, 'Maiha', 2649),
(28, 'Mayo-Belwa', 2649),
(29, 'Michika', 2649),
(30, 'Mubi North', 2649),
(31, 'Mubi South', 2649),
(32, 'Numan', 2649),
(33, 'Shelleng', 2649),
(34, 'Song', 2649),
(35, 'Toungo', 2649),
(36, 'Yola North ', 2649),
(37, 'Yola South', 2649),
(38, 'Abak', 2650),
(39, 'Eastern Obolo', 2650),
(40, 'Eket', 2650),
(41, 'Esit-Eket', 2650),
(42, 'Essien Udim', 2650),
(43, 'Etim-Ekpo', 2650),
(44, 'Etinan', 2650),
(45, 'Ibeno', 2650),
(46, 'Ibesikpo-Asutan', 2650),
(47, 'Ibiono-Ibom', 2650),
(48, 'Ika', 2649),
(49, 'Ikono', 2650),
(50, 'Ikot Abasi', 2650),
(51, 'Ikot Ekpene', 2649),
(52, 'Ini', 2650),
(53, 'Itu', 2650),
(54, 'Mbo', 2650),
(55, 'Mkpat-Enin', 2650),
(56, 'Nsit-Atai', 2650),
(57, 'Nsit-Ibom', 2650),
(58, 'Nsit-Ubium', 2650),
(59, 'Obot-Akara', 2650),
(60, 'Okobo', 2650),
(61, 'Onna', 2650),
(62, 'Oron', 2650),
(63, 'Oruk Anam', 2650),
(64, 'Ukanafun', 2650),
(65, 'Udung-Uko', 2650),
(66, 'Uruan', 2650),
(67, 'Urue-Offong/Oruko', 2650),
(68, 'Uyo', 2650),
(69, 'Aguata', 2651),
(70, 'Awka North', 2651),
(71, 'Awka South', 2651),
(72, 'Anambra East', 2651),
(73, 'Anambra West', 2651),
(74, 'Anaocha', 2651),
(75, 'Ayamelum', 2651),
(76, 'Dunukofia', 2651),
(77, 'Ekwusigo', 2651),
(78, 'Idemili North', 2651),
(79, 'Idemili South', 2651),
(80, 'Ihiala', 2651),
(81, 'Njikoka', 2651),
(82, 'Nnewi North', 2651),
(83, 'Nnewi South', 2651),
(84, 'Ogbaru', 2651),
(85, 'Onitsha North', 2651),
(86, 'Onitsha South', 2651),
(87, 'Orumba North', 2651),
(88, 'Orumba South', 2651),
(89, 'Oyi', 2651),
(90, 'Bauchi', 2652),
(91, 'Dass', 2652),
(92, 'Toro', 2652),
(93, 'Bogoro', 2652),
(94, 'Ningi', 2652),
(95, 'Warji', 2652),
(96, 'Ganjuwa', 2652),
(97, 'Kirfi', 2652),
(98, 'Alkaleri', 2652),
(99, 'Darazo', 2652),
(100, 'Misau', 2652),
(101, 'Giade', 2652),
(102, 'Shira', 2652),
(103, 'Jama\'are', 2652),
(104, 'Katagum', 2652),
(105, 'Itas/Gadau', 2652),
(106, 'Zaki', 2652),
(107, 'Gamawa', 2652),
(108, 'Damban', 2652),
(109, 'Brass', 2653),
(110, 'Ekeremor', 2653),
(111, 'Kolokuma/Opokuma', 2653),
(112, 'Nembe', 2653),
(113, 'Ogbia', 2653),
(114, 'Sagbama', 2653),
(115, 'Southern Ijaw', 2653),
(116, 'Yenagoa', 2653),
(117, 'Ado', 2654),
(118, 'Agatu', 2654),
(119, 'Apa', 2654),
(120, 'Buruku', 2654),
(121, 'Gboko', 2654),
(122, 'Guma', 2654),
(123, 'Gwer East', 2654),
(124, 'Gwer West', 2654),
(125, 'Katsina-Ala', 2654),
(126, 'Konshisha', 2654),
(127, 'Kwande', 2654),
(128, 'Logo', 2654),
(129, 'Makurdi', 2654),
(130, 'Obi', 2654),
(131, 'Ogbadibo', 2654),
(132, 'Ohimini', 2654),
(133, 'Oju', 2654),
(134, 'Okpokwu', 2654),
(135, 'Otukpo', 2654),
(136, 'Tarka', 2654),
(137, 'Ukum', 2654),
(138, 'Ushongo', 2654),
(139, 'Vandeikya', 2654),
(140, 'Maiduguri', 2655),
(141, 'Ngala', 2655),
(142, 'Kala/Balge', 2655),
(143, 'Mafa', 2655),
(144, 'Konduga', 2655),
(145, 'Bama', 2655),
(146, 'Jere', 2655),
(147, 'Dikwa', 2655),
(148, 'Askira/Uba	', 2655),
(149, 'Bayo', 2655),
(150, 'Biu', 2655),
(151, 'Chibok', 2655),
(152, 'Damboa', 2655),
(153, 'Gwoza', 2655),
(154, 'Hawul', 2655),
(155, 'Kwaya Kusar', 2655),
(156, 'Shani', 2655),
(157, 'Abadam', 2655),
(158, 'Gubio', 2655),
(159, 'Guzamala', 2655),
(160, 'Kaga', 2655),
(161, 'Kukawa', 2655),
(162, 'Magumeri', 2655),
(163, 'Marte', 2655),
(164, 'Mobbar', 2655),
(165, 'Monguno', 2655),
(166, 'Nganzai', 2655),
(167, 'Abi', 2656),
(168, 'Akamkpa', 2656),
(169, 'Akpabuyo', 2656),
(170, 'Bekwarra', 2656),
(171, 'Bakassi', 2656),
(172, 'Biase', 2656),
(173, 'Boki', 2656),
(174, 'Calabar Municipal', 2656),
(175, 'Calabar South', 2656),
(176, 'Etung', 2656),
(177, 'Ikom', 2656),
(178, 'Obanliku', 2656),
(179, 'Obubra', 2656),
(180, 'Obudu', 2656),
(181, 'Odukpani', 2656),
(182, 'Ogoja', 2656),
(183, 'Yakuur', 2656),
(184, 'Yala', 2656),
(185, 'Ethiope East', 2657),
(186, 'Ethiope West', 2657),
(187, 'Okpe', 2657),
(188, 'Sapele', 2657),
(189, 'Udu', 2657),
(190, 'Ughelli North', 2657),
(191, 'Ughelli South', 2657),
(192, 'Uvwie', 2657),
(193, 'Aniocha North', 2657),
(194, 'Aniocha South', 2647),
(195, 'Ika North East', 2657),
(196, 'Ika South', 2657),
(197, 'Ndokwa East', 2657),
(198, 'Ndokwa West', 2657),
(199, 'Oshimili North', 2657),
(200, 'Oshimili South', 2657),
(201, 'Ukwuani', 2657),
(202, 'Bomadi', 2657),
(203, 'Burutu', 2657),
(204, 'Isoko North', 2657),
(205, 'Isoko South', 2657),
(206, 'Patani', 2657),
(207, 'Warri North', 2657),
(208, 'Warri South', 2657),
(209, 'Warri South West', 2657),
(210, 'Abakaliki', 2658),
(211, 'Afikpo North', 2658),
(212, 'Afikpo South (Edda)', 2658),
(213, 'Ebonyi', 2658),
(214, 'Ezza North', 2658),
(215, 'Ezza South', 2658),
(216, 'Ikwo', 2658),
(217, 'Ishielu', 2658),
(218, 'Ivo', 2658),
(219, 'Izzi', 2658),
(220, 'Ohaozara', 2658),
(221, 'Onicha', 2658),
(222, 'Ohaukwu', 2658),
(223, 'Aninri', 2661),
(224, 'Awgu', 2661),
(225, 'Enugu East', 2661),
(226, 'Enugu North', 2661),
(227, 'Enugu South', 2661),
(228, 'Ezeagu', 2661),
(229, 'Igbo Etiti', 2661),
(230, 'Igbo Eze North', 2661),
(231, 'Igbo Eze South', 2661),
(232, 'Isi Uzo', 2661),
(233, 'Nkanu East', 2661),
(234, 'Nkanu West', 2661),
(235, 'Nsukka', 2661),
(236, 'Oji River', 2661),
(237, 'Udenu', 2661),
(238, 'Udi', 2661),
(239, 'Uzo-Uwani', 2661),
(240, 'Akoko-Edo', 2659),
(241, 'Egor', 2659),
(242, 'Esan Central', 2659),
(243, 'Esan North-East', 2659),
(244, 'Esan South-East', 2659),
(245, 'Esan West', 2659),
(246, 'Etsako Central', 2659),
(247, 'Etsako East', 2659),
(248, 'Etsako West', 2659),
(249, 'Igueben', 2659),
(250, 'Ikpoba-Okha', 2659),
(251, 'Oredo', 2659),
(252, 'Orhionmwon', 2659),
(253, 'Ovia North-East', 2659),
(254, 'Ovia South-West', 2659),
(255, 'Owan East', 2659),
(256, 'Owan West', 2659),
(257, 'Uhunmwonde', 2659),
(258, 'Ado-Ekiti', 2660),
(259, 'Ikere', 2660),
(260, 'Oye', 2660),
(261, 'Aiyekire (Gbonyin)', 2660),
(262, 'Efon', 2660),
(263, 'Ekiti East', 2660),
(264, 'Ekiti South-West', 2660),
(265, 'Ekiti West', 2660),
(266, 'Emure', 2660),
(267, 'Ido-Osi', 2660),
(268, 'Ijero', 2660),
(269, 'Ikole', 2660),
(270, 'Ilejemeje', 2660),
(271, 'Irepodun/Ifelodun', 2660),
(272, 'Ise/Orun', 2660),
(273, 'Moba', 2660),
(274, 'Akko', 2662),
(275, 'Balanga', 2662),
(276, 'Balanga', 2662),
(277, 'Dukku', 2662),
(278, 'Funakaye', 2662),
(279, 'Gombe', 2662),
(280, 'Kaltungo', 2662),
(281, 'Kwami', 2662),
(282, 'Nafada', 2662),
(283, 'Shongom', 2662),
(284, 'Yamaltu/Deba', 2662),
(285, 'Aboh Mbaise', 2663),
(286, 'Ahiazu Mbaise', 2663),
(287, 'Ehime Mbano', 2663),
(288, 'Ezinihitte Mbaise', 2663),
(289, 'Ideato North', 2663),
(290, 'Ideato South', 2663),
(291, 'Ihitte/Uboma', 2663),
(292, 'Ikeduru', 2663),
(293, 'Isiala Mbano', 2663),
(294, 'Isu', 2663),
(295, 'Mbaitoli', 2663),
(296, 'Ngor Okpala', 2663),
(297, 'Njaba', 2663),
(298, 'Nkwerre', 2663),
(299, 'Nwangele', 2663),
(300, 'Obowo', 2663),
(301, 'Oguta', 2663),
(302, 'Ohaji/Egbema', 2663),
(303, 'Okigwe', 2663),
(304, 'Onuimo', 2663),
(305, 'Orlu', 2663),
(306, 'Orsu', 2663),
(307, 'Oru East', 2663),
(308, 'Oru West', 2663),
(309, 'Owerri Municipal', 2663),
(310, 'Owerri North', 2663),
(311, 'Owerri West', 2663),
(312, 'Auyo', 2664),
(313, 'Babura', 2664),
(314, 'Biriniwa', 2664),
(315, 'Birnin Kudu', 2664),
(316, 'Buji', 2664),
(317, 'Dutse', 2664),
(318, 'Gagarawa', 2664),
(319, 'Garki', 2664),
(320, 'Gumel', 2664),
(321, 'Guri', 2664),
(322, 'Gwaram', 2664),
(323, 'Gwiwa', 2664),
(324, 'Hadejia', 2664),
(325, 'Jahun', 2664),
(326, 'Kafin Hausa', 2664),
(327, 'Kaugama', 2664),
(328, 'Kazaure', 2664),
(329, 'Kiri Kasama', 2664),
(330, 'Kiyawa', 2664),
(331, 'Maigatari', 2664),
(332, 'Malam Madori', 2664),
(333, 'Miga', 2664),
(334, 'Rimgim', 2664),
(335, 'Roni', 2664),
(336, 'Sule Tankarkar', 2664),
(337, 'Taura', 2664),
(338, 'Yankwashi', 2664),
(339, 'Birnin Gwari', 2665),
(340, 'Chikun', 2665),
(341, 'Giwa', 2665),
(342, 'Igabi', 2665),
(343, 'Ikara', 2665),
(344, 'Jaba', 2665),
(345, 'Jema\'a', 2665),
(346, 'Kachia', 2665),
(347, 'Kaduna North', 2665),
(348, 'Kaduna South', 2665),
(349, 'Kagarko', 2665),
(350, 'Kajuru', 2665),
(351, 'Kaura', 2665),
(352, 'Kauru', 2665),
(353, 'Kubau', 2665),
(354, 'Kudan', 2665),
(355, 'Lere', 2665),
(356, 'Makarfi', 2665),
(357, 'Sabon Gari', 2665),
(358, 'Sanga', 2665),
(359, 'Soba', 2665),
(360, 'Zangon Kataf', 2665),
(361, 'Zaria', 2665),
(362, 'Fagge', 2666),
(363, 'Dala', 2666),
(364, 'Gwale', 2666),
(365, 'Warawa', 2666),
(366, 'Dawakin Kudu', 2666),
(367, 'Wudil', 2666),
(368, 'Ajingi', 2666),
(369, 'Gaya', 2666),
(370, 'Albasu', 2666),
(371, 'Takai', 2666),
(372, 'Garko', 2666),
(373, 'Sumaila', 2666),
(374, 'Kiru', 2666),
(375, 'Bebeji', 2666),
(376, 'Garun Mallam', 2666),
(377, 'Kura', 2666),
(378, 'Madobi', 2666),
(379, 'Doguwa', 2666),
(380, 'Tudun Wada', 2666),
(381, 'Rano', 2666),
(382, 'Kibiya', 2666),
(383, 'Bunkure', 2666),
(384, 'Kabo', 2666),
(385, 'Rogo', 2666),
(386, 'Karaye', 2666),
(387, 'Gwarzo', 2666),
(388, 'Tsanyawa', 2666),
(389, 'Shanono', 2666),
(390, 'Kunchi', 2666),
(391, 'Bichi', 2666),
(392, 'Dambatta', 2666),
(393, 'Makoda', 2666),
(394, 'Minjibir', 2666),
(395, 'Gabasawa', 2666),
(396, 'Gezawa', 2666),
(397, 'Bagwai', 2666),
(398, 'Rimin Gado', 2666),
(399, 'Tofa', 2666),
(400, 'Dawakin Tofa', 2666),
(401, 'Ungogo', 2666),
(402, 'Kano Metropolitan Area', 2666),
(403, 'Kumbotso', 2666),
(404, 'Nassarawa', 2666),
(405, 'Tarauni', 2666),
(406, 'Kano Municipal', 2666),
(407, 'Bakori', 2667),
(408, 'Batagarawa', 2667),
(409, 'Batsari', 2667),
(410, 'Baure', 2667),
(411, 'Bindawa', 2667),
(412, 'Charanchi', 2667),
(413, 'Dan Musa', 2667),
(414, 'Dandume', 2667),
(415, 'Danja', 2667),
(416, 'Daura', 2667),
(417, 'Dutsi', 2667),
(418, 'Dutsin_ma', 2667),
(419, 'Faskari', 2667),
(420, 'Funtua', 2667),
(421, 'Ingawa', 2667),
(422, 'Kafur', 2667),
(423, 'Jibia', 2667),
(424, 'Kankara', 2667),
(425, 'Kaita', 2667),
(426, 'Katsina', 2667),
(427, 'Kankia', 2667),
(428, 'Kusada', 2667),
(429, 'Kurfi', 2667),
(430, 'Mai\'Adua', 2667),
(431, 'Malumfashi', 2667),
(432, 'Matazu', 2667),
(433, 'Mani', 2667),
(434, 'Matashi', 2667),
(435, 'Musawa', 2667),
(436, 'Rimi', 2667),
(437, 'Sabuwa', 2667),
(438, 'Safana', 2667),
(439, 'Sandamu', 2667),
(440, 'Zango', 2667),
(441, 'Aleiro', 2667),
(442, 'Arewa Dandi', 2667),
(443, 'Argungu', 2667),
(444, 'Augiei', 2667),
(445, 'Bagudo', 2667),
(446, 'Birnin Kebbi', 2667),
(447, 'Dandi', 2667),
(448, 'Gwandu', 2667),
(449, 'Fakai', 2667),
(450, 'Koko/Besse', 2667),
(451, 'Jega', 2667),
(452, 'Kalgo', 2667),
(453, 'Maiyama', 2667),
(454, 'Ngaski', 2667),
(455, 'Sakaba', 2667),
(456, 'Shanga', 2667),
(457, 'Suru', 2667),
(458, 'Danko/Wasagu', 2667),
(459, 'Yauri', 2667),
(460, 'Zuru', 2667),
(461, 'Adavi', 2669),
(462, 'Ajaokuta', 2669),
(463, 'Ankpa', 2669),
(464, 'Bassa', 2669),
(465, 'Dekina', 2669),
(466, 'Igalamela-Odolu', 2669),
(467, 'Ibaji', 2669),
(468, 'Idah', 2669),
(469, 'Kabba/Bunu', 2669),
(470, 'Ijumu', 2669),
(471, 'Koton Karfe', 2669),
(472, 'Lokoja', 2669),
(473, 'Mopa-Muro', 2669),
(474, 'Ofu', 2669),
(475, 'Ogori/Magongo', 2669),
(476, 'Okehi', 2669),
(477, 'Okene', 2669),
(478, 'Olamaboro', 2669),
(479, 'Omala', 2669),
(480, 'Yagba East', 2669),
(481, 'Yagba West', 2669),
(482, 'Asa', 2670),
(483, 'Baruten', 2670),
(484, 'Edu', 2670),
(485, 'Ifelodun', 2670),
(486, 'Ilorin East', 2670),
(487, 'Irepodun', 2670),
(488, 'Ilorin South', 2670),
(489, 'Ilorin West', 2670),
(490, 'Kaiama', 2670),
(491, 'Isin', 2670),
(492, 'Moro', 2670),
(493, 'Oke Ero', 2670),
(494, 'Ofa', 2670),
(495, 'Oyun', 2670),
(496, 'Pategi', 2670),
(497, 'Agege', 2671),
(498, 'Alimosho', 2671),
(499, 'Ifako-Ijaye', 2671),
(500, 'Mushin', 2671),
(501, 'Ikeja', 2671),
(502, 'Kosofe', 2671),
(503, 'Ifako-Ijaye', 2671),
(504, 'Ifako-Ijaye', 2671),
(505, 'Ifako-Ijaye', 2671),
(506, 'Ifako-Ijaye', 2671),
(507, 'Ifako-Ijaye', 2671),
(508, 'Oshodi-Isolo', 2671),
(509, 'Shomolu', 2671),
(510, 'Eti-Osa', 2671),
(511, 'Apapa', 2671),
(512, 'Lagos Island', 2671),
(513, 'Lagos Mainland', 2671),
(514, 'Surulere', 2671),
(515, 'Ajeromi-Ifelodun', 2671),
(516, 'Amuwo-Odofin', 2671),
(517, 'Ojo', 2671),
(518, 'Badagry', 2671),
(519, 'Ikorodu', 2671),
(520, 'Ibeju-Lekki', 2671),
(521, 'Epee', 2671),
(522, 'Karu', 2672),
(523, 'Kokona', 2672),
(524, 'Keffi', 2672),
(525, 'Nasarawa', 2672),
(526, 'Toto', 2672),
(527, 'Nasarawa Egon', 2672),
(528, 'Akwanga', 2672),
(529, 'Wamba', 2672),
(530, 'Keana', 2672),
(531, 'Awe', 2672),
(532, 'Doma', 2672),
(533, 'Lafia', 2672),
(534, 'Obi', 2672),
(535, 'Agaie', 2673),
(536, 'Agwara', 2673),
(537, 'Bida', 2673),
(538, 'Borgu', 2673),
(539, 'Boso', 2673),
(540, 'Chanchaga', 2673),
(541, 'Edati', 2673),
(542, 'Gbako', 2673),
(543, 'Gurara', 2673),
(544, 'Katcha', 2673),
(545, 'Kontagora', 2673),
(546, 'Lapai', 2673),
(547, 'Lavun', 2673),
(548, 'Magama', 2673),
(549, 'Mariga', 2673),
(550, 'Mashegu', 2673),
(551, 'Paikoro', 2673),
(552, 'Mokwa', 2673),
(553, 'Munya', 2673),
(554, 'Rijaua', 2673),
(555, 'Rafi', 2673),
(556, 'Shiroro', 2673),
(557, 'Suleja', 2673),
(558, 'Wushishi', 2673),
(559, 'Abeokuta North', 2674),
(560, 'Abeokuta South', 2674),
(561, 'Ado-Odo/Ota', 2674),
(562, 'Ewekoro', 2674),
(563, 'Ifo', 2674),
(564, 'Ijebu North East', 2674),
(565, 'Ijebu North', 2674),
(566, 'Ijebu East', 2674),
(567, 'Ijebu Ode', 2674),
(568, 'Ikenne', 2674),
(569, 'Imeko Afon', 2674),
(570, 'Ipokia', 2674),
(571, 'Obafemi Owode', 2674),
(572, 'Odogbolu', 2674),
(573, 'Ogun Waterside', 2674),
(574, 'Odeda', 2674),
(575, 'Remo North', 2674),
(576, 'Sagamu', 2674),
(577, 'Yewa North', 2674),
(578, 'Yewa South', 2674),
(579, 'Akoko North-East', 2675),
(580, 'Akoko North-West', 2675),
(581, 'Akoko South-East', 2675),
(582, 'Akure North', 2675),
(583, 'Akure South', 2675),
(584, 'Ese Odo', 2675),
(585, 'Idanre', 2675),
(586, 'Ifedore', 2675),
(587, 'Ile Oluji/Okeigbo ', 2675),
(588, 'Ilaje', 2675),
(589, 'Odigbo ', 2675),
(590, 'Irele', 2675),
(591, 'Okitipupa', 2675),
(592, 'Ondo East', 2675),
(593, 'Ondo West', 2675),
(594, 'Ose', 2675),
(595, 'Owo', 2675),
(596, 'Aiyedaade', 2676),
(597, 'Aiyedaade', 2676),
(598, 'Aiyedire', 2676),
(599, 'Atakunmosa East', 2676),
(600, 'Atakunmosa West', 2676),
(601, 'Boluwaduro', 2676),
(602, 'Boripe', 2676),
(603, 'Ede North', 2676),
(604, 'Ede South', 2676),
(605, 'Egbedore', 2676),
(606, 'Ejigbo', 2676),
(607, 'Ife Central', 2676),
(608, 'Ife East', 2676),
(609, 'Ife North', 2676),
(610, 'Ife South', 2676),
(611, 'Ifelodun', 2676),
(612, 'Ifedayo', 2676),
(613, 'Ilesa East', 2676),
(614, 'Illa', 2676),
(615, 'Ilesa West', 2676),
(616, 'Irepodun', 2676),
(617, 'Irewole', 2676),
(618, 'Isokan', 2676),
(619, 'Iwo', 2676),
(620, 'Obokun', 2676),
(621, 'Ola Oluwa', 2676),
(622, 'Odo Otin', 2676),
(623, 'Olorunda', 2676),
(624, 'Oriade', 2676),
(625, 'Orolu', 2676),
(626, 'Osogbo', 2676),
(627, 'Afijio Jobele', 2677),
(628, 'Akinyele Moniya', 2677),
(629, 'Egbeda', 2677),
(630, 'Agodi Gate', 2677),
(631, 'Ibadan North-East', 2677),
(632, 'Ibadan North-West', 2677),
(633, 'Ibadan South-West', 2677),
(634, 'Ibadan South-East', 2677),
(635, 'Ibarapa Central', 2677),
(636, 'Ibarapa East Eruwa', 2677),
(637, 'Irepo', 2677),
(638, 'Ido', 2677),
(639, 'Iseyin', 2677),
(640, 'Kajola', 2677),
(641, 'Ogbomosho North', 2677),
(642, 'Lagelu', 2677),
(643, 'Ogbomosho South', 2677),
(644, 'Oyo West Ojongbodu', 2677),
(645, 'Atiba Ofa Meta', 2677),
(646, 'Atisobo Dede', 2677),
(647, 'Saki West', 2677),
(648, 'Saki East', 2677),
(649, 'Itesiwaju Otu', 2677),
(650, 'Iwajowa', 2677),
(651, 'Ibarapa North', 2677),
(652, 'Ororunsogo', 2677),
(653, 'Ogo Oluwa', 2677),
(654, 'Oluyole', 2677),
(655, 'Orelope', 2677),
(656, 'Surulere', 2677),
(657, 'Oyo East', 2677),
(658, 'Ori Ire', 2677),
(659, 'Ona Ara Akanran', 2677),
(660, 'Barkin Ladi', 2678),
(661, 'Bassa', 2678),
(662, 'Bokkos', 2678),
(663, 'Kanam', 2678),
(664, 'Jos East', 2678),
(665, 'Jos North', 2678),
(666, 'Jos South', 2678),
(667, 'Langtang North', 2678),
(668, 'Kanke', 2678),
(669, 'Langtang South', 2678),
(670, 'Mangu', 2678),
(671, 'Mikang', 2678),
(672, 'Qua\'an Pans', 2678),
(673, 'Pankshin', 2678),
(674, 'Shendam', 2678),
(675, 'Riyom', 2678),
(676, 'Wase', 2678),
(677, 'Port Harcourt', 2679),
(678, 'Obio-Akpor', 2679),
(679, 'Okrika', 2679),
(680, 'Ogu–Bolo', 2679),
(681, 'Tai', 2679),
(682, 'Eleme', 2679),
(683, 'Gokana', 2679),
(684, 'Oyigbo', 2679),
(685, 'Khana', 2679),
(686, 'Opobo–Nkoro', 2679),
(687, 'Andoni', 2679),
(688, 'Degema', 2679),
(689, 'Bonny', 2679),
(690, 'Akuku-Toru', 2679),
(691, 'Asari-Toru', 2679),
(692, 'Abua–Odual', 2679),
(693, 'Ogba–Egbema–Ndoni', 2679),
(694, 'Ahoada East', 2679),
(695, 'Ahoada West', 2679),
(696, 'Emohua', 2679),
(697, 'Ikwere', 2679),
(698, 'Etche', 2679),
(699, 'Omuma', 2679),
(700, 'Binji', 2680),
(701, 'Bodinga', 2680),
(702, 'Dange Shuni', 2680),
(703, 'Goronyo', 2680),
(704, 'Gada', 2680),
(705, 'Gwadabawa', 2680),
(706, 'Gudu', 2680),
(707, 'Kebbe', 2680),
(708, 'Ilela', 2680),
(709, 'Isa', 2680),
(710, 'Rabah', 2680),
(711, 'Kware', 2680),
(712, 'Sabon Birni', 2680),
(713, 'Shagari', 2680),
(714, 'Silame', 2680),
(715, 'Sokoto North', 2680),
(716, 'Sokoto South', 2680),
(717, 'Tangaza', 2680),
(718, 'Tambuwal', 2680),
(719, 'Tureta', 2680),
(720, 'Wamako', 2680),
(721, 'Wurno', 2680),
(722, 'Yabo', 2680),
(723, 'Ardo Kola', 2681),
(724, 'Donga', 2681),
(725, 'Bali', 2681),
(726, 'Gashaka', 2681),
(727, 'Gassol', 2681),
(728, 'Jalingo', 2681),
(729, 'Ibi', 2681),
(730, 'Karim Lamido', 2681),
(731, 'Kurmi', 2681),
(732, 'Sardauna', 2681),
(733, 'Lau', 2681),
(734, 'Wukari', 2681),
(735, 'Takum', 2681),
(736, 'Ussa', 2681),
(737, 'Yorro', 2681),
(738, 'Zing', 2681),
(739, 'Bade', 2682),
(740, 'Bursari', 2682),
(741, 'Damaturu', 2682),
(742, 'Gujba', 2682),
(743, 'Geidam', 2682),
(744, 'Gulani', 2682),
(745, 'Fika', 2682),
(746, 'Fune', 2682),
(747, 'Karasuwa', 2682),
(748, 'Jakusko', 2682),
(749, 'Nangere', 2682),
(750, 'Machina', 2682),
(751, 'Potiskum', 2682),
(752, 'Nguru', 2682),
(753, 'Tarmuwa', 2682),
(754, 'Yunusari', 2682),
(755, 'Yusufari', 2682),
(756, 'Anka', 2683),
(757, 'Bakura', 2683),
(758, 'Birnin Magaji/Kiyaw', 2683),
(759, 'Bukkuyum', 2683),
(760, 'Bungudu', 2683),
(761, 'Gummi', 2683),
(762, 'Tsafe', 2683),
(763, 'Kaura Namoda', 2683),
(764, 'Gusau', 2683),
(765, 'Maradun', 2683),
(766, 'Maru', 2683),
(767, 'Shinkafi', 2683),
(768, 'Talata Mafara', 2683),
(769, 'Zumi', 2683),
(770, 'Abaji', 2648),
(771, 'Abuja', 2648),
(772, 'Gwagwalada', 2648),
(773, 'Bwari', 2648),
(774, 'Kwali', 2648),
(775, 'Kuje', 2648),
(776, 'Djérem', 653),
(777, 'Faro-et-Déo', 653),
(778, 'Mayo-Banyo', 653),
(779, 'Mbéré', 653),
(780, 'Vina,', 653),
(781, 'Lekié', 1207),
(782, 'Haute-Sanaga', 1207),
(783, 'Mbam-et-Inoubou ', 1207),
(784, 'Mbam-et-Inoubou ', 3604),
(785, 'Mbam-et-Inoubou ', 1608),
(786, 'Mbam-et-Inoubou ', 1784),
(787, 'Mbam-et-Inoubou ', 654),
(788, 'Haute-Sanaga', 654),
(789, 'Lekié', 654),
(790, 'Mbam-et-Kim', 654),
(791, 'Méfou-et-Afamba', 654),
(792, 'Méfou-et-Akono ', 654),
(793, 'Mfoundi ', 654),
(794, 'Nyong-et-Kéllé', 654),
(795, 'Nyong-et-Mfoumou ', 654),
(796, 'Nyong-et-So\'o', 654),
(797, 'Boumba-et-Ngoko', 655),
(798, 'Haut-Nyong ', 655),
(799, 'Kadey', 655),
(800, 'Lom-et-Djérem', 655),
(801, 'Moungo', 656),
(802, 'Moungo', 1096),
(803, 'Nkam', 1096),
(804, 'Nkam', 656),
(805, 'Sanaga-Maritime', 656),
(806, 'Wouri,', 656),
(807, 'Noun', 1613),
(808, 'Noun', 660),
(809, 'Ndé', 660),
(810, 'Haut-Nkam', 660),
(811, 'Ménoua', 660),
(812, 'Mifi', 660),
(813, 'Bamboutos', 660),
(814, 'Océan', 2602),
(815, 'Océan', 1614),
(816, 'Océan', 661),
(817, 'Vallée-du-Ntem', 661),
(818, 'Mvila ', 661),
(819, 'Dja-et-Lobo', 661),
(820, 'Bénoué', 2601),
(821, 'Bénoué', 1610),
(822, 'Bénoué', 1274),
(823, 'Bénoué', 657),
(824, 'Faro', 657),
(825, 'Mayo-Louti', 657),
(826, 'Mayo-Rey', 657),
(827, 'Diamaré', 658),
(828, 'Logone-et-Chari', 658),
(829, 'Mayo-Danay', 658),
(830, 'Mayo-Kani,', 658),
(831, 'Mayo-Sava', 658),
(832, 'Mayo-Tsanaga', 658),
(833, 'Fako', 662),
(834, 'Koupé-Manengouba', 662),
(835, 'Lebialem', 662),
(836, 'Manyu', 662),
(837, 'Meme', 662),
(838, ' Ndian', 662),
(839, 'Bui', 659),
(840, 'Donga-Mantung', 659),
(841, 'Menchum', 659),
(842, 'Mezam', 659),
(843, 'Momo', 659),
(844, 'East London', 3238),
(845, 'Port Elizabeth', 3238),
(846, 'Mthatha', 3238),
(847, 'Queenstown', 3238),
(848, 'Grahamstown', 3238),
(849, 'Jeffreys Bay', 3238),
(850, 'Graaff-Reinet', 3238),
(851, 'Port Alfred', 3238),
(852, 'Mdantsane', 3238),
(853, 'Cradock', 3238),
(854, 'King William\'s Town', 3238),
(855, 'Aliwal North', 3238),
(856, 'Uitenhage', 3238),
(857, 'Kouga', 3238),
(858, 'Mangaung Metropolitan Municipality', 3239),
(859, 'Lejweleputswa', 3239),
(860, 'Thabo Mofutsanyana', 3239),
(861, 'Fezile Dabi', 3239),
(862, 'Amajuba District (Newcastle)', 3243),
(863, 'Zululand District (Ulundi)', 3243),
(864, 'uMkhanyakude District (Mkuze)', 3243),
(865, 'King Cetshwayo District (Richards Bay) ', 3243),
(866, 'uMzinyathi District (Dundee)', 3243),
(867, 'Harry Gwala District (Ixopo)', 3243),
(868, 'Ugu District (Port Shepstone)', 3243),
(869, 'iLembe District (kwaDukuza)', 3243),
(870, 'uMgungundlovu District (Pietermaritzburg)', 3243),
(871, 'Uthukela District (Ladysmith)', 3243),
(872, 'Capricorn District', 3244),
(873, 'Mopani District', 3244),
(874, 'Sekhukhune District', 3244),
(875, 'Waterberg District', 3244),
(876, 'Vhembe District', 3244),
(877, 'Ehlanzeni District', 3245),
(878, 'Gert Sibande District', 3245),
(879, 'Nkangala District', 3245),
(880, 'Bojanala Platinum District', 3246),
(881, 'Dr Ruth Segomotsi Mompati District', 3246),
(882, 'Ngaka Modiri Molema District', 3246),
(883, 'Dr Kenneth Kaunda District', 3246),
(884, 'Frances Baard District', 3247),
(885, 'John Taolo Gaetsewe District', 3247),
(886, 'Namakwa District', 3247),
(887, 'Pixley ka Seme District', 3247),
(888, 'ZF Mgcawu District (formerly Siyanda)', 3247),
(889, 'Tshwane Metropolitan Municipality (Pretoria)', 3240),
(890, 'Johannesburg Metropolitan Municipality', 3240),
(891, 'Ekurhuleni Metropolitan Municipality', 3240),
(892, 'Others', 4121);

-- --------------------------------------------------------

--
-- Table structure for table `libbooks`
--

CREATE TABLE `libbooks` (
  `id` int(11) NOT NULL,
  `bookname` varchar(122) NOT NULL,
  `author` varchar(188) NOT NULL,
  `section_id` int(11) NOT NULL,
  `isbn` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `libborrow`
--

CREATE TABLE `libborrow` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `libbook_id` int(11) NOT NULL,
  `date1` date NOT NULL,
  `date2` date NOT NULL,
  `date3` date NOT NULL,
  `greaceinterval` int(11) NOT NULL,
  `dayrate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `libsettings`
--

CREATE TABLE `libsettings` (
  `id` int(11) NOT NULL,
  `graceinterval` int(11) NOT NULL,
  `dayrate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `libsettings`
--

INSERT INTO `libsettings` (`id`, `graceinterval`, `dayrate`) VALUES
(1, 3, 100);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `title`, `timestamp`, `user_id`, `description`, `ip`, `type`) VALUES
(1, 'added a new admin School', '2025-10-05 08:00:18', 1, 'Created new admin Administrator', '::1', 'Add'),
(2, 'Updated a teacher 15', '2025-10-05 11:39:35', 10, 'updated teacher with user id : 10', '::1', 'Edit');

-- --------------------------------------------------------

--
-- Table structure for table `modes`
--

CREATE TABLE `modes` (
  `id` int(11) NOT NULL,
  `name` varchar(44) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `modes`
--

INSERT INTO `modes` (`id`, `name`) VALUES
(1, 'UTME'),
(2, 'DIRECT ENTRY'),
(3, 'Distance Learning'),
(4, 'TNE'),
(5, 'IJMB');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `details` text NOT NULL,
  `dateposted` timestamp NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `viewcount` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'live',
  `newsimage` varchar(188) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `details`, `dateposted`, `user_id`, `viewcount`, `status`, `newsimage`) VALUES
(1, 'Claretian University of Nigeria Nekede Resumes for 2022/2023 Academic Session', '<p><!-- wp:paragraph --></p>\r\n\r\n<p>It is with great joy that the management and staff of Claretian University of Nigeria Maryland Nekede welcome back to campus both her old and new students to the new academic year.</p>\r\n\r\n<p><!-- /wp:paragraph --><!-- wp:paragraph --></p>\r\n\r\n<p>School resumed on Friday 4th of November 2022 for the new students while old students resumed earlier on the 31st of October 2022.</p>\r\n\r\n<p><!-- /wp:paragraph --><!-- wp:paragraph --></p>\r\n\r\n<p>academic activities are expected to take off immediately while candidates who applied to the school for nursing have been advised to be a little patient while the school tidies up with JAMB so that their admission can be effected for them to also resume.</p>\r\n\r\n<p><!-- /wp:paragraph --></p>\r\n', '2022-11-24 15:31:25', 1, NULL, 'live', '24_11_22_03_31_24637f8e4cec916_cun1.jpg'),
(2, 'NURSING AND MIDWIFERY COUNCIL OF NIGERIA CONCLUDES ADVISORY/ACCREDITATION VISIT TO CUN', '<p><!-- wp:paragraph --></p>\r\n\r\n<p>On Monday the 24th of October, 2022, an Advisory/Accreditation team from the Nursing and Midwifery Council of Nigeria (NMCN) Abuja, FCT, visited Claretian <a href=\"https://sportscentralngr.com/news/readnews/12910/costa-rica-friendly-victor-osimhen-and-alex-iwobi-are-notable-names-missing-ahead-of-the-clash\">University</a> of Nigeria CUN.</p>\r\n\r\n<p><!-- /wp:paragraph --><!-- wp:paragraph --></p>\r\n\r\n<p>In his welcome Address, the Vice Chancellor of CUN, Rev. Fr. Professor Wenceslaus Madu, expressed joy and optimism that the visit would bring a heart-warming outcome. He thanked the Secretary General/Registrar of NMCN, Dr. Faruk Umar Abubakar for his rare commitment in promoting and maintaining excellence in the Nursing and Midwifery education in Nigeria.</p>\r\n\r\n<p><!-- /wp:paragraph --><!-- wp:image {\"id\":4561,\"sizeSlug\":\"full\",\"linkDestination\":\"none\"} --></p>\r\n\r\n<p><img alt=\"Nursing and Midwifery Council Of Nigeria\" src=\"https://claretianuniversity.edu.ng/wp-content/uploads/2022/10/nmcn.png\" /></p>\r\n\r\n<p>Nursing and Midwifery Council Of Nigeria</p>\r\n\r\n<p><!-- /wp:image --><!-- wp:paragraph --></p>\r\n\r\n<p>The VC appreciated the Honourable Commissioner for Health in Imo State, Dr. Prosper Ohayagha Success for coming despite his busy schedules. He equally thanked the leader of the team and her colleagues. &quot;We look forward to your visionary recommendations for suitability index, criteria for employment, staff, student ratio and other parameters that would place us in the trajectory of a promising beginning,&quot; the VC said.</p>\r\n\r\n<p><!-- /wp:paragraph --><!-- wp:paragraph --></p>\r\n\r\n<p>Read also : <a href=\"https://claretianuniversity.edu.ng/admission-admission-admission/\">Admission! Admission!! Admission!!!</a></p>\r\n\r\n<p><!-- /wp:paragraph --><!-- wp:paragraph --></p>\r\n\r\n<p>The Nursing and Midwifery Council of Nigeria is a body charged with the responsibility of approving and regulating the Nursing and Midwifery education in Nigeria. Their visit was to ascertain the level of preparedness for the commencement of Bachelor of Nursing Science (BNSc) degree program in the University.</p>\r\n\r\n<p><!-- /wp:paragraph --><!-- wp:paragraph --></p>\r\n\r\n<p>Recall that a team of assessors from the National Universities&#39; Commission (NUC) had paid a similar visit to CUN on the 8th of September, 2022. After the visit, the Commission sent in their reports and approved the facilities for the commencent of Nursing Sciences Programme in the University.</p>\r\n\r\n<p><!-- /wp:paragraph --><!-- wp:paragraph --></p>\r\n\r\n<p>Mark Irechukwu<br />\r\nPublic Relations Unit, CUN.</p>\r\n\r\n<p><!-- /wp:paragraph --></p>\r\n', '2022-11-24 15:33:54', 1, NULL, 'live', '24_11_22_03_33_54637f8ee24dffe_nmcn (1).png');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(188) NOT NULL,
  `message` varchar(500) NOT NULL,
  `datecreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `recipients` varchar(44) NOT NULL DEFAULT 'students',
  `status` varchar(44) NOT NULL DEFAULT 'active',
  `viewcount` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `datecreated`, `user_id`, `recipients`, `status`, `viewcount`) VALUES
(1, 'Testing', 'This is a testing notifications', '2025-08-23 17:13:15', 1, 'students', 'Active', 5);

-- --------------------------------------------------------

--
-- Table structure for table `paylogs`
--

CREATE TABLE `paylogs` (
  `id` int(11) NOT NULL,
  `transdate` timestamp NULL DEFAULT current_timestamp(),
  `student_id` int(11) NOT NULL,
  `tref` varchar(99) NOT NULL,
  `responsecode` varchar(22) NOT NULL,
  `amount` varchar(12) NOT NULL,
  `paymethod` varchar(44) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `formonth` varchar(60) NOT NULL,
  `deduction` int(11) DEFAULT 0,
  `grosspay` int(11) NOT NULL,
  `netpay` int(11) NOT NULL,
  `dategenerated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `name` varchar(222) NOT NULL,
  `votingstarts` varchar(22) DEFAULT NULL,
  `votingends` varchar(22) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

CREATE TABLE `privileges` (
  `id` int(11) NOT NULL,
  `name` varchar(98) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `privileges`
--

INSERT INTO `privileges` (`id`, `name`) VALUES
(1, 'Admission'),
(2, 'Student'),
(3, 'Result'),
(4, 'Report'),
(5, 'Transcript'),
(6, 'Setting'),
(7, 'Admin'),
(8, 'News and Events'),
(9, 'HRM'),
(10, 'Hostels'),
(11, 'Manage Emails');

-- --------------------------------------------------------

--
-- Table structure for table `programes`
--

CREATE TABLE `programes` (
  `id` int(11) NOT NULL,
  `name` varchar(22) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programetypes`
--

CREATE TABLE `programetypes` (
  `id` int(11) NOT NULL,
  `name` varchar(188) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programmes`
--

CREATE TABLE `programmes` (
  `id` int(11) NOT NULL,
  `name` varchar(188) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `setassignment_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','theory') NOT NULL DEFAULT 'multiple_choice',
  `points` int(11) NOT NULL DEFAULT 1,
  `order_number` int(11) NOT NULL DEFAULT 1,
  `difficulty_level` enum('easy','medium','hard') DEFAULT 'medium',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(500) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `order_number` int(11) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `class_arm_id` int(11) DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `score` decimal(8,2) NOT NULL,
  `grade` char(3) NOT NULL,
  `remark` varchar(64) DEFAULT NULL,
  `uploaddate` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `regno` varchar(50) NOT NULL,
  `creditload` int(11) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `ca` decimal(11,0) NOT NULL,
  `total` decimal(11,0) NOT NULL,
  `iscarryover` varchar(11) NOT NULL DEFAULT 'no',
  `homework_project` decimal(5,2) DEFAULT 0.00 COMMENT 'Homework/Project score (10%)',
  `first_ca` decimal(5,2) DEFAULT 0.00 COMMENT '1st Continuous Assessment',
  `second_ca` decimal(5,2) DEFAULT 0.00 COMMENT '2nd Continuous Assessment',
  `first_exam` decimal(5,2) DEFAULT 0.00 COMMENT '1st Exam score',
  `second_exam` decimal(5,2) DEFAULT 0.00 COMMENT '2nd Exam score',
  `third_exam` decimal(5,2) DEFAULT 0.00 COMMENT '3rd Exam score',
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending' COMMENT 'Admin approval status',
  `approved_by` int(11) DEFAULT NULL COMMENT 'User ID who approved the result',
  `approved_at` timestamp NULL DEFAULT NULL COMMENT 'When the result was approved',
  `rejection_reason` text DEFAULT NULL COMMENT 'Reason for rejection if applicable'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Student'),
(3, 'Teacher'),
(4, 'Parent'),
(5, 'Super Admin'),
(6, 'Secretary'),
(7, 'Bursar');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `sectionname` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `name` varchar(44) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `name`) VALUES
(1, 'First Term'),
(2, 'Second Term'),
(3, 'Third Term');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `name` varchar(44) NOT NULL,
  `user_id` int(11) NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `name`, `user_id`, `createdate`) VALUES
(3, '2019/2020', 10231, '2019-08-27 07:49:24'),
(4, '2020/2021', 1, '2021-02-04 14:04:48'),
(5, '2021/2022', 1, '2021-06-29 08:21:19'),
(6, '2022/2023', 1, '2022-06-29 09:14:46'),
(7, '2023/2024', 1, '2022-06-29 09:15:03'),
(8, '2024/2025', 1, '2022-06-29 09:15:20');

-- --------------------------------------------------------

--
-- Table structure for table `setassignments`
--

CREATE TABLE `setassignments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `details` text NOT NULL,
  `test_type` enum('assignment','cbt_test') DEFAULT 'assignment',
  `total_questions` int(11) DEFAULT 0,
  `time_limit` int(11) DEFAULT NULL,
  `passing_score` int(11) DEFAULT 50,
  `teacher_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'open',
  `closedate` datetime NOT NULL,
  `opendate` datetime DEFAULT NULL,
  `datecreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `regfee` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `address` varchar(278) NOT NULL,
  `email` varchar(156) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `invoiceprefix` varchar(10) NOT NULL,
  `adminprefix` varchar(10) NOT NULL,
  `logo` varchar(256) NOT NULL,
  `staffprefix` varchar(28) NOT NULL,
  `regnoformat` varchar(30) NOT NULL,
  `session_id` int(11) NOT NULL,
  `currenttermends` varchar(20) NOT NULL,
  `nexttermbegins` varchar(20) NOT NULL,
  `application_no_prefix` varchar(10) NOT NULL,
  `rector` varchar(222) NOT NULL,
  `registrar` varchar(222) NOT NULL,
  `rectorcerts` varchar(144) NOT NULL,
  `registrarcerts` varchar(144) NOT NULL,
  `school_stamp` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `semester_id`, `description`, `regfee`, `name`, `address`, `email`, `phone`, `invoiceprefix`, `adminprefix`, `logo`, `staffprefix`, `regnoformat`, `session_id`, `currenttermends`, `nexttermbegins`, `application_no_prefix`, `rector`, `registrar`, `rectorcerts`, `registrarcerts`, `school_stamp`) VALUES
(1, 1, 'Every Student a Unique Treasure to Unearth', 1700, 'TREASURE SCIENTIA SCHOOL', '7, TREASURE ESTATE, OWERRI, IMO STATE', 'info@tss.sch.ng', '+(234) 80 6166 6506', 'TSS/Inv/', 'TSS/', '02_09_25_07_24_0668b74456f4104_loginlogo.png', 'TSS/Adm', 'TSS/', 8, '01/08/2025', '31/08/2025', 'TSSAPP', 'DR MRS CHIMAOBI INNOCENT', 'MR CHIMAOBI INNOCENT', 'PhD', 'PhD', '02_10_25_12_46_4068de7430083d0_school_stamp.png');

-- --------------------------------------------------------

--
-- Table structure for table `sparents`
--

CREATE TABLE `sparents` (
  `id` int(11) NOT NULL,
  `fathersname` varchar(188) NOT NULL,
  `mothersname` varchar(188) NOT NULL,
  `fatherphone` varchar(18) NOT NULL,
  `motherphone` varchar(18) DEFAULT NULL,
  `fathersjob` varchar(166) DEFAULT NULL,
  `mothersjob` varchar(166) NOT NULL,
  `pemailaddress` varchar(202) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(200) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sparents`
--

INSERT INTO `sparents` (`id`, `fathersname`, `mothersname`, `fatherphone`, `motherphone`, `fathersjob`, `mothersjob`, `pemailaddress`, `user_id`, `address`, `status`) VALUES
(1, 'ANDERSON UDO', 'ANDERSON UDO', '08000000000', '08000000000', 'Business', 'Business', 'anderson121@gmail.com', 17, 'Owerri, Imo State', 'active'),
(2, 'ARINZECHUKWU ARINZECHUKWU', 'ARINZECHUKWU ARINZECHUKWU', '08000000000', '08000000000', 'Business', 'Business', 'arinze121@gmail.com', 18, 'Owerri, Imo State', 'active'),
(3, 'CHUKWUEMEKA CHUKWUEMEKA', 'CHUKWUEMEKA CHUKWUEMEKA', '08000000000', '08000000000', 'Business', 'Business', 'Chuks121@gmail.com', 19, 'Owerri, Imo State', 'active'),
(4, 'CHUKWUMA CHUKWUMA', 'CHUKWUMA CHUKWUMA', '08000000000', '08000000000', 'Business', 'Business', 'Chukwuma121@gmail.com', 20, 'Owerri, Imo State', 'active'),
(5, 'DURU DURU', 'DURU DURU', '08000000000', '08000000000', 'Business', 'Business', 'Duru121@gmail.com', 21, 'Owerri, Imo State', 'active'),
(6, 'EJIOGU EJIOGU', 'EJIOGU EJIOGU', '08000000000', '08000000000', 'Business', 'Business', 'Ejiogu121@gmail.com', 22, 'Owerri, Imo State', 'active'),
(7, 'EMMANUEL EMMANUEL', 'EMMANUEL EMMANUEL', '08000000000', '08000000000', 'Business', 'Business', 'Manuel121@gmail.com', 23, 'Owerri, Imo State', 'active'),
(8, 'IBEZIM IBEZIM', 'IBEZIM IBEZIM', '08000000000', '08000000000', 'Business', 'Business', 'Ibe121@gmail.com', 24, 'Owerri, Imo State', 'active'),
(9, 'ILONAH ILONAH', 'ILONAH ILONAH', '08000000000', '08000000000', 'Business', 'Business', 'ILONAH121@gmail.com', 25, 'Owerri, Imo State', 'active'),
(10, 'NNANNA NNANNA', 'NNANNA NNANNA', '08000000000', '08000000000', 'Business', 'Business', 'nna121@gmail.com', 26, 'Owerri, Imo State', 'active'),
(11, 'NWOKEFORO NWOKEFORO', 'NWOKEFORO NWOKEFORO', '08000000000', '08000000000', 'Business', 'Business', 'nwoke121@gmail.com', 27, 'Owerri, Imo State', 'active'),
(12, 'NZEADI NZEADI', 'NZEADI NZEADI', '08000000000', '08000000000', 'Business', 'Business', 'nzeadi121@gmail.com', 28, 'Owerri, Imo State', 'active'),
(13, '& OGUZIE', '& OGUZIE', '08000000000', '08000000000', 'Business', 'Business', 'Oguzie121@gmail.com', 29, 'Owerri, Imo State', 'active'),
(14, 'UDEDIBIA UDEDIBIA', 'UDEDIBIA UDEDIBIA', '08000000000', '08000000000', 'Business', 'Business', 'udedibia@gmail.com', 30, 'Owerri, Imo State', 'active'),
(15, 'UMUNNA UMUNNA', 'UMUNNA UMUNNA', '08000000000', '08000000000', 'Business', 'Business', 'umunna121@gmail.com', 31, 'Owerri, Imo State', 'active'),
(16, 'CHIGOZIE CHIGOZIE', 'CHIGOZIE CHIGOZIE', '08000000000', '08000000000', 'Business', 'Business', 'Chigo121@gmail.com', 32, 'Owerri, Imo State', 'active'),
(17, 'EMEROLE EMEROLE', 'EMEROLE EMEROLE', '08000000000', '08000000000', 'Business', 'Business', 'emerole121@gmail.com', 33, 'Owerri, Imo State', 'active'),
(18, 'ETOK ETOK', 'ETOK ETOK', '08000000000', '08000000000', 'Business', 'Business', 'Etok121@gmail.com', 34, 'Owerri, Imo State', 'active'),
(19, 'ILOH ILOH', 'ILOH ILOH', '08000000000', '08000000000', 'Business', 'Business', 'iloh21@gmail.com', 35, 'Owerri, Imo State', 'active'),
(20, '&MEEGHAEEL &MEEGHAEEL', '&MEEGHAEEL &MEEGHAEEL', '08000000000', '08000000000', 'Business', 'Business', 'meeg121@gmail.com', 36, 'Owerri, Imo State', 'active'),
(21, 'NDUKAUBA BRYAN', 'NDUKAUBA BRYAN', '08000000000', '08000000000', 'Business', 'Business', 'ndukuba121@gmail.com', 37, 'Owerri, Imo State', 'active'),
(22, 'NWOSU NWOSU', 'NWOSU NWOSU', '08000000000', '08000000000', 'Business', 'Business', 'Nwosu121@gmail.com', 38, 'Owerri, Imo State', 'active'),
(23, 'OKAFOR OKAFOR', 'OKAFOR OKAFOR', '08000000000', '08000000000', 'Business', 'Business', 'okafor111@gmail.com', 39, 'Owerri, Imo State', 'active'),
(24, 'OPARA OPARA', 'OPARA OPARA', '08000000000', '08000000000', 'Business', 'Business', 'opara111@gmail.com', 40, 'Owerri, Imo State', 'active'),
(25, 'UDUSHIRINWA UDUSHIRINWA', 'UDUSHIRINWA UDUSHIRINWA', '08000000000', '08000000000', 'Business', 'Business', 'UDU111@gmail.com', 41, 'Owerri, Imo State', 'active'),
(26, 'ANYANWU ANYANWU', 'ANYANWU ANYANWU', '08000000000', '08000000000', 'Business', 'Business', 'anyanwu121@gmail.com', 42, 'Owerri, Imo State', 'active'),
(27, 'AMADI AMADI', 'AMADI AMADI', '08000000000', '08000000000', 'Business', 'Business', 'Amadi111@gmail.com', 43, 'Owerri, Imo State', 'active'),
(28, 'COLLINS COLLINS', 'COLLINS COLLINS', '08000000000', '08000000000', 'Business', 'Business', 'Collin111@gmail.com', 44, 'Owerri, Imo State', 'active'),
(29, 'EKEH EKEH', 'EKEH EKEH', '08000000000', '08000000000', 'Business', 'Business', 'Ekeh121@gmail.com', 45, 'Owerri, Imo State', 'active'),
(30, 'CHINEDU CHINEDU', 'CHINEDU CHINEDU', '08000000000', '08000000000', 'Business', 'Business', 'Chinedu121@gmail.com', 46, 'Owerri, Imo State', 'active'),
(31, 'IFEANYI-AMADI IFEANYI-AMADI', 'IFEANYI-AMADI IFEANYI-AMADI', '08000000000', '08000000000', 'Business', 'Business', 'ify121@gmail.com', 47, 'Owerri, Imo State', 'active'),
(32, 'JOHN JOHN', 'JOHN JOHN', '08000000000', '08000000000', 'Business', 'Business', 'John121@gmail.com', 48, 'Owerri, Imo State', 'active'),
(33, 'NKEMAKOLAM NKEMAKOLAM', 'NKEMAKOLAM NKEMAKOLAM', '08000000000', '08000000000', 'Business', 'Business', 'Nkem121@gmail.com', 49, 'Owerri, Imo State', 'active'),
(34, 'UGBOAGA UGBOAGA', 'UGBOAGA UGBOAGA', '08000000000', '08000000000', 'Business', 'Business', 'ugboaga121@gmail.com', 50, 'Owerri, Imo State', 'active'),
(35, 'UKAEGBU UKAEGBU', 'UKAEGBU UKAEGBU', '08000000000', '08000000000', 'Business', 'Business', 'uka121@gmail.com', 51, 'Owerri, Imo State', 'active'),
(36, 'SAMUEL SAMUEL', 'SAMUEL SAMUEL', '08000000000', '08000000000', 'Business', 'Business', 'Sam121@gmail.com', 52, 'Owerri, Imo State', 'active'),
(37, 'AGBARA AGBARA', 'AGBARA AGBARA', '08000000000', '08000000000', 'Business', 'Business', 'Agbara121@gmail.com', 53, 'Owerri, Imo State', 'active'),
(38, 'ASIBIE ASIBIE', 'ASIBIE ASIBIE', '08000000000', '08000000000', 'Business', 'Business', 'Asibie121@gmail.com', 54, 'Owerri, Imo State', 'active'),
(39, 'CHIBUEZE CHIBUEZE', 'CHIBUEZE CHIBUEZE', '08000000000', '08000000000', 'Business', 'Business', 'Chibueze121@gmail.com', 55, 'Owerri, Imo State', 'active'),
(40, 'EMMANUEL EMMANUEL', 'EMMANUEL EMMANUEL', '08000000000', '08000000000', 'Business', 'Business', 'Emma51@gmail.com', 56, 'Owerri, Imo State', 'active'),
(41, 'NNAWUIHE NNAWUIHE', 'NNAWUIHE NNAWUIHE', '08000000000', '08000000000', 'Business', 'Business', 'Nnawuihe121@gmail.com', 57, 'Owerri, Imo State', 'active'),
(42, 'OKERE OKERE', 'OKERE OKERE', '08000000000', '08000000000', 'Business', 'Business', 'Okere121@gmail.com', 58, 'Owerri, Imo State', 'active'),
(43, 'OKECHUKWU OKECHUKWU', 'OKECHUKWU OKECHUKWU', '08000000000', '08000000000', 'Business', 'Business', 'okechukwu51@gmail.com', 59, 'Owerri, Imo State', 'active'),
(44, 'WOKO WOKO', 'WOKO WOKO', '08000000000', '08000000000', 'Business', 'Business', 'woko51@gmail.com', 60, 'Owerri, Imo State', 'active'),
(45, 'ACHINIKE ACHINIKE', 'ACHINIKE ACHINIKE', '08000000000', '08000000000', 'Business', 'Business', 'achinike121@gmail.com', 61, 'Owerri, Imo State', 'active'),
(46, 'COLE COLE', 'COLE COLE', '08000000000', '08000000000', 'Business', 'Business', 'cole121@gmail.com', 62, 'Owerri, Imo State', 'active'),
(47, 'CHUKWUEMEKA CHUKWUEMEKA', 'CHUKWUEMEKA CHUKWUEMEKA', '08000000000', '08000000000', 'Business', 'Business', 'CHUKS87@gmail.com', 63, 'Owerri, Imo State', 'active'),
(48, 'ISAAC ISAAC', 'ISAAC ISAAC', '08000000000', '08000000000', 'Business', 'Business', 'isaac121@gmail.com', 64, 'Owerri, Imo State', 'active'),
(49, 'JACOB JACOB', 'JACOB JACOB', '08000000000', '08000000000', 'Business', 'Business', 'jacob121@gmail.com', 65, 'Owerri, Imo State', 'active'),
(50, 'IFEANYI IFEANYI', 'IFEANYI IFEANYI', '08000000000', '08000000000', 'Business', 'Business', 'ifeanyi87@gmail.com', 66, 'Owerri, Imo State', 'active'),
(51, 'NDUBUIKWO NDUBUIKWO', 'NDUBUIKWO NDUBUIKWO', '08000000000', '08000000000', 'Business', 'Business', 'ndubuikwo121@gmail.com', 67, 'Owerri, Imo State', 'active'),
(52, 'OYEJELAM OYEJELAM', 'OYEJELAM OYEJELAM', '08000000000', '08000000000', 'Business', 'Business', 'oyeje11@gmail.com', 68, 'Owerri, Imo State', 'active'),
(53, 'THEODORE THEODORE', 'THEODORE THEODORE', '08000000000', '08000000000', 'Business', 'Business', 'theodore121@gmail.com', 69, 'Owerri, Imo State', 'active'),
(54, 'AKAJIAKU AKAJIAKU', 'AKAJIAKU AKAJIAKU', '08000000000', '08000000000', 'Business', 'Business', 'Akajiaku121@gmail.com', 70, 'Owerri, Imo State', 'active'),
(55, 'ABALANNE ABALANNE', 'ABALANNE ABALANNE', '08000000000', '08000000000', 'Business', 'Business', 'abala57@gmail.com', 71, 'Owerri, Imo State', 'active'),
(56, 'CHIKWADO CHIKWADO', 'CHIKWADO CHIKWADO', '08000000000', '08000000000', 'Business', 'Business', 'Chikwado121@gmail.com', 72, 'Owerri, Imo State', 'active'),
(57, 'CHUKWUEMEKA CHUKWUEMEKA', 'CHUKWUEMEKA CHUKWUEMEKA', '08000000000', '08000000000', 'Business', 'Business', 'chukwuemeka77@gmail.com', 73, 'Owerri, Imo State', 'active'),
(58, 'IBEAWUCHI IBEAWUCHI', 'IBEAWUCHI IBEAWUCHI', '08000000000', '08000000000', 'Business', 'Business', 'ibeawuchi121@gmail.com', 74, 'Owerri, Imo State', 'active'),
(59, 'MODESTUS MODESTUS', 'MODESTUS MODESTUS', '08000000000', '08000000000', 'Business', 'Business', 'modestus121@gmail.com', 75, 'Owerri, Imo State', 'active'),
(60, 'NELSON NELSON', 'NELSON NELSON', '08000000000', '08000000000', 'Business', 'Business', 'Neslon12@gmail.com', 76, 'Owerri, Imo State', 'active'),
(61, 'OBINNA OBINNA', 'OBINNA OBINNA', '08000000000', '08000000000', 'Business', 'Business', 'Obinna121@gmail.com', 77, 'Owerri, Imo State', 'active'),
(62, 'KABIRI KABIRI', 'KABIRI KABIRI', '08000000000', '08000000000', 'Business', 'Business', 'Kabriri56@gmail.com', 78, 'Owerri, Imo State', 'active'),
(63, 'UCHENNA UCHENNA', 'UCHENNA UCHENNA', '08000000000', '08000000000', 'Business', 'Business', 'Uchenna121@gmail.com', 79, 'Owerri, Imo State', 'active'),
(64, 'HENRY HENRY', 'HENRY HENRY', '08000000000', '08000000000', 'Business', 'Business', 'henry121@gmail.com', 80, 'Owerri, Imo State', 'active'),
(65, 'UMUNNA UMUNNA', 'UMUNNA UMUNNA', '08000000000', '08000000000', 'Business', 'Business', 'Umunna77@gmail.com', 81, 'Owerri, Imo State', 'active'),
(66, 'ACHOLONU ACHOLONU', 'ACHOLONU ACHOLONU', '08000000000', '08000000000', 'Business', 'Business', 'acholonu121@gmail.com', 82, 'Owerri, Imo State', 'active'),
(67, 'AKABUOGU AKABUOGU', 'AKABUOGU AKABUOGU', '08000000000', '08000000000', 'Business', 'Business', 'Akabu121@gmail.com', 83, 'Owerri, Imo State', 'active'),
(68, 'CHRISTOPHER CHRISTOPHER', 'CHRISTOPHER CHRISTOPHER', '08000000000', '08000000000', 'Business', 'Business', 'Chris77@gmail.com', 84, 'Owerri, Imo State', 'active'),
(69, 'CHUKWUEKE CHUKWUEKE', 'CHUKWUEKE CHUKWUEKE', '08000000000', '08000000000', 'Business', 'Business', 'Chukwueke121@gmail.com', 85, 'Owerri, Imo State', 'active'),
(70, 'DANIEL DANIEL', 'DANIEL DANIEL', '08000000000', '08000000000', 'Business', 'Business', 'daniel121@gmail.com', 86, 'Owerri, Imo State', 'active'),
(71, 'DIMEKE DIMEKE', 'DIMEKE DIMEKE', '08000000000', '08000000000', 'Business', 'Business', 'Dimeke77@gmail.com', 87, 'Owerri, Imo State', 'active'),
(72, 'EKEH EKEH', 'EKEH EKEH', '08000000000', '08000000000', 'Business', 'Business', 'Ekeh77@gmail.com', 88, 'Owerri, Imo State', 'active'),
(73, 'IDEBA IDEBA', 'IDEBA IDEBA', '08000000000', '08000000000', 'Business', 'Business', 'Ideba121@gmail.com', 89, 'Owerri, Imo State', 'active'),
(74, 'KALU KALU', 'KALU KALU', '08000000000', '08000000000', 'Business', 'Business', 'Kalu88@gmail.com', 90, 'Owerri, Imo State', 'active'),
(75, 'OBIAKU OBIAKU', 'OBIAKU OBIAKU', '08000000000', '08000000000', 'Business', 'Business', 'Obiaku111@gmail.com', 91, 'Owerri, Imo State', 'active'),
(76, 'OBIOMA OBIOMA', 'OBIOMA OBIOMA', '08000000000', '08000000000', 'Business', 'Business', 'Obioma23@gmail.com', 92, 'Owerri, Imo State', 'active'),
(77, 'OKEKA OKEKA', 'OKEKA OKEKA', '08000000000', '08000000000', 'Business', 'Business', 'Okey77@gmail.com', 93, 'Owerri, Imo State', 'active'),
(78, 'OZOEMENA OZOEMENA', 'OZOEMENA OZOEMENA', '08000000000', '08000000000', 'Business', 'Business', 'Ozoemena121@gmail.com', 94, 'Owerri, Imo State', 'active'),
(79, 'SAMUEL SAMUEL', 'SAMUEL SAMUEL', '08000000000', '08000000000', 'Business', 'Business', 'Sam86@gmail.com', 95, 'Owerri, Imo State', 'active'),
(80, 'NWANEKEZIE NWANEKEZIE', 'NWANEKEZIE NWANEKEZIE', '08000000000', '08000000000', 'Business', 'Business', 'Nwane89@gmail.com', 96, 'Owerri, Imo State', 'active'),
(81, 'AHAM AHAM', 'AHAM AHAM', '08000000000', '08000000000', 'Business', 'Business', 'Aham23@gmail.com', 97, 'Owerri, Imo State', 'active'),
(82, 'AJOKU AJOKU', 'AJOKU AJOKU', '08000000000', '08000000000', 'Business', 'Business', 'Ajoku121@gmail.com', 98, 'Owerri, Imo State', 'active'),
(83, 'ANNAH ANNAH', 'ANNAH ANNAH', '08000000000', '08000000000', 'Business', 'Business', 'Annah121@gmail.com', 99, 'Owerri, Imo State', 'active'),
(84, 'JACOB JACOB', 'JACOB JACOB', '08000000000', '08000000000', 'Business', 'Business', 'Jacob55@gmail.com', 100, 'Owerri, Imo State', 'active'),
(85, 'KINDNESS KINDNESS', 'KINDNESS KINDNESS', '08000000000', '08000000000', 'Business', 'Business', 'Kind121@gmail.com', 101, 'Owerri, Imo State', 'active'),
(86, 'NWAOSU NWAOSU', 'NWAOSU NWAOSU', '08000000000', '08000000000', 'Business', 'Business', 'Nwaosu88@gmail.com', 102, 'Owerri, Imo State', 'active'),
(87, 'JOHN MADUAGWU', 'JOHN MADUAGWU', '08000000000', '08000000000', 'Business', 'Business', 'Maduagwu121@gmail.com', 103, 'Owerri, Imo State', 'active'),
(88, 'MARCUS MARCUS', 'MARCUS MARCUS', '08000000000', '08000000000', 'Business', 'Business', 'Marcus121@gmail.com', 104, 'Owerri, Imo State', 'active'),
(89, 'OPARA OPARA', 'OPARA OPARA', '08000000000', '08000000000', 'Business', 'Business', 'Opara89@gmail.com', 105, 'Owerri, Imo State', 'active'),
(90, 'ONUOHA ONUOHA', 'ONUOHA ONUOHA', '08000000000', '08000000000', 'Business', 'Business', 'Onuoha99@gmail.com', 106, 'Owerri, Imo State', 'active'),
(91, 'JUDE JUDE', 'JUDE JUDE', '08000000000', '08000000000', 'Business', 'Business', 'Jude99@gmail.com', 107, 'Owerri, Imo State', 'active'),
(92, 'OHAMEZU OHAMEZU', 'OHAMEZU OHAMEZU', '08000000000', '08000000000', 'Business', 'Business', 'oham121@gmail.com', 108, 'Owerri, Imo State', 'active'),
(93, 'RICHARDS RICHARDS', 'RICHARDS RICHARDS', '08000000000', '08000000000', 'Business', 'Business', 'Richards121@gmail.com', 109, 'Owerri, Imo State', 'active'),
(94, 'NWORGU NWORGU', 'NWORGU NWORGU', '08000000000', '08000000000', 'Business', 'Business', 'Nwaorgu17@gmail.com', 110, 'Owerri, Imo State', 'active'),
(95, 'CHIBUZOR CHIBUZOR', 'CHIBUZOR CHIBUZOR', '08000000000', '08000000000', 'Business', 'Business', 'chibu88@gmail.com', 111, 'Owerri, Imo State', 'active'),
(96, 'IBEKWE IBEKWE', 'IBEKWE IBEKWE', '08000000000', '08000000000', 'Business', 'Business', 'ibekwe22@gmail.com', 112, 'Owerri, Imo State', 'active'),
(97, 'ALFRED ALFRED', 'ALFRED ALFRED', '08000000000', '08000000000', 'Business', 'Business', 'Alfred88@gmail.com', 113, 'Owerri, Imo State', 'active'),
(98, 'OGBONNA OGBONNA', 'OGBONNA OGBONNA', '08000000000', '08000000000', 'Business', 'Business', 'Ogbonna22@gmail.com', 114, 'Owerri, Imo State', 'active'),
(99, 'OSCAR OSCAR', 'OSCAR OSCAR', '08000000000', '08000000000', 'Business', 'Business', 'Oscar44@gmail.com', 115, 'Owerri, Imo State', 'active'),
(100, 'UGWUEGBU UGWUEGBU', 'UGWUEGBU UGWUEGBU', '08000000000', '08000000000', 'Business', 'Business', 'Ugwuegbu12@gmail.com', 116, 'Owerri, Imo State', 'active'),
(101, 'VICTOR VICTOR', 'VICTOR VICTOR', '08000000000', '08000000000', 'Business', 'Business', 'Vickrez22@gmail.com', 117, 'Owerri, Imo State', 'active'),
(102, 'ONYEIKE ONYEIKE', 'ONYEIKE ONYEIKE', '08000000000', '08000000000', 'Business', 'Business', 'Onyeike22@gmail.com', 118, 'Owerri, Imo State', 'active'),
(103, 'MOSES MOSES', 'MOSES MOSES', '08000000000', '08000000000', 'Business', 'Business', 'Mos112@gmail.com', 119, 'Owerri, Imo State', 'active'),
(104, 'UGWUEGBU UGWUEGBU', 'UGWUEGBU UGWUEGBU', '08000000000', '08000000000', 'Business', 'Business', 'Ugwu76@gmail.com', 120, 'Owerri, Imo State', 'active'),
(105, 'NWADIKE NWADIKE', 'NWADIKE NWADIKE', '08000000000', '08000000000', 'Business', 'Business', 'Nwadike22@gmail.com', 121, 'Owerri, Imo State', 'active'),
(106, 'CHIDIEBERE CHIDIEBERE', 'CHIDIEBERE CHIDIEBERE', '08000000000', '08000000000', 'Business', 'Business', 'Chidi99@gmail.com', 122, 'Owerri, Imo State', 'active'),
(107, 'NDUKUBA NDUKUBA', 'NDUKUBA NDUKUBA', '08000000000', '08000000000', 'Business', 'Business', 'Ndukuba88@gmail.com', 123, 'Owerri, Imo State', 'active'),
(108, 'NWANERI NWANERI', 'NWANERI NWANERI', '08000000000', '08000000000', 'Business', 'Business', 'Nwaneri70@gmail.com', 124, 'Owerri, Imo State', 'active'),
(109, 'EMMANUEL EMMANUEL', 'EMMANUEL EMMANUEL', '08000000000', '08000000000', 'Business', 'Business', 'Emma100@gmail.com', 125, 'Owerri, Imo State', 'active'),
(110, 'OLUEZE OLUEZE', 'OLUEZE OLUEZE', '08000000000', '08000000000', 'Business', 'Business', 'Olueze45@gmail.com', 126, 'Owerri, Imo State', 'active'),
(111, 'UNAMBA UNAMBA', 'UNAMBA UNAMBA', '08000000000', '08000000000', 'Business', 'Business', 'Unamba78@gmail.com', 127, 'Owerri, Imo State', 'active'),
(112, 'EKEIGWE EKEIGWE', 'EKEIGWE EKEIGWE', '08000000000', '08000000000', 'Business', 'Business', 'Ekeigwe23@gmail.com', 128, 'Owerri, Imo State', 'active'),
(113, 'IFEANYICHUKWU IFEANYICHUKWU', 'IFEANYICHUKWU IFEANYICHUKWU', '08000000000', '08000000000', 'Business', 'Business', 'Ifeanyi22@gmail.com', 129, 'Owerri, Imo State', 'active'),
(114, 'UWANDU UWANDU', 'UWANDU UWANDU', '08000000000', '08000000000', 'Business', 'Business', 'Uwandu28@gmail.com', 130, 'Owerri, Imo State', 'active'),
(115, 'EZERIBE EZERIBE', 'EZERIBE EZERIBE', '08000000000', '08000000000', 'Business', 'Business', 'Ezeribe45@gmail.com', 131, 'Owerri, Imo State', 'active'),
(116, 'FRANKLYN CHERISH', 'FRANKLYN CHERISH', '08000000000', '08000000000', 'Business', 'Business', 'Frank22@gmail.com', 132, 'Owerri, Imo State', 'active'),
(117, 'ANYANWU ANYANWU', 'ANYANWU ANYANWU', '08000000000', '08000000000', 'Business', 'Business', 'ANYANWU112@gmail.com', 133, 'Owerri, Imo State', 'active'),
(118, 'AMARACHUKWU AMARACHUKWU', 'AMARACHUKWU AMARACHUKWU', '08000000000', '08000000000', 'Business', 'Business', 'mara24@gmail.com', 134, 'Owerri, Imo State', 'active'),
(119, 'EZE EZE', 'EZE EZE', '08000000000', '08000000000', 'Business', 'Business', 'Eze77@gmail.com', 135, 'Owerri, Imo State', 'active'),
(120, 'OSUJI OSUJI', 'OSUJI OSUJI', '08000000000', '08000000000', 'Business', 'Business', 'Osuji22@gmail.com', 136, 'Owerri, Imo State', 'active'),
(121, 'BONIFCAE BONIFCAE', 'BONIFCAE BONIFCAE', '08000000000', '08000000000', 'Business', 'Business', 'bonny12@gmail.com', 137, 'Owerri, Imo State', 'active'),
(122, 'CHIMENKA CHIMENKA', 'CHIMENKA CHIMENKA', '08000000000', '08000000000', 'Business', 'Business', 'chimex14@gmail.com', 138, 'Owerri, Imo State', 'active'),
(123, 'DURU DURU', 'DURU DURU', '08000000000', '08000000000', 'Business', 'Business', 'duru89@gmail.com', 139, 'Owerri, Imo State', 'active'),
(124, 'ALFRED ALFRED', 'ALFRED ALFRED', '08000000000', '08000000000', 'Business', 'Business', 'Alfred77@gmail.com', 140, 'Owerri, Imo State', 'active'),
(125, 'AGAZIEM AGAZIEM', 'AGAZIEM AGAZIEM', '08000000000', '08000000000', 'Business', 'Business', 'Agaziem34@gmail.com', 141, 'Owerri, Imo State', 'active'),
(126, 'MOSES MOSES', 'MOSES MOSES', '08000000000', '08000000000', 'Business', 'Business', 'Moses22@gmail.com', 142, 'Owerri, Imo State', 'active'),
(127, 'HAMZA HAMZA', 'HAMZA HAMZA', '08000000000', '08000000000', 'Business', 'Business', 'hamza44@gmail.com', 143, 'Owerri, Imo State', 'active'),
(128, 'AGBARA AGBARA', 'AGBARA AGBARA', '08000000000', '08000000000', 'Business', 'Business', 'Agbara90@gmail.com', 144, 'Owerri, Imo State', 'active'),
(129, 'ERIC ERIC', 'ERIC ERIC', '08000000000', '08000000000', 'Business', 'Business', 'Eric22@gmail.com', 145, 'Owerri, Imo State', 'active'),
(130, 'CHIKWADO CHIKWADO', 'CHIKWADO CHIKWADO', '08000000000', '08000000000', 'Business', 'Business', 'Chikwado99@gmail.com', 146, 'Owerri, Imo State', 'active'),
(131, 'COLLINS COLLINS', 'COLLINS COLLINS', '08000000000', '08000000000', 'Business', 'Business', 'Collins90@gmail.com', 147, 'Owerri, Imo State', 'active'),
(132, 'ANDERSON ANDERSON', 'ANDERSON ANDERSON', '08000000000', '08000000000', 'Business', 'Business', 'Ander23@gmail.com', 148, 'Owerri, Imo State', 'active'),
(133, 'MIRABEL MIRABEL', 'MIRABEL MIRABEL', '08000000000', '08000000000', 'Business', 'Business', 'Mirabel22@gmail.com', 149, 'Owerri, Imo State', 'active'),
(134, 'EXCEL EXCEL', 'EXCEL EXCEL', '08000000000', '08000000000', 'Business', 'Business', 'Excel46@gmail.com', 150, 'Owerri, Imo State', 'active'),
(135, 'CHUKWUDI CHUKWUDI', 'CHUKWUDI CHUKWUDI', '08000000000', '08000000000', 'Business', 'Business', 'chukwu90@gmail.com', 151, 'Owerri, Imo State', 'active'),
(136, 'UMUNNA UMUNNA', 'UMUNNA UMUNNA', '08000000000', '08000000000', 'Business', 'Business', 'Umunna56@gmail.com', 152, 'Owerri, Imo State', 'active'),
(137, 'CHIBUZOR CHIBUZOR', 'CHIBUZOR CHIBUZOR', '08000000000', '08000000000', 'Business', 'Business', 'Buzor44@gmail.com', 153, 'Owerri, Imo State', 'active'),
(138, 'IFEANYI IFEANYI', 'IFEANYI IFEANYI', '08000000000', '08000000000', 'Business', 'Business', 'Ifeco22@gmail.com', 154, 'Owerri, Imo State', 'active'),
(139, 'MBAH MBAH', 'MBAH MBAH', '08000000000', '08000000000', 'Business', 'Business', 'Mbah87@gmail.com', 155, 'Owerri, Imo State', 'active'),
(140, '& AMADI', '& AMADI', '08000000000', '08000000000', 'Business', 'Business', 'Amadi24@gmail.com', 156, 'Owerri, Imo State', 'active'),
(141, 'ANYANWU ANYANWU', 'ANYANWU ANYANWU', '08000000000', '08000000000', 'Business', 'Business', 'Anyanwu18@gmail.com', 157, 'Owerri, Imo State', 'active'),
(142, 'ANYADIEGWU ANYADIEGWU', 'ANYADIEGWU ANYADIEGWU', '08000000000', '08000000000', 'Business', 'Business', 'egwu44@gmail.com', 158, 'Owerri, Imo State', 'active'),
(143, 'AGBARA AGBARA', 'AGBARA AGBARA', '08000000000', '08000000000', 'Business', 'Business', 'Agbara80@gmail.com', 159, 'Owerri, Imo State', 'active'),
(144, 'CHUKWUMA CHUKWUMA', 'CHUKWUMA CHUKWUMA', '08000000000', '08000000000', 'Business', 'Business', 'Chuks92@gmail.com', 160, 'Owerri, Imo State', 'active'),
(145, 'CHUKWUEMEKA CHUKWUEMEKA', 'CHUKWUEMEKA CHUKWUEMEKA', '08000000000', '08000000000', 'Business', 'Business', 'chikky24@gmail.com', 161, 'Owerri, Imo State', 'active'),
(146, 'EMEA EMEA', 'EMEA EMEA', '08000000000', '08000000000', 'Business', 'Business', 'Emea77@gmail.com', 162, 'Owerri, Imo State', 'active'),
(147, 'EZERIBE EZERIBE', 'EZERIBE EZERIBE', '08000000000', '08000000000', 'Business', 'Business', 'Ezeribe96@gmail.com', 163, 'Owerri, Imo State', 'active'),
(148, 'LINUS LINUS', 'LINUS LINUS', '08000000000', '08000000000', 'Business', 'Business', 'Linus56@gmail.com', 164, 'Owerri, Imo State', 'active'),
(149, 'NZE NZE', 'NZE NZE', '08000000000', '08000000000', 'Business', 'Business', 'Nzeh78@gmail.com', 165, 'Owerri, Imo State', 'active'),
(150, 'NNAMOCHA NNAMOCHA', 'NNAMOCHA NNAMOCHA', '08000000000', '08000000000', 'Business', 'Business', 'ocha22@gmail.com', 166, 'Owerri, Imo State', 'active'),
(151, 'NDUKWU NDUKWU', 'NDUKWU NDUKWU', '08000000000', '08000000000', 'Business', 'Business', 'Ndu24@gmail.com', 167, 'Owerri, Imo State', 'active'),
(152, 'OHANU OHANU', 'OHANU OHANU', '08000000000', '08000000000', 'Business', 'Business', 'Ohannu1@gmail.com', 168, 'Owerri, Imo State', 'active'),
(153, 'OKERE OKERE', 'OKERE OKERE', '08000000000', '08000000000', 'Business', 'Business', 'Okere90@gmail.com', 169, 'Owerri, Imo State', 'active'),
(154, 'UKAEGBU UKAEGBU', 'UKAEGBU UKAEGBU', '08000000000', '08000000000', 'Business', 'Business', 'Ukaegbu23@gmail.com', 170, 'Owerri, Imo State', 'active'),
(155, 'UHUEGBU UHUEGBU', 'UHUEGBU UHUEGBU', '08000000000', '08000000000', 'Business', 'Business', 'Uhuegbu76@gmail.com', 171, 'Owerri, Imo State', 'active'),
(156, 'ANYASORO ANYASORO', 'ANYASORO ANYASORO', '08000000000', '08000000000', 'Business', 'Business', 'soro98@gmail.com', 172, 'Owerri, Imo State', 'active'),
(157, 'CHRISTOPHER CHRISTOPHER', 'CHRISTOPHER CHRISTOPHER', '08000000000', '08000000000', 'Business', 'Business', 'Topher90@gmail.com', 173, 'Owerri, Imo State', 'active'),
(158, 'ETHELBERT ETHELBERT', 'ETHELBERT ETHELBERT', '08000000000', '08000000000', 'Business', 'Business', 'Bert45@gmail.com', 174, 'Owerri, Imo State', 'active'),
(159, 'NNANNA NNANNA', 'NNANNA NNANNA', '08000000000', '08000000000', 'Business', 'Business', 'nnan24@gmail.com', 175, 'Owerri, Imo State', 'active'),
(160, 'NNOROM NNOROM', 'NNOROM NNOROM', '08000000000', '08000000000', 'Business', 'Business', 'Nnorom45@gmail.com', 176, 'Owerri, Imo State', 'active'),
(161, 'ACHINEIKE ACHINEIKE', 'ACHINEIKE ACHINEIKE', '08000000000', '08000000000', 'Business', 'Business', 'Achi76@gmail.com', 177, 'Owerri, Imo State', 'active'),
(162, 'AMAEFULA AMAEFULA', 'AMAEFULA AMAEFULA', '08000000000', '08000000000', 'Business', 'Business', 'Amaefula90@gmail.com', 178, 'Owerri, Imo State', 'active'),
(163, 'CHIKWADO CHIKWADO', 'CHIKWADO CHIKWADO', '08000000000', '08000000000', 'Business', 'Business', 'Chikwa70@gmail.com', 179, 'Owerri, Imo State', 'active'),
(164, 'CHINEDU CHINEDU', 'CHINEDU CHINEDU', '08000000000', '08000000000', 'Business', 'Business', 'Nedu11@gmail.com', 180, 'Owerri, Imo State', 'active'),
(165, 'CHUKWUDI CHUKWUDI', 'CHUKWUDI CHUKWUDI', '08000000000', '08000000000', 'Business', 'Business', 'Chukky114@gmail.com', 181, 'Owerri, Imo State', 'active'),
(166, 'CHUKWUEMEKA CHUKWUEMEKA', 'CHUKWUEMEKA CHUKWUEMEKA', '08000000000', '08000000000', 'Business', 'Business', 'Emi12@gmail.com', 182, 'Owerri, Imo State', 'active'),
(167, 'DABERECHI DABERECHI', 'DABERECHI DABERECHI', '08000000000', '08000000000', 'Business', 'Business', 'Dabbs34@gmail.com', 183, 'Owerri, Imo State', 'active'),
(168, 'EVANS EVANS', 'EVANS EVANS', '08000000000', '08000000000', 'Business', 'Business', 'Evans56@gmail.com', 184, 'Owerri, Imo State', 'active'),
(169, 'MADUKWE MADUKWE', 'MADUKWE MADUKWE', '08000000000', '08000000000', 'Business', 'Business', 'Madukwe27@gmail.com', 185, 'Owerri, Imo State', 'active'),
(170, 'NAZEGBULAM NAZEGBULAM', 'NAZEGBULAM NAZEGBULAM', '08000000000', '08000000000', 'Business', 'Business', 'Naze77@gmail.com', 186, 'Owerri, Imo State', 'active'),
(171, 'ODOM ODOM', 'ODOM ODOM', '08000000000', '08000000000', 'Business', 'Business', 'Odom44@gmail.com', 187, 'Owerri, Imo State', 'active'),
(172, 'OKEKE OKEKE', 'OKEKE OKEKE', '08000000000', '08000000000', 'Business', 'Business', 'Okeke88@gmail.com', 188, 'Owerri, Imo State', 'active'),
(173, 'OKECHUKWU OKECHUKWU', 'OKECHUKWU OKECHUKWU', '08000000000', '08000000000', 'Business', 'Business', 'oke99@gmail.com', 189, 'Owerri, Imo State', 'active'),
(174, 'OPARA OPARA', 'OPARA OPARA', '08000000000', '08000000000', 'Business', 'Business', 'Opara11@gmail.com', 190, 'Owerri, Imo State', 'active'),
(175, 'AMARACHI AMARACHI', 'AMARACHI AMARACHI', '08000000000', '08000000000', 'Business', 'Business', 'Amara56@gmail.com', 191, 'Owerri, Imo State', 'active'),
(176, 'EMENYONU EMENYONU', 'EMENYONU EMENYONU', '08000000000', '08000000000', 'Business', 'Business', 'Emenyonu12@gmail.com', 192, 'Owerri, Imo State', 'active'),
(177, 'EZEM EZEM', 'EZEM EZEM', '08000000000', '08000000000', 'Business', 'Business', 'Ezem99@gmail.com', 193, 'Owerri, Imo State', 'active'),
(178, 'GERALD-PETERS GERALD-PETERS', 'GERALD-PETERS GERALD-PETERS', '08000000000', '08000000000', 'Business', 'Business', 'Pet23@gmail.com', 194, 'Owerri, Imo State', 'active'),
(179, 'IBEKWE IBEKWE', 'IBEKWE IBEKWE', '08000000000', '08000000000', 'Business', 'Business', 'IBEH90@gmail.com', 195, 'Owerri, Imo State', 'active'),
(180, 'NJOKU NJOKU', 'NJOKU NJOKU', '08000000000', '08000000000', 'Business', 'Business', 'njo21@gmail.com', 196, 'Owerri, Imo State', 'active'),
(181, 'OJUKWU OJUKWU', 'OJUKWU OJUKWU', '08000000000', '08000000000', 'Business', 'Business', 'oju24@gmail.com', 197, 'Owerri, Imo State', 'active'),
(182, 'OKEKE OKEKE', 'OKEKE OKEKE', '08000000000', '08000000000', 'Business', 'Business', 'okeke100@gmail.com', 198, 'Owerri, Imo State', 'active'),
(183, 'ONWUADIHA ONWUADIHA', 'ONWUADIHA ONWUADIHA', '08000000000', '08000000000', 'Business', 'Business', 'Onwuadi23@gmail.com', 199, 'Owerri, Imo State', 'active'),
(184, 'ADINDU ADINDU', 'ADINDU ADINDU', '08000000000', '08000000000', 'Business', 'Business', 'ADIN24@gmail.com', 200, 'Owerri, Imo State', 'active'),
(185, 'AKABUOGU AKABUOGU', 'AKABUOGU AKABUOGU', '08000000000', '08000000000', 'Business', 'Business', 'Akabu79@gmail.com', 201, 'Owerri, Imo State', 'active'),
(186, 'ANYAWELECHI ANYAWELECHI', 'ANYAWELECHI ANYAWELECHI', '08000000000', '08000000000', 'Business', 'Business', 'Lewechi12@gmail.com', 202, 'Owerri, Imo State', 'active'),
(187, 'CALLISTUS CALLISTUS', 'CALLISTUS CALLISTUS', '08000000000', '08000000000', 'Business', 'Business', 'Cali71@gmail.com', 203, 'Owerri, Imo State', 'active'),
(188, 'CHIKWE CHIKWE', 'CHIKWE CHIKWE', '08000000000', '08000000000', 'Business', 'Business', 'Chikwe43@gmail.com', 204, 'Owerri, Imo State', 'active'),
(189, 'OBILOR OBILOR', 'OBILOR OBILOR', '08000000000', '08000000000', 'Business', 'Business', 'Obilor45@gmail.com', 205, 'Owerri, Imo State', 'active'),
(190, 'OKAFOR OKAFOR', 'OKAFOR OKAFOR', '08000000000', '08000000000', 'Business', 'Business', 'Kafor42@gmail.com', 206, 'Owerri, Imo State', 'active'),
(191, 'ONWUSONYE ONWUSONYE', 'ONWUSONYE ONWUSONYE', '08000000000', '08000000000', 'Business', 'Business', 'sonye11@gmail.com', 207, 'Owerri, Imo State', 'active'),
(192, 'KABIRI KABIRI', 'KABIRI KABIRI', '08000000000', '08000000000', 'Business', 'Business', 'Kab90@gmail.com', 208, 'Owerri, Imo State', 'active'),
(193, 'CHILAKA CHILAKA', 'CHILAKA CHILAKA', '08000000000', '08000000000', 'Business', 'Business', 'Chilaka22@gmail.com', 209, 'Owerri, Imo State', 'active'),
(194, 'ANAH ANAH', 'ANAH ANAH', '08000000000', '08000000000', 'Business', 'Business', 'Anah70@gmail.com', 210, 'Owerri, Imo State', 'active'),
(195, 'ENYIOHA ENYIOHA', 'ENYIOHA ENYIOHA', '08000000000', '08000000000', 'Business', 'Business', 'Enyioha24@gmail.com', 211, 'Owerri, Imo State', 'active'),
(196, 'ONYEKURU ONYEKURU', 'ONYEKURU ONYEKURU', '08000000000', '08000000000', 'Business', 'Business', 'ONYE22@gmail.com', 212, 'Owerri, Imo State', 'active'),
(197, 'NNAMOCHA NNAMOCHA', 'NNAMOCHA NNAMOCHA', '08000000000', '08000000000', 'Business', 'Business', 'Nnam51@gmail.com', 213, 'Owerri, Imo State', 'active'),
(198, 'NWOKORIE NWOKORIE', 'NWOKORIE NWOKORIE', '08000000000', '08000000000', 'Business', 'Business', 'Nwoko12@gmail.com', 214, 'Owerri, Imo State', 'active'),
(199, 'UGOCHUKWU UGOCHUKWU', 'UGOCHUKWU UGOCHUKWU', '08000000000', '08000000000', 'Business', 'Business', 'Ugo99@gmail.com', 215, 'Owerri, Imo State', 'active'),
(200, 'DICK DICK', 'DICK DICK', '08000000000', '08000000000', 'Business', 'Business', 'Dick79@gmail.com', 216, 'Owerri, Imo State', 'active'),
(201, 'OKOREAFFIA RUTH', 'OKOREAFFIA RUTH', '08000000000', '08000000000', 'Business', 'Business', 'Okoreaffia91@gmail.com', 217, 'Owerri, Imo State', 'active'),
(202, 'AHAMEFULA AHAMEFULA', 'AHAMEFULA AHAMEFULA', '08000000000', '08000000000', 'Business', 'Business', 'Efula12@gmail.com', 218, 'Owerri, Imo State', 'active'),
(203, 'IWORISOU IWORISOU', 'IWORISOU IWORISOU', '08000000000', '08000000000', 'Business', 'Business', 'Iwori24@gmail.com', 219, 'Owerri, Imo State', 'active'),
(204, 'ONYECHEGE ONYECHEGE', 'ONYECHEGE ONYECHEGE', '08000000000', '08000000000', 'Business', 'Business', 'Chege19@gmail.com', 220, 'Owerri, Imo State', 'active'),
(205, 'AKALEZI AKALEZI', 'AKALEZI AKALEZI', '08000000000', '08000000000', 'Business', 'Business', 'Akalezi25@gmail.com', 221, 'Owerri, Imo State', 'active'),
(206, 'ANYADIEGWU ANYADIEGWU', 'ANYADIEGWU ANYADIEGWU', '08000000000', '08000000000', 'Business', 'Business', 'Diegwu25@gmail.com', 222, 'Owerri, Imo State', 'active'),
(207, 'BEDE BEDE', 'BEDE BEDE', '08000000000', '08000000000', 'Business', 'Business', 'BEDE77@gmail.com', 223, 'Owerri, Imo State', 'active'),
(208, 'DANIEL DANIEL', 'DANIEL DANIEL', '08000000000', '08000000000', 'Business', 'Business', 'Dann99@gmail.com', 224, 'Owerri, Imo State', 'active'),
(209, 'ENYERIBE ENYERIBE', 'ENYERIBE ENYERIBE', '08000000000', '08000000000', 'Business', 'Business', 'Enyeribe20@gmail.com', 225, 'Owerri, Imo State', 'active'),
(210, 'IFEANYI-AMADI IFEANYI-AMADI', 'IFEANYI-AMADI IFEANYI-AMADI', '08000000000', '08000000000', 'Business', 'Business', 'Ifyamadi@gmail.com', 226, 'Owerri, Imo State', 'active'),
(211, 'IJIOMA IJIOMA', 'IJIOMA IJIOMA', '08000000000', '08000000000', 'Business', 'Business', 'Ijioma22@gmail.com', 227, 'Owerri, Imo State', 'active'),
(212, 'MADUKWE MADUKWE', 'MADUKWE MADUKWE', '08000000000', '08000000000', 'Business', 'Business', 'Madukwe99@gmail.com', 228, 'Owerri, Imo State', 'active'),
(213, 'NZEH NZEH', 'NZEH NZEH', '08000000000', '08000000000', 'Business', 'Business', 'Melvin24@gmail.com', 229, 'Owerri, Imo State', 'active'),
(214, 'ONYEAMAKU ONYEAMAKU', 'ONYEAMAKU ONYEAMAKU', '08000000000', '08000000000', 'Business', 'Business', 'Amaku25@gmail.com', 230, 'Owerri, Imo State', 'active'),
(215, 'UGOCHUKWU UGOCHUKWU', 'UGOCHUKWU UGOCHUKWU', '08000000000', '08000000000', 'Business', 'Business', 'ugochu11@gmail.com', 231, 'Owerri, Imo State', 'active'),
(216, 'TOCHUKWU TOCHUKWU', 'TOCHUKWU TOCHUKWU', '08000000000', '08000000000', 'Business', 'Business', 'toch11@gmail.com', 232, 'Owerri, Imo State', 'active'),
(217, 'SOLOMON SOLOMON', 'SOLOMON SOLOMON', '08000000000', '08000000000', 'Business', 'Business', 'solo24@gmail.com', 233, 'Owerri, Imo State', 'active'),
(218, 'EZE EZE', 'EZE EZE', '08000000000', '08000000000', 'Business', 'Business', 'eze90@gmail.com', 234, 'Owerri, Imo State', 'active'),
(219, 'WILSON WILSON', 'WILSON WILSON', '08000000000', '08000000000', 'Business', 'Business', 'wilson@gmail.com', 235, 'Owerri, Imo State', 'active'),
(220, 'EZEIGBO EZEIGBO', 'EZEIGBO EZEIGBO', '08000000000', '08000000000', 'Business', 'Business', 'Ezeigbo@gmail.com', 236, 'Owerri, Imo State', 'active'),
(221, 'Anoruo Anoruo', 'Anoruo Anoruo', '08000000000', '08000000000', 'Business', 'Business', 'anoruo135@gmail.com', 237, 'Owerri, Imo State', 'active'),
(222, 'Emerole Emerole', 'Emerole Emerole', '08000000000', '08000000000', 'Business', 'Business', 'emerole636@gmail.com', 238, 'Owerri, Imo State', 'active'),
(223, 'Ekeigwe Ekeigwe', 'Ekeigwe Ekeigwe', '08000000000', '08000000000', 'Business', 'Business', 'ekeigwe2020@gmail.com', 239, 'Owerri, Imo State', 'active'),
(224, 'Christopher Christopher', 'Christopher Christopher', '08000000000', '08000000000', 'Business', 'Business', 'christopher@gmail.com', 240, 'Owerri, Imo State', 'active'),
(225, 'Ndukuba Ndukuba', 'Ndukuba Ndukuba', '08000000000', '08000000000', 'Business', 'Business', 'ndukuba2019@gmail.com', 241, 'Owerri, Imo State', 'active'),
(226, 'Okonkwo Okonkwo', 'Okonkwo Okonkwo', '08000000000', '08000000000', 'Business', 'Business', 'okonkwo49@gmail.com', 242, 'Owerri, Imo State', 'active'),
(227, 'Thompson Thompson', 'Thompson Thompson', '08000000000', '08000000000', 'Business', 'Business', 'thompson1946@gmail.com', 243, 'Owerri, Imo State', 'active'),
(228, 'Ugochukwu Ugochukwu', 'Ugochukwu Ugochukwu', '08000000000', '08000000000', 'Business', 'Business', 'ugochukwn49@gmail.com', 244, 'Owerri, Imo State', 'active'),
(229, 'Amadi B', 'Amadi B', '08000000000', '08000000000', 'Business', 'Business', 'amadibb19@gmail.com', 245, 'Owerri, Imo State', 'active'),
(230, 'Adindu Adindu', 'Adindu Adindu', '08000000000', '08000000000', 'Business', 'Business', 'adindu364@gmail.com', 246, 'Owerri, Imo State', 'active'),
(231, 'Christopher O', 'Christopher O', '08000000000', '08000000000', 'Business', 'Business', 'christopheroo12@gmail.com', 247, 'Owerri, Imo State', 'active'),
(232, 'Emmanuel Ch', 'Emmanuel Ch', '08000000000', '08000000000', 'Business', 'Business', 'emmanuel0909@gmail.com', 248, 'Owerri, Imo State', 'active'),
(233, 'Franklin Franklin', 'Franklin Franklin', '08000000000', '08000000000', 'Business', 'Business', 'franklin410@gmail.com', 249, 'Owerri, Imo State', 'active'),
(234, 'Yioma Yioma', 'Yioma Yioma', '08000000000', '08000000000', 'Business', 'Business', 'yioma3026@gmail.com', 250, 'Owerri, Imo State', 'active'),
(235, 'Ibeagu Ibeagu', 'Ibeagu Ibeagu', '08000000000', '08000000000', 'Business', 'Business', 'ibeagu995@gmail.com', 251, 'Owerri, Imo State', 'active'),
(236, 'Isaac Isaac', 'Isaac Isaac', '08000000000', '08000000000', 'Business', 'Business', 'isaac4012@gmail.com', 252, 'Owerri, Imo State', 'active'),
(237, 'John E', 'John E', '08000000000', '08000000000', 'Business', 'Business', 'johnee@gmail.com', 253, 'Owerri, Imo State', 'active'),
(238, 'Jude Jude', 'Jude Jude', '08000000000', '08000000000', 'Business', 'Business', 'jude2121@gmail.com', 254, 'Owerri, Imo State', 'active'),
(239, 'Linus Linus', 'Linus Linus', '08000000000', '08000000000', 'Business', 'Business', 'linus1700@gmail.com', 255, 'Owerri, Imo State', 'active'),
(240, 'Nwaneri Nwaneri', 'Nwaneri Nwaneri', '08000000000', '08000000000', 'Business', 'Business', 'nwaneri342@gmail.com', 256, 'Owerri, Imo State', 'active'),
(241, 'Oguzie Oguzie', 'Oguzie Oguzie', '08000000000', '08000000000', 'Business', 'Business', 'oguzie123@gmail.com', 257, 'Owerri, Imo State', 'active'),
(242, 'Ozurumba Ozurumba', 'Ozurumba Ozurumba', '08000000000', '08000000000', 'Business', 'Business', 'ozurumba32@gmail.com', 258, 'Owerri, Imo State', 'active'),
(243, 'Ozumba Ozumba', 'Ozumba Ozumba', '08000000000', '08000000000', 'Business', 'Business', 'ozumba435@gmail.com', 259, 'Owerri, Imo State', 'active'),
(244, 'Donatus Donatus', 'Donatus Donatus', '08000000000', '08000000000', 'Business', 'Business', 'donatus45@gmail.com', 260, 'Owerri, Imo State', 'active'),
(245, 'Mbamala Mbamala', 'Mbamala Mbamala', '08000000000', '08000000000', 'Business', 'Business', 'mbamala76@gmail.com', 261, 'Owerri, Imo State', 'active'),
(246, 'Ebere Ebere', 'Ebere Ebere', '08000000000', '08000000000', 'Business', 'Business', 'ebere144@gmail.com', 262, 'Owerri, Imo State', 'active'),
(247, 'Albert Albert', 'Albert Albert', '08000000000', '08000000000', 'Business', 'Business', 'albert63@gmail.com', 263, 'Owerri, Imo State', 'active'),
(248, 'Egwim Egwim', 'Egwim Egwim', '08000000000', '08000000000', 'Business', 'Business', 'egwim65@gmail.com', 264, 'Owerri, Imo State', 'active'),
(249, 'Achilonu Achilonu', 'Achilonu Achilonu', '08000000000', '08000000000', 'Business', 'Business', 'achilonu57@gmail.com', 265, 'Owerri, Imo State', 'active'),
(250, 'Anumudu Anumudu', 'Anumudu Anumudu', '08000000000', '08000000000', 'Business', 'Business', 'anumudu133@gmail.com', 266, 'Owerri, Imo State', 'active'),
(251, 'Augustine Augustine', 'Augustine Augustine', '08000000000', '08000000000', 'Business', 'Business', 'augustine98@gmail.com', 267, 'Owerri, Imo State', 'active'),
(252, 'Boniface Boniface', 'Boniface Boniface', '08000000000', '08000000000', 'Business', 'Business', 'boniface23@gmail.com', 268, 'Owerri, Imo State', 'active'),
(253, 'Cyril Cyril', 'Cyril Cyril', '08000000000', '08000000000', 'Business', 'Business', 'cyril67@gmail.com', 269, 'Owerri, Imo State', 'active'),
(254, 'Daniel D', 'Daniel D', '08000000000', '08000000000', 'Business', 'Business', 'danieldd133@gmail.com', 270, 'Owerri, Imo State', 'active'),
(255, 'Iloh Iloh', 'Iloh Iloh', '08000000000', '08000000000', 'Business', 'Business', 'iloh2019@gmail.com', 271, 'Owerri, Imo State', 'active'),
(256, 'Leonard Leonard', 'Leonard Leonard', '08000000000', '08000000000', 'Business', 'Business', 'leonard67@gmail.com', 272, 'Owerri, Imo State', 'active'),
(257, 'Marcus Marcus', 'Marcus Marcus', '08000000000', '08000000000', 'Business', 'Business', 'marcus12@gmail.com', 273, 'Owerri, Imo State', 'active'),
(258, 'Ndukauba Ndukauba', 'Ndukauba Ndukauba', '08000000000', '08000000000', 'Business', 'Business', 'ndukauba45@gmail.com', 274, 'Owerri, Imo State', 'active'),
(259, 'Nwachukwu Nwachukwu', 'Nwachukwu Nwachukwu', '08000000000', '08000000000', 'Business', 'Business', 'nwachukwu67@gmail.com', 275, 'Owerri, Imo State', 'active'),
(260, 'Okereke Ch', 'Okereke Ch', '08000000000', '08000000000', 'Business', 'Business', 'okerekech67@gmail.com', 276, 'Owerri, Imo State', 'active'),
(261, 'Theodore Theodore', 'Theodore Theodore', '08000000000', '08000000000', 'Business', 'Business', 'theodore23@gmail.com', 277, 'Owerri, Imo State', 'active'),
(262, 'Ugwuegbu Ugwuegbu', 'Ugwuegbu Ugwuegbu', '08000000000', '08000000000', 'Business', 'Business', 'ugwuegbu66@gmail.com', 278, 'Owerri, Imo State', 'active'),
(263, 'Victor Victor', 'Victor Victor', '08000000000', '08000000000', 'Business', 'Business', 'victor676@gmail.com', 279, 'Owerri, Imo State', 'active'),
(264, 'Agaziem Agaziem', 'Agaziem Agaziem', '08000000000', '08000000000', 'Business', 'Business', 'agaziem54@gmail.com', 280, 'Owerri, Imo State', 'active'),
(265, 'Anyanwu Anyanwu', 'Anyanwu Anyanwu', '08000000000', '08000000000', 'Business', 'Business', 'anyanwu34@gmail.com', 281, 'Owerri, Imo State', 'active'),
(266, 'Cyril Ch', 'Cyril Ch', '08000000000', '08000000000', 'Business', 'Business', 'cyrilch34@gmail.com', 282, 'Owerri, Imo State', 'active'),
(267, 'Duru Duru', 'Duru Duru', '08000000000', '08000000000', 'Business', 'Business', 'duru87@gmail.com', 283, 'Owerri, Imo State', 'active'),
(268, 'Eke Eke', 'Eke Eke', '08000000000', '08000000000', 'Business', 'Business', 'eke2014@gmail.com', 284, 'Owerri, Imo State', 'active'),
(269, 'Eze Eze', 'Eze Eze', '08000000000', '08000000000', 'Business', 'Business', 'eze7535@gmail.com', 285, 'Owerri, Imo State', 'active'),
(270, 'Kamdirichukwn Kamdirichukwn', 'Kamdirichukwn Kamdirichukwn', '08000000000', '08000000000', 'Business', 'Business', 'kamdirichukwn67@gmail.com', 286, 'Owerri, Imo State', 'active'),
(271, 'Kanu Kanu', 'Kanu Kanu', '08000000000', '08000000000', 'Business', 'Business', 'kanu54@gmail.com', 287, 'Owerri, Imo State', 'active'),
(272, 'Emmanuel CHUK', 'Emmanuel CHUK', '08000000000', '08000000000', 'Business', 'Business', 'emmanuelchuk01@gmail.com', 288, 'Owerri, Imo State', 'active'),
(273, 'Okeke S', 'Okeke S', '08000000000', '08000000000', 'Business', 'Business', 'okekess@gmail.com', 289, 'Owerri, Imo State', 'active'),
(274, 'Paul Paul', 'Paul Paul', '08000000000', '08000000000', 'Business', 'Business', 'paul67@gmail.com', 290, 'Owerri, Imo State', 'active'),
(275, 'Nzeh Nzeh', 'Nzeh Nzeh', '08000000000', '08000000000', 'Business', 'Business', 'nzed45@gmail.com', 291, 'Owerri, Imo State', 'active'),
(276, 'Thaddeus Thaddeus', 'Thaddeus Thaddeus', '08000000000', '08000000000', 'Business', 'Business', 'thaddeus67@gmail.com', 292, 'Owerri, Imo State', 'active'),
(277, 'Ugwuegbu D', 'Ugwuegbu D', '08000000000', '08000000000', 'Business', 'Business', 'ugwuegbudd56@gmail.com', 293, 'Owerri, Imo State', 'active'),
(278, 'Ukoha Ukoha', 'Ukoha Ukoha', '08000000000', '08000000000', 'Business', 'Business', 'ukoha57@gmail.com', 294, 'Owerri, Imo State', 'active'),
(279, 'Chigoziem Chigoziem', 'Chigoziem Chigoziem', '08000000000', '08000000000', 'Business', 'Business', 'chigoziem67@gmail.com', 295, 'Owerri, Imo State', 'active'),
(280, 'Onyeanu Onyeanu', 'Onyeanu Onyeanu', '08000000000', '08000000000', 'Business', 'Business', 'onyeanu14@gmail.com', 296, 'Owerri, Imo State', 'active'),
(281, 'Chukwuemeka Chi', 'Chukwuemeka Chi', '08000000000', '08000000000', 'Business', 'Business', 'chukwuemekaccc637@gmail.com', 297, 'Owerri, Imo State', 'active'),
(282, 'Onwusonye Onwusonye', 'Onwusonye Onwusonye', '08000000000', '08000000000', 'Business', 'Business', 'onwusonye12@gmail.com', 298, 'Owerri, Imo State', 'active'),
(283, 'Ekeh Ekeh', 'Ekeh Ekeh', '08000000000', '08000000000', 'Business', 'Business', 'eken612@gmail.com', 299, 'Owerri, Imo State', 'active'),
(284, 'Onyeanu M', 'Onyeanu M', '08000000000', '08000000000', 'Business', 'Business', 'onyeanumm74@gmail.com', 300, 'Owerri, Imo State', 'active'),
(285, 'Alozie-oji Jide', 'Alozie-oji Jide', '08000000000', '08000000000', 'Business', 'Business', 'alozieojizz11@gmail.com', 301, 'Owerri, Imo State', 'active'),
(286, 'Ebere E', 'Ebere E', '08000000000', '08000000000', 'Business', 'Business', 'ebererr12@gmail.com', 302, 'Owerri, Imo State', 'active'),
(287, 'Henry-akpa chi', 'Henry-akpa chi', '08000000000', '08000000000', 'Business', 'Business', 'henryakpachi2014@gmail.com', 303, 'Owerri, Imo State', 'active'),
(288, 'Onyewuchi Onyewuchi', 'Onyewuchi Onyewuchi', '08000000000', '08000000000', 'Business', 'Business', 'onyewuchi22@gmail.com', 304, 'Owerri, Imo State', 'active'),
(289, 'Nwachukwu S', 'Nwachukwu S', '08000000000', '08000000000', 'Business', 'Business', 'nwachukwnss12@gmail.com', 305, 'Owerri, Imo State', 'active'),
(290, 'Alozie-oji N', 'Alozie-oji N', '08000000000', '08000000000', 'Business', 'Business', 'alozieojizz41@gmail.com', 306, 'Owerri, Imo State', 'active'),
(291, 'Eleanya Chi', 'Eleanya Chi', '08000000000', '08000000000', 'Business', 'Business', 'Eleanyacc@gmail.com', 307, 'Owerri, Imo State', 'active'),
(292, 'Ojeh D', 'Ojeh D', '08000000000', '08000000000', 'Business', 'Business', 'ojehdd56@gmail.com', 308, 'Owerri, Imo State', 'active'),
(293, 'Ibe Ibe', 'Ibe Ibe', '08000000000', '08000000000', 'Business', 'Business', 'ibe2019@gmail.com', 309, 'Owerri, Imo State', 'active'),
(294, 'Onyemaobi Onyemaobi', 'Onyemaobi Onyemaobi', '08000000000', '08000000000', 'Business', 'Business', 'onyemaobi67@gmail.com', 310, 'Owerri, Imo State', 'active'),
(295, 'Ayegba Ayegba', 'Ayegba Ayegba', '08000000000', '08000000000', 'Business', 'Business', 'ayegba310@gmail.com', 311, 'Owerri, Imo State', 'active'),
(296, 'Njoku Njoku', 'Njoku Njoku', '08000000000', '08000000000', 'Business', 'Business', 'njoku514@gmail.com', 312, 'Owerri, Imo State', 'active'),
(297, 'Njoku J', 'Njoku J', '08000000000', '08000000000', 'Business', 'Business', 'Jnjoku121@gmail.com', 313, 'Owerri, Imo State', 'active'),
(298, 'Michelle Michelle', 'Michelle Michelle', '08000000000', '08000000000', 'Business', 'Business', 'michelle120@gmail.com', 314, 'Owerri, Imo State', 'active'),
(299, 'Marcus N', 'Marcus N', '08000000000', '08000000000', 'Business', 'Business', 'nmarcus123@gmail.com', 315, 'Owerri, Imo State', 'active'),
(300, 'Okonkwo Z', 'Okonkwo Z', '08000000000', '08000000000', 'Business', 'Business', 'zokonkwo617@gmail.com', 316, 'Owerri, Imo State', 'active'),
(301, 'Obika Obika', 'Obika Obika', '08000000000', '08000000000', 'Business', 'Business', 'obika123@gmail.com', 317, 'Owerri, Imo State', 'active'),
(302, 'Ahumibe Ahumibe', 'Ahumibe Ahumibe', '08000000000', '08000000000', 'Business', 'Business', 'ahumibe76@gmail.com', 318, 'Owerri, Imo State', 'active'),
(303, 'OPARA C.', 'OPARA C.', '08000000000', '08000000000', 'Business', 'Business', 'oparac2025@gmail.com', 319, 'Owerri, Imo State', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `sparents_students`
--

CREATE TABLE `sparents_students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE `sponsors` (
  `id` int(11) NOT NULL,
  `name` varchar(244) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(444) DEFAULT NULL,
  `emailaddress` varchar(222) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `dateadded` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sponsors`
--

INSERT INTO `sponsors` (`id`, `name`, `phone`, `address`, `emailaddress`, `admin_id`, `dateadded`) VALUES
(1, 'WEST', '+2347036614567', 'Nekede Imo State', 'westcmf@gmail.com', 1, '2021-12-03 11:31:59'),
(2, 'EAST', '07036614567', 'Nekede Imo State', 'east@cun.edu.ng', 1, '2022-02-01 15:13:32'),
(3, 'Sr. Uzoma', '09000000000', 'Claretian Hospital Network, Nekede', 'uzoma@gmail.com', 1, '2022-06-29 09:19:15');

-- --------------------------------------------------------

--
-- Table structure for table `sponsorshippayments`
--

CREATE TABLE `sponsorshippayments` (
  `id` int(11) NOT NULL,
  `sref` varchar(222) NOT NULL,
  `sponsorship_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `datecreated` timestamp NULL DEFAULT current_timestamp(),
  `paystatus` varchar(22) NOT NULL DEFAULT 'Unpaid',
  `isictfee` varchar(8) DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsorships`
--

CREATE TABLE `sponsorships` (
  `id` int(11) NOT NULL,
  `sponsor_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `noofstudents` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `datecreated` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsorships_students`
--

CREATE TABLE `sponsorships_students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `sponsorship_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staffdepartments`
--

CREATE TABLE `staffdepartments` (
  `id` int(11) NOT NULL,
  `name` varchar(240) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staffdepartments`
--

INSERT INTO `staffdepartments` (`id`, `name`) VALUES
(1, 'ICT'),
(2, 'Works'),
(3, 'Finance'),
(4, 'Accounts');

-- --------------------------------------------------------

--
-- Table structure for table `staffgrades`
--

CREATE TABLE `staffgrades` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `basicsalary` int(11) NOT NULL,
  `tax` int(11) NOT NULL DEFAULT 0,
  `deduction` int(11) NOT NULL DEFAULT 0,
  `allowance` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staffgrades`
--

INSERT INTO `staffgrades` (`id`, `name`, `basicsalary`, `tax`, `deduction`, `allowance`) VALUES
(1, 'Grade Level 1', 30000, 0, 100, 2000),
(2, 'Grade Level 2', 45000, 1500, 500, 470);

-- --------------------------------------------------------

--
-- Table structure for table `staffmessages`
--

CREATE TABLE `staffmessages` (
  `id` int(11) NOT NULL,
  `title` varchar(188) NOT NULL,
  `message` varchar(600) NOT NULL,
  `datecreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `teacher_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(44) DEFAULT 'unseen'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `name`, `country_id`) VALUES
(1, 'Andaman and Nicobar Islands', 101),
(2, 'Andhra Pradesh', 101),
(3, 'Arunachal Pradesh', 101),
(4, 'Assam', 101),
(5, 'Bihar', 101),
(6, 'Chandigarh', 101),
(7, 'Chhattisgarh', 101),
(8, 'Dadra and Nagar Haveli', 101),
(9, 'Daman and Diu', 101),
(10, 'Delhi', 101),
(11, 'Goa', 101),
(12, 'Gujarat', 101),
(13, 'Haryana', 101),
(14, 'Himachal Pradesh', 101),
(15, 'Jammu and Kashmir', 101),
(16, 'Jharkhand', 101),
(17, 'Karnataka', 101),
(18, 'Kenmore', 101),
(19, 'Kerala', 101),
(20, 'Lakshadweep', 101),
(21, 'Madhya Pradesh', 101),
(22, 'Maharashtra', 101),
(23, 'Manipur', 101),
(24, 'Meghalaya', 101),
(25, 'Mizoram', 101),
(26, 'Nagaland', 101),
(27, 'Narora', 101),
(28, 'Natwar', 101),
(29, 'Odisha', 101),
(30, 'Paschim Medinipur', 101),
(31, 'Pondicherry', 101),
(32, 'Punjab', 101),
(33, 'Rajasthan', 101),
(34, 'Sikkim', 101),
(35, 'Tamil Nadu', 101),
(36, 'Telangana', 101),
(37, 'Tripura', 101),
(38, 'Uttar Pradesh', 101),
(39, 'Uttarakhand', 101),
(40, 'Vaishali', 101),
(41, 'West Bengal', 101),
(42, 'Badakhshan', 1),
(43, 'Badgis', 1),
(44, 'Baglan', 1),
(45, 'Balkh', 1),
(46, 'Bamiyan', 1),
(47, 'Farah', 1),
(48, 'Faryab', 1),
(49, 'Gawr', 1),
(50, 'Gazni', 1),
(51, 'Herat', 1),
(52, 'Hilmand', 1),
(53, 'Jawzjan', 1),
(54, 'Kabul', 1),
(55, 'Kapisa', 1),
(56, 'Khawst', 1),
(57, 'Kunar', 1),
(58, 'Lagman', 1),
(59, 'Lawghar', 1),
(60, 'Nangarhar', 1),
(61, 'Nimruz', 1),
(62, 'Nuristan', 1),
(63, 'Paktika', 1),
(64, 'Paktiya', 1),
(65, 'Parwan', 1),
(66, 'Qandahar', 1),
(67, 'Qunduz', 1),
(68, 'Samangan', 1),
(69, 'Sar-e Pul', 1),
(70, 'Takhar', 1),
(71, 'Uruzgan', 1),
(72, 'Wardag', 1),
(73, 'Zabul', 1),
(74, 'Berat', 2),
(75, 'Bulqize', 2),
(76, 'Delvine', 2),
(77, 'Devoll', 2),
(78, 'Dibre', 2),
(79, 'Durres', 2),
(80, 'Elbasan', 2),
(81, 'Fier', 2),
(82, 'Gjirokaster', 2),
(83, 'Gramsh', 2),
(84, 'Has', 2),
(85, 'Kavaje', 2),
(86, 'Kolonje', 2),
(87, 'Korce', 2),
(88, 'Kruje', 2),
(89, 'Kucove', 2),
(90, 'Kukes', 2),
(91, 'Kurbin', 2),
(92, 'Lezhe', 2),
(93, 'Librazhd', 2),
(94, 'Lushnje', 2),
(95, 'Mallakaster', 2),
(96, 'Malsi e Madhe', 2),
(97, 'Mat', 2),
(98, 'Mirdite', 2),
(99, 'Peqin', 2),
(100, 'Permet', 2),
(101, 'Pogradec', 2),
(102, 'Puke', 2),
(103, 'Sarande', 2),
(104, 'Shkoder', 2),
(105, 'Skrapar', 2),
(106, 'Tepelene', 2),
(107, 'Tirane', 2),
(108, 'Tropoje', 2),
(109, 'Vlore', 2),
(110, '\'Ayn Daflah', 3),
(111, '\'Ayn Tamushanat', 3),
(112, 'Adrar', 3),
(113, 'Algiers', 3),
(114, 'Annabah', 3),
(115, 'Bashshar', 3),
(116, 'Batnah', 3),
(117, 'Bijayah', 3),
(118, 'Biskrah', 3),
(119, 'Blidah', 3),
(120, 'Buirah', 3),
(121, 'Bumardas', 3),
(122, 'Burj Bu Arririj', 3),
(123, 'Ghalizan', 3),
(124, 'Ghardayah', 3),
(125, 'Ilizi', 3),
(126, 'Jijili', 3),
(127, 'Jilfah', 3),
(128, 'Khanshalah', 3),
(129, 'Masilah', 3),
(130, 'Midyah', 3),
(131, 'Milah', 3),
(132, 'Muaskar', 3),
(133, 'Mustaghanam', 3),
(134, 'Naama', 3),
(135, 'Oran', 3),
(136, 'Ouargla', 3),
(137, 'Qalmah', 3),
(138, 'Qustantinah', 3),
(139, 'Sakikdah', 3),
(140, 'Satif', 3),
(141, 'Sayda\'', 3),
(142, 'Sidi ban-al-\'Abbas', 3),
(143, 'Suq Ahras', 3),
(144, 'Tamanghasat', 3),
(145, 'Tibazah', 3),
(146, 'Tibissah', 3),
(147, 'Tilimsan', 3),
(148, 'Tinduf', 3),
(149, 'Tisamsilt', 3),
(150, 'Tiyarat', 3),
(151, 'Tizi Wazu', 3),
(152, 'Umm-al-Bawaghi', 3),
(153, 'Wahran', 3),
(154, 'Warqla', 3),
(155, 'Wilaya d Alger', 3),
(156, 'Wilaya de Bejaia', 3),
(157, 'Wilaya de Constantine', 3),
(158, 'al-Aghwat', 3),
(159, 'al-Bayadh', 3),
(160, 'al-Jaza\'ir', 3),
(161, 'al-Wad', 3),
(162, 'ash-Shalif', 3),
(163, 'at-Tarif', 3),
(164, 'Eastern', 4),
(165, 'Manu\'a', 4),
(166, 'Swains Island', 4),
(167, 'Western', 4),
(168, 'Andorra la Vella', 5),
(169, 'Canillo', 5),
(170, 'Encamp', 5),
(171, 'La Massana', 5),
(172, 'Les Escaldes', 5),
(173, 'Ordino', 5),
(174, 'Sant Julia de Loria', 5),
(175, 'Bengo', 6),
(176, 'Benguela', 6),
(177, 'Bie', 6),
(178, 'Cabinda', 6),
(179, 'Cunene', 6),
(180, 'Huambo', 6),
(181, 'Huila', 6),
(182, 'Kuando-Kubango', 6),
(183, 'Kwanza Norte', 6),
(184, 'Kwanza Sul', 6),
(185, 'Luanda', 6),
(186, 'Lunda Norte', 6),
(187, 'Lunda Sul', 6),
(188, 'Malanje', 6),
(189, 'Moxico', 6),
(190, 'Namibe', 6),
(191, 'Uige', 6),
(192, 'Zaire', 6),
(193, 'Other Provinces', 7),
(194, 'Sector claimed by Argentina/Ch', 8),
(195, 'Sector claimed by Argentina/UK', 8),
(196, 'Sector claimed by Australia', 8),
(197, 'Sector claimed by France', 8),
(198, 'Sector claimed by New Zealand', 8),
(199, 'Sector claimed by Norway', 8),
(200, 'Unclaimed Sector', 8),
(201, 'Barbuda', 9),
(202, 'Saint George', 9),
(203, 'Saint John', 9),
(204, 'Saint Mary', 9),
(205, 'Saint Paul', 9),
(206, 'Saint Peter', 9),
(207, 'Saint Philip', 9),
(208, 'Buenos Aires', 10),
(209, 'Catamarca', 10),
(210, 'Chaco', 10),
(211, 'Chubut', 10),
(212, 'Cordoba', 10),
(213, 'Corrientes', 10),
(214, 'Distrito Federal', 10),
(215, 'Entre Rios', 10),
(216, 'Formosa', 10),
(217, 'Jujuy', 10),
(218, 'La Pampa', 10),
(219, 'La Rioja', 10),
(220, 'Mendoza', 10),
(221, 'Misiones', 10),
(222, 'Neuquen', 10),
(223, 'Rio Negro', 10),
(224, 'Salta', 10),
(225, 'San Juan', 10),
(226, 'San Luis', 10),
(227, 'Santa Cruz', 10),
(228, 'Santa Fe', 10),
(229, 'Santiago del Estero', 10),
(230, 'Tierra del Fuego', 10),
(231, 'Tucuman', 10),
(232, 'Aragatsotn', 11),
(233, 'Ararat', 11),
(234, 'Armavir', 11),
(235, 'Gegharkunik', 11),
(236, 'Kotaik', 11),
(237, 'Lori', 11),
(238, 'Shirak', 11),
(239, 'Stepanakert', 11),
(240, 'Syunik', 11),
(241, 'Tavush', 11),
(242, 'Vayots Dzor', 11),
(243, 'Yerevan', 11),
(244, 'Aruba', 12),
(245, 'Auckland', 13),
(246, 'Australian Capital Territory', 13),
(247, 'Balgowlah', 13),
(248, 'Balmain', 13),
(249, 'Bankstown', 13),
(250, 'Baulkham Hills', 13),
(251, 'Bonnet Bay', 13),
(252, 'Camberwell', 13),
(253, 'Carole Park', 13),
(254, 'Castle Hill', 13),
(255, 'Caulfield', 13),
(256, 'Chatswood', 13),
(257, 'Cheltenham', 13),
(258, 'Cherrybrook', 13),
(259, 'Clayton', 13),
(260, 'Collingwood', 13),
(261, 'Frenchs Forest', 13),
(262, 'Hawthorn', 13),
(263, 'Jannnali', 13),
(264, 'Knoxfield', 13),
(265, 'Melbourne', 13),
(266, 'New South Wales', 13),
(267, 'Northern Territory', 13),
(268, 'Perth', 13),
(269, 'Queensland', 13),
(270, 'South Australia', 13),
(271, 'Tasmania', 13),
(272, 'Templestowe', 13),
(273, 'Victoria', 13),
(274, 'Werribee south', 13),
(275, 'Western Australia', 13),
(276, 'Wheeler', 13),
(277, 'Bundesland Salzburg', 14),
(278, 'Bundesland Steiermark', 14),
(279, 'Bundesland Tirol', 14),
(280, 'Burgenland', 14),
(281, 'Carinthia', 14),
(282, 'Karnten', 14),
(283, 'Liezen', 14),
(284, 'Lower Austria', 14),
(285, 'Niederosterreich', 14),
(286, 'Oberosterreich', 14),
(287, 'Salzburg', 14),
(288, 'Schleswig-Holstein', 14),
(289, 'Steiermark', 14),
(290, 'Styria', 14),
(291, 'Tirol', 14),
(292, 'Upper Austria', 14),
(293, 'Vorarlberg', 14),
(294, 'Wien', 14),
(295, 'Abseron', 15),
(296, 'Baki Sahari', 15),
(297, 'Ganca', 15),
(298, 'Ganja', 15),
(299, 'Kalbacar', 15),
(300, 'Lankaran', 15),
(301, 'Mil-Qarabax', 15),
(302, 'Mugan-Salyan', 15),
(303, 'Nagorni-Qarabax', 15),
(304, 'Naxcivan', 15),
(305, 'Priaraks', 15),
(306, 'Qazax', 15),
(307, 'Saki', 15),
(308, 'Sirvan', 15),
(309, 'Xacmaz', 15),
(310, 'Abaco', 16),
(311, 'Acklins Island', 16),
(312, 'Andros', 16),
(313, 'Berry Islands', 16),
(314, 'Biminis', 16),
(315, 'Cat Island', 16),
(316, 'Crooked Island', 16),
(317, 'Eleuthera', 16),
(318, 'Exuma and Cays', 16),
(319, 'Grand Bahama', 16),
(320, 'Inagua Islands', 16),
(321, 'Long Island', 16),
(322, 'Mayaguana', 16),
(323, 'New Providence', 16),
(324, 'Ragged Island', 16),
(325, 'Rum Cay', 16),
(326, 'San Salvador', 16),
(327, '\'Isa', 17),
(328, 'Badiyah', 17),
(329, 'Hidd', 17),
(330, 'Jidd Hafs', 17),
(331, 'Mahama', 17),
(332, 'Manama', 17),
(333, 'Sitrah', 17),
(334, 'al-Manamah', 17),
(335, 'al-Muharraq', 17),
(336, 'ar-Rifa\'a', 17),
(337, 'Bagar Hat', 18),
(338, 'Bandarban', 18),
(339, 'Barguna', 18),
(340, 'Barisal', 18),
(341, 'Bhola', 18),
(342, 'Bogora', 18),
(343, 'Brahman Bariya', 18),
(344, 'Chandpur', 18),
(345, 'Chattagam', 18),
(346, 'Chittagong Division', 18),
(347, 'Chuadanga', 18),
(348, 'Dhaka', 18),
(349, 'Dinajpur', 18),
(350, 'Faridpur', 18),
(351, 'Feni', 18),
(352, 'Gaybanda', 18),
(353, 'Gazipur', 18),
(354, 'Gopalganj', 18),
(355, 'Habiganj', 18),
(356, 'Jaipur Hat', 18),
(357, 'Jamalpur', 18),
(358, 'Jessor', 18),
(359, 'Jhalakati', 18),
(360, 'Jhanaydah', 18),
(361, 'Khagrachhari', 18),
(362, 'Khulna', 18),
(363, 'Kishorganj', 18),
(364, 'Koks Bazar', 18),
(365, 'Komilla', 18),
(366, 'Kurigram', 18),
(367, 'Kushtiya', 18),
(368, 'Lakshmipur', 18),
(369, 'Lalmanir Hat', 18),
(370, 'Madaripur', 18),
(371, 'Magura', 18),
(372, 'Maimansingh', 18),
(373, 'Manikganj', 18),
(374, 'Maulvi Bazar', 18),
(375, 'Meherpur', 18),
(376, 'Munshiganj', 18),
(377, 'Naral', 18),
(378, 'Narayanganj', 18),
(379, 'Narsingdi', 18),
(380, 'Nator', 18),
(381, 'Naugaon', 18),
(382, 'Nawabganj', 18),
(383, 'Netrakona', 18),
(384, 'Nilphamari', 18),
(385, 'Noakhali', 18),
(386, 'Pabna', 18),
(387, 'Panchagarh', 18),
(388, 'Patuakhali', 18),
(389, 'Pirojpur', 18),
(390, 'Rajbari', 18),
(391, 'Rajshahi', 18),
(392, 'Rangamati', 18),
(393, 'Rangpur', 18),
(394, 'Satkhira', 18),
(395, 'Shariatpur', 18),
(396, 'Sherpur', 18),
(397, 'Silhat', 18),
(398, 'Sirajganj', 18),
(399, 'Sunamganj', 18),
(400, 'Tangayal', 18),
(401, 'Thakurgaon', 18),
(402, 'Christ Church', 19),
(403, 'Saint Andrew', 19),
(404, 'Saint George', 19),
(405, 'Saint James', 19),
(406, 'Saint John', 19),
(407, 'Saint Joseph', 19),
(408, 'Saint Lucy', 19),
(409, 'Saint Michael', 19),
(410, 'Saint Peter', 19),
(411, 'Saint Philip', 19),
(412, 'Saint Thomas', 19),
(413, 'Brest', 20),
(414, 'Homjel\'', 20),
(415, 'Hrodna', 20),
(416, 'Mahiljow', 20),
(417, 'Mahilyowskaya Voblasts', 20),
(418, 'Minsk', 20),
(419, 'Minskaja Voblasts\'', 20),
(420, 'Petrik', 20),
(421, 'Vicebsk', 20),
(422, 'Antwerpen', 21),
(423, 'Berchem', 21),
(424, 'Brabant', 21),
(425, 'Brabant Wallon', 21),
(426, 'Brussel', 21),
(427, 'East Flanders', 21),
(428, 'Hainaut', 21),
(429, 'Liege', 21),
(430, 'Limburg', 21),
(431, 'Luxembourg', 21),
(432, 'Namur', 21),
(433, 'Ontario', 21),
(434, 'Oost-Vlaanderen', 21),
(435, 'Provincie Brabant', 21),
(436, 'Vlaams-Brabant', 21),
(437, 'Wallonne', 21),
(438, 'West-Vlaanderen', 21),
(439, 'Belize', 22),
(440, 'Cayo', 22),
(441, 'Corozal', 22),
(442, 'Orange Walk', 22),
(443, 'Stann Creek', 22),
(444, 'Toledo', 22),
(445, 'Alibori', 23),
(446, 'Atacora', 23),
(447, 'Atlantique', 23),
(448, 'Borgou', 23),
(449, 'Collines', 23),
(450, 'Couffo', 23),
(451, 'Donga', 23),
(452, 'Littoral', 23),
(453, 'Mono', 23),
(454, 'Oueme', 23),
(455, 'Plateau', 23),
(456, 'Zou', 23),
(457, 'Hamilton', 24),
(458, 'Saint George', 24),
(459, 'Bumthang', 25),
(460, 'Chhukha', 25),
(461, 'Chirang', 25),
(462, 'Daga', 25),
(463, 'Geylegphug', 25),
(464, 'Ha', 25),
(465, 'Lhuntshi', 25),
(466, 'Mongar', 25),
(467, 'Pemagatsel', 25),
(468, 'Punakha', 25),
(469, 'Rinpung', 25),
(470, 'Samchi', 25),
(471, 'Samdrup Jongkhar', 25),
(472, 'Shemgang', 25),
(473, 'Tashigang', 25),
(474, 'Timphu', 25),
(475, 'Tongsa', 25),
(476, 'Wangdiphodrang', 25),
(477, 'Beni', 26),
(478, 'Chuquisaca', 26),
(479, 'Cochabamba', 26),
(480, 'La Paz', 26),
(481, 'Oruro', 26),
(482, 'Pando', 26),
(483, 'Potosi', 26),
(484, 'Santa Cruz', 26),
(485, 'Tarija', 26),
(486, 'Federacija Bosna i Hercegovina', 27),
(487, 'Republika Srpska', 27),
(488, 'Central Bobonong', 28),
(489, 'Central Boteti', 28),
(490, 'Central Mahalapye', 28),
(491, 'Central Serowe-Palapye', 28),
(492, 'Central Tutume', 28),
(493, 'Chobe', 28),
(494, 'Francistown', 28),
(495, 'Gaborone', 28),
(496, 'Ghanzi', 28),
(497, 'Jwaneng', 28),
(498, 'Kgalagadi North', 28),
(499, 'Kgalagadi South', 28),
(500, 'Kgatleng', 28),
(501, 'Kweneng', 28),
(502, 'Lobatse', 28),
(503, 'Ngamiland', 28),
(504, 'Ngwaketse', 28),
(505, 'North East', 28),
(506, 'Okavango', 28),
(507, 'Orapa', 28),
(508, 'Selibe Phikwe', 28),
(509, 'South East', 28),
(510, 'Sowa', 28),
(511, 'Bouvet Island', 29),
(512, 'Acre', 30),
(513, 'Alagoas', 30),
(514, 'Amapa', 30),
(515, 'Amazonas', 30),
(516, 'Bahia', 30),
(517, 'Ceara', 30),
(518, 'Distrito Federal', 30),
(519, 'Espirito Santo', 30),
(520, 'Estado de Sao Paulo', 30),
(521, 'Goias', 30),
(522, 'Maranhao', 30),
(523, 'Mato Grosso', 30),
(524, 'Mato Grosso do Sul', 30),
(525, 'Minas Gerais', 30),
(526, 'Para', 30),
(527, 'Paraiba', 30),
(528, 'Parana', 30),
(529, 'Pernambuco', 30),
(530, 'Piaui', 30),
(531, 'Rio Grande do Norte', 30),
(532, 'Rio Grande do Sul', 30),
(533, 'Rio de Janeiro', 30),
(534, 'Rondonia', 30),
(535, 'Roraima', 30),
(536, 'Santa Catarina', 30),
(537, 'Sao Paulo', 30),
(538, 'Sergipe', 30),
(539, 'Tocantins', 30),
(540, 'British Indian Ocean Territory', 31),
(541, 'Belait', 32),
(542, 'Brunei-Muara', 32),
(543, 'Temburong', 32),
(544, 'Tutong', 32),
(545, 'Blagoevgrad', 33),
(546, 'Burgas', 33),
(547, 'Dobrich', 33),
(548, 'Gabrovo', 33),
(549, 'Haskovo', 33),
(550, 'Jambol', 33),
(551, 'Kardzhali', 33),
(552, 'Kjustendil', 33),
(553, 'Lovech', 33),
(554, 'Montana', 33),
(555, 'Oblast Sofiya-Grad', 33),
(556, 'Pazardzhik', 33),
(557, 'Pernik', 33),
(558, 'Pleven', 33),
(559, 'Plovdiv', 33),
(560, 'Razgrad', 33),
(561, 'Ruse', 33),
(562, 'Shumen', 33),
(563, 'Silistra', 33),
(564, 'Sliven', 33),
(565, 'Smoljan', 33),
(566, 'Sofija grad', 33),
(567, 'Sofijska oblast', 33),
(568, 'Stara Zagora', 33),
(569, 'Targovishte', 33),
(570, 'Varna', 33),
(571, 'Veliko Tarnovo', 33),
(572, 'Vidin', 33),
(573, 'Vraca', 33),
(574, 'Yablaniza', 33),
(575, 'Bale', 34),
(576, 'Bam', 34),
(577, 'Bazega', 34),
(578, 'Bougouriba', 34),
(579, 'Boulgou', 34),
(580, 'Boulkiemde', 34),
(581, 'Comoe', 34),
(582, 'Ganzourgou', 34),
(583, 'Gnagna', 34),
(584, 'Gourma', 34),
(585, 'Houet', 34),
(586, 'Ioba', 34),
(587, 'Kadiogo', 34),
(588, 'Kenedougou', 34),
(589, 'Komandjari', 34),
(590, 'Kompienga', 34),
(591, 'Kossi', 34),
(592, 'Kouritenga', 34),
(593, 'Kourweogo', 34),
(594, 'Leraba', 34),
(595, 'Mouhoun', 34),
(596, 'Nahouri', 34),
(597, 'Namentenga', 34),
(598, 'Noumbiel', 34),
(599, 'Oubritenga', 34),
(600, 'Oudalan', 34),
(601, 'Passore', 34),
(602, 'Poni', 34),
(603, 'Sanguie', 34),
(604, 'Sanmatenga', 34),
(605, 'Seno', 34),
(606, 'Sissili', 34),
(607, 'Soum', 34),
(608, 'Sourou', 34),
(609, 'Tapoa', 34),
(610, 'Tuy', 34),
(611, 'Yatenga', 34),
(612, 'Zondoma', 34),
(613, 'Zoundweogo', 34),
(614, 'Bubanza', 35),
(615, 'Bujumbura', 35),
(616, 'Bururi', 35),
(617, 'Cankuzo', 35),
(618, 'Cibitoke', 35),
(619, 'Gitega', 35),
(620, 'Karuzi', 35),
(621, 'Kayanza', 35),
(622, 'Kirundo', 35),
(623, 'Makamba', 35),
(624, 'Muramvya', 35),
(625, 'Muyinga', 35),
(626, 'Ngozi', 35),
(627, 'Rutana', 35),
(628, 'Ruyigi', 35),
(629, 'Banteay Mean Chey', 36),
(630, 'Bat Dambang', 36),
(631, 'Kampong Cham', 36),
(632, 'Kampong Chhnang', 36),
(633, 'Kampong Spoeu', 36),
(634, 'Kampong Thum', 36),
(635, 'Kampot', 36),
(636, 'Kandal', 36),
(637, 'Kaoh Kong', 36),
(638, 'Kracheh', 36),
(639, 'Krong Kaeb', 36),
(640, 'Krong Pailin', 36),
(641, 'Krong Preah Sihanouk', 36),
(642, 'Mondol Kiri', 36),
(643, 'Otdar Mean Chey', 36),
(644, 'Phnum Penh', 36),
(645, 'Pousat', 36),
(646, 'Preah Vihear', 36),
(647, 'Prey Veaeng', 36),
(648, 'Rotanak Kiri', 36),
(649, 'Siem Reab', 36),
(650, 'Stueng Traeng', 36),
(651, 'Svay Rieng', 36),
(652, 'Takaev', 36),
(653, 'Adamaoua', 37),
(654, 'Centre', 37),
(655, 'Est', 37),
(656, 'Littoral', 37),
(657, 'Nord', 37),
(658, 'Nord Extreme', 37),
(659, 'Nordouest', 37),
(660, 'Ouest', 37),
(661, 'Sud', 37),
(662, 'Sudouest', 37),
(663, 'Alberta', 38),
(664, 'British Columbia', 38),
(665, 'Manitoba', 38),
(666, 'New Brunswick', 38),
(667, 'Newfoundland and Labrador', 38),
(668, 'Northwest Territories', 38),
(669, 'Nova Scotia', 38),
(670, 'Nunavut', 38),
(671, 'Ontario', 38),
(672, 'Prince Edward Island', 38),
(673, 'Quebec', 38),
(674, 'Saskatchewan', 38),
(675, 'Yukon', 38),
(676, 'Boavista', 39),
(677, 'Brava', 39),
(678, 'Fogo', 39),
(679, 'Maio', 39),
(680, 'Sal', 39),
(681, 'Santo Antao', 39),
(682, 'Sao Nicolau', 39),
(683, 'Sao Tiago', 39),
(684, 'Sao Vicente', 39),
(685, 'Grand Cayman', 40),
(686, 'Bamingui-Bangoran', 41),
(687, 'Bangui', 41),
(688, 'Basse-Kotto', 41),
(689, 'Haut-Mbomou', 41),
(690, 'Haute-Kotto', 41),
(691, 'Kemo', 41),
(692, 'Lobaye', 41),
(693, 'Mambere-Kadei', 41),
(694, 'Mbomou', 41),
(695, 'Nana-Gribizi', 41),
(696, 'Nana-Mambere', 41),
(697, 'Ombella Mpoko', 41),
(698, 'Ouaka', 41),
(699, 'Ouham', 41),
(700, 'Ouham-Pende', 41),
(701, 'Sangha-Mbaere', 41),
(702, 'Vakaga', 41),
(703, 'Batha', 42),
(704, 'Biltine', 42),
(705, 'Bourkou-Ennedi-Tibesti', 42),
(706, 'Chari-Baguirmi', 42),
(707, 'Guera', 42),
(708, 'Kanem', 42),
(709, 'Lac', 42),
(710, 'Logone Occidental', 42),
(711, 'Logone Oriental', 42),
(712, 'Mayo-Kebbi', 42),
(713, 'Moyen-Chari', 42),
(714, 'Ouaddai', 42),
(715, 'Salamat', 42),
(716, 'Tandjile', 42),
(717, 'Aisen', 43),
(718, 'Antofagasta', 43),
(719, 'Araucania', 43),
(720, 'Atacama', 43),
(721, 'Bio Bio', 43),
(722, 'Coquimbo', 43),
(723, 'Libertador General Bernardo O\'', 43),
(724, 'Los Lagos', 43),
(725, 'Magellanes', 43),
(726, 'Maule', 43),
(727, 'Metropolitana', 43),
(728, 'Metropolitana de Santiago', 43),
(729, 'Tarapaca', 43),
(730, 'Valparaiso', 43),
(731, 'Anhui', 44),
(732, 'Anhui Province', 44),
(733, 'Anhui Sheng', 44),
(734, 'Aomen', 44),
(735, 'Beijing', 44),
(736, 'Beijing Shi', 44),
(737, 'Chongqing', 44),
(738, 'Fujian', 44),
(739, 'Fujian Sheng', 44),
(740, 'Gansu', 44),
(741, 'Guangdong', 44),
(742, 'Guangdong Sheng', 44),
(743, 'Guangxi', 44),
(744, 'Guizhou', 44),
(745, 'Hainan', 44),
(746, 'Hebei', 44),
(747, 'Heilongjiang', 44),
(748, 'Henan', 44),
(749, 'Hubei', 44),
(750, 'Hunan', 44),
(751, 'Jiangsu', 44),
(752, 'Jiangsu Sheng', 44),
(753, 'Jiangxi', 44),
(754, 'Jilin', 44),
(755, 'Liaoning', 44),
(756, 'Liaoning Sheng', 44),
(757, 'Nei Monggol', 44),
(758, 'Ningxia Hui', 44),
(759, 'Qinghai', 44),
(760, 'Shaanxi', 44),
(761, 'Shandong', 44),
(762, 'Shandong Sheng', 44),
(763, 'Shanghai', 44),
(764, 'Shanxi', 44),
(765, 'Sichuan', 44),
(766, 'Tianjin', 44),
(767, 'Xianggang', 44),
(768, 'Xinjiang', 44),
(769, 'Xizang', 44),
(770, 'Yunnan', 44),
(771, 'Zhejiang', 44),
(772, 'Zhejiang Sheng', 44),
(773, 'Christmas Island', 45),
(774, 'Cocos (Keeling) Islands', 46),
(775, 'Amazonas', 47),
(776, 'Antioquia', 47),
(777, 'Arauca', 47),
(778, 'Atlantico', 47),
(779, 'Bogota', 47),
(780, 'Bolivar', 47),
(781, 'Boyaca', 47),
(782, 'Caldas', 47),
(783, 'Caqueta', 47),
(784, 'Casanare', 47),
(785, 'Cauca', 47),
(786, 'Cesar', 47),
(787, 'Choco', 47),
(788, 'Cordoba', 47),
(789, 'Cundinamarca', 47),
(790, 'Guainia', 47),
(791, 'Guaviare', 47),
(792, 'Huila', 47),
(793, 'La Guajira', 47),
(794, 'Magdalena', 47),
(795, 'Meta', 47),
(796, 'Narino', 47),
(797, 'Norte de Santander', 47),
(798, 'Putumayo', 47),
(799, 'Quindio', 47),
(800, 'Risaralda', 47),
(801, 'San Andres y Providencia', 47),
(802, 'Santander', 47),
(803, 'Sucre', 47),
(804, 'Tolima', 47),
(805, 'Valle del Cauca', 47),
(806, 'Vaupes', 47),
(807, 'Vichada', 47),
(808, 'Mwali', 48),
(809, 'Njazidja', 48),
(810, 'Nzwani', 48),
(811, 'Bouenza', 49),
(812, 'Brazzaville', 49),
(813, 'Cuvette', 49),
(814, 'Kouilou', 49),
(815, 'Lekoumou', 49),
(816, 'Likouala', 49),
(817, 'Niari', 49),
(818, 'Plateaux', 49),
(819, 'Pool', 49),
(820, 'Sangha', 49),
(821, 'Bandundu', 50),
(822, 'Bas-Congo', 50),
(823, 'Equateur', 50),
(824, 'Haut-Congo', 50),
(825, 'Kasai-Occidental', 50),
(826, 'Kasai-Oriental', 50),
(827, 'Katanga', 50),
(828, 'Kinshasa', 50),
(829, 'Maniema', 50),
(830, 'Nord-Kivu', 50),
(831, 'Sud-Kivu', 50),
(832, 'Aitutaki', 51),
(833, 'Atiu', 51),
(834, 'Mangaia', 51),
(835, 'Manihiki', 51),
(836, 'Mauke', 51),
(837, 'Mitiaro', 51),
(838, 'Nassau', 51),
(839, 'Pukapuka', 51),
(840, 'Rakahanga', 51),
(841, 'Rarotonga', 51),
(842, 'Tongareva', 51),
(843, 'Alajuela', 52),
(844, 'Cartago', 52),
(845, 'Guanacaste', 52),
(846, 'Heredia', 52),
(847, 'Limon', 52),
(848, 'Puntarenas', 52),
(849, 'San Jose', 52),
(850, 'Abidjan', 53),
(851, 'Agneby', 53),
(852, 'Bafing', 53),
(853, 'Denguele', 53),
(854, 'Dix-huit Montagnes', 53),
(855, 'Fromager', 53),
(856, 'Haut-Sassandra', 53),
(857, 'Lacs', 53),
(858, 'Lagunes', 53),
(859, 'Marahoue', 53),
(860, 'Moyen-Cavally', 53),
(861, 'Moyen-Comoe', 53),
(862, 'N\'zi-Comoe', 53),
(863, 'Sassandra', 53),
(864, 'Savanes', 53),
(865, 'Sud-Bandama', 53),
(866, 'Sud-Comoe', 53),
(867, 'Vallee du Bandama', 53),
(868, 'Worodougou', 53),
(869, 'Zanzan', 53),
(870, 'Bjelovar-Bilogora', 54),
(871, 'Dubrovnik-Neretva', 54),
(872, 'Grad Zagreb', 54),
(873, 'Istra', 54),
(874, 'Karlovac', 54),
(875, 'Koprivnica-Krizhevci', 54),
(876, 'Krapina-Zagorje', 54),
(877, 'Lika-Senj', 54),
(878, 'Medhimurje', 54),
(879, 'Medimurska Zupanija', 54),
(880, 'Osijek-Baranja', 54),
(881, 'Osjecko-Baranjska Zupanija', 54),
(882, 'Pozhega-Slavonija', 54),
(883, 'Primorje-Gorski Kotar', 54),
(884, 'Shibenik-Knin', 54),
(885, 'Sisak-Moslavina', 54),
(886, 'Slavonski Brod-Posavina', 54),
(887, 'Split-Dalmacija', 54),
(888, 'Varazhdin', 54),
(889, 'Virovitica-Podravina', 54),
(890, 'Vukovar-Srijem', 54),
(891, 'Zadar', 54),
(892, 'Zagreb', 54),
(893, 'Camaguey', 55),
(894, 'Ciego de Avila', 55),
(895, 'Cienfuegos', 55),
(896, 'Ciudad de la Habana', 55),
(897, 'Granma', 55),
(898, 'Guantanamo', 55),
(899, 'Habana', 55),
(900, 'Holguin', 55),
(901, 'Isla de la Juventud', 55),
(902, 'La Habana', 55),
(903, 'Las Tunas', 55),
(904, 'Matanzas', 55),
(905, 'Pinar del Rio', 55),
(906, 'Sancti Spiritus', 55),
(907, 'Santiago de Cuba', 55),
(908, 'Villa Clara', 55),
(909, 'Government controlled area', 56),
(910, 'Limassol', 56),
(911, 'Nicosia District', 56),
(912, 'Paphos', 56),
(913, 'Turkish controlled area', 56),
(914, 'Central Bohemian', 57),
(915, 'Frycovice', 57),
(916, 'Jihocesky Kraj', 57),
(917, 'Jihochesky', 57),
(918, 'Jihomoravsky', 57),
(919, 'Karlovarsky', 57),
(920, 'Klecany', 57),
(921, 'Kralovehradecky', 57),
(922, 'Liberecky', 57),
(923, 'Lipov', 57),
(924, 'Moravskoslezsky', 57),
(925, 'Olomoucky', 57),
(926, 'Olomoucky Kraj', 57),
(927, 'Pardubicky', 57),
(928, 'Plzensky', 57),
(929, 'Praha', 57),
(930, 'Rajhrad', 57),
(931, 'Smirice', 57),
(932, 'South Moravian', 57),
(933, 'Straz nad Nisou', 57),
(934, 'Stredochesky', 57),
(935, 'Unicov', 57),
(936, 'Ustecky', 57),
(937, 'Valletta', 57),
(938, 'Velesin', 57),
(939, 'Vysochina', 57),
(940, 'Zlinsky', 57),
(941, 'Arhus', 58),
(942, 'Bornholm', 58),
(943, 'Frederiksborg', 58),
(944, 'Fyn', 58),
(945, 'Hovedstaden', 58),
(946, 'Kobenhavn', 58),
(947, 'Kobenhavns Amt', 58),
(948, 'Kobenhavns Kommune', 58),
(949, 'Nordjylland', 58),
(950, 'Ribe', 58),
(951, 'Ringkobing', 58),
(952, 'Roervig', 58),
(953, 'Roskilde', 58),
(954, 'Roslev', 58),
(955, 'Sjaelland', 58),
(956, 'Soeborg', 58),
(957, 'Sonderjylland', 58),
(958, 'Storstrom', 58),
(959, 'Syddanmark', 58),
(960, 'Toelloese', 58),
(961, 'Vejle', 58),
(962, 'Vestsjalland', 58),
(963, 'Viborg', 58),
(964, '\'Ali Sabih', 59),
(965, 'Dikhil', 59),
(966, 'Jibuti', 59),
(967, 'Tajurah', 59),
(968, 'Ubuk', 59),
(969, 'Saint Andrew', 60),
(970, 'Saint David', 60),
(971, 'Saint George', 60),
(972, 'Saint John', 60),
(973, 'Saint Joseph', 60),
(974, 'Saint Luke', 60),
(975, 'Saint Mark', 60),
(976, 'Saint Patrick', 60),
(977, 'Saint Paul', 60),
(978, 'Saint Peter', 60),
(979, 'Azua', 61),
(980, 'Bahoruco', 61),
(981, 'Barahona', 61),
(982, 'Dajabon', 61),
(983, 'Distrito Nacional', 61),
(984, 'Duarte', 61),
(985, 'El Seybo', 61),
(986, 'Elias Pina', 61),
(987, 'Espaillat', 61),
(988, 'Hato Mayor', 61),
(989, 'Independencia', 61),
(990, 'La Altagracia', 61),
(991, 'La Romana', 61),
(992, 'La Vega', 61),
(993, 'Maria Trinidad Sanchez', 61),
(994, 'Monsenor Nouel', 61),
(995, 'Monte Cristi', 61),
(996, 'Monte Plata', 61),
(997, 'Pedernales', 61),
(998, 'Peravia', 61),
(999, 'Puerto Plata', 61),
(1000, 'Salcedo', 61),
(1001, 'Samana', 61),
(1002, 'San Cristobal', 61),
(1003, 'San Juan', 61),
(1004, 'San Pedro de Macoris', 61),
(1005, 'Sanchez Ramirez', 61),
(1006, 'Santiago', 61),
(1007, 'Santiago Rodriguez', 61),
(1008, 'Valverde', 61),
(1009, 'Aileu', 62),
(1010, 'Ainaro', 62),
(1011, 'Ambeno', 62),
(1012, 'Baucau', 62),
(1013, 'Bobonaro', 62),
(1014, 'Cova Lima', 62),
(1015, 'Dili', 62),
(1016, 'Ermera', 62),
(1017, 'Lautem', 62),
(1018, 'Liquica', 62),
(1019, 'Manatuto', 62),
(1020, 'Manufahi', 62),
(1021, 'Viqueque', 62),
(1022, 'Azuay', 63),
(1023, 'Bolivar', 63),
(1024, 'Canar', 63),
(1025, 'Carchi', 63),
(1026, 'Chimborazo', 63),
(1027, 'Cotopaxi', 63),
(1028, 'El Oro', 63),
(1029, 'Esmeraldas', 63),
(1030, 'Galapagos', 63),
(1031, 'Guayas', 63),
(1032, 'Imbabura', 63),
(1033, 'Loja', 63),
(1034, 'Los Rios', 63),
(1035, 'Manabi', 63),
(1036, 'Morona Santiago', 63),
(1037, 'Napo', 63),
(1038, 'Orellana', 63),
(1039, 'Pastaza', 63),
(1040, 'Pichincha', 63),
(1041, 'Sucumbios', 63),
(1042, 'Tungurahua', 63),
(1043, 'Zamora Chinchipe', 63),
(1044, 'Aswan', 64),
(1045, 'Asyut', 64),
(1046, 'Bani Suwayf', 64),
(1047, 'Bur Sa\'id', 64),
(1048, 'Cairo', 64),
(1049, 'Dumyat', 64),
(1050, 'Kafr-ash-Shaykh', 64),
(1051, 'Matruh', 64),
(1052, 'Muhafazat ad Daqahliyah', 64),
(1053, 'Muhafazat al Fayyum', 64),
(1054, 'Muhafazat al Gharbiyah', 64),
(1055, 'Muhafazat al Iskandariyah', 64),
(1056, 'Muhafazat al Qahirah', 64),
(1057, 'Qina', 64),
(1058, 'Sawhaj', 64),
(1059, 'Sina al-Janubiyah', 64),
(1060, 'Sina ash-Shamaliyah', 64),
(1061, 'ad-Daqahliyah', 64),
(1062, 'al-Bahr-al-Ahmar', 64),
(1063, 'al-Buhayrah', 64),
(1064, 'al-Fayyum', 64),
(1065, 'al-Gharbiyah', 64),
(1066, 'al-Iskandariyah', 64),
(1067, 'al-Ismailiyah', 64),
(1068, 'al-Jizah', 64),
(1069, 'al-Minufiyah', 64),
(1070, 'al-Minya', 64),
(1071, 'al-Qahira', 64),
(1072, 'al-Qalyubiyah', 64),
(1073, 'al-Uqsur', 64),
(1074, 'al-Wadi al-Jadid', 64),
(1075, 'as-Suways', 64),
(1076, 'ash-Sharqiyah', 64),
(1077, 'Ahuachapan', 65),
(1078, 'Cabanas', 65),
(1079, 'Chalatenango', 65),
(1080, 'Cuscatlan', 65),
(1081, 'La Libertad', 65),
(1082, 'La Paz', 65),
(1083, 'La Union', 65),
(1084, 'Morazan', 65),
(1085, 'San Miguel', 65),
(1086, 'San Salvador', 65),
(1087, 'San Vicente', 65),
(1088, 'Santa Ana', 65),
(1089, 'Sonsonate', 65),
(1090, 'Usulutan', 65),
(1091, 'Annobon', 66),
(1092, 'Bioko Norte', 66),
(1093, 'Bioko Sur', 66),
(1094, 'Centro Sur', 66),
(1095, 'Kie-Ntem', 66),
(1096, 'Litoral', 66),
(1097, 'Wele-Nzas', 66),
(1098, 'Anseba', 67),
(1099, 'Debub', 67),
(1100, 'Debub-Keih-Bahri', 67),
(1101, 'Gash-Barka', 67),
(1102, 'Maekel', 67),
(1103, 'Semien-Keih-Bahri', 67),
(1104, 'Harju', 68),
(1105, 'Hiiu', 68),
(1106, 'Ida-Viru', 68),
(1107, 'Jarva', 68),
(1108, 'Jogeva', 68),
(1109, 'Laane', 68),
(1110, 'Laane-Viru', 68),
(1111, 'Parnu', 68),
(1112, 'Polva', 68),
(1113, 'Rapla', 68),
(1114, 'Saare', 68),
(1115, 'Tartu', 68),
(1116, 'Valga', 68),
(1117, 'Viljandi', 68),
(1118, 'Voru', 68),
(1119, 'Addis Abeba', 69),
(1120, 'Afar', 69),
(1121, 'Amhara', 69),
(1122, 'Benishangul', 69),
(1123, 'Diredawa', 69),
(1124, 'Gambella', 69),
(1125, 'Harar', 69),
(1126, 'Jigjiga', 69),
(1127, 'Mekele', 69),
(1128, 'Oromia', 69),
(1129, 'Somali', 69),
(1130, 'Southern', 69),
(1131, 'Tigray', 69),
(1132, 'Christmas Island', 70),
(1133, 'Cocos Islands', 70),
(1134, 'Coral Sea Islands', 70),
(1135, 'Falkland Islands', 71),
(1136, 'South Georgia', 71),
(1137, 'Klaksvik', 72),
(1138, 'Nor ara Eysturoy', 72),
(1139, 'Nor oy', 72),
(1140, 'Sandoy', 72),
(1141, 'Streymoy', 72),
(1142, 'Su uroy', 72),
(1143, 'Sy ra Eysturoy', 72),
(1144, 'Torshavn', 72),
(1145, 'Vaga', 72),
(1146, 'Central', 73),
(1147, 'Eastern', 73),
(1148, 'Northern', 73),
(1149, 'South Pacific', 73),
(1150, 'Western', 73),
(1151, 'Ahvenanmaa', 74),
(1152, 'Etela-Karjala', 74),
(1153, 'Etela-Pohjanmaa', 74),
(1154, 'Etela-Savo', 74),
(1155, 'Etela-Suomen Laani', 74),
(1156, 'Ita-Suomen Laani', 74),
(1157, 'Ita-Uusimaa', 74),
(1158, 'Kainuu', 74),
(1159, 'Kanta-Hame', 74),
(1160, 'Keski-Pohjanmaa', 74),
(1161, 'Keski-Suomi', 74),
(1162, 'Kymenlaakso', 74),
(1163, 'Lansi-Suomen Laani', 74),
(1164, 'Lappi', 74),
(1165, 'Northern Savonia', 74),
(1166, 'Ostrobothnia', 74),
(1167, 'Oulun Laani', 74),
(1168, 'Paijat-Hame', 74),
(1169, 'Pirkanmaa', 74),
(1170, 'Pohjanmaa', 74),
(1171, 'Pohjois-Karjala', 74),
(1172, 'Pohjois-Pohjanmaa', 74),
(1173, 'Pohjois-Savo', 74),
(1174, 'Saarijarvi', 74),
(1175, 'Satakunta', 74),
(1176, 'Southern Savonia', 74),
(1177, 'Tavastia Proper', 74),
(1178, 'Uleaborgs Lan', 74),
(1179, 'Uusimaa', 74),
(1180, 'Varsinais-Suomi', 74),
(1181, 'Ain', 75),
(1182, 'Aisne', 75),
(1183, 'Albi Le Sequestre', 75),
(1184, 'Allier', 75),
(1185, 'Alpes-Cote dAzur', 75),
(1186, 'Alpes-Maritimes', 75),
(1187, 'Alpes-de-Haute-Provence', 75),
(1188, 'Alsace', 75),
(1189, 'Aquitaine', 75),
(1190, 'Ardeche', 75),
(1191, 'Ardennes', 75),
(1192, 'Ariege', 75),
(1193, 'Aube', 75),
(1194, 'Aude', 75),
(1195, 'Auvergne', 75),
(1196, 'Aveyron', 75),
(1197, 'Bas-Rhin', 75),
(1198, 'Basse-Normandie', 75),
(1199, 'Bouches-du-Rhone', 75),
(1200, 'Bourgogne', 75),
(1201, 'Bretagne', 75),
(1202, 'Brittany', 75),
(1203, 'Burgundy', 75),
(1204, 'Calvados', 75),
(1205, 'Cantal', 75),
(1206, 'Cedex', 75),
(1207, 'Centre', 75),
(1208, 'Charente', 75),
(1209, 'Charente-Maritime', 75),
(1210, 'Cher', 75),
(1211, 'Correze', 75),
(1212, 'Corse-du-Sud', 75),
(1213, 'Cote-d\'Or', 75),
(1214, 'Cotes-d\'Armor', 75),
(1215, 'Creuse', 75),
(1216, 'Crolles', 75),
(1217, 'Deux-Sevres', 75),
(1218, 'Dordogne', 75),
(1219, 'Doubs', 75),
(1220, 'Drome', 75),
(1221, 'Essonne', 75),
(1222, 'Eure', 75),
(1223, 'Eure-et-Loir', 75),
(1224, 'Feucherolles', 75),
(1225, 'Finistere', 75),
(1226, 'Franche-Comte', 75),
(1227, 'Gard', 75),
(1228, 'Gers', 75),
(1229, 'Gironde', 75),
(1230, 'Haut-Rhin', 75),
(1231, 'Haute-Corse', 75),
(1232, 'Haute-Garonne', 75),
(1233, 'Haute-Loire', 75),
(1234, 'Haute-Marne', 75),
(1235, 'Haute-Saone', 75),
(1236, 'Haute-Savoie', 75),
(1237, 'Haute-Vienne', 75),
(1238, 'Hautes-Alpes', 75),
(1239, 'Hautes-Pyrenees', 75),
(1240, 'Hauts-de-Seine', 75),
(1241, 'Herault', 75),
(1242, 'Ile-de-France', 75),
(1243, 'Ille-et-Vilaine', 75),
(1244, 'Indre', 75),
(1245, 'Indre-et-Loire', 75),
(1246, 'Isere', 75),
(1247, 'Jura', 75),
(1248, 'Klagenfurt', 75),
(1249, 'Landes', 75),
(1250, 'Languedoc-Roussillon', 75),
(1251, 'Larcay', 75),
(1252, 'Le Castellet', 75),
(1253, 'Le Creusot', 75),
(1254, 'Limousin', 75),
(1255, 'Loir-et-Cher', 75),
(1256, 'Loire', 75),
(1257, 'Loire-Atlantique', 75),
(1258, 'Loiret', 75),
(1259, 'Lorraine', 75),
(1260, 'Lot', 75),
(1261, 'Lot-et-Garonne', 75),
(1262, 'Lower Normandy', 75),
(1263, 'Lozere', 75),
(1264, 'Maine-et-Loire', 75),
(1265, 'Manche', 75),
(1266, 'Marne', 75),
(1267, 'Mayenne', 75),
(1268, 'Meurthe-et-Moselle', 75),
(1269, 'Meuse', 75),
(1270, 'Midi-Pyrenees', 75),
(1271, 'Morbihan', 75),
(1272, 'Moselle', 75),
(1273, 'Nievre', 75),
(1274, 'Nord', 75),
(1275, 'Nord-Pas-de-Calais', 75),
(1276, 'Oise', 75),
(1277, 'Orne', 75),
(1278, 'Paris', 75),
(1279, 'Pas-de-Calais', 75),
(1280, 'Pays de la Loire', 75),
(1281, 'Pays-de-la-Loire', 75),
(1282, 'Picardy', 75),
(1283, 'Puy-de-Dome', 75),
(1284, 'Pyrenees-Atlantiques', 75),
(1285, 'Pyrenees-Orientales', 75),
(1286, 'Quelmes', 75),
(1287, 'Rhone', 75),
(1288, 'Rhone-Alpes', 75),
(1289, 'Saint Ouen', 75),
(1290, 'Saint Viatre', 75),
(1291, 'Saone-et-Loire', 75),
(1292, 'Sarthe', 75),
(1293, 'Savoie', 75),
(1294, 'Seine-Maritime', 75),
(1295, 'Seine-Saint-Denis', 75),
(1296, 'Seine-et-Marne', 75),
(1297, 'Somme', 75),
(1298, 'Sophia Antipolis', 75),
(1299, 'Souvans', 75),
(1300, 'Tarn', 75),
(1301, 'Tarn-et-Garonne', 75),
(1302, 'Territoire de Belfort', 75),
(1303, 'Treignac', 75),
(1304, 'Upper Normandy', 75),
(1305, 'Val-d\'Oise', 75),
(1306, 'Val-de-Marne', 75),
(1307, 'Var', 75),
(1308, 'Vaucluse', 75),
(1309, 'Vellise', 75),
(1310, 'Vendee', 75),
(1311, 'Vienne', 75),
(1312, 'Vosges', 75),
(1313, 'Yonne', 75),
(1314, 'Yvelines', 75),
(1315, 'Cayenne', 76),
(1316, 'Saint-Laurent-du-Maroni', 76),
(1317, 'Iles du Vent', 77),
(1318, 'Iles sous le Vent', 77),
(1319, 'Marquesas', 77),
(1320, 'Tuamotu', 77),
(1321, 'Tubuai', 77),
(1322, 'Amsterdam', 78),
(1323, 'Crozet Islands', 78),
(1324, 'Kerguelen', 78),
(1325, 'Estuaire', 79),
(1326, 'Haut-Ogooue', 79),
(1327, 'Moyen-Ogooue', 79),
(1328, 'Ngounie', 79),
(1329, 'Nyanga', 79),
(1330, 'Ogooue-Ivindo', 79),
(1331, 'Ogooue-Lolo', 79),
(1332, 'Ogooue-Maritime', 79),
(1333, 'Woleu-Ntem', 79),
(1334, 'Banjul', 80),
(1335, 'Basse', 80),
(1336, 'Brikama', 80),
(1337, 'Janjanbureh', 80),
(1338, 'Kanifing', 80),
(1339, 'Kerewan', 80),
(1340, 'Kuntaur', 80),
(1341, 'Mansakonko', 80),
(1342, 'Abhasia', 81),
(1343, 'Ajaria', 81),
(1344, 'Guria', 81),
(1345, 'Imereti', 81),
(1346, 'Kaheti', 81),
(1347, 'Kvemo Kartli', 81),
(1348, 'Mcheta-Mtianeti', 81),
(1349, 'Racha', 81),
(1350, 'Samagrelo-Zemo Svaneti', 81),
(1351, 'Samche-Zhavaheti', 81),
(1352, 'Shida Kartli', 81),
(1353, 'Tbilisi', 81),
(1354, 'Auvergne', 82),
(1355, 'Baden-Wurttemberg', 82),
(1356, 'Bavaria', 82),
(1357, 'Bayern', 82),
(1358, 'Beilstein Wurtt', 82),
(1359, 'Berlin', 82),
(1360, 'Brandenburg', 82),
(1361, 'Bremen', 82),
(1362, 'Dreisbach', 82),
(1363, 'Freistaat Bayern', 82),
(1364, 'Hamburg', 82),
(1365, 'Hannover', 82),
(1366, 'Heroldstatt', 82),
(1367, 'Hessen', 82),
(1368, 'Kortenberg', 82),
(1369, 'Laasdorf', 82),
(1370, 'Land Baden-Wurttemberg', 82),
(1371, 'Land Bayern', 82),
(1372, 'Land Brandenburg', 82),
(1373, 'Land Hessen', 82),
(1374, 'Land Mecklenburg-Vorpommern', 82),
(1375, 'Land Nordrhein-Westfalen', 82),
(1376, 'Land Rheinland-Pfalz', 82),
(1377, 'Land Sachsen', 82),
(1378, 'Land Sachsen-Anhalt', 82),
(1379, 'Land Thuringen', 82),
(1380, 'Lower Saxony', 82),
(1381, 'Mecklenburg-Vorpommern', 82),
(1382, 'Mulfingen', 82),
(1383, 'Munich', 82),
(1384, 'Neubeuern', 82),
(1385, 'Niedersachsen', 82),
(1386, 'Noord-Holland', 82),
(1387, 'Nordrhein-Westfalen', 82),
(1388, 'North Rhine-Westphalia', 82),
(1389, 'Osterode', 82),
(1390, 'Rheinland-Pfalz', 82),
(1391, 'Rhineland-Palatinate', 82),
(1392, 'Saarland', 82),
(1393, 'Sachsen', 82),
(1394, 'Sachsen-Anhalt', 82),
(1395, 'Saxony', 82),
(1396, 'Schleswig-Holstein', 82),
(1397, 'Thuringia', 82),
(1398, 'Webling', 82),
(1399, 'Weinstrabe', 82),
(1400, 'schlobborn', 82),
(1401, 'Ashanti', 83),
(1402, 'Brong-Ahafo', 83),
(1403, 'Central', 83),
(1404, 'Eastern', 83),
(1405, 'Greater Accra', 83),
(1406, 'Northern', 83),
(1407, 'Upper East', 83),
(1408, 'Upper West', 83),
(1409, 'Volta', 83),
(1410, 'Western', 83),
(1411, 'Gibraltar', 84),
(1412, 'Acharnes', 85),
(1413, 'Ahaia', 85),
(1414, 'Aitolia kai Akarnania', 85),
(1415, 'Argolis', 85),
(1416, 'Arkadia', 85),
(1417, 'Arta', 85),
(1418, 'Attica', 85),
(1419, 'Attiki', 85),
(1420, 'Ayion Oros', 85),
(1421, 'Crete', 85),
(1422, 'Dodekanisos', 85),
(1423, 'Drama', 85),
(1424, 'Evia', 85),
(1425, 'Evritania', 85),
(1426, 'Evros', 85),
(1427, 'Evvoia', 85),
(1428, 'Florina', 85),
(1429, 'Fokis', 85),
(1430, 'Fthiotis', 85),
(1431, 'Grevena', 85),
(1432, 'Halandri', 85),
(1433, 'Halkidiki', 85),
(1434, 'Hania', 85),
(1435, 'Heraklion', 85),
(1436, 'Hios', 85),
(1437, 'Ilia', 85),
(1438, 'Imathia', 85),
(1439, 'Ioannina', 85),
(1440, 'Iraklion', 85),
(1441, 'Karditsa', 85),
(1442, 'Kastoria', 85),
(1443, 'Kavala', 85),
(1444, 'Kefallinia', 85),
(1445, 'Kerkira', 85),
(1446, 'Kiklades', 85),
(1447, 'Kilkis', 85),
(1448, 'Korinthia', 85),
(1449, 'Kozani', 85),
(1450, 'Lakonia', 85),
(1451, 'Larisa', 85),
(1452, 'Lasithi', 85),
(1453, 'Lesvos', 85),
(1454, 'Levkas', 85),
(1455, 'Magnisia', 85),
(1456, 'Messinia', 85),
(1457, 'Nomos Attikis', 85),
(1458, 'Nomos Zakynthou', 85),
(1459, 'Pella', 85),
(1460, 'Pieria', 85),
(1461, 'Piraios', 85),
(1462, 'Preveza', 85),
(1463, 'Rethimni', 85),
(1464, 'Rodopi', 85),
(1465, 'Samos', 85),
(1466, 'Serrai', 85),
(1467, 'Thesprotia', 85),
(1468, 'Thessaloniki', 85),
(1469, 'Trikala', 85),
(1470, 'Voiotia', 85),
(1471, 'West Greece', 85),
(1472, 'Xanthi', 85),
(1473, 'Zakinthos', 85),
(1474, 'Aasiaat', 86),
(1475, 'Ammassalik', 86),
(1476, 'Illoqqortoormiut', 86),
(1477, 'Ilulissat', 86),
(1478, 'Ivittuut', 86),
(1479, 'Kangaatsiaq', 86),
(1480, 'Maniitsoq', 86),
(1481, 'Nanortalik', 86),
(1482, 'Narsaq', 86),
(1483, 'Nuuk', 86),
(1484, 'Paamiut', 86),
(1485, 'Qaanaaq', 86),
(1486, 'Qaqortoq', 86),
(1487, 'Qasigiannguit', 86),
(1488, 'Qeqertarsuaq', 86),
(1489, 'Sisimiut', 86),
(1490, 'Udenfor kommunal inddeling', 86),
(1491, 'Upernavik', 86),
(1492, 'Uummannaq', 86),
(1493, 'Carriacou-Petite Martinique', 87),
(1494, 'Saint Andrew', 87),
(1495, 'Saint Davids', 87),
(1496, 'Saint George\'s', 87),
(1497, 'Saint John', 87),
(1498, 'Saint Mark', 87),
(1499, 'Saint Patrick', 87),
(1500, 'Basse-Terre', 88),
(1501, 'Grande-Terre', 88),
(1502, 'Iles des Saintes', 88),
(1503, 'La Desirade', 88),
(1504, 'Marie-Galante', 88),
(1505, 'Saint Barthelemy', 88),
(1506, 'Saint Martin', 88),
(1507, 'Agana Heights', 89),
(1508, 'Agat', 89),
(1509, 'Barrigada', 89),
(1510, 'Chalan-Pago-Ordot', 89),
(1511, 'Dededo', 89),
(1512, 'Hagatna', 89),
(1513, 'Inarajan', 89),
(1514, 'Mangilao', 89),
(1515, 'Merizo', 89),
(1516, 'Mongmong-Toto-Maite', 89),
(1517, 'Santa Rita', 89),
(1518, 'Sinajana', 89),
(1519, 'Talofofo', 89),
(1520, 'Tamuning', 89),
(1521, 'Yigo', 89),
(1522, 'Yona', 89),
(1523, 'Alta Verapaz', 90),
(1524, 'Baja Verapaz', 90),
(1525, 'Chimaltenango', 90),
(1526, 'Chiquimula', 90),
(1527, 'El Progreso', 90),
(1528, 'Escuintla', 90),
(1529, 'Guatemala', 90),
(1530, 'Huehuetenango', 90),
(1531, 'Izabal', 90),
(1532, 'Jalapa', 90),
(1533, 'Jutiapa', 90),
(1534, 'Peten', 90),
(1535, 'Quezaltenango', 90),
(1536, 'Quiche', 90),
(1537, 'Retalhuleu', 90),
(1538, 'Sacatepequez', 90),
(1539, 'San Marcos', 90),
(1540, 'Santa Rosa', 90),
(1541, 'Solola', 90),
(1542, 'Suchitepequez', 90),
(1543, 'Totonicapan', 90),
(1544, 'Zacapa', 90),
(1545, 'Alderney', 91),
(1546, 'Castel', 91),
(1547, 'Forest', 91),
(1548, 'Saint Andrew', 91),
(1549, 'Saint Martin', 91),
(1550, 'Saint Peter Port', 91),
(1551, 'Saint Pierre du Bois', 91),
(1552, 'Saint Sampson', 91),
(1553, 'Saint Saviour', 91),
(1554, 'Sark', 91),
(1555, 'Torteval', 91),
(1556, 'Vale', 91),
(1557, 'Beyla', 92),
(1558, 'Boffa', 92),
(1559, 'Boke', 92),
(1560, 'Conakry', 92),
(1561, 'Coyah', 92),
(1562, 'Dabola', 92),
(1563, 'Dalaba', 92),
(1564, 'Dinguiraye', 92),
(1565, 'Faranah', 92),
(1566, 'Forecariah', 92),
(1567, 'Fria', 92),
(1568, 'Gaoual', 92),
(1569, 'Gueckedou', 92),
(1570, 'Kankan', 92),
(1571, 'Kerouane', 92),
(1572, 'Kindia', 92),
(1573, 'Kissidougou', 92),
(1574, 'Koubia', 92),
(1575, 'Koundara', 92),
(1576, 'Kouroussa', 92),
(1577, 'Labe', 92),
(1578, 'Lola', 92),
(1579, 'Macenta', 92),
(1580, 'Mali', 92),
(1581, 'Mamou', 92),
(1582, 'Mandiana', 92),
(1583, 'Nzerekore', 92),
(1584, 'Pita', 92),
(1585, 'Siguiri', 92),
(1586, 'Telimele', 92),
(1587, 'Tougue', 92),
(1588, 'Yomou', 92),
(1589, 'Bafata', 93),
(1590, 'Bissau', 93),
(1591, 'Bolama', 93),
(1592, 'Cacheu', 93),
(1593, 'Gabu', 93),
(1594, 'Oio', 93),
(1595, 'Quinara', 93),
(1596, 'Tombali', 93),
(1597, 'Barima-Waini', 94),
(1598, 'Cuyuni-Mazaruni', 94),
(1599, 'Demerara-Mahaica', 94),
(1600, 'East Berbice-Corentyne', 94),
(1601, 'Essequibo Islands-West Demerar', 94),
(1602, 'Mahaica-Berbice', 94),
(1603, 'Pomeroon-Supenaam', 94),
(1604, 'Potaro-Siparuni', 94),
(1605, 'Upper Demerara-Berbice', 94),
(1606, 'Upper Takutu-Upper Essequibo', 94),
(1607, 'Artibonite', 95),
(1608, 'Centre', 95),
(1609, 'Grand\'Anse', 95),
(1610, 'Nord', 95),
(1611, 'Nord-Est', 95),
(1612, 'Nord-Ouest', 95),
(1613, 'Ouest', 95),
(1614, 'Sud', 95),
(1615, 'Sud-Est', 95),
(1616, 'Heard and McDonald Islands', 96),
(1617, 'Atlantida', 97),
(1618, 'Choluteca', 97),
(1619, 'Colon', 97),
(1620, 'Comayagua', 97),
(1621, 'Copan', 97),
(1622, 'Cortes', 97),
(1623, 'Distrito Central', 97),
(1624, 'El Paraiso', 97),
(1625, 'Francisco Morazan', 97),
(1626, 'Gracias a Dios', 97),
(1627, 'Intibuca', 97),
(1628, 'Islas de la Bahia', 97),
(1629, 'La Paz', 97),
(1630, 'Lempira', 97),
(1631, 'Ocotepeque', 97),
(1632, 'Olancho', 97),
(1633, 'Santa Barbara', 97),
(1634, 'Valle', 97),
(1635, 'Yoro', 97),
(1636, 'Hong Kong', 98),
(1637, 'Bacs-Kiskun', 99),
(1638, 'Baranya', 99),
(1639, 'Bekes', 99),
(1640, 'Borsod-Abauj-Zemplen', 99),
(1641, 'Budapest', 99),
(1642, 'Csongrad', 99),
(1643, 'Fejer', 99),
(1644, 'Gyor-Moson-Sopron', 99),
(1645, 'Hajdu-Bihar', 99),
(1646, 'Heves', 99),
(1647, 'Jasz-Nagykun-Szolnok', 99),
(1648, 'Komarom-Esztergom', 99),
(1649, 'Nograd', 99),
(1650, 'Pest', 99),
(1651, 'Somogy', 99),
(1652, 'Szabolcs-Szatmar-Bereg', 99),
(1653, 'Tolna', 99),
(1654, 'Vas', 99),
(1655, 'Veszprem', 99),
(1656, 'Zala', 99),
(1657, 'Austurland', 100),
(1658, 'Gullbringusysla', 100),
(1659, 'Hofu borgarsva i', 100),
(1660, 'Nor urland eystra', 100),
(1661, 'Nor urland vestra', 100),
(1662, 'Su urland', 100),
(1663, 'Su urnes', 100),
(1664, 'Vestfir ir', 100),
(1665, 'Vesturland', 100),
(1666, 'Aceh', 102),
(1667, 'Bali', 102),
(1668, 'Bangka-Belitung', 102),
(1669, 'Banten', 102),
(1670, 'Bengkulu', 102),
(1671, 'Gandaria', 102),
(1672, 'Gorontalo', 102),
(1673, 'Jakarta', 102),
(1674, 'Jambi', 102),
(1675, 'Jawa Barat', 102),
(1676, 'Jawa Tengah', 102),
(1677, 'Jawa Timur', 102),
(1678, 'Kalimantan Barat', 102),
(1679, 'Kalimantan Selatan', 102),
(1680, 'Kalimantan Tengah', 102),
(1681, 'Kalimantan Timur', 102),
(1682, 'Kendal', 102),
(1683, 'Lampung', 102),
(1684, 'Maluku', 102),
(1685, 'Maluku Utara', 102),
(1686, 'Nusa Tenggara Barat', 102),
(1687, 'Nusa Tenggara Timur', 102),
(1688, 'Papua', 102),
(1689, 'Riau', 102),
(1690, 'Riau Kepulauan', 102),
(1691, 'Solo', 102),
(1692, 'Sulawesi Selatan', 102),
(1693, 'Sulawesi Tengah', 102),
(1694, 'Sulawesi Tenggara', 102),
(1695, 'Sulawesi Utara', 102),
(1696, 'Sumatera Barat', 102),
(1697, 'Sumatera Selatan', 102),
(1698, 'Sumatera Utara', 102),
(1699, 'Yogyakarta', 102),
(1700, 'Ardabil', 103),
(1701, 'Azarbayjan-e Bakhtari', 103),
(1702, 'Azarbayjan-e Khavari', 103),
(1703, 'Bushehr', 103),
(1704, 'Chahar Mahal-e Bakhtiari', 103),
(1705, 'Esfahan', 103),
(1706, 'Fars', 103),
(1707, 'Gilan', 103),
(1708, 'Golestan', 103),
(1709, 'Hamadan', 103),
(1710, 'Hormozgan', 103),
(1711, 'Ilam', 103),
(1712, 'Kerman', 103),
(1713, 'Kermanshah', 103),
(1714, 'Khorasan', 103),
(1715, 'Khuzestan', 103),
(1716, 'Kohgiluyeh-e Boyerahmad', 103),
(1717, 'Kordestan', 103),
(1718, 'Lorestan', 103),
(1719, 'Markazi', 103),
(1720, 'Mazandaran', 103),
(1721, 'Ostan-e Esfahan', 103),
(1722, 'Qazvin', 103),
(1723, 'Qom', 103),
(1724, 'Semnan', 103),
(1725, 'Sistan-e Baluchestan', 103),
(1726, 'Tehran', 103),
(1727, 'Yazd', 103),
(1728, 'Zanjan', 103),
(1729, 'Babil', 104),
(1730, 'Baghdad', 104),
(1731, 'Dahuk', 104),
(1732, 'Dhi Qar', 104),
(1733, 'Diyala', 104),
(1734, 'Erbil', 104),
(1735, 'Irbil', 104),
(1736, 'Karbala', 104),
(1737, 'Kurdistan', 104),
(1738, 'Maysan', 104),
(1739, 'Ninawa', 104),
(1740, 'Salah-ad-Din', 104),
(1741, 'Wasit', 104),
(1742, 'al-Anbar', 104),
(1743, 'al-Basrah', 104),
(1744, 'al-Muthanna', 104),
(1745, 'al-Qadisiyah', 104),
(1746, 'an-Najaf', 104),
(1747, 'as-Sulaymaniyah', 104),
(1748, 'at-Ta\'mim', 104),
(1749, 'Armagh', 105),
(1750, 'Carlow', 105),
(1751, 'Cavan', 105),
(1752, 'Clare', 105),
(1753, 'Cork', 105),
(1754, 'Donegal', 105),
(1755, 'Dublin', 105),
(1756, 'Galway', 105),
(1757, 'Kerry', 105),
(1758, 'Kildare', 105),
(1759, 'Kilkenny', 105),
(1760, 'Laois', 105),
(1761, 'Leinster', 105),
(1762, 'Leitrim', 105),
(1763, 'Limerick', 105),
(1764, 'Loch Garman', 105),
(1765, 'Longford', 105),
(1766, 'Louth', 105),
(1767, 'Mayo', 105),
(1768, 'Meath', 105),
(1769, 'Monaghan', 105),
(1770, 'Offaly', 105),
(1771, 'Roscommon', 105),
(1772, 'Sligo', 105),
(1773, 'Tipperary North Riding', 105),
(1774, 'Tipperary South Riding', 105),
(1775, 'Ulster', 105),
(1776, 'Waterford', 105),
(1777, 'Westmeath', 105),
(1778, 'Wexford', 105),
(1779, 'Wicklow', 105),
(1780, 'Beit Hanania', 106),
(1781, 'Ben Gurion Airport', 106),
(1782, 'Bethlehem', 106),
(1783, 'Caesarea', 106),
(1784, 'Centre', 106),
(1785, 'Gaza', 106),
(1786, 'Hadaron', 106),
(1787, 'Haifa District', 106),
(1788, 'Hamerkaz', 106),
(1789, 'Hazafon', 106),
(1790, 'Hebron', 106),
(1791, 'Jaffa', 106),
(1792, 'Jerusalem', 106),
(1793, 'Khefa', 106),
(1794, 'Kiryat Yam', 106),
(1795, 'Lower Galilee', 106),
(1796, 'Qalqilya', 106),
(1797, 'Talme Elazar', 106),
(1798, 'Tel Aviv', 106),
(1799, 'Tsafon', 106),
(1800, 'Umm El Fahem', 106),
(1801, 'Yerushalayim', 106),
(1802, 'Abruzzi', 107),
(1803, 'Abruzzo', 107),
(1804, 'Agrigento', 107),
(1805, 'Alessandria', 107),
(1806, 'Ancona', 107),
(1807, 'Arezzo', 107),
(1808, 'Ascoli Piceno', 107),
(1809, 'Asti', 107),
(1810, 'Avellino', 107),
(1811, 'Bari', 107),
(1812, 'Basilicata', 107),
(1813, 'Belluno', 107),
(1814, 'Benevento', 107),
(1815, 'Bergamo', 107),
(1816, 'Biella', 107),
(1817, 'Bologna', 107),
(1818, 'Bolzano', 107),
(1819, 'Brescia', 107),
(1820, 'Brindisi', 107),
(1821, 'Calabria', 107),
(1822, 'Campania', 107),
(1823, 'Cartoceto', 107),
(1824, 'Caserta', 107),
(1825, 'Catania', 107),
(1826, 'Chieti', 107),
(1827, 'Como', 107),
(1828, 'Cosenza', 107),
(1829, 'Cremona', 107),
(1830, 'Cuneo', 107),
(1831, 'Emilia-Romagna', 107),
(1832, 'Ferrara', 107),
(1833, 'Firenze', 107),
(1834, 'Florence', 107),
(1835, 'Forli-Cesena ', 107),
(1836, 'Friuli-Venezia Giulia', 107),
(1837, 'Frosinone', 107),
(1838, 'Genoa', 107),
(1839, 'Gorizia', 107),
(1840, 'L\'Aquila', 107),
(1841, 'Lazio', 107),
(1842, 'Lecce', 107),
(1843, 'Lecco', 107),
(1844, 'Lecco Province', 107),
(1845, 'Liguria', 107),
(1846, 'Lodi', 107),
(1847, 'Lombardia', 107),
(1848, 'Lombardy', 107),
(1849, 'Macerata', 107),
(1850, 'Mantova', 107),
(1851, 'Marche', 107),
(1852, 'Messina', 107),
(1853, 'Milan', 107),
(1854, 'Modena', 107),
(1855, 'Molise', 107),
(1856, 'Molteno', 107),
(1857, 'Montenegro', 107),
(1858, 'Monza and Brianza', 107),
(1859, 'Naples', 107),
(1860, 'Novara', 107),
(1861, 'Padova', 107),
(1862, 'Parma', 107),
(1863, 'Pavia', 107),
(1864, 'Perugia', 107),
(1865, 'Pesaro-Urbino', 107),
(1866, 'Piacenza', 107),
(1867, 'Piedmont', 107),
(1868, 'Piemonte', 107),
(1869, 'Pisa', 107),
(1870, 'Pordenone', 107),
(1871, 'Potenza', 107),
(1872, 'Puglia', 107),
(1873, 'Reggio Emilia', 107),
(1874, 'Rimini', 107),
(1875, 'Roma', 107),
(1876, 'Salerno', 107),
(1877, 'Sardegna', 107),
(1878, 'Sassari', 107),
(1879, 'Savona', 107),
(1880, 'Sicilia', 107),
(1881, 'Siena', 107),
(1882, 'Sondrio', 107),
(1883, 'South Tyrol', 107),
(1884, 'Taranto', 107),
(1885, 'Teramo', 107),
(1886, 'Torino', 107),
(1887, 'Toscana', 107),
(1888, 'Trapani', 107),
(1889, 'Trentino-Alto Adige', 107),
(1890, 'Trento', 107),
(1891, 'Treviso', 107),
(1892, 'Udine', 107),
(1893, 'Umbria', 107),
(1894, 'Valle d\'Aosta', 107),
(1895, 'Varese', 107),
(1896, 'Veneto', 107),
(1897, 'Venezia', 107),
(1898, 'Verbano-Cusio-Ossola', 107),
(1899, 'Vercelli', 107),
(1900, 'Verona', 107),
(1901, 'Vicenza', 107),
(1902, 'Viterbo', 107),
(1903, 'Buxoro Viloyati', 108),
(1904, 'Clarendon', 108),
(1905, 'Hanover', 108),
(1906, 'Kingston', 108),
(1907, 'Manchester', 108),
(1908, 'Portland', 108),
(1909, 'Saint Andrews', 108),
(1910, 'Saint Ann', 108),
(1911, 'Saint Catherine', 108),
(1912, 'Saint Elizabeth', 108),
(1913, 'Saint James', 108),
(1914, 'Saint Mary', 108),
(1915, 'Saint Thomas', 108),
(1916, 'Trelawney', 108),
(1917, 'Westmoreland', 108),
(1918, 'Aichi', 109),
(1919, 'Akita', 109),
(1920, 'Aomori', 109),
(1921, 'Chiba', 109),
(1922, 'Ehime', 109),
(1923, 'Fukui', 109),
(1924, 'Fukuoka', 109),
(1925, 'Fukushima', 109),
(1926, 'Gifu', 109),
(1927, 'Gumma', 109),
(1928, 'Hiroshima', 109),
(1929, 'Hokkaido', 109),
(1930, 'Hyogo', 109),
(1931, 'Ibaraki', 109),
(1932, 'Ishikawa', 109),
(1933, 'Iwate', 109),
(1934, 'Kagawa', 109),
(1935, 'Kagoshima', 109),
(1936, 'Kanagawa', 109),
(1937, 'Kanto', 109),
(1938, 'Kochi', 109),
(1939, 'Kumamoto', 109),
(1940, 'Kyoto', 109),
(1941, 'Mie', 109),
(1942, 'Miyagi', 109),
(1943, 'Miyazaki', 109),
(1944, 'Nagano', 109),
(1945, 'Nagasaki', 109),
(1946, 'Nara', 109),
(1947, 'Niigata', 109),
(1948, 'Oita', 109),
(1949, 'Okayama', 109),
(1950, 'Okinawa', 109),
(1951, 'Osaka', 109),
(1952, 'Saga', 109),
(1953, 'Saitama', 109),
(1954, 'Shiga', 109),
(1955, 'Shimane', 109),
(1956, 'Shizuoka', 109),
(1957, 'Tochigi', 109),
(1958, 'Tokushima', 109),
(1959, 'Tokyo', 109),
(1960, 'Tottori', 109),
(1961, 'Toyama', 109),
(1962, 'Wakayama', 109),
(1963, 'Yamagata', 109),
(1964, 'Yamaguchi', 109),
(1965, 'Yamanashi', 109),
(1966, 'Grouville', 110),
(1967, 'Saint Brelade', 110),
(1968, 'Saint Clement', 110),
(1969, 'Saint Helier', 110),
(1970, 'Saint John', 110),
(1971, 'Saint Lawrence', 110),
(1972, 'Saint Martin', 110),
(1973, 'Saint Mary', 110),
(1974, 'Saint Peter', 110),
(1975, 'Saint Saviour', 110),
(1976, 'Trinity', 110),
(1977, '\'Ajlun', 111),
(1978, 'Amman', 111),
(1979, 'Irbid', 111),
(1980, 'Jarash', 111),
(1981, 'Ma\'an', 111),
(1982, 'Madaba', 111),
(1983, 'al-\'Aqabah', 111),
(1984, 'al-Balqa\'', 111),
(1985, 'al-Karak', 111),
(1986, 'al-Mafraq', 111),
(1987, 'at-Tafilah', 111),
(1988, 'az-Zarqa\'', 111),
(1989, 'Akmecet', 112),
(1990, 'Akmola', 112),
(1991, 'Aktobe', 112),
(1992, 'Almati', 112),
(1993, 'Atirau', 112),
(1994, 'Batis Kazakstan', 112),
(1995, 'Burlinsky Region', 112),
(1996, 'Karagandi', 112),
(1997, 'Kostanay', 112),
(1998, 'Mankistau', 112),
(1999, 'Ontustik Kazakstan', 112),
(2000, 'Pavlodar', 112),
(2001, 'Sigis Kazakstan', 112),
(2002, 'Soltustik Kazakstan', 112),
(2003, 'Taraz', 112),
(2004, 'Central', 113),
(2005, 'Coast', 113),
(2006, 'Eastern', 113),
(2007, 'Nairobi', 113),
(2008, 'North Eastern', 113),
(2009, 'Nyanza', 113),
(2010, 'Rift Valley', 113),
(2011, 'Western', 113),
(2012, 'Abaiang', 114),
(2013, 'Abemana', 114),
(2014, 'Aranuka', 114),
(2015, 'Arorae', 114),
(2016, 'Banaba', 114),
(2017, 'Beru', 114),
(2018, 'Butaritari', 114),
(2019, 'Kiritimati', 114),
(2020, 'Kuria', 114),
(2021, 'Maiana', 114),
(2022, 'Makin', 114),
(2023, 'Marakei', 114),
(2024, 'Nikunau', 114),
(2025, 'Nonouti', 114),
(2026, 'Onotoa', 114),
(2027, 'Phoenix Islands', 114),
(2028, 'Tabiteuea North', 114),
(2029, 'Tabiteuea South', 114),
(2030, 'Tabuaeran', 114),
(2031, 'Tamana', 114),
(2032, 'Tarawa North', 114),
(2033, 'Tarawa South', 114),
(2034, 'Teraina', 114),
(2035, 'Chagangdo', 115),
(2036, 'Hamgyeongbukto', 115),
(2037, 'Hamgyeongnamdo', 115),
(2038, 'Hwanghaebukto', 115),
(2039, 'Hwanghaenamdo', 115),
(2040, 'Kaeseong', 115),
(2041, 'Kangweon', 115),
(2042, 'Nampo', 115),
(2043, 'Pyeonganbukto', 115),
(2044, 'Pyeongannamdo', 115),
(2045, 'Pyeongyang', 115),
(2046, 'Yanggang', 115),
(2047, 'Busan', 116),
(2048, 'Cheju', 116),
(2049, 'Chollabuk', 116),
(2050, 'Chollanam', 116),
(2051, 'Chungbuk', 116),
(2052, 'Chungcheongbuk', 116),
(2053, 'Chungcheongnam', 116),
(2054, 'Chungnam', 116),
(2055, 'Daegu', 116),
(2056, 'Gangwon-do', 116),
(2057, 'Goyang-si', 116),
(2058, 'Gyeonggi-do', 116),
(2059, 'Gyeongsang ', 116),
(2060, 'Gyeongsangnam-do', 116),
(2061, 'Incheon', 116),
(2062, 'Jeju-Si', 116),
(2063, 'Jeonbuk', 116),
(2064, 'Kangweon', 116),
(2065, 'Kwangju', 116),
(2066, 'Kyeonggi', 116),
(2067, 'Kyeongsangbuk', 116),
(2068, 'Kyeongsangnam', 116),
(2069, 'Kyonggi-do', 116),
(2070, 'Kyungbuk-Do', 116),
(2071, 'Kyunggi-Do', 116),
(2072, 'Kyunggi-do', 116),
(2073, 'Pusan', 116),
(2074, 'Seoul', 116),
(2075, 'Sudogwon', 116),
(2076, 'Taegu', 116),
(2077, 'Taejeon', 116),
(2078, 'Taejon-gwangyoksi', 116),
(2079, 'Ulsan', 116),
(2080, 'Wonju', 116),
(2081, 'gwangyoksi', 116),
(2082, 'Al Asimah', 117),
(2083, 'Hawalli', 117),
(2084, 'Mishref', 117),
(2085, 'Qadesiya', 117),
(2086, 'Safat', 117),
(2087, 'Salmiya', 117),
(2088, 'al-Ahmadi', 117),
(2089, 'al-Farwaniyah', 117),
(2090, 'al-Jahra', 117),
(2091, 'al-Kuwayt', 117),
(2092, 'Batken', 118),
(2093, 'Bishkek', 118),
(2094, 'Chui', 118),
(2095, 'Issyk-Kul', 118),
(2096, 'Jalal-Abad', 118),
(2097, 'Naryn', 118),
(2098, 'Osh', 118),
(2099, 'Talas', 118),
(2100, 'Attopu', 119),
(2101, 'Bokeo', 119),
(2102, 'Bolikhamsay', 119),
(2103, 'Champasak', 119),
(2104, 'Houaphanh', 119),
(2105, 'Khammouane', 119),
(2106, 'Luang Nam Tha', 119),
(2107, 'Luang Prabang', 119),
(2108, 'Oudomxay', 119),
(2109, 'Phongsaly', 119),
(2110, 'Saravan', 119),
(2111, 'Savannakhet', 119),
(2112, 'Sekong', 119),
(2113, 'Viangchan Prefecture', 119),
(2114, 'Viangchan Province', 119),
(2115, 'Xaignabury', 119),
(2116, 'Xiang Khuang', 119),
(2117, 'Aizkraukles', 120),
(2118, 'Aluksnes', 120),
(2119, 'Balvu', 120),
(2120, 'Bauskas', 120),
(2121, 'Cesu', 120),
(2122, 'Daugavpils', 120),
(2123, 'Daugavpils City', 120),
(2124, 'Dobeles', 120),
(2125, 'Gulbenes', 120),
(2126, 'Jekabspils', 120),
(2127, 'Jelgava', 120),
(2128, 'Jelgavas', 120),
(2129, 'Jurmala City', 120),
(2130, 'Kraslavas', 120),
(2131, 'Kuldigas', 120),
(2132, 'Liepaja', 120),
(2133, 'Liepajas', 120),
(2134, 'Limbazhu', 120),
(2135, 'Ludzas', 120),
(2136, 'Madonas', 120),
(2137, 'Ogres', 120),
(2138, 'Preilu', 120),
(2139, 'Rezekne', 120),
(2140, 'Rezeknes', 120),
(2141, 'Riga', 120),
(2142, 'Rigas', 120),
(2143, 'Saldus', 120),
(2144, 'Talsu', 120),
(2145, 'Tukuma', 120),
(2146, 'Valkas', 120),
(2147, 'Valmieras', 120),
(2148, 'Ventspils', 120),
(2149, 'Ventspils City', 120),
(2150, 'Beirut', 121),
(2151, 'Jabal Lubnan', 121),
(2152, 'Mohafazat Liban-Nord', 121),
(2153, 'Mohafazat Mont-Liban', 121),
(2154, 'Sidon', 121),
(2155, 'al-Biqa', 121),
(2156, 'al-Janub', 121),
(2157, 'an-Nabatiyah', 121),
(2158, 'ash-Shamal', 121),
(2159, 'Berea', 122),
(2160, 'Butha-Buthe', 122),
(2161, 'Leribe', 122),
(2162, 'Mafeteng', 122),
(2163, 'Maseru', 122),
(2164, 'Mohale\'s Hoek', 122),
(2165, 'Mokhotlong', 122),
(2166, 'Qacha\'s Nek', 122),
(2167, 'Quthing', 122),
(2168, 'Thaba-Tseka', 122),
(2169, 'Bomi', 123),
(2170, 'Bong', 123),
(2171, 'Grand Bassa', 123),
(2172, 'Grand Cape Mount', 123),
(2173, 'Grand Gedeh', 123),
(2174, 'Loffa', 123),
(2175, 'Margibi', 123),
(2176, 'Maryland and Grand Kru', 123),
(2177, 'Montserrado', 123),
(2178, 'Nimba', 123),
(2179, 'Rivercess', 123),
(2180, 'Sinoe', 123),
(2181, 'Ajdabiya', 124);
INSERT INTO `states` (`id`, `name`, `country_id`) VALUES
(2182, 'Fezzan', 124),
(2183, 'Banghazi', 124),
(2184, 'Darnah', 124),
(2185, 'Ghadamis', 124),
(2186, 'Gharyan', 124),
(2187, 'Misratah', 124),
(2188, 'Murzuq', 124),
(2189, 'Sabha', 124),
(2190, 'Sawfajjin', 124),
(2191, 'Surt', 124),
(2192, 'Tarabulus', 124),
(2193, 'Tarhunah', 124),
(2194, 'Tripolitania', 124),
(2195, 'Tubruq', 124),
(2196, 'Yafran', 124),
(2197, 'Zlitan', 124),
(2198, 'al-\'Aziziyah', 124),
(2199, 'al-Fatih', 124),
(2200, 'al-Jabal al Akhdar', 124),
(2201, 'al-Jufrah', 124),
(2202, 'al-Khums', 124),
(2203, 'al-Kufrah', 124),
(2204, 'an-Nuqat al-Khams', 124),
(2205, 'ash-Shati\'', 124),
(2206, 'az-Zawiyah', 124),
(2207, 'Balzers', 125),
(2208, 'Eschen', 125),
(2209, 'Gamprin', 125),
(2210, 'Mauren', 125),
(2211, 'Planken', 125),
(2212, 'Ruggell', 125),
(2213, 'Schaan', 125),
(2214, 'Schellenberg', 125),
(2215, 'Triesen', 125),
(2216, 'Triesenberg', 125),
(2217, 'Vaduz', 125),
(2218, 'Alytaus', 126),
(2219, 'Anyksciai', 126),
(2220, 'Kauno', 126),
(2221, 'Klaipedos', 126),
(2222, 'Marijampoles', 126),
(2223, 'Panevezhio', 126),
(2224, 'Panevezys', 126),
(2225, 'Shiauliu', 126),
(2226, 'Taurages', 126),
(2227, 'Telshiu', 126),
(2228, 'Telsiai', 126),
(2229, 'Utenos', 126),
(2230, 'Vilniaus', 126),
(2231, 'Capellen', 127),
(2232, 'Clervaux', 127),
(2233, 'Diekirch', 127),
(2234, 'Echternach', 127),
(2235, 'Esch-sur-Alzette', 127),
(2236, 'Grevenmacher', 127),
(2237, 'Luxembourg', 127),
(2238, 'Mersch', 127),
(2239, 'Redange', 127),
(2240, 'Remich', 127),
(2241, 'Vianden', 127),
(2242, 'Wiltz', 127),
(2243, 'Macau', 128),
(2244, 'Berovo', 129),
(2245, 'Bitola', 129),
(2246, 'Brod', 129),
(2247, 'Debar', 129),
(2248, 'Delchevo', 129),
(2249, 'Demir Hisar', 129),
(2250, 'Gevgelija', 129),
(2251, 'Gostivar', 129),
(2252, 'Kavadarci', 129),
(2253, 'Kichevo', 129),
(2254, 'Kochani', 129),
(2255, 'Kratovo', 129),
(2256, 'Kriva Palanka', 129),
(2257, 'Krushevo', 129),
(2258, 'Kumanovo', 129),
(2259, 'Negotino', 129),
(2260, 'Ohrid', 129),
(2261, 'Prilep', 129),
(2262, 'Probishtip', 129),
(2263, 'Radovish', 129),
(2264, 'Resen', 129),
(2265, 'Shtip', 129),
(2266, 'Skopje', 129),
(2267, 'Struga', 129),
(2268, 'Strumica', 129),
(2269, 'Sveti Nikole', 129),
(2270, 'Tetovo', 129),
(2271, 'Valandovo', 129),
(2272, 'Veles', 129),
(2273, 'Vinica', 129),
(2274, 'Antananarivo', 130),
(2275, 'Antsiranana', 130),
(2276, 'Fianarantsoa', 130),
(2277, 'Mahajanga', 130),
(2278, 'Toamasina', 130),
(2279, 'Toliary', 130),
(2280, 'Balaka', 131),
(2281, 'Blantyre City', 131),
(2282, 'Chikwawa', 131),
(2283, 'Chiradzulu', 131),
(2284, 'Chitipa', 131),
(2285, 'Dedza', 131),
(2286, 'Dowa', 131),
(2287, 'Karonga', 131),
(2288, 'Kasungu', 131),
(2289, 'Lilongwe City', 131),
(2290, 'Machinga', 131),
(2291, 'Mangochi', 131),
(2292, 'Mchinji', 131),
(2293, 'Mulanje', 131),
(2294, 'Mwanza', 131),
(2295, 'Mzimba', 131),
(2296, 'Mzuzu City', 131),
(2297, 'Nkhata Bay', 131),
(2298, 'Nkhotakota', 131),
(2299, 'Nsanje', 131),
(2300, 'Ntcheu', 131),
(2301, 'Ntchisi', 131),
(2302, 'Phalombe', 131),
(2303, 'Rumphi', 131),
(2304, 'Salima', 131),
(2305, 'Thyolo', 131),
(2306, 'Zomba Municipality', 131),
(2307, 'Johor', 132),
(2308, 'Kedah', 132),
(2309, 'Kelantan', 132),
(2310, 'Kuala Lumpur', 132),
(2311, 'Labuan', 132),
(2312, 'Melaka', 132),
(2313, 'Negeri Johor', 132),
(2314, 'Negeri Sembilan', 132),
(2315, 'Pahang', 132),
(2316, 'Penang', 132),
(2317, 'Perak', 132),
(2318, 'Perlis', 132),
(2319, 'Pulau Pinang', 132),
(2320, 'Sabah', 132),
(2321, 'Sarawak', 132),
(2322, 'Selangor', 132),
(2323, 'Sembilan', 132),
(2324, 'Terengganu', 132),
(2325, 'Alif Alif', 133),
(2326, 'Alif Dhaal', 133),
(2327, 'Baa', 133),
(2328, 'Dhaal', 133),
(2329, 'Faaf', 133),
(2330, 'Gaaf Alif', 133),
(2331, 'Gaaf Dhaal', 133),
(2332, 'Ghaviyani', 133),
(2333, 'Haa Alif', 133),
(2334, 'Haa Dhaal', 133),
(2335, 'Kaaf', 133),
(2336, 'Laam', 133),
(2337, 'Lhaviyani', 133),
(2338, 'Male', 133),
(2339, 'Miim', 133),
(2340, 'Nuun', 133),
(2341, 'Raa', 133),
(2342, 'Shaviyani', 133),
(2343, 'Siin', 133),
(2344, 'Thaa', 133),
(2345, 'Vaav', 133),
(2346, 'Bamako', 134),
(2347, 'Gao', 134),
(2348, 'Kayes', 134),
(2349, 'Kidal', 134),
(2350, 'Koulikoro', 134),
(2351, 'Mopti', 134),
(2352, 'Segou', 134),
(2353, 'Sikasso', 134),
(2354, 'Tombouctou', 134),
(2355, 'Gozo and Comino', 135),
(2356, 'Inner Harbour', 135),
(2357, 'Northern', 135),
(2358, 'Outer Harbour', 135),
(2359, 'South Eastern', 135),
(2360, 'Valletta', 135),
(2361, 'Western', 135),
(2362, 'Castletown', 136),
(2363, 'Douglas', 136),
(2364, 'Laxey', 136),
(2365, 'Onchan', 136),
(2366, 'Peel', 136),
(2367, 'Port Erin', 136),
(2368, 'Port Saint Mary', 136),
(2369, 'Ramsey', 136),
(2370, 'Ailinlaplap', 137),
(2371, 'Ailuk', 137),
(2372, 'Arno', 137),
(2373, 'Aur', 137),
(2374, 'Bikini', 137),
(2375, 'Ebon', 137),
(2376, 'Enewetak', 137),
(2377, 'Jabat', 137),
(2378, 'Jaluit', 137),
(2379, 'Kili', 137),
(2380, 'Kwajalein', 137),
(2381, 'Lae', 137),
(2382, 'Lib', 137),
(2383, 'Likiep', 137),
(2384, 'Majuro', 137),
(2385, 'Maloelap', 137),
(2386, 'Mejit', 137),
(2387, 'Mili', 137),
(2388, 'Namorik', 137),
(2389, 'Namu', 137),
(2390, 'Rongelap', 137),
(2391, 'Ujae', 137),
(2392, 'Utrik', 137),
(2393, 'Wotho', 137),
(2394, 'Wotje', 137),
(2395, 'Fort-de-France', 138),
(2396, 'La Trinite', 138),
(2397, 'Le Marin', 138),
(2398, 'Saint-Pierre', 138),
(2399, 'Adrar', 139),
(2400, 'Assaba', 139),
(2401, 'Brakna', 139),
(2402, 'Dhakhlat Nawadibu', 139),
(2403, 'Hudh-al-Gharbi', 139),
(2404, 'Hudh-ash-Sharqi', 139),
(2405, 'Inshiri', 139),
(2406, 'Nawakshut', 139),
(2407, 'Qidimagha', 139),
(2408, 'Qurqul', 139),
(2409, 'Taqant', 139),
(2410, 'Tiris Zammur', 139),
(2411, 'Trarza', 139),
(2412, 'Black River', 140),
(2413, 'Eau Coulee', 140),
(2414, 'Flacq', 140),
(2415, 'Floreal', 140),
(2416, 'Grand Port', 140),
(2417, 'Moka', 140),
(2418, 'Pamplempousses', 140),
(2419, 'Plaines Wilhelm', 140),
(2420, 'Port Louis', 140),
(2421, 'Riviere du Rempart', 140),
(2422, 'Rodrigues', 140),
(2423, 'Rose Hill', 140),
(2424, 'Savanne', 140),
(2425, 'Mayotte', 141),
(2426, 'Pamanzi', 141),
(2427, 'Aguascalientes', 142),
(2428, 'Baja California', 142),
(2429, 'Baja California Sur', 142),
(2430, 'Campeche', 142),
(2431, 'Chiapas', 142),
(2432, 'Chihuahua', 142),
(2433, 'Coahuila', 142),
(2434, 'Colima', 142),
(2435, 'Distrito Federal', 142),
(2436, 'Durango', 142),
(2437, 'Estado de Mexico', 142),
(2438, 'Guanajuato', 142),
(2439, 'Guerrero', 142),
(2440, 'Hidalgo', 142),
(2441, 'Jalisco', 142),
(2442, 'Mexico', 142),
(2443, 'Michoacan', 142),
(2444, 'Morelos', 142),
(2445, 'Nayarit', 142),
(2446, 'Nuevo Leon', 142),
(2447, 'Oaxaca', 142),
(2448, 'Puebla', 142),
(2449, 'Queretaro', 142),
(2450, 'Quintana Roo', 142),
(2451, 'San Luis Potosi', 142),
(2452, 'Sinaloa', 142),
(2453, 'Sonora', 142),
(2454, 'Tabasco', 142),
(2455, 'Tamaulipas', 142),
(2456, 'Tlaxcala', 142),
(2457, 'Veracruz', 142),
(2458, 'Yucatan', 142),
(2459, 'Zacatecas', 142),
(2460, 'Chuuk', 143),
(2461, 'Kusaie', 143),
(2462, 'Pohnpei', 143),
(2463, 'Yap', 143),
(2464, 'Balti', 144),
(2465, 'Cahul', 144),
(2466, 'Chisinau', 144),
(2467, 'Chisinau Oras', 144),
(2468, 'Edinet', 144),
(2469, 'Gagauzia', 144),
(2470, 'Lapusna', 144),
(2471, 'Orhei', 144),
(2472, 'Soroca', 144),
(2473, 'Taraclia', 144),
(2474, 'Tighina', 144),
(2475, 'Transnistria', 144),
(2476, 'Ungheni', 144),
(2477, 'Fontvieille', 145),
(2478, 'La Condamine', 145),
(2479, 'Monaco-Ville', 145),
(2480, 'Monte Carlo', 145),
(2481, 'Arhangaj', 146),
(2482, 'Bajan-Olgij', 146),
(2483, 'Bajanhongor', 146),
(2484, 'Bulgan', 146),
(2485, 'Darhan-Uul', 146),
(2486, 'Dornod', 146),
(2487, 'Dornogovi', 146),
(2488, 'Dundgovi', 146),
(2489, 'Govi-Altaj', 146),
(2490, 'Govisumber', 146),
(2491, 'Hentij', 146),
(2492, 'Hovd', 146),
(2493, 'Hovsgol', 146),
(2494, 'Omnogovi', 146),
(2495, 'Orhon', 146),
(2496, 'Ovorhangaj', 146),
(2497, 'Selenge', 146),
(2498, 'Suhbaatar', 146),
(2499, 'Tov', 146),
(2500, 'Ulaanbaatar', 146),
(2501, 'Uvs', 146),
(2502, 'Zavhan', 146),
(2503, 'Montserrat', 147),
(2504, 'Agadir', 148),
(2505, 'Casablanca', 148),
(2506, 'Chaouia-Ouardigha', 148),
(2507, 'Doukkala-Abda', 148),
(2508, 'Fes-Boulemane', 148),
(2509, 'Gharb-Chrarda-Beni Hssen', 148),
(2510, 'Guelmim', 148),
(2511, 'Kenitra', 148),
(2512, 'Marrakech-Tensift-Al Haouz', 148),
(2513, 'Meknes-Tafilalet', 148),
(2514, 'Oriental', 148),
(2515, 'Oujda', 148),
(2516, 'Province de Tanger', 148),
(2517, 'Rabat-Sale-Zammour-Zaer', 148),
(2518, 'Sala Al Jadida', 148),
(2519, 'Settat', 148),
(2520, 'Souss Massa-Draa', 148),
(2521, 'Tadla-Azilal', 148),
(2522, 'Tangier-Tetouan', 148),
(2523, 'Taza-Al Hoceima-Taounate', 148),
(2524, 'Wilaya de Casablanca', 148),
(2525, 'Wilaya de Rabat-Sale', 148),
(2526, 'Cabo Delgado', 149),
(2527, 'Gaza', 149),
(2528, 'Inhambane', 149),
(2529, 'Manica', 149),
(2530, 'Maputo', 149),
(2531, 'Maputo Provincia', 149),
(2532, 'Nampula', 149),
(2533, 'Niassa', 149),
(2534, 'Sofala', 149),
(2535, 'Tete', 149),
(2536, 'Zambezia', 149),
(2537, 'Ayeyarwady', 150),
(2538, 'Bago', 150),
(2539, 'Chin', 150),
(2540, 'Kachin', 150),
(2541, 'Kayah', 150),
(2542, 'Kayin', 150),
(2543, 'Magway', 150),
(2544, 'Mandalay', 150),
(2545, 'Mon', 150),
(2546, 'Nay Pyi Taw', 150),
(2547, 'Rakhine', 150),
(2548, 'Sagaing', 150),
(2549, 'Shan', 150),
(2550, 'Tanintharyi', 150),
(2551, 'Yangon', 150),
(2552, 'Caprivi', 151),
(2553, 'Erongo', 151),
(2554, 'Hardap', 151),
(2555, 'Karas', 151),
(2556, 'Kavango', 151),
(2557, 'Khomas', 151),
(2558, 'Kunene', 151),
(2559, 'Ohangwena', 151),
(2560, 'Omaheke', 151),
(2561, 'Omusati', 151),
(2562, 'Oshana', 151),
(2563, 'Oshikoto', 151),
(2564, 'Otjozondjupa', 151),
(2565, 'Yaren', 152),
(2566, 'Bagmati', 153),
(2567, 'Bheri', 153),
(2568, 'Dhawalagiri', 153),
(2569, 'Gandaki', 153),
(2570, 'Janakpur', 153),
(2571, 'Karnali', 153),
(2572, 'Koshi', 153),
(2573, 'Lumbini', 153),
(2574, 'Mahakali', 153),
(2575, 'Mechi', 153),
(2576, 'Narayani', 153),
(2577, 'Rapti', 153),
(2578, 'Sagarmatha', 153),
(2579, 'Seti', 153),
(2580, 'Bonaire', 154),
(2581, 'Curacao', 154),
(2582, 'Saba', 154),
(2583, 'Sint Eustatius', 154),
(2584, 'Sint Maarten', 154),
(2585, 'Amsterdam', 155),
(2586, 'Benelux', 155),
(2587, 'Drenthe', 155),
(2588, 'Flevoland', 155),
(2589, 'Friesland', 155),
(2590, 'Gelderland', 155),
(2591, 'Groningen', 155),
(2592, 'Limburg', 155),
(2593, 'Noord-Brabant', 155),
(2594, 'Noord-Holland', 155),
(2595, 'Overijssel', 155),
(2596, 'South Holland', 155),
(2597, 'Utrecht', 155),
(2598, 'Zeeland', 155),
(2599, 'Zuid-Holland', 155),
(2600, 'Iles', 156),
(2601, 'Nord', 156),
(2602, 'Sud', 156),
(2603, 'Area Outside Region', 157),
(2604, 'Auckland', 157),
(2605, 'Bay of Plenty', 157),
(2606, 'Canterbury', 157),
(2607, 'Christchurch', 157),
(2608, 'Gisborne', 157),
(2609, 'Hawke\'s Bay', 157),
(2610, 'Manawatu-Wanganui', 157),
(2611, 'Marlborough', 157),
(2612, 'Nelson', 157),
(2613, 'Northland', 157),
(2614, 'Otago', 157),
(2615, 'Rodney', 157),
(2616, 'Southland', 157),
(2617, 'Taranaki', 157),
(2618, 'Tasman', 157),
(2619, 'Waikato', 157),
(2620, 'Wellington', 157),
(2621, 'West Coast', 157),
(2622, 'Atlantico Norte', 158),
(2623, 'Atlantico Sur', 158),
(2624, 'Boaco', 158),
(2625, 'Carazo', 158),
(2626, 'Chinandega', 158),
(2627, 'Chontales', 158),
(2628, 'Esteli', 158),
(2629, 'Granada', 158),
(2630, 'Jinotega', 158),
(2631, 'Leon', 158),
(2632, 'Madriz', 158),
(2633, 'Managua', 158),
(2634, 'Masaya', 158),
(2635, 'Matagalpa', 158),
(2636, 'Nueva Segovia', 158),
(2637, 'Rio San Juan', 158),
(2638, 'Rivas', 158),
(2639, 'Agadez', 159),
(2640, 'Diffa', 159),
(2641, 'Dosso', 159),
(2642, 'Maradi', 159),
(2643, 'Niamey', 159),
(2644, 'Tahoua', 159),
(2645, 'Tillabery', 159),
(2646, 'Zinder', 159),
(2647, 'Abia', 160),
(2648, 'Abuja Federal Capital Territor', 160),
(2649, 'Adamawa', 160),
(2650, 'Akwa Ibom', 160),
(2651, 'Anambra', 160),
(2652, 'Bauchi', 160),
(2653, 'Bayelsa', 160),
(2654, 'Benue', 160),
(2655, 'Borno', 160),
(2656, 'Cross River', 160),
(2657, 'Delta', 160),
(2658, 'Ebonyi', 160),
(2659, 'Edo', 160),
(2660, 'Ekiti', 160),
(2661, 'Enugu', 160),
(2662, 'Gombe', 160),
(2663, 'Imo', 160),
(2664, 'Jigawa', 160),
(2665, 'Kaduna', 160),
(2666, 'Kano', 160),
(2667, 'Katsina', 160),
(2668, 'Kebbi', 160),
(2669, 'Kogi', 160),
(2670, 'Kwara', 160),
(2671, 'Lagos', 160),
(2672, 'Nassarawa', 160),
(2673, 'Niger', 160),
(2674, 'Ogun', 160),
(2675, 'Ondo', 160),
(2676, 'Osun', 160),
(2677, 'Oyo', 160),
(2678, 'Plateau', 160),
(2679, 'Rivers', 160),
(2680, 'Sokoto', 160),
(2681, 'Taraba', 160),
(2682, 'Yobe', 160),
(2683, 'Zamfara', 160),
(2684, 'Niue', 161),
(2685, 'Norfolk Island', 162),
(2686, 'Northern Islands', 163),
(2687, 'Rota', 163),
(2688, 'Saipan', 163),
(2689, 'Tinian', 163),
(2690, 'Akershus', 164),
(2691, 'Aust Agder', 164),
(2692, 'Bergen', 164),
(2693, 'Buskerud', 164),
(2694, 'Finnmark', 164),
(2695, 'Hedmark', 164),
(2696, 'Hordaland', 164),
(2697, 'Moere og Romsdal', 164),
(2698, 'Nord Trondelag', 164),
(2699, 'Nordland', 164),
(2700, 'Oestfold', 164),
(2701, 'Oppland', 164),
(2702, 'Oslo', 164),
(2703, 'Rogaland', 164),
(2704, 'Soer Troendelag', 164),
(2705, 'Sogn og Fjordane', 164),
(2706, 'Stavern', 164),
(2707, 'Sykkylven', 164),
(2708, 'Telemark', 164),
(2709, 'Troms', 164),
(2710, 'Vest Agder', 164),
(2711, 'Vestfold', 164),
(2712, 'ÃƒÂ˜stfold', 164),
(2713, 'Al Buraimi', 165),
(2714, 'Dhufar', 165),
(2715, 'Masqat', 165),
(2716, 'Musandam', 165),
(2717, 'Rusayl', 165),
(2718, 'Wadi Kabir', 165),
(2719, 'ad-Dakhiliyah', 165),
(2720, 'adh-Dhahirah', 165),
(2721, 'al-Batinah', 165),
(2722, 'ash-Sharqiyah', 165),
(2723, 'Baluchistan', 166),
(2724, 'Federal Capital Area', 166),
(2725, 'Federally administered Tribal ', 166),
(2726, 'North-West Frontier', 166),
(2727, 'Northern Areas', 166),
(2728, 'Punjab', 166),
(2729, 'Sind', 166),
(2730, 'Aimeliik', 167),
(2731, 'Airai', 167),
(2732, 'Angaur', 167),
(2733, 'Hatobohei', 167),
(2734, 'Kayangel', 167),
(2735, 'Koror', 167),
(2736, 'Melekeok', 167),
(2737, 'Ngaraard', 167),
(2738, 'Ngardmau', 167),
(2739, 'Ngaremlengui', 167),
(2740, 'Ngatpang', 167),
(2741, 'Ngchesar', 167),
(2742, 'Ngerchelong', 167),
(2743, 'Ngiwal', 167),
(2744, 'Peleliu', 167),
(2745, 'Sonsorol', 167),
(2746, 'Ariha', 168),
(2747, 'Bayt Lahm', 168),
(2748, 'Bethlehem', 168),
(2749, 'Dayr-al-Balah', 168),
(2750, 'Ghazzah', 168),
(2751, 'Ghazzah ash-Shamaliyah', 168),
(2752, 'Janin', 168),
(2753, 'Khan Yunis', 168),
(2754, 'Nabulus', 168),
(2755, 'Qalqilyah', 168),
(2756, 'Rafah', 168),
(2757, 'Ram Allah wal-Birah', 168),
(2758, 'Salfit', 168),
(2759, 'Tubas', 168),
(2760, 'Tulkarm', 168),
(2761, 'al-Khalil', 168),
(2762, 'al-Quds', 168),
(2763, 'Bocas del Toro', 169),
(2764, 'Chiriqui', 169),
(2765, 'Cocle', 169),
(2766, 'Colon', 169),
(2767, 'Darien', 169),
(2768, 'Embera', 169),
(2769, 'Herrera', 169),
(2770, 'Kuna Yala', 169),
(2771, 'Los Santos', 169),
(2772, 'Ngobe Bugle', 169),
(2773, 'Panama', 169),
(2774, 'Veraguas', 169),
(2775, 'East New Britain', 170),
(2776, 'East Sepik', 170),
(2777, 'Eastern Highlands', 170),
(2778, 'Enga', 170),
(2779, 'Fly River', 170),
(2780, 'Gulf', 170),
(2781, 'Madang', 170),
(2782, 'Manus', 170),
(2783, 'Milne Bay', 170),
(2784, 'Morobe', 170),
(2785, 'National Capital District', 170),
(2786, 'New Ireland', 170),
(2787, 'North Solomons', 170),
(2788, 'Oro', 170),
(2789, 'Sandaun', 170),
(2790, 'Simbu', 170),
(2791, 'Southern Highlands', 170),
(2792, 'West New Britain', 170),
(2793, 'Western Highlands', 170),
(2794, 'Alto Paraguay', 171),
(2795, 'Alto Parana', 171),
(2796, 'Amambay', 171),
(2797, 'Asuncion', 171),
(2798, 'Boqueron', 171),
(2799, 'Caaguazu', 171),
(2800, 'Caazapa', 171),
(2801, 'Canendiyu', 171),
(2802, 'Central', 171),
(2803, 'Concepcion', 171),
(2804, 'Cordillera', 171),
(2805, 'Guaira', 171),
(2806, 'Itapua', 171),
(2807, 'Misiones', 171),
(2808, 'Neembucu', 171),
(2809, 'Paraguari', 171),
(2810, 'Presidente Hayes', 171),
(2811, 'San Pedro', 171),
(2812, 'Amazonas', 172),
(2813, 'Ancash', 172),
(2814, 'Apurimac', 172),
(2815, 'Arequipa', 172),
(2816, 'Ayacucho', 172),
(2817, 'Cajamarca', 172),
(2818, 'Cusco', 172),
(2819, 'Huancavelica', 172),
(2820, 'Huanuco', 172),
(2821, 'Ica', 172),
(2822, 'Junin', 172),
(2823, 'La Libertad', 172),
(2824, 'Lambayeque', 172),
(2825, 'Lima y Callao', 172),
(2826, 'Loreto', 172),
(2827, 'Madre de Dios', 172),
(2828, 'Moquegua', 172),
(2829, 'Pasco', 172),
(2830, 'Piura', 172),
(2831, 'Puno', 172),
(2832, 'San Martin', 172),
(2833, 'Tacna', 172),
(2834, 'Tumbes', 172),
(2835, 'Ucayali', 172),
(2836, 'Batangas', 173),
(2837, 'Bicol', 173),
(2838, 'Bulacan', 173),
(2839, 'Cagayan', 173),
(2840, 'Caraga', 173),
(2841, 'Central Luzon', 173),
(2842, 'Central Mindanao', 173),
(2843, 'Central Visayas', 173),
(2844, 'Cordillera', 173),
(2845, 'Davao', 173),
(2846, 'Eastern Visayas', 173),
(2847, 'Greater Metropolitan Area', 173),
(2848, 'Ilocos', 173),
(2849, 'Laguna', 173),
(2850, 'Luzon', 173),
(2851, 'Mactan', 173),
(2852, 'Metropolitan Manila Area', 173),
(2853, 'Muslim Mindanao', 173),
(2854, 'Northern Mindanao', 173),
(2855, 'Southern Mindanao', 173),
(2856, 'Southern Tagalog', 173),
(2857, 'Western Mindanao', 173),
(2858, 'Western Visayas', 173),
(2859, 'Pitcairn Island', 174),
(2860, 'Biale Blota', 175),
(2861, 'Dobroszyce', 175),
(2862, 'Dolnoslaskie', 175),
(2863, 'Dziekanow Lesny', 175),
(2864, 'Hopowo', 175),
(2865, 'Kartuzy', 175),
(2866, 'Koscian', 175),
(2867, 'Krakow', 175),
(2868, 'Kujawsko-Pomorskie', 175),
(2869, 'Lodzkie', 175),
(2870, 'Lubelskie', 175),
(2871, 'Lubuskie', 175),
(2872, 'Malomice', 175),
(2873, 'Malopolskie', 175),
(2874, 'Mazowieckie', 175),
(2875, 'Mirkow', 175),
(2876, 'Opolskie', 175),
(2877, 'Ostrowiec', 175),
(2878, 'Podkarpackie', 175),
(2879, 'Podlaskie', 175),
(2880, 'Polska', 175),
(2881, 'Pomorskie', 175),
(2882, 'Poznan', 175),
(2883, 'Pruszkow', 175),
(2884, 'Rymanowska', 175),
(2885, 'Rzeszow', 175),
(2886, 'Slaskie', 175),
(2887, 'Stare Pole', 175),
(2888, 'Swietokrzyskie', 175),
(2889, 'Warminsko-Mazurskie', 175),
(2890, 'Warsaw', 175),
(2891, 'Wejherowo', 175),
(2892, 'Wielkopolskie', 175),
(2893, 'Wroclaw', 175),
(2894, 'Zachodnio-Pomorskie', 175),
(2895, 'Zukowo', 175),
(2896, 'Abrantes', 176),
(2897, 'Acores', 176),
(2898, 'Alentejo', 176),
(2899, 'Algarve', 176),
(2900, 'Braga', 176),
(2901, 'Centro', 176),
(2902, 'Distrito de Leiria', 176),
(2903, 'Distrito de Viana do Castelo', 176),
(2904, 'Distrito de Vila Real', 176),
(2905, 'Distrito do Porto', 176),
(2906, 'Lisboa e Vale do Tejo', 176),
(2907, 'Madeira', 176),
(2908, 'Norte', 176),
(2909, 'Paivas', 176),
(2910, 'Arecibo', 177),
(2911, 'Bayamon', 177),
(2912, 'Carolina', 177),
(2913, 'Florida', 177),
(2914, 'Guayama', 177),
(2915, 'Humacao', 177),
(2916, 'Mayaguez-Aguadilla', 177),
(2917, 'Ponce', 177),
(2918, 'Salinas', 177),
(2919, 'San Juan', 177),
(2920, 'Doha', 178),
(2921, 'Jarian-al-Batnah', 178),
(2922, 'Umm Salal', 178),
(2923, 'ad-Dawhah', 178),
(2924, 'al-Ghuwayriyah', 178),
(2925, 'al-Jumayliyah', 178),
(2926, 'al-Khawr', 178),
(2927, 'al-Wakrah', 178),
(2928, 'ar-Rayyan', 178),
(2929, 'ash-Shamal', 178),
(2930, 'Saint-Benoit', 179),
(2931, 'Saint-Denis', 179),
(2932, 'Saint-Paul', 179),
(2933, 'Saint-Pierre', 179),
(2934, 'Alba', 180),
(2935, 'Arad', 180),
(2936, 'Arges', 180),
(2937, 'Bacau', 180),
(2938, 'Bihor', 180),
(2939, 'Bistrita-Nasaud', 180),
(2940, 'Botosani', 180),
(2941, 'Braila', 180),
(2942, 'Brasov', 180),
(2943, 'Bucuresti', 180),
(2944, 'Buzau', 180),
(2945, 'Calarasi', 180),
(2946, 'Caras-Severin', 180),
(2947, 'Cluj', 180),
(2948, 'Constanta', 180),
(2949, 'Covasna', 180),
(2950, 'Dambovita', 180),
(2951, 'Dolj', 180),
(2952, 'Galati', 180),
(2953, 'Giurgiu', 180),
(2954, 'Gorj', 180),
(2955, 'Harghita', 180),
(2956, 'Hunedoara', 180),
(2957, 'Ialomita', 180),
(2958, 'Iasi', 180),
(2959, 'Ilfov', 180),
(2960, 'Maramures', 180),
(2961, 'Mehedinti', 180),
(2962, 'Mures', 180),
(2963, 'Neamt', 180),
(2964, 'Olt', 180),
(2965, 'Prahova', 180),
(2966, 'Salaj', 180),
(2967, 'Satu Mare', 180),
(2968, 'Sibiu', 180),
(2969, 'Sondelor', 180),
(2970, 'Suceava', 180),
(2971, 'Teleorman', 180),
(2972, 'Timis', 180),
(2973, 'Tulcea', 180),
(2974, 'Valcea', 180),
(2975, 'Vaslui', 180),
(2976, 'Vrancea', 180),
(2977, 'Adygeja', 181),
(2978, 'Aga', 181),
(2979, 'Alanija', 181),
(2980, 'Altaj', 181),
(2981, 'Amur', 181),
(2982, 'Arhangelsk', 181),
(2983, 'Astrahan', 181),
(2984, 'Bashkortostan', 181),
(2985, 'Belgorod', 181),
(2986, 'Brjansk', 181),
(2987, 'Burjatija', 181),
(2988, 'Chechenija', 181),
(2989, 'Cheljabinsk', 181),
(2990, 'Chita', 181),
(2991, 'Chukotka', 181),
(2992, 'Chuvashija', 181),
(2993, 'Dagestan', 181),
(2994, 'Evenkija', 181),
(2995, 'Gorno-Altaj', 181),
(2996, 'Habarovsk', 181),
(2997, 'Hakasija', 181),
(2998, 'Hanty-Mansija', 181),
(2999, 'Ingusetija', 181),
(3000, 'Irkutsk', 181),
(3001, 'Ivanovo', 181),
(3002, 'Jamalo-Nenets', 181),
(3003, 'Jaroslavl', 181),
(3004, 'Jevrej', 181),
(3005, 'Kabardino-Balkarija', 181),
(3006, 'Kaliningrad', 181),
(3007, 'Kalmykija', 181),
(3008, 'Kaluga', 181),
(3009, 'Kamchatka', 181),
(3010, 'Karachaj-Cherkessija', 181),
(3011, 'Karelija', 181),
(3012, 'Kemerovo', 181),
(3013, 'Khabarovskiy Kray', 181),
(3014, 'Kirov', 181),
(3015, 'Komi', 181),
(3016, 'Komi-Permjakija', 181),
(3017, 'Korjakija', 181),
(3018, 'Kostroma', 181),
(3019, 'Krasnodar', 181),
(3020, 'Krasnojarsk', 181),
(3021, 'Krasnoyarskiy Kray', 181),
(3022, 'Kurgan', 181),
(3023, 'Kursk', 181),
(3024, 'Leningrad', 181),
(3025, 'Lipeck', 181),
(3026, 'Magadan', 181),
(3027, 'Marij El', 181),
(3028, 'Mordovija', 181),
(3029, 'Moscow', 181),
(3030, 'Moskovskaja Oblast', 181),
(3031, 'Moskovskaya Oblast', 181),
(3032, 'Moskva', 181),
(3033, 'Murmansk', 181),
(3034, 'Nenets', 181),
(3035, 'Nizhnij Novgorod', 181),
(3036, 'Novgorod', 181),
(3037, 'Novokusnezk', 181),
(3038, 'Novosibirsk', 181),
(3039, 'Omsk', 181),
(3040, 'Orenburg', 181),
(3041, 'Orjol', 181),
(3042, 'Penza', 181),
(3043, 'Perm', 181),
(3044, 'Primorje', 181),
(3045, 'Pskov', 181),
(3046, 'Pskovskaya Oblast', 181),
(3047, 'Rjazan', 181),
(3048, 'Rostov', 181),
(3049, 'Saha', 181),
(3050, 'Sahalin', 181),
(3051, 'Samara', 181),
(3052, 'Samarskaya', 181),
(3053, 'Sankt-Peterburg', 181),
(3054, 'Saratov', 181),
(3055, 'Smolensk', 181),
(3056, 'Stavropol', 181),
(3057, 'Sverdlovsk', 181),
(3058, 'Tajmyrija', 181),
(3059, 'Tambov', 181),
(3060, 'Tatarstan', 181),
(3061, 'Tjumen', 181),
(3062, 'Tomsk', 181),
(3063, 'Tula', 181),
(3064, 'Tver', 181),
(3065, 'Tyva', 181),
(3066, 'Udmurtija', 181),
(3067, 'Uljanovsk', 181),
(3068, 'Ulyanovskaya Oblast', 181),
(3069, 'Ust-Orda', 181),
(3070, 'Vladimir', 181),
(3071, 'Volgograd', 181),
(3072, 'Vologda', 181),
(3073, 'Voronezh', 181),
(3074, 'Butare', 182),
(3075, 'Byumba', 182),
(3076, 'Cyangugu', 182),
(3077, 'Gikongoro', 182),
(3078, 'Gisenyi', 182),
(3079, 'Gitarama', 182),
(3080, 'Kibungo', 182),
(3081, 'Kibuye', 182),
(3082, 'Kigali-ngali', 182),
(3083, 'Ruhengeri', 182),
(3084, 'Ascension', 183),
(3085, 'Gough Island', 183),
(3086, 'Saint Helena', 183),
(3087, 'Tristan da Cunha', 183),
(3088, 'Christ Church Nichola Town', 184),
(3089, 'Saint Anne Sandy Point', 184),
(3090, 'Saint George Basseterre', 184),
(3091, 'Saint George Gingerland', 184),
(3092, 'Saint James Windward', 184),
(3093, 'Saint John Capesterre', 184),
(3094, 'Saint John Figtree', 184),
(3095, 'Saint Mary Cayon', 184),
(3096, 'Saint Paul Capesterre', 184),
(3097, 'Saint Paul Charlestown', 184),
(3098, 'Saint Peter Basseterre', 184),
(3099, 'Saint Thomas Lowland', 184),
(3100, 'Saint Thomas Middle Island', 184),
(3101, 'Trinity Palmetto Point', 184),
(3102, 'Anse-la-Raye', 185),
(3103, 'Canaries', 185),
(3104, 'Castries', 185),
(3105, 'Choiseul', 185),
(3106, 'Dennery', 185),
(3107, 'Gros Inlet', 185),
(3108, 'Laborie', 185),
(3109, 'Micoud', 185),
(3110, 'Soufriere', 185),
(3111, 'Vieux Fort', 185),
(3112, 'Miquelon-Langlade', 186),
(3113, 'Saint-Pierre', 186),
(3114, 'Charlotte', 187),
(3115, 'Grenadines', 187),
(3116, 'Saint Andrew', 187),
(3117, 'Saint David', 187),
(3118, 'Saint George', 187),
(3119, 'Saint Patrick', 187),
(3120, 'A\'ana', 188),
(3121, 'Aiga-i-le-Tai', 188),
(3122, 'Atua', 188),
(3123, 'Fa\'asaleleaga', 188),
(3124, 'Gaga\'emauga', 188),
(3125, 'Gagaifomauga', 188),
(3126, 'Palauli', 188),
(3127, 'Satupa\'itea', 188),
(3128, 'Tuamasaga', 188),
(3129, 'Va\'a-o-Fonoti', 188),
(3130, 'Vaisigano', 188),
(3131, 'Acquaviva', 189),
(3132, 'Borgo Maggiore', 189),
(3133, 'Chiesanuova', 189),
(3134, 'Domagnano', 189),
(3135, 'Faetano', 189),
(3136, 'Fiorentino', 189),
(3137, 'Montegiardino', 189),
(3138, 'San Marino', 189),
(3139, 'Serravalle', 189),
(3140, 'Agua Grande', 190),
(3141, 'Cantagalo', 190),
(3142, 'Lemba', 190),
(3143, 'Lobata', 190),
(3144, 'Me-Zochi', 190),
(3145, 'Pague', 190),
(3146, 'Al Khobar', 191),
(3147, 'Aseer', 191),
(3148, 'Ash Sharqiyah', 191),
(3149, 'Asir', 191),
(3150, 'Central Province', 191),
(3151, 'Eastern Province', 191),
(3152, 'Ha\'il', 191),
(3153, 'Jawf', 191),
(3154, 'Jizan', 191),
(3155, 'Makkah', 191),
(3156, 'Najran', 191),
(3157, 'Qasim', 191),
(3158, 'Tabuk', 191),
(3159, 'Western Province', 191),
(3160, 'al-Bahah', 191),
(3161, 'al-Hudud-ash-Shamaliyah', 191),
(3162, 'al-Madinah', 191),
(3163, 'ar-Riyad', 191),
(3164, 'Dakar', 192),
(3165, 'Diourbel', 192),
(3166, 'Fatick', 192),
(3167, 'Kaolack', 192),
(3168, 'Kolda', 192),
(3169, 'Louga', 192),
(3170, 'Saint-Louis', 192),
(3171, 'Tambacounda', 192),
(3172, 'Thies', 192),
(3173, 'Ziguinchor', 192),
(3174, 'Central Serbia', 193),
(3175, 'Kosovo and Metohija', 193),
(3176, 'Vojvodina', 193),
(3177, 'Anse Boileau', 194),
(3178, 'Anse Royale', 194),
(3179, 'Cascade', 194),
(3180, 'Takamaka', 194),
(3181, 'Victoria', 194),
(3182, 'Eastern', 195),
(3183, 'Northern', 195),
(3184, 'Southern', 195),
(3185, 'Western', 195),
(3186, 'Singapore', 196),
(3187, 'Banskobystricky', 197),
(3188, 'Bratislavsky', 197),
(3189, 'Kosicky', 197),
(3190, 'Nitriansky', 197),
(3191, 'Presovsky', 197),
(3192, 'Trenciansky', 197),
(3193, 'Trnavsky', 197),
(3194, 'Zilinsky', 197),
(3195, 'Benedikt', 198),
(3196, 'Gorenjska', 198),
(3197, 'Gorishka', 198),
(3198, 'Jugovzhodna Slovenija', 198),
(3199, 'Koroshka', 198),
(3200, 'Notranjsko-krashka', 198),
(3201, 'Obalno-krashka', 198),
(3202, 'Obcina Domzale', 198),
(3203, 'Obcina Vitanje', 198),
(3204, 'Osrednjeslovenska', 198),
(3205, 'Podravska', 198),
(3206, 'Pomurska', 198),
(3207, 'Savinjska', 198),
(3208, 'Slovenian Littoral', 198),
(3209, 'Spodnjeposavska', 198),
(3210, 'Zasavska', 198),
(3211, 'Pitcairn', 199),
(3212, 'Central', 200),
(3213, 'Choiseul', 200),
(3214, 'Guadalcanal', 200),
(3215, 'Isabel', 200),
(3216, 'Makira and Ulawa', 200),
(3217, 'Malaita', 200),
(3218, 'Rennell and Bellona', 200),
(3219, 'Temotu', 200),
(3220, 'Western', 200),
(3221, 'Awdal', 201),
(3222, 'Bakol', 201),
(3223, 'Banadir', 201),
(3224, 'Bari', 201),
(3225, 'Bay', 201),
(3226, 'Galgudug', 201),
(3227, 'Gedo', 201),
(3228, 'Hiran', 201),
(3229, 'Jubbada Hose', 201),
(3230, 'Jubbadha Dexe', 201),
(3231, 'Mudug', 201),
(3232, 'Nugal', 201),
(3233, 'Sanag', 201),
(3234, 'Shabellaha Dhexe', 201),
(3235, 'Shabellaha Hose', 201),
(3236, 'Togdher', 201),
(3237, 'Woqoyi Galbed', 201),
(3238, 'Eastern Cape', 202),
(3239, 'Free State', 202),
(3240, 'Gauteng', 202),
(3241, 'Kempton Park', 202),
(3242, 'Kramerville', 202),
(3243, 'KwaZulu Natal', 202),
(3244, 'Limpopo', 202),
(3245, 'Mpumalanga', 202),
(3246, 'North West', 202),
(3247, 'Northern Cape', 202),
(3248, 'Parow', 202),
(3249, 'Table View', 202),
(3250, 'Umtentweni', 202),
(3251, 'Western Cape', 202),
(3252, 'South Georgia', 203),
(3253, 'Central Equatoria', 204),
(3254, 'A Coruna', 205),
(3255, 'Alacant', 205),
(3256, 'Alava', 205),
(3257, 'Albacete', 205),
(3258, 'Almeria', 205),
(3259, 'Andalucia', 205),
(3260, 'Asturias', 205),
(3261, 'Avila', 205),
(3262, 'Badajoz', 205),
(3263, 'Balears', 205),
(3264, 'Barcelona', 205),
(3265, 'Bertamirans', 205),
(3266, 'Biscay', 205),
(3267, 'Burgos', 205),
(3268, 'Caceres', 205),
(3269, 'Cadiz', 205),
(3270, 'Cantabria', 205),
(3271, 'Castello', 205),
(3272, 'Catalunya', 205),
(3273, 'Ceuta', 205),
(3274, 'Ciudad Real', 205),
(3275, 'Comunidad Autonoma de Canarias', 205),
(3276, 'Comunidad Autonoma de Cataluna', 205),
(3277, 'Comunidad Autonoma de Galicia', 205),
(3278, 'Comunidad Autonoma de las Isla', 205),
(3279, 'Comunidad Autonoma del Princip', 205),
(3280, 'Comunidad Valenciana', 205),
(3281, 'Cordoba', 205),
(3282, 'Cuenca', 205),
(3283, 'Gipuzkoa', 205),
(3284, 'Girona', 205),
(3285, 'Granada', 205),
(3286, 'Guadalajara', 205),
(3287, 'Guipuzcoa', 205),
(3288, 'Huelva', 205),
(3289, 'Huesca', 205),
(3290, 'Jaen', 205),
(3291, 'La Rioja', 205),
(3292, 'Las Palmas', 205),
(3293, 'Leon', 205),
(3294, 'Lerida', 205),
(3295, 'Lleida', 205),
(3296, 'Lugo', 205),
(3297, 'Madrid', 205),
(3298, 'Malaga', 205),
(3299, 'Melilla', 205),
(3300, 'Murcia', 205),
(3301, 'Navarra', 205),
(3302, 'Ourense', 205),
(3303, 'Pais Vasco', 205),
(3304, 'Palencia', 205),
(3305, 'Pontevedra', 205),
(3306, 'Salamanca', 205),
(3307, 'Santa Cruz de Tenerife', 205),
(3308, 'Segovia', 205),
(3309, 'Sevilla', 205),
(3310, 'Soria', 205),
(3311, 'Tarragona', 205),
(3312, 'Tenerife', 205),
(3313, 'Teruel', 205),
(3314, 'Toledo', 205),
(3315, 'Valencia', 205),
(3316, 'Valladolid', 205),
(3317, 'Vizcaya', 205),
(3318, 'Zamora', 205),
(3319, 'Zaragoza', 205),
(3320, 'Amparai', 206),
(3321, 'Anuradhapuraya', 206),
(3322, 'Badulla', 206),
(3323, 'Boralesgamuwa', 206),
(3324, 'Colombo', 206),
(3325, 'Galla', 206),
(3326, 'Gampaha', 206),
(3327, 'Hambantota', 206),
(3328, 'Kalatura', 206),
(3329, 'Kegalla', 206),
(3330, 'Kilinochchi', 206),
(3331, 'Kurunegala', 206),
(3332, 'Madakalpuwa', 206),
(3333, 'Maha Nuwara', 206),
(3334, 'Malwana', 206),
(3335, 'Mannarama', 206),
(3336, 'Matale', 206),
(3337, 'Matara', 206),
(3338, 'Monaragala', 206),
(3339, 'Mullaitivu', 206),
(3340, 'North Eastern Province', 206),
(3341, 'North Western Province', 206),
(3342, 'Nuwara Eliya', 206),
(3343, 'Polonnaruwa', 206),
(3344, 'Puttalama', 206),
(3345, 'Ratnapuraya', 206),
(3346, 'Southern Province', 206),
(3347, 'Tirikunamalaya', 206),
(3348, 'Tuscany', 206),
(3349, 'Vavuniyawa', 206),
(3350, 'Western Province', 206),
(3351, 'Yapanaya', 206),
(3352, 'kadawatha', 206),
(3353, 'A\'ali-an-Nil', 207),
(3354, 'Bahr-al-Jabal', 207),
(3355, 'Central Equatoria', 207),
(3356, 'Gharb Bahr-al-Ghazal', 207),
(3357, 'Gharb Darfur', 207),
(3358, 'Gharb Kurdufan', 207),
(3359, 'Gharb-al-Istiwa\'iyah', 207),
(3360, 'Janub Darfur', 207),
(3361, 'Janub Kurdufan', 207),
(3362, 'Junqali', 207),
(3363, 'Kassala', 207),
(3364, 'Nahr-an-Nil', 207),
(3365, 'Shamal Bahr-al-Ghazal', 207),
(3366, 'Shamal Darfur', 207),
(3367, 'Shamal Kurdufan', 207),
(3368, 'Sharq-al-Istiwa\'iyah', 207),
(3369, 'Sinnar', 207),
(3370, 'Warab', 207),
(3371, 'Wilayat al Khartum', 207),
(3372, 'al-Bahr-al-Ahmar', 207),
(3373, 'al-Buhayrat', 207),
(3374, 'al-Jazirah', 207),
(3375, 'al-Khartum', 207),
(3376, 'al-Qadarif', 207),
(3377, 'al-Wahdah', 207),
(3378, 'an-Nil-al-Abyad', 207),
(3379, 'an-Nil-al-Azraq', 207),
(3380, 'ash-Shamaliyah', 207),
(3381, 'Brokopondo', 208),
(3382, 'Commewijne', 208),
(3383, 'Coronie', 208),
(3384, 'Marowijne', 208),
(3385, 'Nickerie', 208),
(3386, 'Para', 208),
(3387, 'Paramaribo', 208),
(3388, 'Saramacca', 208),
(3389, 'Wanica', 208),
(3390, 'Svalbard', 209),
(3391, 'Hhohho', 210),
(3392, 'Lubombo', 210),
(3393, 'Manzini', 210),
(3394, 'Shiselweni', 210),
(3395, 'Alvsborgs Lan', 211),
(3396, 'Angermanland', 211),
(3397, 'Blekinge', 211),
(3398, 'Bohuslan', 211),
(3399, 'Dalarna', 211),
(3400, 'Gavleborg', 211),
(3401, 'Gaza', 211),
(3402, 'Gotland', 211),
(3403, 'Halland', 211),
(3404, 'Jamtland', 211),
(3405, 'Jonkoping', 211),
(3406, 'Kalmar', 211),
(3407, 'Kristianstads', 211),
(3408, 'Kronoberg', 211),
(3409, 'Norrbotten', 211),
(3410, 'Orebro', 211),
(3411, 'Ostergotland', 211),
(3412, 'Saltsjo-Boo', 211),
(3413, 'Skane', 211),
(3414, 'Smaland', 211),
(3415, 'Sodermanland', 211),
(3416, 'Stockholm', 211),
(3417, 'Uppsala', 211),
(3418, 'Varmland', 211),
(3419, 'Vasterbotten', 211),
(3420, 'Vastergotland', 211),
(3421, 'Vasternorrland', 211),
(3422, 'Vastmanland', 211),
(3423, 'Vastra Gotaland', 211),
(3424, 'Aargau', 212),
(3425, 'Appenzell Inner-Rhoden', 212),
(3426, 'Appenzell-Ausser Rhoden', 212),
(3427, 'Basel-Landschaft', 212),
(3428, 'Basel-Stadt', 212),
(3429, 'Bern', 212),
(3430, 'Canton Ticino', 212),
(3431, 'Fribourg', 212),
(3432, 'Geneve', 212),
(3433, 'Glarus', 212),
(3434, 'Graubunden', 212),
(3435, 'Heerbrugg', 212),
(3436, 'Jura', 212),
(3437, 'Kanton Aargau', 212),
(3438, 'Luzern', 212),
(3439, 'Morbio Inferiore', 212),
(3440, 'Muhen', 212),
(3441, 'Neuchatel', 212),
(3442, 'Nidwalden', 212),
(3443, 'Obwalden', 212),
(3444, 'Sankt Gallen', 212),
(3445, 'Schaffhausen', 212),
(3446, 'Schwyz', 212),
(3447, 'Solothurn', 212),
(3448, 'Thurgau', 212),
(3449, 'Ticino', 212),
(3450, 'Uri', 212),
(3451, 'Valais', 212),
(3452, 'Vaud', 212),
(3453, 'Vauffelin', 212),
(3454, 'Zug', 212),
(3455, 'Zurich', 212),
(3456, 'Aleppo', 213),
(3457, 'Dar\'a', 213),
(3458, 'Dayr-az-Zawr', 213),
(3459, 'Dimashq', 213),
(3460, 'Halab', 213),
(3461, 'Hamah', 213),
(3462, 'Hims', 213),
(3463, 'Idlib', 213),
(3464, 'Madinat Dimashq', 213),
(3465, 'Tartus', 213),
(3466, 'al-Hasakah', 213),
(3467, 'al-Ladhiqiyah', 213),
(3468, 'al-Qunaytirah', 213),
(3469, 'ar-Raqqah', 213),
(3470, 'as-Suwayda', 213),
(3471, 'Changhwa', 214),
(3472, 'Chiayi Hsien', 214),
(3473, 'Chiayi Shih', 214),
(3474, 'Eastern Taipei', 214),
(3475, 'Hsinchu Hsien', 214),
(3476, 'Hsinchu Shih', 214),
(3477, 'Hualien', 214),
(3478, 'Ilan', 214),
(3479, 'Kaohsiung Hsien', 214),
(3480, 'Kaohsiung Shih', 214),
(3481, 'Keelung Shih', 214),
(3482, 'Kinmen', 214),
(3483, 'Miaoli', 214),
(3484, 'Nantou', 214),
(3485, 'Northern Taiwan', 214),
(3486, 'Penghu', 214),
(3487, 'Pingtung', 214),
(3488, 'Taichung', 214),
(3489, 'Taichung Hsien', 214),
(3490, 'Taichung Shih', 214),
(3491, 'Tainan Hsien', 214),
(3492, 'Tainan Shih', 214),
(3493, 'Taipei Hsien', 214),
(3494, 'Taipei Shih / Taipei Hsien', 214),
(3495, 'Taitung', 214),
(3496, 'Taoyuan', 214),
(3497, 'Yilan', 214),
(3498, 'Yun-Lin Hsien', 214),
(3499, 'Yunlin', 214),
(3500, 'Dushanbe', 215),
(3501, 'Gorno-Badakhshan', 215),
(3502, 'Karotegin', 215),
(3503, 'Khatlon', 215),
(3504, 'Sughd', 215),
(3505, 'Arusha', 216),
(3506, 'Dar es Salaam', 216),
(3507, 'Dodoma', 216),
(3508, 'Iringa', 216),
(3509, 'Kagera', 216),
(3510, 'Kigoma', 216),
(3511, 'Kilimanjaro', 216),
(3512, 'Lindi', 216),
(3513, 'Mara', 216),
(3514, 'Mbeya', 216),
(3515, 'Morogoro', 216),
(3516, 'Mtwara', 216),
(3517, 'Mwanza', 216),
(3518, 'Pwani', 216),
(3519, 'Rukwa', 216),
(3520, 'Ruvuma', 216),
(3521, 'Shinyanga', 216),
(3522, 'Singida', 216),
(3523, 'Tabora', 216),
(3524, 'Tanga', 216),
(3525, 'Zanzibar and Pemba', 216),
(3526, 'Amnat Charoen', 217),
(3527, 'Ang Thong', 217),
(3528, 'Bangkok', 217),
(3529, 'Buri Ram', 217),
(3530, 'Chachoengsao', 217),
(3531, 'Chai Nat', 217),
(3532, 'Chaiyaphum', 217),
(3533, 'Changwat Chaiyaphum', 217),
(3534, 'Chanthaburi', 217),
(3535, 'Chiang Mai', 217),
(3536, 'Chiang Rai', 217),
(3537, 'Chon Buri', 217),
(3538, 'Chumphon', 217),
(3539, 'Kalasin', 217),
(3540, 'Kamphaeng Phet', 217),
(3541, 'Kanchanaburi', 217),
(3542, 'Khon Kaen', 217),
(3543, 'Krabi', 217),
(3544, 'Krung Thep', 217),
(3545, 'Lampang', 217),
(3546, 'Lamphun', 217),
(3547, 'Loei', 217),
(3548, 'Lop Buri', 217),
(3549, 'Mae Hong Son', 217),
(3550, 'Maha Sarakham', 217),
(3551, 'Mukdahan', 217),
(3552, 'Nakhon Nayok', 217),
(3553, 'Nakhon Pathom', 217),
(3554, 'Nakhon Phanom', 217),
(3555, 'Nakhon Ratchasima', 217),
(3556, 'Nakhon Sawan', 217),
(3557, 'Nakhon Si Thammarat', 217),
(3558, 'Nan', 217),
(3559, 'Narathiwat', 217),
(3560, 'Nong Bua Lam Phu', 217),
(3561, 'Nong Khai', 217),
(3562, 'Nonthaburi', 217),
(3563, 'Pathum Thani', 217),
(3564, 'Pattani', 217),
(3565, 'Phangnga', 217),
(3566, 'Phatthalung', 217),
(3567, 'Phayao', 217),
(3568, 'Phetchabun', 217),
(3569, 'Phetchaburi', 217),
(3570, 'Phichit', 217),
(3571, 'Phitsanulok', 217),
(3572, 'Phra Nakhon Si Ayutthaya', 217),
(3573, 'Phrae', 217),
(3574, 'Phuket', 217),
(3575, 'Prachin Buri', 217),
(3576, 'Prachuap Khiri Khan', 217),
(3577, 'Ranong', 217),
(3578, 'Ratchaburi', 217),
(3579, 'Rayong', 217),
(3580, 'Roi Et', 217),
(3581, 'Sa Kaeo', 217),
(3582, 'Sakon Nakhon', 217),
(3583, 'Samut Prakan', 217),
(3584, 'Samut Sakhon', 217),
(3585, 'Samut Songkhran', 217),
(3586, 'Saraburi', 217),
(3587, 'Satun', 217),
(3588, 'Si Sa Ket', 217),
(3589, 'Sing Buri', 217),
(3590, 'Songkhla', 217),
(3591, 'Sukhothai', 217),
(3592, 'Suphan Buri', 217),
(3593, 'Surat Thani', 217),
(3594, 'Surin', 217),
(3595, 'Tak', 217),
(3596, 'Trang', 217),
(3597, 'Trat', 217),
(3598, 'Ubon Ratchathani', 217),
(3599, 'Udon Thani', 217),
(3600, 'Uthai Thani', 217),
(3601, 'Uttaradit', 217),
(3602, 'Yala', 217),
(3603, 'Yasothon', 217),
(3604, 'Centre', 218),
(3605, 'Kara', 218),
(3606, 'Maritime', 218),
(3607, 'Plateaux', 218),
(3608, 'Savanes', 218),
(3609, 'Atafu', 219),
(3610, 'Fakaofo', 219),
(3611, 'Nukunonu', 219),
(3612, 'Eua', 220),
(3613, 'Ha\'apai', 220),
(3614, 'Niuas', 220),
(3615, 'Tongatapu', 220),
(3616, 'Vava\'u', 220),
(3617, 'Arima-Tunapuna-Piarco', 221),
(3618, 'Caroni', 221),
(3619, 'Chaguanas', 221),
(3620, 'Couva-Tabaquite-Talparo', 221),
(3621, 'Diego Martin', 221),
(3622, 'Glencoe', 221),
(3623, 'Penal Debe', 221),
(3624, 'Point Fortin', 221),
(3625, 'Port of Spain', 221),
(3626, 'Princes Town', 221),
(3627, 'Saint George', 221),
(3628, 'San Fernando', 221),
(3629, 'San Juan', 221),
(3630, 'Sangre Grande', 221),
(3631, 'Siparia', 221),
(3632, 'Tobago', 221),
(3633, 'Aryanah', 222),
(3634, 'Bajah', 222),
(3635, 'Bin \'Arus', 222),
(3636, 'Binzart', 222),
(3637, 'Gouvernorat de Ariana', 222),
(3638, 'Gouvernorat de Nabeul', 222),
(3639, 'Gouvernorat de Sousse', 222),
(3640, 'Hammamet Yasmine', 222),
(3641, 'Jundubah', 222),
(3642, 'Madaniyin', 222),
(3643, 'Manubah', 222),
(3644, 'Monastir', 222),
(3645, 'Nabul', 222),
(3646, 'Qabis', 222),
(3647, 'Qafsah', 222),
(3648, 'Qibili', 222),
(3649, 'Safaqis', 222),
(3650, 'Sfax', 222),
(3651, 'Sidi Bu Zayd', 222),
(3652, 'Silyanah', 222),
(3653, 'Susah', 222),
(3654, 'Tatawin', 222),
(3655, 'Tawzar', 222),
(3656, 'Tunis', 222),
(3657, 'Zaghwan', 222),
(3658, 'al-Kaf', 222),
(3659, 'al-Mahdiyah', 222),
(3660, 'al-Munastir', 222),
(3661, 'al-Qasrayn', 222),
(3662, 'al-Qayrawan', 222),
(3663, 'Adana', 223),
(3664, 'Adiyaman', 223),
(3665, 'Afyon', 223),
(3666, 'Agri', 223),
(3667, 'Aksaray', 223),
(3668, 'Amasya', 223),
(3669, 'Ankara', 223),
(3670, 'Antalya', 223),
(3671, 'Ardahan', 223),
(3672, 'Artvin', 223),
(3673, 'Aydin', 223),
(3674, 'Balikesir', 223),
(3675, 'Bartin', 223),
(3676, 'Batman', 223),
(3677, 'Bayburt', 223),
(3678, 'Bilecik', 223),
(3679, 'Bingol', 223),
(3680, 'Bitlis', 223),
(3681, 'Bolu', 223),
(3682, 'Burdur', 223),
(3683, 'Bursa', 223),
(3684, 'Canakkale', 223),
(3685, 'Cankiri', 223),
(3686, 'Corum', 223),
(3687, 'Denizli', 223),
(3688, 'Diyarbakir', 223),
(3689, 'Duzce', 223),
(3690, 'Edirne', 223),
(3691, 'Elazig', 223),
(3692, 'Erzincan', 223),
(3693, 'Erzurum', 223),
(3694, 'Eskisehir', 223),
(3695, 'Gaziantep', 223),
(3696, 'Giresun', 223),
(3697, 'Gumushane', 223),
(3698, 'Hakkari', 223),
(3699, 'Hatay', 223),
(3700, 'Icel', 223),
(3701, 'Igdir', 223),
(3702, 'Isparta', 223),
(3703, 'Istanbul', 223),
(3704, 'Izmir', 223),
(3705, 'Kahramanmaras', 223),
(3706, 'Karabuk', 223),
(3707, 'Karaman', 223),
(3708, 'Kars', 223),
(3709, 'Karsiyaka', 223),
(3710, 'Kastamonu', 223),
(3711, 'Kayseri', 223),
(3712, 'Kilis', 223),
(3713, 'Kirikkale', 223),
(3714, 'Kirklareli', 223),
(3715, 'Kirsehir', 223),
(3716, 'Kocaeli', 223),
(3717, 'Konya', 223),
(3718, 'Kutahya', 223),
(3719, 'Lefkosa', 223),
(3720, 'Malatya', 223),
(3721, 'Manisa', 223),
(3722, 'Mardin', 223),
(3723, 'Mugla', 223),
(3724, 'Mus', 223),
(3725, 'Nevsehir', 223),
(3726, 'Nigde', 223),
(3727, 'Ordu', 223),
(3728, 'Osmaniye', 223),
(3729, 'Rize', 223),
(3730, 'Sakarya', 223),
(3731, 'Samsun', 223),
(3732, 'Sanliurfa', 223),
(3733, 'Siirt', 223),
(3734, 'Sinop', 223),
(3735, 'Sirnak', 223),
(3736, 'Sivas', 223),
(3737, 'Tekirdag', 223),
(3738, 'Tokat', 223),
(3739, 'Trabzon', 223),
(3740, 'Tunceli', 223),
(3741, 'Usak', 223),
(3742, 'Van', 223),
(3743, 'Yalova', 223),
(3744, 'Yozgat', 223),
(3745, 'Zonguldak', 223),
(3746, 'Ahal', 224),
(3747, 'Asgabat', 224),
(3748, 'Balkan', 224),
(3749, 'Dasoguz', 224),
(3750, 'Lebap', 224),
(3751, 'Mari', 224),
(3752, 'Grand Turk', 225),
(3753, 'South Caicos and East Caicos', 225),
(3754, 'Funafuti', 226),
(3755, 'Nanumanga', 226),
(3756, 'Nanumea', 226),
(3757, 'Niutao', 226),
(3758, 'Nui', 226),
(3759, 'Nukufetau', 226),
(3760, 'Nukulaelae', 226),
(3761, 'Vaitupu', 226),
(3762, 'Central', 227),
(3763, 'Eastern', 227),
(3764, 'Northern', 227),
(3765, 'Western', 227),
(3766, 'Cherkas\'ka', 228),
(3767, 'Chernihivs\'ka', 228),
(3768, 'Chernivets\'ka', 228),
(3769, 'Crimea', 228),
(3770, 'Dnipropetrovska', 228),
(3771, 'Donets\'ka', 228),
(3772, 'Ivano-Frankivs\'ka', 228),
(3773, 'Kharkiv', 228),
(3774, 'Kharkov', 228),
(3775, 'Khersonska', 228),
(3776, 'Khmel\'nyts\'ka', 228),
(3777, 'Kirovohrad', 228),
(3778, 'Krym', 228),
(3779, 'Kyyiv', 228),
(3780, 'Kyyivs\'ka', 228),
(3781, 'L\'vivs\'ka', 228),
(3782, 'Luhans\'ka', 228),
(3783, 'Mykolayivs\'ka', 228),
(3784, 'Odes\'ka', 228),
(3785, 'Odessa', 228),
(3786, 'Poltavs\'ka', 228),
(3787, 'Rivnens\'ka', 228),
(3788, 'Sevastopol\'', 228),
(3789, 'Sums\'ka', 228),
(3790, 'Ternopil\'s\'ka', 228),
(3791, 'Volyns\'ka', 228),
(3792, 'Vynnyts\'ka', 228),
(3793, 'Zakarpats\'ka', 228),
(3794, 'Zaporizhia', 228),
(3795, 'Zhytomyrs\'ka', 228),
(3796, 'Abu Zabi', 229),
(3797, 'Ajman', 229),
(3798, 'Dubai', 229),
(3799, 'Ras al-Khaymah', 229),
(3800, 'Sharjah', 229),
(3801, 'Sharjha', 229),
(3802, 'Umm al Qaywayn', 229),
(3803, 'al-Fujayrah', 229),
(3804, 'ash-Shariqah', 229),
(3805, 'Aberdeen', 230),
(3806, 'Aberdeenshire', 230),
(3807, 'Argyll', 230),
(3808, 'Armagh', 230),
(3809, 'Bedfordshire', 230),
(3810, 'Belfast', 230),
(3811, 'Berkshire', 230),
(3812, 'Birmingham', 230),
(3813, 'Brechin', 230),
(3814, 'Bridgnorth', 230),
(3815, 'Bristol', 230),
(3816, 'Buckinghamshire', 230),
(3817, 'Cambridge', 230),
(3818, 'Cambridgeshire', 230),
(3819, 'Channel Islands', 230),
(3820, 'Cheshire', 230),
(3821, 'Cleveland', 230),
(3822, 'Co Fermanagh', 230),
(3823, 'Conwy', 230),
(3824, 'Cornwall', 230),
(3825, 'Coventry', 230),
(3826, 'Craven Arms', 230),
(3827, 'Cumbria', 230),
(3828, 'Denbighshire', 230),
(3829, 'Derby', 230),
(3830, 'Derbyshire', 230),
(3831, 'Devon', 230),
(3832, 'Dial Code Dungannon', 230),
(3833, 'Didcot', 230),
(3834, 'Dorset', 230),
(3835, 'Dunbartonshire', 230),
(3836, 'Durham', 230),
(3837, 'East Dunbartonshire', 230),
(3838, 'East Lothian', 230),
(3839, 'East Midlands', 230),
(3840, 'East Sussex', 230),
(3841, 'East Yorkshire', 230),
(3842, 'England', 230),
(3843, 'Essex', 230),
(3844, 'Fermanagh', 230),
(3845, 'Fife', 230),
(3846, 'Flintshire', 230),
(3847, 'Fulham', 230),
(3848, 'Gainsborough', 230),
(3849, 'Glocestershire', 230),
(3850, 'Gwent', 230),
(3851, 'Hampshire', 230),
(3852, 'Hants', 230),
(3853, 'Herefordshire', 230),
(3854, 'Hertfordshire', 230),
(3855, 'Ireland', 230),
(3856, 'Isle Of Man', 230),
(3857, 'Isle of Wight', 230),
(3858, 'Kenford', 230),
(3859, 'Kent', 230),
(3860, 'Kilmarnock', 230),
(3861, 'Lanarkshire', 230),
(3862, 'Lancashire', 230),
(3863, 'Leicestershire', 230),
(3864, 'Lincolnshire', 230),
(3865, 'Llanymynech', 230),
(3866, 'London', 230),
(3867, 'Ludlow', 230),
(3868, 'Manchester', 230),
(3869, 'Mayfair', 230),
(3870, 'Merseyside', 230),
(3871, 'Mid Glamorgan', 230),
(3872, 'Middlesex', 230),
(3873, 'Mildenhall', 230),
(3874, 'Monmouthshire', 230),
(3875, 'Newton Stewart', 230),
(3876, 'Norfolk', 230),
(3877, 'North Humberside', 230),
(3878, 'North Yorkshire', 230),
(3879, 'Northamptonshire', 230),
(3880, 'Northants', 230),
(3881, 'Northern Ireland', 230),
(3882, 'Northumberland', 230),
(3883, 'Nottinghamshire', 230),
(3884, 'Oxford', 230),
(3885, 'Powys', 230),
(3886, 'Roos-shire', 230),
(3887, 'SUSSEX', 230),
(3888, 'Sark', 230),
(3889, 'Scotland', 230),
(3890, 'Scottish Borders', 230),
(3891, 'Shropshire', 230),
(3892, 'Somerset', 230),
(3893, 'South Glamorgan', 230),
(3894, 'South Wales', 230),
(3895, 'South Yorkshire', 230),
(3896, 'Southwell', 230),
(3897, 'Staffordshire', 230),
(3898, 'Strabane', 230),
(3899, 'Suffolk', 230),
(3900, 'Surrey', 230),
(3901, 'Sussex', 230),
(3902, 'Twickenham', 230),
(3903, 'Tyne and Wear', 230),
(3904, 'Tyrone', 230),
(3905, 'Utah', 230),
(3906, 'Wales', 230),
(3907, 'Warwickshire', 230),
(3908, 'West Lothian', 230),
(3909, 'West Midlands', 230),
(3910, 'West Sussex', 230),
(3911, 'West Yorkshire', 230),
(3912, 'Whissendine', 230),
(3913, 'Wiltshire', 230),
(3914, 'Wokingham', 230),
(3915, 'Worcestershire', 230),
(3916, 'Wrexham', 230),
(3917, 'Wurttemberg', 230),
(3918, 'Yorkshire', 230),
(3919, 'Alabama', 231),
(3920, 'Alaska', 231),
(3921, 'Arizona', 231),
(3922, 'Arkansas', 231),
(3923, 'Byram', 231),
(3924, 'California', 231),
(3925, 'Cokato', 231),
(3926, 'Colorado', 231),
(3927, 'Connecticut', 231),
(3928, 'Delaware', 231),
(3929, 'District of Columbia', 231),
(3930, 'Florida', 231),
(3931, 'Georgia', 231),
(3932, 'Hawaii', 231),
(3933, 'Idaho', 231),
(3934, 'Illinois', 231),
(3935, 'Indiana', 231),
(3936, 'Iowa', 231),
(3937, 'Kansas', 231),
(3938, 'Kentucky', 231),
(3939, 'Louisiana', 231),
(3940, 'Lowa', 231),
(3941, 'Maine', 231),
(3942, 'Maryland', 231),
(3943, 'Massachusetts', 231),
(3944, 'Medfield', 231),
(3945, 'Michigan', 231),
(3946, 'Minnesota', 231),
(3947, 'Mississippi', 231),
(3948, 'Missouri', 231),
(3949, 'Montana', 231),
(3950, 'Nebraska', 231),
(3951, 'Nevada', 231),
(3952, 'New Hampshire', 231),
(3953, 'New Jersey', 231),
(3954, 'New Jersy', 231),
(3955, 'New Mexico', 231),
(3956, 'New York', 231),
(3957, 'North Carolina', 231),
(3958, 'North Dakota', 231),
(3959, 'Ohio', 231),
(3960, 'Oklahoma', 231),
(3961, 'Ontario', 231),
(3962, 'Oregon', 231),
(3963, 'Pennsylvania', 231),
(3964, 'Ramey', 231),
(3965, 'Rhode Island', 231),
(3966, 'South Carolina', 231),
(3967, 'South Dakota', 231),
(3968, 'Sublimity', 231),
(3969, 'Tennessee', 231),
(3970, 'Texas', 231),
(3971, 'Trimble', 231),
(3972, 'Utah', 231),
(3973, 'Vermont', 231),
(3974, 'Virginia', 231),
(3975, 'Washington', 231),
(3976, 'West Virginia', 231),
(3977, 'Wisconsin', 231),
(3978, 'Wyoming', 231),
(3979, 'United States Minor Outlying I', 232),
(3980, 'Artigas', 233),
(3981, 'Canelones', 233),
(3982, 'Cerro Largo', 233),
(3983, 'Colonia', 233),
(3984, 'Durazno', 233),
(3985, 'FLorida', 233),
(3986, 'Flores', 233),
(3987, 'Lavalleja', 233),
(3988, 'Maldonado', 233),
(3989, 'Montevideo', 233),
(3990, 'Paysandu', 233),
(3991, 'Rio Negro', 233),
(3992, 'Rivera', 233),
(3993, 'Rocha', 233),
(3994, 'Salto', 233),
(3995, 'San Jose', 233),
(3996, 'Soriano', 233),
(3997, 'Tacuarembo', 233),
(3998, 'Treinta y Tres', 233),
(3999, 'Andijon', 234),
(4000, 'Buhoro', 234),
(4001, 'Buxoro Viloyati', 234),
(4002, 'Cizah', 234),
(4003, 'Fargona', 234),
(4004, 'Horazm', 234),
(4005, 'Kaskadar', 234),
(4006, 'Korakalpogiston', 234),
(4007, 'Namangan', 234),
(4008, 'Navoi', 234),
(4009, 'Samarkand', 234),
(4010, 'Sirdare', 234),
(4011, 'Surhondar', 234),
(4012, 'Toskent', 234),
(4013, 'Malampa', 235),
(4014, 'Penama', 235),
(4015, 'Sanma', 235),
(4016, 'Shefa', 235),
(4017, 'Tafea', 235),
(4018, 'Torba', 235),
(4019, 'Vatican City State (Holy See)', 236),
(4020, 'Amazonas', 237),
(4021, 'Anzoategui', 237),
(4022, 'Apure', 237),
(4023, 'Aragua', 237),
(4024, 'Barinas', 237),
(4025, 'Bolivar', 237),
(4026, 'Carabobo', 237),
(4027, 'Cojedes', 237),
(4028, 'Delta Amacuro', 237),
(4029, 'Distrito Federal', 237),
(4030, 'Falcon', 237),
(4031, 'Guarico', 237),
(4032, 'Lara', 237),
(4033, 'Merida', 237),
(4034, 'Miranda', 237),
(4035, 'Monagas', 237),
(4036, 'Nueva Esparta', 237),
(4037, 'Portuguesa', 237),
(4038, 'Sucre', 237),
(4039, 'Tachira', 237),
(4040, 'Trujillo', 237),
(4041, 'Vargas', 237),
(4042, 'Yaracuy', 237),
(4043, 'Zulia', 237),
(4044, 'Bac Giang', 238),
(4045, 'Binh Dinh', 238),
(4046, 'Binh Duong', 238),
(4047, 'Da Nang', 238),
(4048, 'Dong Bang Song Cuu Long', 238),
(4049, 'Dong Bang Song Hong', 238),
(4050, 'Dong Nai', 238),
(4051, 'Dong Nam Bo', 238),
(4052, 'Duyen Hai Mien Trung', 238),
(4053, 'Hanoi', 238),
(4054, 'Hung Yen', 238),
(4055, 'Khu Bon Cu', 238),
(4056, 'Long An', 238),
(4057, 'Mien Nui Va Trung Du', 238),
(4058, 'Thai Nguyen', 238),
(4059, 'Thanh Pho Ho Chi Minh', 238),
(4060, 'Thu Do Ha Noi', 238),
(4061, 'Tinh Can Tho', 238),
(4062, 'Tinh Da Nang', 238),
(4063, 'Tinh Gia Lai', 238),
(4064, 'Anegada', 239),
(4065, 'Jost van Dyke', 239),
(4066, 'Tortola', 239),
(4067, 'Saint Croix', 240),
(4068, 'Saint John', 240),
(4069, 'Saint Thomas', 240),
(4070, 'Alo', 241),
(4071, 'Singave', 241),
(4072, 'Wallis', 241),
(4073, 'Bu Jaydur', 242),
(4074, 'Wad-adh-Dhahab', 242),
(4075, 'al-\'Ayun', 242),
(4076, 'as-Samarah', 242),
(4077, '\'Adan', 243),
(4078, 'Abyan', 243),
(4079, 'Dhamar', 243),
(4080, 'Hadramaut', 243),
(4081, 'Hajjah', 243),
(4082, 'Hudaydah', 243),
(4083, 'Ibb', 243),
(4084, 'Lahij', 243),
(4085, 'Ma\'rib', 243),
(4086, 'Madinat San\'a', 243),
(4087, 'Sa\'dah', 243),
(4088, 'Sana', 243),
(4089, 'Shabwah', 243),
(4090, 'Ta\'izz', 243),
(4091, 'al-Bayda', 243),
(4092, 'al-Hudaydah', 243),
(4093, 'al-Jawf', 243),
(4094, 'al-Mahrah', 243),
(4095, 'al-Mahwit', 243),
(4096, 'Central Serbia', 244),
(4097, 'Kosovo and Metohija', 244),
(4098, 'Montenegro', 244),
(4099, 'Republic of Serbia', 244),
(4100, 'Serbia', 244),
(4101, 'Vojvodina', 244),
(4102, 'Central', 245),
(4103, 'Copperbelt', 245),
(4104, 'Eastern', 245),
(4105, 'Luapala', 245),
(4106, 'Lusaka', 245),
(4107, 'North-Western', 245),
(4108, 'Northern', 245),
(4109, 'Southern', 245),
(4110, 'Western', 245),
(4111, 'Bulawayo', 246),
(4112, 'Harare', 246),
(4113, 'Manicaland', 246),
(4114, 'Mashonaland Central', 246),
(4115, 'Mashonaland East', 246),
(4116, 'Mashonaland West', 246),
(4117, 'Masvingo', 246),
(4118, 'Matabeleland North', 246),
(4119, 'Matabeleland South', 246),
(4120, 'Midlands', 246),
(4121, 'Others', 247);

-- --------------------------------------------------------

--
-- Table structure for table `studentmessages`
--

CREATE TABLE `studentmessages` (
  `id` int(11) NOT NULL,
  `title` varchar(188) NOT NULL,
  `messages` varchar(600) NOT NULL,
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'unseen',
  `mfor` varchar(19) NOT NULL DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `fname` varchar(188) NOT NULL,
  `lname` varchar(188) NOT NULL,
  `mname` varchar(188) DEFAULT NULL,
  `dob` varchar(44) NOT NULL,
  `joindate` timestamp NOT NULL DEFAULT current_timestamp(),
  `department_id` int(11) NOT NULL,
  `class_arm_id` int(11) DEFAULT NULL,
  `olevelresulturl` varchar(188) NOT NULL,
  `jamb` int(11) DEFAULT NULL,
  `birthcerturl` varchar(188) DEFAULT NULL,
  `othercerts` varchar(188) DEFAULT NULL,
  `email` varchar(188) NOT NULL,
  `state_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `address` varchar(200) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `fathersname` varchar(188) NOT NULL,
  `mothersname` varchar(188) NOT NULL,
  `fatherphone` varchar(16) NOT NULL,
  `motherphone` varchar(16) NOT NULL,
  `lga_id` int(199) DEFAULT NULL,
  `community` varchar(188) DEFAULT NULL,
  `passporturl` varchar(199) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `regno` varchar(50) DEFAULT NULL,
  `jamb_notification` varchar(222) DEFAULT NULL,
  `jambresult` varchar(222) DEFAULT NULL,
  `jamb_admin_letter` varchar(222) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Applied',
  `admissiondate` varchar(54) DEFAULT NULL,
  `gender` varchar(32) NOT NULL,
  `application_no` varchar(66) DEFAULT NULL,
  `level_id` int(11) NOT NULL DEFAULT 1,
  `sparent_id` int(11) NOT NULL,
  `religion` varchar(200) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `jambregno` varchar(88) DEFAULT NULL,
  `previousschool` varchar(188) NOT NULL,
  `programme_id` int(11) NOT NULL,
  `fathersjob` varchar(120) DEFAULT NULL,
  `mothersjob` varchar(120) DEFAULT NULL,
  `studentstatus` varchar(44) DEFAULT NULL,
  `mode_id` int(11) NOT NULL,
  `universitymail` varchar(220) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `programetype_id` int(11) DEFAULT NULL,
  `duration_id` int(11) DEFAULT NULL,
  `landlocation` varchar(444) DEFAULT NULL,
  `landsize` varchar(222) DEFAULT NULL,
  `landowner` varchar(222) DEFAULT NULL,
  `landaccessurl` varchar(222) DEFAULT NULL,
  `session_id` int(11) NOT NULL DEFAULT 6,
  `isclaretian` varchar(14) NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option_id` int(11) DEFAULT NULL,
  `theory_answer` text DEFAULT NULL,
  `theory_score` int(11) DEFAULT NULL,
  `answered_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcategory`
--

CREATE TABLE `subcategory` (
  `s_c_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_name` varchar(255) NOT NULL,
  `subcategory_status` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `subjectcode` varchar(16) NOT NULL,
  `department_id` int(11) NOT NULL,
  `creditload` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  `semester_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `subjectcode`, `department_id`, `creditload`, `user_id`, `created_date`, `status`, `semester_id`, `level_id`) VALUES
(1, 'English Studies', 'ENG', 1, 0, 1, '2025-08-21 14:34:50', 1, 0, 0),
(2, 'Mathematics', 'MTH', 1, 0, 1, '2025-08-21 14:35:09', 1, 0, 0),
(9, 'English Language', 'ENG SSS', 2, 0, 1, '2025-08-26 12:05:14', 1, 0, 0),
(10, 'Mathematics', 'MTH SSS', 2, 0, 1, '2025-08-26 12:05:15', 1, 0, 0),
(11, 'Igbo Language', 'IGBO', 1, 0, 1, '2025-09-29 20:03:39', 1, 0, 0),
(12, 'Integrated Science', 'IS', 1, 0, 1, '2025-09-29 20:04:09', 1, 0, 0),
(13, 'English Studies', 'ES', 3, 0, 1, '2025-09-29 20:04:54', 1, 0, 0),
(14, 'P.H.E', 'PHE', 1, 0, 1, '2025-09-29 20:05:24', 1, 0, 0),
(15, 'Digital Technologies', 'DI', 1, 0, 1, '2025-09-29 20:05:42', 1, 0, 0),
(16, 'C.R.S', 'CRS', 1, 0, 1, '2025-09-29 20:06:00', 1, 0, 0),
(17, 'Nigerian History', 'NH', 1, 0, 1, '2025-09-29 20:06:20', 1, 0, 0),
(18, 'Social & Citizenship Studies', 'SCS', 1, 0, 1, '2025-09-29 20:06:40', 1, 0, 0),
(19, 'CCA - Fine Arts', 'FA', 1, 0, 1, '2025-09-29 20:07:01', 1, 0, 0),
(20, 'Music', 'M', 1, 0, 1, '2025-09-29 20:07:17', 1, 0, 0),
(21, 'Business Studies', 'BS', 1, 0, 1, '2025-09-29 20:07:33', 1, 0, 0),
(22, 'French', 'F', 1, 0, 1, '2025-09-29 20:07:50', 1, 0, 0),
(23, 'Live Stock Farming', 'LSF', 1, 0, 1, '2025-09-29 20:09:28', 1, 0, 0),
(24, 'Beauty & Cosmetology', 'BC', 1, 0, 1, '2025-09-29 20:09:49', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `subjects_students`
--

CREATE TABLE `subjects_students` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects_teachers`
--

CREATE TABLE `subjects_teachers` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplies`
--

CREATE TABLE `supplies` (
  `id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `vendor` varchar(144) NOT NULL,
  `date` date NOT NULL,
  `invoiceid` varchar(44) NOT NULL,
  `libbook_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gender` varchar(8) NOT NULL,
  `address` varchar(255) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `profile` varchar(255) NOT NULL,
  `cv` varchar(128) DEFAULT NULL,
  `qualification` varchar(16) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `passport` varchar(156) DEFAULT NULL,
  `firstname` varchar(188) NOT NULL,
  `lastname` varchar(188) NOT NULL,
  `middlename` varchar(188) DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `staffgrade_id` int(11) NOT NULL,
  `staffdepartment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `gender`, `address`, `country_id`, `state_id`, `phone`, `profile`, `cv`, `qualification`, `date_created`, `passport`, `firstname`, `lastname`, `middlename`, `department_id`, `staffgrade_id`, `staffdepartment_id`) VALUES
(1, 3, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'CHUKWUMA', 'ANN', NULL, 0, 0, 0),
(2, 4, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'IDEBA', 'ONYINYECHUKWU', 'MIRIAM', 0, 0, 0),
(3, 5, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'MBATA', 'AMARACHI', NULL, 0, 0, 0),
(4, 6, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'NWANERI', 'KASOM', 'SANDRA', 0, 0, 0),
(5, 7, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'EMERENIN', 'N', 'REGINA', 0, 0, 0),
(6, 8, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'UGWUEGBU', 'CHIAMAKA', 'VIVIAN', 0, 0, 0),
(7, 9, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'NGOZI', 'NWANKWO', NULL, 0, 0, 0),
(8, 10, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'BETTY', 'KEN', NULL, 0, 0, 0),
(9, 11, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'CHUKWUEMEKA', 'C', NULL, 0, 0, 0),
(10, 12, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'CHUKWUNYERE', 'FAVOUR', NULL, 0, 0, 0),
(11, 13, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'UKPABIO', 'HOSSANA', NULL, 0, 0, 0),
(12, 14, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'MIRABLE', 'CHIMA', 'CHIZURUM', 0, 0, 0),
(13, 15, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'UBA', 'DEBORAH', NULL, 0, 0, 0),
(14, 16, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', '', NULL, 'BSc', '2025-10-05 09:13:07', NULL, 'OBI', 'CHIOMA', 'JOY', 0, 0, 0),
(15, 10, 'Male', 'Heartland Estate Owerri', 160, 0, '08089898983', 'Good profile', '05_10_25_11_39_3568e258f74dd4d_Results.pdf', 'BSc', '2025-10-05 11:39:35', '05_10_25_11_39_3568e258f74755e_school_stamp.png', 'BETTY', 'KEN', NULL, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

CREATE TABLE `timetables` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `timetable` text NOT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `contents` longtext NOT NULL,
  `user_id` int(11) NOT NULL,
  `uploaddate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedon` varchar(44) DEFAULT NULL,
  `title` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `transdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` varchar(18) NOT NULL,
  `paystatus` varchar(44) NOT NULL DEFAULT 'initialized',
  `payref` varchar(188) NOT NULL,
  `gresponse` varchar(88) NOT NULL DEFAULT 'initialized',
  `session_id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `pgateway` varchar(60) DEFAULT NULL,
  `paymentlogid` varchar(222) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trequests`
--

CREATE TABLE `trequests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `orderdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `institution` varchar(344) NOT NULL,
  `status` varchar(44) NOT NULL DEFAULT 'applied',
  `continent_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `address` varchar(244) NOT NULL,
  `courier_id` int(11) NOT NULL,
  `amount` varchar(44) NOT NULL,
  `deliverystatus` varchar(44) DEFAULT 'awaiting',
  `fee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userlogins`
--

CREATE TABLE `userlogins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `logintime` timestamp NOT NULL DEFAULT current_timestamp(),
  `logouttime` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `userlogins`
--

INSERT INTO `userlogins` (`id`, `user_id`, `logintime`, `logouttime`) VALUES
(1, 10, '2025-10-05 09:15:41', '2025-10-05 09:21:53'),
(2, 10, '2025-10-05 09:21:53', '2025-10-05 11:16:45'),
(3, 1, '2025-10-05 10:59:42', '2025-10-05 12:29:22'),
(4, 36, '2025-10-05 11:00:43', '2025-10-05 11:14:55'),
(5, 10, '2025-10-05 11:16:45', '2025-10-05 12:27:19'),
(6, 1, '2025-10-05 12:29:22', '0000-00-00 00:00:00'),
(7, 2, '2025-10-05 12:29:46', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role_id` int(11) NOT NULL,
  `fname` varchar(64) NOT NULL,
  `lname` varchar(64) NOT NULL,
  `mname` varchar(64) DEFAULT NULL,
  `gender` varchar(15) NOT NULL,
  `address` varchar(250) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `department_id` int(11) NOT NULL,
  `profile` text DEFAULT NULL,
  `dob` varchar(64) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `passport` varchar(128) DEFAULT NULL,
  `useruniquid` varchar(28) NOT NULL,
  `userstatus` varchar(30) NOT NULL DEFAULT 'Enabled',
  `verification_key` varchar(188) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL COMMENT '6-digit OTP code for password reset',
  `otp_expires` datetime DEFAULT NULL COMMENT 'OTP expiration timestamp'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `fname`, `lname`, `mname`, `gender`, `address`, `country_id`, `state_id`, `phone`, `department_id`, `profile`, `dob`, `created_date`, `created_by`, `passport`, `useruniquid`, `userstatus`, `verification_key`, `otp_code`, `otp_expires`) VALUES
(1, 'chukwudi.aniegboka@netpro.africa', '$2y$10$U2fSzQbHsHmDge9s264esOYHYa79SqNjL5MEVwX5MwPRolXUFkp36', 5, 'Chukwudi', 'Aniegboka', NULL, 'Male', 'Lagos, Nigeria', 160, 193, '080', 0, NULL, NULL, '2025-10-05 09:09:09', 0, NULL, '', 'Enabled', NULL, NULL, NULL),
(2, 'admin@tss.sch.ng', '$2y$10$U2fSzQbHsHmDge9s264esOYHYa79SqNjL5MEVwX5MwPRolXUFkp36', 1, 'School', 'Administrator', NULL, 'Male', 'Owerri, Imo State', 160, 193, '080', 0, NULL, NULL, '2025-10-05 09:11:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(3, 'chukwuann2023@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'CHUKWUMA', 'ANN', NULL, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(4, 'miriamonyii2023@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'IDEBA', 'ONYINYECHUKWU', 'MIRIAM', 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(5, 'amaramba111@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'MBATA', 'AMARACHI', NULL, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(6, 'sandrakas111@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'NWANERI', 'KASOM', 'SANDRA', 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(7, 'reginaeme111@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'EMERENIN', 'N', 'REGINA', 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(8, 'vivianchi111@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'UGWUEGBU', 'CHIAMAKA', 'VIVIAN', 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(9, 'ngozi24@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'NGOZI', 'NWANKWO', NULL, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(10, 'betty24@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'BETTY', 'KEN', NULL, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, '05_10_25_11_39_3568e258f74755e_school_stamp.png', '', 'Enabled', NULL, NULL, NULL),
(11, 'mercy24@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'CHUKWUEMEKA', 'C', NULL, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(12, 'chukwunyeref2024@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'CHUKWUNYERE', 'FAVOUR', NULL, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(13, 'hossana2024@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'UKPABIO', 'HOSSANA', NULL, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(14, 'mirablechizzy93@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'MIRABLE', 'CHIMA', 'CHIZURUM', 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(15, 'ubadeborah2025@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'UBA', 'DEBORAH', NULL, 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(16, 'obijoy2023@gmail.com', '$2y$10$yY.aHu3Eb6Bf1YBr2vyA.uC5uURRkM/eHBy.gaSpbimx4WD7KXX7a', 3, 'OBI', 'CHIOMA', 'JOY', 'Female', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1980-01-01', '2025-10-05 09:13:07', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(17, 'anderson121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANDERSON', 'UDO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(18, 'arinze121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ARINZECHUKWU', 'ARINZECHUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(19, 'Chuks121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUEMEKA', 'CHUKWUEMEKA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(20, 'Chukwuma121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUMA', 'CHUKWUMA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(21, 'Duru121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'DURU', 'DURU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(22, 'Ejiogu121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EJIOGU', 'EJIOGU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(23, 'Manuel121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EMMANUEL', 'EMMANUEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(24, 'Ibe121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IBEZIM', 'IBEZIM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(25, 'ILONAH121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ILONAH', 'ILONAH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(26, 'nna121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NNANNA', 'NNANNA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(27, 'nwoke121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NWOKEFORO', 'NWOKEFORO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(28, 'nzeadi121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NZEADI', 'NZEADI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(29, 'Oguzie121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, '&', 'OGUZIE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(30, 'udedibia@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UDEDIBIA', 'UDEDIBIA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(31, 'umunna121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UMUNNA', 'UMUNNA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(32, 'Chigo121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIGOZIE', 'CHIGOZIE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(33, 'emerole121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EMEROLE', 'EMEROLE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:21', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(34, 'Etok121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ETOK', 'ETOK', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(35, 'iloh21@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ILOH', 'ILOH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(36, 'meeg121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, '&MEEGHAEEL', '&MEEGHAEEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(37, 'ndukuba121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NDUKAUBA', 'BRYAN', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(38, 'Nwosu121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NWOSU', 'NWOSU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(39, 'okafor111@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKAFOR', 'OKAFOR', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(40, 'opara111@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OPARA', 'OPARA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(41, 'UDU111@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UDUSHIRINWA', 'UDUSHIRINWA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(42, 'anyanwu121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANYANWU', 'ANYANWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(43, 'Amadi111@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AMADI', 'AMADI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(44, 'Collin111@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'COLLINS', 'COLLINS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(45, 'Ekeh121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EKEH', 'EKEH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(46, 'Chinedu121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHINEDU', 'CHINEDU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(47, 'ify121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IFEANYI-AMADI', 'IFEANYI-AMADI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(48, 'John121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'JOHN', 'JOHN', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(49, 'Nkem121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NKEMAKOLAM', 'NKEMAKOLAM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(50, 'ugboaga121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UGBOAGA', 'UGBOAGA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(51, 'uka121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UKAEGBU', 'UKAEGBU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(52, 'Sam121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'SAMUEL', 'SAMUEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(53, 'Agbara121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AGBARA', 'AGBARA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(54, 'Asibie121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ASIBIE', 'ASIBIE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(55, 'Chibueze121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIBUEZE', 'CHIBUEZE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(56, 'Emma51@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EMMANUEL', 'EMMANUEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(57, 'Nnawuihe121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NNAWUIHE', 'NNAWUIHE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(58, 'Okere121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKERE', 'OKERE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(59, 'okechukwu51@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKECHUKWU', 'OKECHUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(60, 'woko51@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'WOKO', 'WOKO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(61, 'achinike121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ACHINIKE', 'ACHINIKE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(62, 'cole121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'COLE', 'COLE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(63, 'CHUKS87@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUEMEKA', 'CHUKWUEMEKA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(64, 'isaac121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ISAAC', 'ISAAC', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(65, 'jacob121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'JACOB', 'JACOB', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(66, 'ifeanyi87@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IFEANYI', 'IFEANYI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(67, 'ndubuikwo121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NDUBUIKWO', 'NDUBUIKWO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(68, 'oyeje11@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OYEJELAM', 'OYEJELAM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(69, 'theodore121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'THEODORE', 'THEODORE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(70, 'Akajiaku121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AKAJIAKU', 'AKAJIAKU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(71, 'abala57@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ABALANNE', 'ABALANNE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(72, 'Chikwado121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIKWADO', 'CHIKWADO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(73, 'chukwuemeka77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUEMEKA', 'CHUKWUEMEKA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(74, 'ibeawuchi121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IBEAWUCHI', 'IBEAWUCHI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(75, 'modestus121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'MODESTUS', 'MODESTUS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(76, 'Neslon12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NELSON', 'NELSON', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(77, 'Obinna121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OBINNA', 'OBINNA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(78, 'Kabriri56@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'KABIRI', 'KABIRI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(79, 'Uchenna121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UCHENNA', 'UCHENNA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(80, 'henry121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'HENRY', 'HENRY', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(81, 'Umunna77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UMUNNA', 'UMUNNA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(82, 'acholonu121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ACHOLONU', 'ACHOLONU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(83, 'Akabu121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AKABUOGU', 'AKABUOGU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(84, 'Chris77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHRISTOPHER', 'CHRISTOPHER', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(85, 'Chukwueke121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUEKE', 'CHUKWUEKE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(86, 'daniel121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'DANIEL', 'DANIEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(87, 'Dimeke77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'DIMEKE', 'DIMEKE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(88, 'Ekeh77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EKEH', 'EKEH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(89, 'Ideba121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IDEBA', 'IDEBA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(90, 'Kalu88@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'KALU', 'KALU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(91, 'Obiaku111@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OBIAKU', 'OBIAKU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(92, 'Obioma23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OBIOMA', 'OBIOMA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(93, 'Okey77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKEKA', 'OKEKA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(94, 'Ozoemena121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OZOEMENA', 'OZOEMENA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(95, 'Sam86@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'SAMUEL', 'SAMUEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(96, 'Nwane89@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NWANEKEZIE', 'NWANEKEZIE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(97, 'Aham23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AHAM', 'AHAM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(98, 'Ajoku121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AJOKU', 'AJOKU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(99, 'Annah121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANNAH', 'ANNAH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(100, 'Jacob55@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'JACOB', 'JACOB', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(101, 'Kind121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'KINDNESS', 'KINDNESS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(102, 'Nwaosu88@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NWAOSU', 'NWAOSU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(103, 'Maduagwu121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'JOHN', 'MADUAGWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(104, 'Marcus121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'MARCUS', 'MARCUS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(105, 'Opara89@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OPARA', 'OPARA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(106, 'Onuoha99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ONUOHA', 'ONUOHA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(107, 'Jude99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'JUDE', 'JUDE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(108, 'oham121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OHAMEZU', 'OHAMEZU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(109, 'Richards121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'RICHARDS', 'RICHARDS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(110, 'Nwaorgu17@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NWORGU', 'NWORGU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(111, 'chibu88@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIBUZOR', 'CHIBUZOR', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(112, 'ibekwe22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IBEKWE', 'IBEKWE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(113, 'Alfred88@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ALFRED', 'ALFRED', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(114, 'Ogbonna22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OGBONNA', 'OGBONNA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(115, 'Oscar44@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OSCAR', 'OSCAR', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(116, 'Ugwuegbu12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UGWUEGBU', 'UGWUEGBU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(117, 'Vickrez22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'VICTOR', 'VICTOR', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(118, 'Onyeike22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ONYEIKE', 'ONYEIKE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(119, 'Mos112@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'MOSES', 'MOSES', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(120, 'Ugwu76@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UGWUEGBU', 'UGWUEGBU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(121, 'Nwadike22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NWADIKE', 'NWADIKE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(122, 'Chidi99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIDIEBERE', 'CHIDIEBERE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(123, 'Ndukuba88@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NDUKUBA', 'NDUKUBA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(124, 'Nwaneri70@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NWANERI', 'NWANERI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(125, 'Emma100@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EMMANUEL', 'EMMANUEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(126, 'Olueze45@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OLUEZE', 'OLUEZE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(127, 'Unamba78@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UNAMBA', 'UNAMBA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(128, 'Ekeigwe23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EKEIGWE', 'EKEIGWE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(129, 'Ifeanyi22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IFEANYICHUKWU', 'IFEANYICHUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(130, 'Uwandu28@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UWANDU', 'UWANDU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(131, 'Ezeribe45@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EZERIBE', 'EZERIBE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(132, 'Frank22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'FRANKLYN', 'CHERISH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(133, 'ANYANWU112@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANYANWU', 'ANYANWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(134, 'mara24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AMARACHUKWU', 'AMARACHUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(135, 'Eze77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EZE', 'EZE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(136, 'Osuji22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OSUJI', 'OSUJI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(137, 'bonny12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'BONIFCAE', 'BONIFCAE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(138, 'chimex14@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIMENKA', 'CHIMENKA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(139, 'duru89@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'DURU', 'DURU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(140, 'Alfred77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ALFRED', 'ALFRED', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(141, 'Agaziem34@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AGAZIEM', 'AGAZIEM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(142, 'Moses22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'MOSES', 'MOSES', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(143, 'hamza44@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'HAMZA', 'HAMZA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(144, 'Agbara90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AGBARA', 'AGBARA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(145, 'Eric22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ERIC', 'ERIC', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(146, 'Chikwado99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIKWADO', 'CHIKWADO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(147, 'Collins90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'COLLINS', 'COLLINS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(148, 'Ander23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANDERSON', 'ANDERSON', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(149, 'Mirabel22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'MIRABEL', 'MIRABEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(150, 'Excel46@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EXCEL', 'EXCEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(151, 'chukwu90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUDI', 'CHUKWUDI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(152, 'Umunna56@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UMUNNA', 'UMUNNA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(153, 'Buzor44@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIBUZOR', 'CHIBUZOR', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(154, 'Ifeco22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IFEANYI', 'IFEANYI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(155, 'Mbah87@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'MBAH', 'MBAH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(156, 'Amadi24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, '&', 'AMADI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(157, 'Anyanwu18@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANYANWU', 'ANYANWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(158, 'egwu44@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANYADIEGWU', 'ANYADIEGWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(159, 'Agbara80@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AGBARA', 'AGBARA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(160, 'Chuks92@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUMA', 'CHUKWUMA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(161, 'chikky24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUEMEKA', 'CHUKWUEMEKA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(162, 'Emea77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EMEA', 'EMEA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(163, 'Ezeribe96@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EZERIBE', 'EZERIBE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(164, 'Linus56@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'LINUS', 'LINUS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(165, 'Nzeh78@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NZE', 'NZE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(166, 'ocha22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NNAMOCHA', 'NNAMOCHA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(167, 'Ndu24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NDUKWU', 'NDUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(168, 'Ohannu1@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OHANU', 'OHANU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(169, 'Okere90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKERE', 'OKERE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(170, 'Ukaegbu23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UKAEGBU', 'UKAEGBU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(171, 'Uhuegbu76@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UHUEGBU', 'UHUEGBU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(172, 'soro98@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANYASORO', 'ANYASORO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(173, 'Topher90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHRISTOPHER', 'CHRISTOPHER', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(174, 'Bert45@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ETHELBERT', 'ETHELBERT', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(175, 'nnan24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NNANNA', 'NNANNA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(176, 'Nnorom45@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NNOROM', 'NNOROM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(177, 'Achi76@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ACHINEIKE', 'ACHINEIKE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(178, 'Amaefula90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AMAEFULA', 'AMAEFULA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(179, 'Chikwa70@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIKWADO', 'CHIKWADO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(180, 'Nedu11@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHINEDU', 'CHINEDU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(181, 'Chukky114@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUDI', 'CHUKWUDI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(182, 'Emi12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHUKWUEMEKA', 'CHUKWUEMEKA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(183, 'Dabbs34@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'DABERECHI', 'DABERECHI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(184, 'Evans56@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EVANS', 'EVANS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(185, 'Madukwe27@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'MADUKWE', 'MADUKWE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(186, 'Naze77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NAZEGBULAM', 'NAZEGBULAM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(187, 'Odom44@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ODOM', 'ODOM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(188, 'Okeke88@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKEKE', 'OKEKE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(189, 'oke99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKECHUKWU', 'OKECHUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(190, 'Opara11@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OPARA', 'OPARA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(191, 'Amara56@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AMARACHI', 'AMARACHI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL);
INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `fname`, `lname`, `mname`, `gender`, `address`, `country_id`, `state_id`, `phone`, `department_id`, `profile`, `dob`, `created_date`, `created_by`, `passport`, `useruniquid`, `userstatus`, `verification_key`, `otp_code`, `otp_expires`) VALUES
(192, 'Emenyonu12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EMENYONU', 'EMENYONU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(193, 'Ezem99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EZEM', 'EZEM', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(194, 'Pet23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'GERALD-PETERS', 'GERALD-PETERS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(195, 'IBEH90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IBEKWE', 'IBEKWE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(196, 'njo21@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NJOKU', 'NJOKU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(197, 'oju24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OJUKWU', 'OJUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(198, 'okeke100@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKEKE', 'OKEKE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(199, 'Onwuadi23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ONWUADIHA', 'ONWUADIHA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(200, 'ADIN24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ADINDU', 'ADINDU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(201, 'Akabu79@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AKABUOGU', 'AKABUOGU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(202, 'Lewechi12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANYAWELECHI', 'ANYAWELECHI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(203, 'Cali71@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CALLISTUS', 'CALLISTUS', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(204, 'Chikwe43@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHIKWE', 'CHIKWE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(205, 'Obilor45@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OBILOR', 'OBILOR', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(206, 'Kafor42@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKAFOR', 'OKAFOR', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(207, 'sonye11@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ONWUSONYE', 'ONWUSONYE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(208, 'Kab90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'KABIRI', 'KABIRI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(209, 'Chilaka22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'CHILAKA', 'CHILAKA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(210, 'Anah70@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANAH', 'ANAH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(211, 'Enyioha24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ENYIOHA', 'ENYIOHA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(212, 'ONYE22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ONYEKURU', 'ONYEKURU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(213, 'Nnam51@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NNAMOCHA', 'NNAMOCHA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(214, 'Nwoko12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NWOKORIE', 'NWOKORIE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(215, 'Ugo99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UGOCHUKWU', 'UGOCHUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(216, 'Dick79@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'DICK', 'DICK', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(217, 'Okoreaffia91@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OKOREAFFIA', 'RUTH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(218, 'Efula12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AHAMEFULA', 'AHAMEFULA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(219, 'Iwori24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IWORISOU', 'IWORISOU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(220, 'Chege19@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ONYECHEGE', 'ONYECHEGE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(221, 'Akalezi25@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'AKALEZI', 'AKALEZI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(222, 'Diegwu25@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ANYADIEGWU', 'ANYADIEGWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(223, 'BEDE77@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'BEDE', 'BEDE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(224, 'Dann99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'DANIEL', 'DANIEL', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(225, 'Enyeribe20@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ENYERIBE', 'ENYERIBE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(226, 'Ifyamadi@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IFEANYI-AMADI', 'IFEANYI-AMADI', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(227, 'Ijioma22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'IJIOMA', 'IJIOMA', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(228, 'Madukwe99@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'MADUKWE', 'MADUKWE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(229, 'Melvin24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'NZEH', 'NZEH', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:22', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(230, 'Amaku25@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'ONYEAMAKU', 'ONYEAMAKU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(231, 'ugochu11@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'UGOCHUKWU', 'UGOCHUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(232, 'toch11@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'TOCHUKWU', 'TOCHUKWU', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(233, 'solo24@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'SOLOMON', 'SOLOMON', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(234, 'eze90@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EZE', 'EZE', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(235, 'wilson@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'WILSON', 'WILSON', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(236, 'Ezeigbo@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'EZEIGBO', 'EZEIGBO', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(237, 'anoruo135@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Anoruo', 'Anoruo', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(238, 'emerole636@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Emerole', 'Emerole', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(239, 'ekeigwe2020@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ekeigwe', 'Ekeigwe', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(240, 'christopher@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Christopher', 'Christopher', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(241, 'ndukuba2019@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ndukuba', 'Ndukuba', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(242, 'okonkwo49@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Okonkwo', 'Okonkwo', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(243, 'thompson1946@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Thompson', 'Thompson', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(244, 'ugochukwn49@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ugochukwu', 'Ugochukwu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(245, 'amadibb19@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Amadi', 'B', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(246, 'adindu364@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Adindu', 'Adindu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(247, 'christopheroo12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Christopher', 'O', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(248, 'emmanuel0909@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Emmanuel', 'Ch', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(249, 'franklin410@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Franklin', 'Franklin', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(250, 'yioma3026@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Yioma', 'Yioma', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(251, 'ibeagu995@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ibeagu', 'Ibeagu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(252, 'isaac4012@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Isaac', 'Isaac', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(253, 'johnee@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'John', 'E', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(254, 'jude2121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Jude', 'Jude', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(255, 'linus1700@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Linus', 'Linus', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(256, 'nwaneri342@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Nwaneri', 'Nwaneri', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(257, 'oguzie123@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Oguzie', 'Oguzie', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(258, 'ozurumba32@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ozurumba', 'Ozurumba', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(259, 'ozumba435@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ozumba', 'Ozumba', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(260, 'donatus45@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Donatus', 'Donatus', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(261, 'mbamala76@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Mbamala', 'Mbamala', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(262, 'ebere144@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ebere', 'Ebere', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(263, 'albert63@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Albert', 'Albert', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(264, 'egwim65@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Egwim', 'Egwim', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(265, 'achilonu57@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Achilonu', 'Achilonu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(266, 'anumudu133@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Anumudu', 'Anumudu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(267, 'augustine98@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Augustine', 'Augustine', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(268, 'boniface23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Boniface', 'Boniface', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(269, 'cyril67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Cyril', 'Cyril', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(270, 'danieldd133@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Daniel', 'D', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(271, 'iloh2019@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Iloh', 'Iloh', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(272, 'leonard67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Leonard', 'Leonard', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(273, 'marcus12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Marcus', 'Marcus', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(274, 'ndukauba45@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ndukauba', 'Ndukauba', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(275, 'nwachukwu67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Nwachukwu', 'Nwachukwu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(276, 'okerekech67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Okereke', 'Ch', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(277, 'theodore23@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Theodore', 'Theodore', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(278, 'ugwuegbu66@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ugwuegbu', 'Ugwuegbu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(279, 'victor676@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Victor', 'Victor', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(280, 'agaziem54@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Agaziem', 'Agaziem', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(281, 'anyanwu34@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Anyanwu', 'Anyanwu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(282, 'cyrilch34@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Cyril', 'Ch', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(283, 'duru87@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Duru', 'Duru', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(284, 'eke2014@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Eke', 'Eke', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(285, 'eze7535@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Eze', 'Eze', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(286, 'kamdirichukwn67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Kamdirichukwn', 'Kamdirichukwn', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(287, 'kanu54@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Kanu', 'Kanu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(288, 'emmanuelchuk01@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Emmanuel', 'CHUK', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(289, 'okekess@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Okeke', 'S', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(290, 'paul67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Paul', 'Paul', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(291, 'nzed45@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Nzeh', 'Nzeh', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(292, 'thaddeus67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Thaddeus', 'Thaddeus', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(293, 'ugwuegbudd56@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ugwuegbu', 'D', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(294, 'ukoha57@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ukoha', 'Ukoha', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(295, 'chigoziem67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Chigoziem', 'Chigoziem', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(296, 'onyeanu14@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Onyeanu', 'Onyeanu', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(297, 'chukwuemekaccc637@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Chukwuemeka', 'Chi', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(298, 'onwusonye12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Onwusonye', 'Onwusonye', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(299, 'eken612@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ekeh', 'Ekeh', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(300, 'onyeanumm74@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Onyeanu', 'M', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(301, 'alozieojizz11@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Alozie-oji', 'Jide', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(302, 'ebererr12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ebere', 'E', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(303, 'henryakpachi2014@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Henry-akpa', 'chi', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(304, 'onyewuchi22@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Onyewuchi', 'Onyewuchi', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(305, 'nwachukwnss12@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Nwachukwu', 'S', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(306, 'alozieojizz41@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Alozie-oji', 'N', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(307, 'Eleanyacc@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Eleanya', 'Chi', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(308, 'ojehdd56@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ojeh', 'D', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(309, 'ibe2019@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ibe', 'Ibe', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(310, 'onyemaobi67@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Onyemaobi', 'Onyemaobi', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(311, 'ayegba310@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ayegba', 'Ayegba', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(312, 'njoku514@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Njoku', 'Njoku', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(313, 'Jnjoku121@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Njoku', 'J', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(314, 'michelle120@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Michelle', 'Michelle', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(315, 'nmarcus123@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Marcus', 'N', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(316, 'zokonkwo617@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Okonkwo', 'Z', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(317, 'obika123@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Obika', 'Obika', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(318, 'ahumibe76@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'Ahumibe', 'Ahumibe', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL),
(319, 'oparac2025@gmail.com', '$2y$10$UNytS3qutUN3/ixqstb/neIAbxGf5m0T98NQ3prXaMeTLh/QCp4Rm', 4, 'OPARA', 'C.', NULL, 'Male', 'Owerri, Imo State', 160, 1, '08000000000', 0, '', '1970-01-01', '2025-10-05 10:59:23', 1, NULL, '', 'Enabled', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `position_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins_privileges`
--
ALTER TABLE `admins_privileges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admisionconditions`
--
ALTER TABLE `admisionconditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approvedresults`
--
ALTER TABLE `approvedresults`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_setassignment_submission` (`setassignment_id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `class_group` (`department_id`),
  ADD KEY `attendance_date` (`attendance_date`),
  ADD KEY `class_arm_id` (`class_arm_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrowedbooks`
--
ALTER TABLE `borrowedbooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cafcredit`
--
ALTER TABLE `cafcredit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cafmenu`
--
ALTER TABLE `cafmenu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cafsales`
--
ALTER TABLE `cafsales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `character_developments`
--
ALTER TABLE `character_developments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_session_semester` (`student_id`,`session_id`,`semester_id`),
  ADD KEY `fk_character_student` (`student_id`),
  ADD KEY `fk_character_session` (`session_id`),
  ADD KEY `fk_character_semester` (`semester_id`);

--
-- Indexes for table `class_arms`
--
ALTER TABLE `class_arms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_arm_per_department` (`department_id`,`arm_name`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `class_teacher_id` (`class_teacher_id`);

--
-- Indexes for table `constants`
--
ALTER TABLE `constants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `continents`
--
ALTER TABLE `continents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `couriers`
--
ALTER TABLE `couriers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courseassignments`
--
ALTER TABLE `courseassignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courseassignments_subjects`
--
ALTER TABLE `courseassignments_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coursematerials`
--
ALTER TABLE `coursematerials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courseregistrations`
--
ALTER TABLE `courseregistrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courseregistrations_subjects`
--
ALTER TABLE `courseregistrations_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments_fees`
--
ALTER TABLE `departments_fees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments_levels`
--
ALTER TABLE `departments_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments_programes`
--
ALTER TABLE `departments_programes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments_programmes`
--
ALTER TABLE `departments_programmes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments_semesters`
--
ALTER TABLE `departments_semesters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments_subjects`
--
ALTER TABLE `departments_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `durations`
--
ALTER TABLE `durations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eresources`
--
ALTER TABLE `eresources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feeallocations`
--
ALTER TABLE `feeallocations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_levels`
--
ALTER TABLE `fees_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees_students`
--
ALTER TABLE `fees_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostelrooms`
--
ALTER TABLE `hostelrooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostelrooms_students`
--
ALTER TABLE `hostelrooms_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostels`
--
ALTER TABLE `hostels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lastregs`
--
ALTER TABLE `lastregs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `letters`
--
ALTER TABLE `letters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lgas`
--
ALTER TABLE `lgas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `libbooks`
--
ALTER TABLE `libbooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `libborrow`
--
ALTER TABLE `libborrow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `libsettings`
--
ALTER TABLE `libsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modes`
--
ALTER TABLE `modes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paylogs`
--
ALTER TABLE `paylogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programes`
--
ALTER TABLE `programes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programetypes`
--
ALTER TABLE `programetypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programmes`
--
ALTER TABLE `programmes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setassignment_id` (`setassignment_id`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_arm_id` (`class_arm_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setassignments`
--
ALTER TABLE `setassignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sparents`
--
ALTER TABLE `sparents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sparents_students`
--
ALTER TABLE `sparents_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sponsorshippayments`
--
ALTER TABLE `sponsorshippayments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sponsorships`
--
ALTER TABLE `sponsorships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sponsorships_students`
--
ALTER TABLE `sponsorships_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staffdepartments`
--
ALTER TABLE `staffdepartments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staffgrades`
--
ALTER TABLE `staffgrades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staffmessages`
--
ALTER TABLE `staffmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `studentmessages`
--
ALTER TABLE `studentmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_arm_id` (`class_arm_id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_option_id` (`selected_option_id`);

--
-- Indexes for table `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`s_c_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects_students`
--
ALTER TABLE `subjects_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects_teachers`
--
ALTER TABLE `subjects_teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplies`
--
ALTER TABLE `supplies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trequests`
--
ALTER TABLE `trequests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userlogins`
--
ALTER TABLE `userlogins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admins_privileges`
--
ALTER TABLE `admins_privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `admisionconditions`
--
ALTER TABLE `admisionconditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `approvedresults`
--
ALTER TABLE `approvedresults`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `borrowedbooks`
--
ALTER TABLE `borrowedbooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cafcredit`
--
ALTER TABLE `cafcredit`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `cafmenu`
--
ALTER TABLE `cafmenu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cafsales`
--
ALTER TABLE `cafsales`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `character_developments`
--
ALTER TABLE `character_developments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_arms`
--
ALTER TABLE `class_arms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `constants`
--
ALTER TABLE `constants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `continents`
--
ALTER TABLE `continents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `couriers`
--
ALTER TABLE `couriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courseassignments`
--
ALTER TABLE `courseassignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courseassignments_subjects`
--
ALTER TABLE `courseassignments_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coursematerials`
--
ALTER TABLE `coursematerials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courseregistrations`
--
ALTER TABLE `courseregistrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courseregistrations_subjects`
--
ALTER TABLE `courseregistrations_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments_fees`
--
ALTER TABLE `departments_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `departments_levels`
--
ALTER TABLE `departments_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments_programes`
--
ALTER TABLE `departments_programes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments_programmes`
--
ALTER TABLE `departments_programmes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments_semesters`
--
ALTER TABLE `departments_semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments_subjects`
--
ALTER TABLE `departments_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `durations`
--
ALTER TABLE `durations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eresources`
--
ALTER TABLE `eresources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `feeallocations`
--
ALTER TABLE `feeallocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fees_levels`
--
ALTER TABLE `fees_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_students`
--
ALTER TABLE `fees_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hostelrooms`
--
ALTER TABLE `hostelrooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hostelrooms_students`
--
ALTER TABLE `hostelrooms_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hostels`
--
ALTER TABLE `hostels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lastregs`
--
ALTER TABLE `lastregs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `letters`
--
ALTER TABLE `letters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lgas`
--
ALTER TABLE `lgas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=893;

--
-- AUTO_INCREMENT for table `libbooks`
--
ALTER TABLE `libbooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `libborrow`
--
ALTER TABLE `libborrow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `libsettings`
--
ALTER TABLE `libsettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `modes`
--
ALTER TABLE `modes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `paylogs`
--
ALTER TABLE `paylogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privileges`
--
ALTER TABLE `privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `programes`
--
ALTER TABLE `programes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programetypes`
--
ALTER TABLE `programetypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programmes`
--
ALTER TABLE `programmes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `setassignments`
--
ALTER TABLE `setassignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sparents`
--
ALTER TABLE `sparents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=304;

--
-- AUTO_INCREMENT for table `sparents_students`
--
ALTER TABLE `sparents_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sponsors`
--
ALTER TABLE `sponsors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sponsorshippayments`
--
ALTER TABLE `sponsorshippayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sponsorships`
--
ALTER TABLE `sponsorships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sponsorships_students`
--
ALTER TABLE `sponsorships_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staffdepartments`
--
ALTER TABLE `staffdepartments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staffgrades`
--
ALTER TABLE `staffgrades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staffmessages`
--
ALTER TABLE `staffmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4122;

--
-- AUTO_INCREMENT for table `studentmessages`
--
ALTER TABLE `studentmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `s_c_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `subjects_students`
--
ALTER TABLE `subjects_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects_teachers`
--
ALTER TABLE `subjects_teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplies`
--
ALTER TABLE `supplies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trequests`
--
ALTER TABLE `trequests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userlogins`
--
ALTER TABLE `userlogins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=320;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_ibfk_class_arm` FOREIGN KEY (`class_arm_id`) REFERENCES `class_arms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `character_developments`
--
ALTER TABLE `character_developments`
  ADD CONSTRAINT `fk_character_semester` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_character_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_character_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_arms`
--
ALTER TABLE `class_arms`
  ADD CONSTRAINT `class_arms_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_arms_ibfk_2` FOREIGN KEY (`class_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`setassignment_id`) REFERENCES `setassignments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_class_arm` FOREIGN KEY (`class_arm_id`) REFERENCES `class_arms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_class_arm` FOREIGN KEY (`class_arm_id`) REFERENCES `class_arms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_answers_ibfk_3` FOREIGN KEY (`selected_option_id`) REFERENCES `question_options` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
