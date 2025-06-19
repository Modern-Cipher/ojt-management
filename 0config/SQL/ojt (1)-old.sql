-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2025 at 09:16 AM
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
-- Database: `ojt`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcements_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `announce_images` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `role` enum('all','admin','coordinator','student','trainer','subtrainer') DEFAULT 'all',
  `announce_status` enum('posted','drafted') DEFAULT 'drafted',
  `notified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcements_id`, `users_id`, `title`, `content`, `announce_images`, `created_at`, `role`, `announce_status`, `notified`) VALUES
(17, 1, 'Greetings', 'Good Day to Everyone', NULL, '2025-04-11 11:48:40', 'all', 'posted', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` enum('yes','no') DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`message_id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(20, 19, 18, 'for delete', 'no', '2025-04-01 01:44:28'),
(21, 20, 39, 'im', 'no', '2025-04-01 05:38:59'),
(27, 19, 18, 'f', 'no', '2025-04-01 05:55:52'),
(31, 19, 18, 'ddd', 'no', '2025-04-01 07:34:49'),
(32, 19, 38, 'jane', 'no', '2025-04-01 07:39:02'),
(34, 19, 38, 'ffds', 'no', '2025-04-01 07:56:31'),
(35, 19, 18, 'test', 'no', '2025-04-05 18:26:40'),
(36, 19, 1, 'hello', 'no', '2025-04-05 18:44:36'),
(37, 1, 19, 'hi', 'no', '2025-04-05 18:44:49'),
(38, 19, 18, 'from coordinator', 'no', '2025-04-05 18:45:12'),
(39, 19, 1, 'from coordinato', 'no', '2025-04-05 18:45:45'),
(40, 1, 19, 'xc', 'no', '2025-04-05 18:45:52'),
(41, 19, 1, 'j', 'no', '2025-04-05 18:45:56'),
(42, 19, 18, 'sc', 'no', '2025-04-05 18:49:42'),
(43, 1, 19, 'good day', 'no', '2025-04-05 18:52:01'),
(44, 19, 1, 'd', 'no', '2025-04-05 18:52:30'),
(45, 1, 18, 'd', 'no', '2025-04-05 18:52:35'),
(46, 19, 1, 'd', 'no', '2025-04-05 18:52:39'),
(48, 19, 36, 'ffs', 'no', '2025-04-05 19:18:53'),
(49, 36, 4, 'hello', 'no', '2025-04-05 19:54:11'),
(50, 4, 36, 'yes po', 'no', '2025-04-05 19:54:22'),
(51, 4, 36, 'yes sir', 'no', '2025-04-05 23:49:01'),
(52, 36, 4, 'bakit', 'no', '2025-04-05 23:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `chat_notifications`
--

CREATE TABLE `chat_notifications` (
  `notif_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` enum('yes','no') DEFAULT 'no',
  `related_message_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `chat_notifications`
--

INSERT INTO `chat_notifications` (`notif_id`, `receiver_id`, `sender_id`, `message`, `is_read`, `related_message_id`, `created_at`) VALUES
(23, 18, 19, 'for delete', 'no', 20, '2025-04-01 01:44:28'),
(24, 39, 20, 'im', 'no', 21, '2025-04-01 05:38:59'),
(30, 18, 19, 'f', 'no', 27, '2025-04-01 05:55:52'),
(34, 18, 19, 'ddd', 'no', 31, '2025-04-01 07:34:49'),
(35, 38, 19, 'jane', 'no', 32, '2025-04-01 07:39:02'),
(37, 38, 19, 'ffds', 'no', 34, '2025-04-01 07:56:31'),
(38, 18, 19, 'test', 'no', 35, '2025-04-05 18:26:40'),
(39, 1, 19, 'hello', 'no', 36, '2025-04-05 18:44:36'),
(40, 19, 1, 'hi', 'no', 37, '2025-04-05 18:44:49'),
(41, 18, 19, 'from coordinator', 'no', 38, '2025-04-05 18:45:12'),
(42, 1, 19, 'from coordinato', 'no', 39, '2025-04-05 18:45:45'),
(43, 19, 1, 'xc', 'no', 40, '2025-04-05 18:45:52'),
(44, 1, 19, 'j', 'no', 41, '2025-04-05 18:45:56'),
(45, 18, 19, 'sc', 'no', 42, '2025-04-05 18:49:42'),
(46, 19, 1, 'good day', 'no', 43, '2025-04-05 18:52:01'),
(47, 1, 19, 'd', 'no', 44, '2025-04-05 18:52:30'),
(48, 18, 1, 'd', 'no', 45, '2025-04-05 18:52:35'),
(49, 1, 19, 'd', 'no', 46, '2025-04-05 18:52:39'),
(51, 36, 19, 'ffs', 'no', 48, '2025-04-05 19:18:53'),
(52, 4, 36, 'hello', 'no', 49, '2025-04-05 19:54:11'),
(53, 36, 4, 'yes po', 'no', 50, '2025-04-05 19:54:22'),
(54, 36, 4, 'yes sir', 'no', 51, '2025-04-05 23:49:01'),
(55, 4, 36, 'bakit', 'no', 52, '2025-04-05 23:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comments_id` int(11) NOT NULL,
  `announcements_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `comments` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `notified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `filename`
--

CREATE TABLE `filename` (
  `filename_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `count` enum('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15') DEFAULT '0',
  `category` enum('pre','post','hte','journal') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `filename`
--

INSERT INTO `filename` (`filename_id`, `filename`, `count`, `category`) VALUES
(1, 'Intent Letter', '1', 'pre'),
(2, 'Resume', '3', 'pre'),
(3, 'medical certificate', '2', 'pre'),
(4, 'Certificate of Completion', '1', 'post'),
(5, 'Memorandum of Agreement', '1', 'hte'),
(6, 'Evaluation of Grades', '2', 'hte'),
(7, 'Journal-Week 1', '1', 'journal'),
(8, 'Journal-Week 2', '2', 'journal'),
(9, 'Journal-Week 3', '3', 'journal'),
(10, 'Journal-Week 4', '4', 'journal'),
(11, 'Journal-Week 5', '5', 'journal'),
(12, 'Journal-Week 6', '6', 'journal'),
(13, 'Journal-Week 7', '7', 'journal'),
(14, 'Journal-Week 8', '8', 'journal'),
(15, 'Journal-Week 9', '9', 'journal'),
(16, 'Journal-Week 10', '10', 'journal'),
(17, 'Journal-Week 11', '11', 'journal'),
(18, 'Journal-Week 12', '12', 'journal'),
(19, 'Journal-Week 13', '13', 'journal'),
(20, 'Journal-Week 14', '14', 'journal'),
(21, 'Journal-Week 15', '15', 'journal');

-- --------------------------------------------------------

--
-- Table structure for table `file_comments`
--

CREATE TABLE `file_comments` (
  `file_comment_id` int(11) NOT NULL,
  `filename_id` int(11) NOT NULL,
  `commenter_id` int(11) NOT NULL,
  `uploadedby_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `file_comments`
--

INSERT INTO `file_comments` (`file_comment_id`, `filename_id`, `commenter_id`, `uploadedby_id`, `comment`, `created_at`) VALUES
(54, 4, 18, NULL, 'vdsv', '2025-03-30 15:44:07'),
(55, 4, 18, NULL, 'sdv', '2025-03-30 15:44:17'),
(56, 4, 4, NULL, 'csc', '2025-03-30 15:53:48'),
(57, 4, 18, NULL, 'c', '2025-03-30 15:53:52'),
(58, 4, 4, NULL, 'scs', '2025-03-30 15:53:59'),
(63, 4, 19, 4, 'mary good', '2025-03-30 15:55:53'),
(64, 4, 19, 18, 'aljon good', '2025-03-30 15:55:53'),
(65, 4, 4, NULL, 'axa', '2025-03-30 16:00:51'),
(66, 2, 18, NULL, 'sda', '2025-03-30 16:03:56'),
(67, 2, 4, NULL, 'sdf', '2025-03-30 16:04:11'),
(68, 2, 19, 4, 'resume ni mary', '2025-03-30 16:05:36'),
(70, 6, 36, NULL, 'csaca', '2025-03-30 16:10:30'),
(72, 5, 36, NULL, 'dd', '2025-03-31 05:12:30'),
(74, 5, 36, NULL, 'asd', '2025-03-31 05:12:34'),
(87, 5, 37, NULL, 'test new trainer', '2025-03-31 12:08:03'),
(90, 6, 37, NULL, 'ddd', '2025-03-31 12:41:39'),
(91, 5, 37, NULL, 'dd', '2025-03-31 12:41:41'),
(92, 5, 40, NULL, 'sda', '2025-03-31 12:54:01'),
(93, 6, 40, NULL, 'asdasd', '2025-03-31 12:54:03'),
(94, 5, 20, 40, 'dsfsf from imtrainer', '2025-03-31 13:19:40'),
(96, 5, 19, 37, 'sadsd', '2025-03-31 14:45:11'),
(97, 5, 20, 40, 'dsd', '2025-03-31 14:54:11'),
(98, 5, 19, 36, 'asd', '2025-03-31 14:57:33'),
(99, 5, 37, NULL, 'asd', '2025-03-31 14:57:39'),
(103, 6, 19, 36, 'try', '2025-03-31 15:17:00'),
(105, 5, 19, 37, 'this from accenture', '2025-03-31 15:17:28'),
(106, 5, 20, 40, 'im coord 5 star hotel', '2025-03-31 15:18:03'),
(107, 6, 19, 37, 'hello', '2025-03-31 15:18:48'),
(108, 2, 19, 18, 'dasd', '2025-03-31 16:02:05'),
(109, 2, 18, NULL, 'asdasd', '2025-03-31 16:02:31'),
(110, 3, 18, NULL, 'sadasd', '2025-03-31 16:02:44'),
(111, 3, 19, 18, 'ddddd', '2025-03-31 16:03:09'),
(112, 2, 4, NULL, 'dad', '2025-03-31 16:04:08'),
(113, 1, 4, NULL, 'asdasd', '2025-03-31 16:04:30'),
(114, 3, 4, NULL, 'ddawd', '2025-03-31 16:04:33'),
(115, 2, 19, 4, 'ddd', '2025-03-31 16:04:46'),
(116, 2, 39, NULL, 'gsdgsdg', '2025-03-31 16:07:26'),
(117, 2, 20, 39, 'safasfa', '2025-03-31 16:08:06'),
(118, 4, 19, 4, 'fsdf', '2025-03-31 16:22:09'),
(119, 7, 18, NULL, 'testing journal week 1', '2025-03-31 18:15:21'),
(120, 7, 19, 18, 'good', '2025-03-31 18:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `hte`
--

CREATE TABLE `hte` (
  `hte_id` int(11) NOT NULL,
  `hte_name` varchar(255) NOT NULL,
  `hte_address` varchar(255) DEFAULT NULL,
  `hte_status` enum('approved','pending','rejected') DEFAULT 'pending',
  `trainer_id` int(11) DEFAULT NULL,
  `coordinator_id` int(11) DEFAULT NULL,
  `trainee_id` int(11) DEFAULT NULL,
  `hte_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hte`
--

INSERT INTO `hte` (`hte_id`, `hte_name`, `hte_address`, `hte_status`, `trainer_id`, `coordinator_id`, `trainee_id`, `hte_created`) VALUES
(1, 'PLDT', 'Makakit City Manila', 'approved', 38, 19, NULL, '2025-03-28 14:29:31'),
(3, 'Accenture', 'Manila', 'approved', 37, 19, NULL, '2025-03-28 14:33:08'),
(4, 'Globe Corp', 'Quezon City', 'approved', 36, 19, NULL, '2025-03-28 15:03:44'),
(6, '5 Star Hotel', 'Manila', 'approved', 40, 20, NULL, '2025-03-29 13:49:21'),
(7, 'sfds', 'sdfds', 'approved', NULL, 20, NULL, '2025-03-31 12:56:12');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notifications_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `type` enum('announcements','comments','feedback','chats') DEFAULT NULL,
  `source_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notifications_id`, `receiver_id`, `sender_id`, `type`, `source_id`, `message`, `is_read`, `created_at`) VALUES
(1820, 4, 1, 'announcements', 17, 'New announcement: Greetings', 0, '2025-04-11 11:48:40'),
(1821, 18, 1, 'announcements', 17, 'New announcement: Greetings', 0, '2025-04-11 11:48:40'),
(1822, 19, 1, 'announcements', 17, 'New announcement: Greetings', 0, '2025-04-11 11:48:40'),
(1823, 36, 1, 'announcements', 17, 'New announcement: Greetings', 0, '2025-04-11 11:48:40'),
(1824, 37, 1, 'announcements', 17, 'New announcement: Greetings', 0, '2025-04-11 11:48:40'),
(1825, 38, 1, 'announcements', 17, 'New announcement: Greetings', 0, '2025-04-11 11:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `ojt_status_history`
--

CREATE TABLE `ojt_status_history` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `old_status` enum('pending','deployed','pulled-out','rejected') DEFAULT NULL,
  `new_status` enum('pending','deployed','pulled-out','rejected') NOT NULL,
  `date_assigned` datetime DEFAULT NULL,
  `date_changed` datetime DEFAULT current_timestamp(),
  `old_hte_id` int(11) DEFAULT NULL,
  `new_hte_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ojt_status_history`
--

INSERT INTO `ojt_status_history` (`id`, `student_id`, `old_status`, `new_status`, `date_assigned`, `date_changed`, `old_hte_id`, `new_hte_id`) VALUES
(5, 4, 'deployed', 'deployed', '2025-03-30 02:12:30', '2025-03-30 02:34:03', 1, 3),
(6, 4, 'deployed', 'deployed', '2025-03-30 02:34:03', '2025-03-30 02:34:34', 3, 4),
(7, 18, 'deployed', 'pending', '2025-03-30 02:01:51', '2025-03-30 02:35:12', 3, 1),
(8, 39, 'pending', 'deployed', '2025-03-31 20:48:41', '2025-03-31 20:49:00', NULL, 6),
(9, 39, 'deployed', 'deployed', '2025-03-31 20:49:00', '2025-03-31 21:12:20', 6, 7),
(10, 39, 'deployed', 'deployed', '2025-03-31 21:12:20', '2025-03-31 21:12:26', 7, 6),
(11, 39, 'deployed', 'pulled-out', '2025-03-31 21:12:26', '2025-04-01 00:30:24', 6, 6);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `sessions_id` varchar(255) NOT NULL,
  `users_id` int(11) NOT NULL,
  `session_data` text NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `creation_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `uploads_id` int(11) NOT NULL,
  `filename_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `filepath` varchar(255) NOT NULL,
  `uploadedby_id` int(11) NOT NULL,
  `upload_status` enum('accepted','processing','rejected') DEFAULT 'processing',
  `submitted_on` datetime DEFAULT current_timestamp(),
  `updated_on` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `checkedby_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `users_id` int(11) NOT NULL,
  `coordinator_id` int(11) DEFAULT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `mname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `sex` enum('female','male') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `institute` enum('Institute of Engineering and Applied Technology','Institute of Management') DEFAULT NULL,
  `school_id` varchar(150) DEFAULT NULL,
  `course` enum('BS in Business Administration','BS in Hospitality Management','BS in Agriculture and Biosystems Engineering','BS in Geodetic Engineering','BS in Food Technology','BS in Information Technology') DEFAULT NULL,
  `hte_id` int(11) DEFAULT NULL,
  `year_section` enum('4-A','4-B','4-C','4-D','4-E','4-F','4-G') DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `role` enum('admin','coordinator','student','trainer','subtrainer') DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `temppass` varchar(255) DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `activate` tinyint(1) NOT NULL DEFAULT 0,
  `guid` varchar(255) DEFAULT NULL,
  `image_profile` text DEFAULT NULL,
  `chat_stats` enum('online','offline','default') DEFAULT 'offline',
  `users_account` enum('enabled','disabled') DEFAULT 'enabled',
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attended` enum('yes','no') DEFAULT 'no',
  `assigned` enum('BS in Business Administration','BS in Hospitality Management','BS in Agriculture and Biosystems Engineering','BS in Geodetic Engineering','BS in Food Technology','BS in Information Technology') DEFAULT NULL,
  `ojt_stats` enum('deployed','pending','pulled-out') DEFAULT 'pending',
  `is_completed` enum('c','nyc') DEFAULT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`users_id`, `coordinator_id`, `fname`, `mname`, `lname`, `sex`, `address`, `phone`, `institute`, `school_id`, `course`, `hte_id`, `year_section`, `designation`, `role`, `email`, `username`, `password`, `temppass`, `otp`, `activate`, `guid`, `image_profile`, `chat_stats`, `users_account`, `created_on`, `updated_on`, `attended`, `assigned`, `ojt_stats`, `is_completed`, `time_in`, `time_out`, `remarks`) VALUES
(1, NULL, 'Prof. Michelle', 'M.', 'Cortez', 'male', '-', '', 'Institute of Engineering and Applied Technology', '0987654321', NULL, NULL, NULL, 'Full Stackc', 'admin', 'ieatadmin@gmail.com', 'ieatadmin', '$2y$10$5r97/mooj32K5MZhY85yYuFfeBaMh.GQRRBcFRu14idxwfqAM0qbe', NULL, NULL, 0, NULL, 'KtbGo6.jpg', 'offline', 'enabled', '2025-03-07 13:27:26', '2025-04-12 00:47:59', 'no', NULL, 'pending', NULL, '2025-04-12 08:39:55', '2025-04-12 08:47:59', NULL),
(2, NULL, 'Nerilyn', 'J.', 'Victoria', 'female', '', '', 'Institute of Management', '0', NULL, NULL, NULL, 'Dean', 'admin', 'im@gmail.com', 'imadmin', '$2y$10$W6sIvlDFNWeW4e9.S8mGnuYXzL1HwTafrg4NXdqewSVGRR4WsQUiK', NULL, '777687', 0, NULL, 'AFHWZq.png', 'offline', 'enabled', '2025-03-07 14:56:59', '2025-04-11 22:24:55', 'no', NULL, 'pending', NULL, '2025-04-12 06:24:36', '2025-04-12 06:24:55', NULL),
(4, NULL, 'mary', 'c', 'jane', 'female', 'san miguel', '09876543211', 'Institute of Engineering and Applied Technology', '987654321', 'BS in Information Technology', 4, NULL, NULL, 'student', NULL, 'mary', '$2y$10$KCaV/scZ/R0LRT8HPiU.be3OS2Vqslc/xv.zWnvXV4PhRplNcgah.', NULL, NULL, 1, 'iF2YVO', 'Y8v2s3.jpg', 'offline', 'enabled', '2025-03-08 12:35:30', '2025-04-05 19:36:36', 'yes', NULL, 'deployed', NULL, NULL, NULL, NULL),
(18, NULL, 'aljon1', 'c', 'dumlao', 'male', 'san miguel', '09876543211', 'Institute of Engineering and Applied Technology', '09876543211', 'BS in Information Technology', 1, NULL, NULL, 'student', 'menongdc@gmail.com', 'aljon', '$2y$10$KCaV/scZ/R0LRT8HPiU.be3OS2Vqslc/xv.zWnvXV4PhRplNcgah.', NULL, NULL, 1, 'F1NorU', 'UrFhWA.jpg', 'offline', 'enabled', '2025-03-08 14:02:22', '2025-04-12 00:45:27', 'yes', NULL, 'pending', NULL, '2025-04-12 08:44:55', '2025-04-12 08:45:27', NULL),
(19, NULL, 'Jermyn', 'C', 'Cruz', 'female', 'asfasfasfa', '098765321', 'Institute of Engineering and Applied Technology', '098765411D', 'BS in Information Technology', NULL, NULL, 'Coordinator', 'coordinator', 'ieatcoordinator@gmail.com', 'ieatcoordinator', '$2y$10$gnw/QQ5H68vkHIW1LLZxc.PVMU16pxOm2fvpX.hgwu5.ek8Uj6n2q', '$2y$10$gnw/QQ5H68vkHIW1LLZxc.PVMU16pxOm2fvpX.hgwu5.ek8Uj6n2q', NULL, 0, NULL, 'sCMpcW.jpg', 'offline', 'enabled', '2025-03-11 08:56:42', '2025-04-12 00:43:01', 'no', NULL, 'pending', NULL, '2025-04-12 08:41:23', '2025-04-12 08:43:01', NULL),
(20, NULL, 'Nerilyn', '', 'Victoria', 'female', NULL, NULL, 'Institute of Management', '2025-002', 'BS in Business Administration', NULL, NULL, NULL, 'coordinator', NULL, 'imcoordinator', '$2y$10$qpyyutjT5UAKSXqiP4USweTEGCfMxLewU9CyfA0DzNs4bt8mlCKiy', '$2y$10$qpyyutjT5UAKSXqiP4USweTEGCfMxLewU9CyfA0DzNs4bt8mlCKiy', NULL, 0, NULL, 'default.jpg', 'offline', 'enabled', '2025-03-11 09:32:04', '2025-04-11 04:10:15', 'no', NULL, 'pending', NULL, NULL, NULL, NULL),
(36, 19, 'rb', NULL, 'madis', 'female', NULL, NULL, 'Institute of Engineering and Applied Technology', NULL, 'BS in Information Technology', NULL, NULL, 'trainer1', 'trainer', 'rb@gmail.com', 'rbmadis', '$2y$10$iyH4AL680Rrv5wrr98LlxubcqZ/FMRMOI3wPl1qmIvTpDKvK1Unbi', '$2y$10$iyH4AL680Rrv5wrr98LlxubcqZ/FMRMOI3wPl1qmIvTpDKvK1Unbi', NULL, 0, NULL, '5oYgMe.jpg', 'offline', 'enabled', '2025-03-28 09:56:26', '2025-04-12 00:44:47', 'no', NULL, 'pending', NULL, '2025-04-12 08:43:10', '2025-04-12 08:44:47', NULL),
(37, 19, 'med', NULL, 'bunalade', 'male', NULL, NULL, 'Institute of Engineering and Applied Technology', NULL, 'BS in Information Technology', NULL, NULL, '', 'trainer', '', 'meds', '$2y$10$iyH4AL680Rrv5wrr98LlxubcqZ/FMRMOI3wPl1qmIvTpDKvK1Unbi', '$2y$10$Yxk7EjcGyOV.uWK5CR.bRuiNtZzlwBqkTUPAAFCPoPtKkJaiGf3qq', NULL, 0, NULL, 'siplogo.png', 'offline', 'enabled', '2025-03-28 09:58:11', '2025-03-31 12:07:05', 'no', NULL, 'pending', NULL, NULL, NULL, NULL),
(38, 19, 'jane', NULL, 'dd', 'female', NULL, NULL, 'Institute of Engineering and Applied Technology', NULL, 'BS in Information Technology', NULL, NULL, 'trainer', 'trainer', 'jane@gmail.com', 'janed', '$2y$10$skpN4KBg.zYF10n3JqX2V.yVsgXZptE5PYVLMHeid5SM26m/GMhl.', NULL, NULL, 0, 'asdasd', 'KZVacj.jpg', 'offline', 'enabled', '2025-03-28 11:13:11', '2025-04-11 22:18:06', 'no', NULL, 'pending', NULL, '2025-04-12 06:17:47', '2025-04-12 06:18:06', NULL),
(39, NULL, 'im student', 'f', 'dd', 'female', 'sdfds', NULL, 'Institute of Management', '2025-001', 'BS in Business Administration', 6, NULL, NULL, 'student', NULL, 'imstudent', '$2y$10$iyH4AL680Rrv5wrr98LlxubcqZ/FMRMOI3wPl1qmIvTpDKvK1Unbi', NULL, NULL, 1, NULL, 'ELXJew.png', 'offline', 'enabled', '2025-03-31 12:44:46', '2025-04-11 04:11:05', 'yes', NULL, 'pulled-out', NULL, NULL, NULL, NULL),
(40, 20, 'im trainer', '', 'dd', 'female', '', '', 'Institute of Management', '2025-003', 'BS in Business Administration', NULL, NULL, '', 'trainer', 'imtrainer@gmail.com', 'imtrainer', '$2y$10$H3Xhl4aZ0thK/G3MqdHvlexxDhM6N6p5hUiWQnixbBkd.9ThLssiu', NULL, NULL, 0, NULL, 'siplogo.png', 'offline', 'enabled', '2025-03-31 06:49:34', '2025-04-11 22:20:19', 'no', NULL, 'pending', NULL, '2025-04-12 06:20:09', '2025-04-12 06:20:19', NULL);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `trg_ojt_status_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    -- Log only if student & status or hte_id changed
    IF OLD.role = 'student' AND (OLD.ojt_stats <> NEW.ojt_stats OR OLD.hte_id <> NEW.hte_id) THEN
        INSERT INTO ojt_status_history (
            student_id, 
            old_status, 
            new_status, 
            old_hte_id, 
            new_hte_id, 
            date_assigned, 
            date_changed
        )
        VALUES (
            NEW.users_id, 
            OLD.ojt_stats, 
            NEW.ojt_stats, 
            OLD.hte_id, 
            NEW.hte_id, 
            IFNULL(OLD.updated_on, NOW()), 
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcements_id`),
  ADD KEY `fk_announce_user` (`users_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `chat_notifications`
--
ALTER TABLE `chat_notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comments_id`),
  ADD KEY `fk_comments_announcement` (`announcements_id`),
  ADD KEY `fk_comments_user` (`users_id`);

--
-- Indexes for table `filename`
--
ALTER TABLE `filename`
  ADD PRIMARY KEY (`filename_id`);

--
-- Indexes for table `file_comments`
--
ALTER TABLE `file_comments`
  ADD PRIMARY KEY (`file_comment_id`),
  ADD KEY `filename_id` (`filename_id`),
  ADD KEY `commenter_id` (`commenter_id`);

--
-- Indexes for table `hte`
--
ALTER TABLE `hte`
  ADD PRIMARY KEY (`hte_id`),
  ADD KEY `trainer_id` (`trainer_id`),
  ADD KEY `coordinator_id` (`coordinator_id`),
  ADD KEY `trainee_id` (`trainee_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notifications_id`),
  ADD KEY `fk_receiver` (`receiver_id`),
  ADD KEY `fk_sender` (`sender_id`);

--
-- Indexes for table `ojt_status_history`
--
ALTER TABLE `ojt_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `fk_old_hte_id` (`old_hte_id`),
  ADD KEY `fk_new_hte_id` (`new_hte_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`sessions_id`),
  ADD KEY `users_id` (`users_id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`uploads_id`),
  ADD KEY `filename_id` (`filename_id`),
  ADD KEY `uploadedby_id` (`uploadedby_id`),
  ADD KEY `checkedby_id` (`checkedby_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `guid` (`guid`),
  ADD KEY `fk_users_hte` (`hte_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcements_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `chat_notifications`
--
ALTER TABLE `chat_notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comments_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `filename`
--
ALTER TABLE `filename`
  MODIFY `filename_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `file_comments`
--
ALTER TABLE `file_comments`
  MODIFY `file_comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `hte`
--
ALTER TABLE `hte`
  MODIFY `hte_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notifications_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1826;

--
-- AUTO_INCREMENT for table `ojt_status_history`
--
ALTER TABLE `ojt_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `uploads_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announce_user` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_notifications`
--
ALTER TABLE `chat_notifications`
  ADD CONSTRAINT `chat_notifications_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_notifications_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_announcement` FOREIGN KEY (`announcements_id`) REFERENCES `announcements` (`announcements_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Constraints for table `file_comments`
--
ALTER TABLE `file_comments`
  ADD CONSTRAINT `file_comments_ibfk_1` FOREIGN KEY (`filename_id`) REFERENCES `filename` (`filename_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_comments_ibfk_2` FOREIGN KEY (`commenter_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Constraints for table `hte`
--
ALTER TABLE `hte`
  ADD CONSTRAINT `hte_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`users_id`),
  ADD CONSTRAINT `hte_ibfk_2` FOREIGN KEY (`coordinator_id`) REFERENCES `users` (`users_id`),
  ADD CONSTRAINT `hte_ibfk_3` FOREIGN KEY (`trainee_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`users_id`),
  ADD CONSTRAINT `fk_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `ojt_status_history`
--
ALTER TABLE `ojt_status_history`
  ADD CONSTRAINT `fk_new_hte_id` FOREIGN KEY (`new_hte_id`) REFERENCES `hte` (`hte_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_old_hte_id` FOREIGN KEY (`old_hte_id`) REFERENCES `hte` (`hte_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ojt_status_history_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`filename_id`) REFERENCES `filename` (`filename_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploads_ibfk_2` FOREIGN KEY (`uploadedby_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `uploads_ibfk_3` FOREIGN KEY (`checkedby_id`) REFERENCES `users` (`users_id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_hte` FOREIGN KEY (`hte_id`) REFERENCES `hte` (`hte_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
