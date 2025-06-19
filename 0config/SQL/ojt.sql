-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2025 at 09:01 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `school_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `institute` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL CHECK (`email` like '%@%.%'),
  `selfie_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_info` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `qr_code_data` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_qr_scanned` tinyint(1) DEFAULT 0,
  `scanned_timestamp` datetime DEFAULT NULL,
  `scanned_by_ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_by_device_info` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `school_id`, `first_name`, `middle_name`, `last_name`, `sex`, `institute`, `course`, `email`, `selfie_image_path`, `ip_address`, `device_info`, `timestamp`, `qr_code_data`, `is_qr_scanned`, `scanned_timestamp`, `scanned_by_ip_address`, `scanned_by_device_info`) VALUES
(8, '2018100593', 'meds', 'c', 'bunalde', 'Male', 'Institute of Management', 'BS in Business Administration', 'medel.bunalade@yahoo.com', 'upload_selfie/selfie_1750282231_yoj5FW.jpg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-19 05:30:31', '3814b7adf6ba13908a747d2a9d880547', 0, NULL, NULL, NULL),
(11, 'rwerwerd', 'acasc', 'acasc', 'bunalde', 'Female', 'Institute of Engineering and Applied Technology', 'BS in Information Technology', 'pd776963@gmail.com', 'upload_selfie/selfie_1750283557_XjYFzt.jpg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-19 05:52:37', '0ecc478ec38a2fca908aa17b033360a0', 0, NULL, NULL, NULL),
(14, '2018101104d', 'asdas', 'asd', 'asd', 'Male', 'Institute of Management', 'BS in Hotel Management', 'im@gmail.com', 'upload_selfie/selfie_1750286170_pMrnbU.jpg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-19 06:36:10', '79b88dce692a0d34ccf062372f795850', 0, NULL, NULL, NULL),
(15, 'Jen', 'Ksj', 'Ksks', 'Kdks', 'Female', 'Institute of Engineering and Applied Technology', 'BS in Information Technology', 'm@gmail.com', 'upload_selfie/selfie_1750290127_DySoBd.jpg', '192.168.1.3', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36', '2025-06-19 07:42:07', '1e149d12148f11f676e952eb02952a7e', 1, '2025-06-19 01:43:53', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`message_id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(182, 50, 45, 'hello po', 'yes', '2025-06-18 14:59:58'),
(183, 50, 45, 'd', 'yes', '2025-06-18 15:00:04'),
(184, 49, 46, 'hi', 'yes', '2025-06-18 15:00:16'),
(185, 50, 45, 'Attachment: upload_chatfiles/Internship_MOA_-_Student__Repaired___signed__ebzicg.pdf|Internship MOA - Student (Repaired) (signed).pdf|application/pdf', 'yes', '2025-06-18 15:00:29'),
(186, 50, 45, 'Attachment: upload_chatfiles/2024_MOA-for-OJT-Small-business-or-Sole_SHIFT101-1_z3vweu.docx|2024_MOA-for-OJT-Small-business-or-Sole_SHIFT101-1.docx|application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'yes', '2025-06-18 15:00:34'),
(187, 49, 46, 'Attachment: upload_chatfiles/5__BunaladeMedelC_BSIT4A_PhotocopyID_jge8o4.jpg|5. BunaladeMedelC_BSIT4A_PhotocopyID.jpg|image/jpeg', 'yes', '2025-06-18 15:00:43'),
(188, 50, 45, 'V', 'no', '2025-06-18 16:36:55'),
(189, 45, 47, 'Please Send the MOA Thanks', 'yes', '2025-06-18 16:45:54'),
(190, 47, 45, 'Attachment: upload_chatfiles/INTERNSHIP_PERFORMANCE_RATING_FORM_d4c6v1.docx|INTERNSHIP PERFORMANCE RATING FORM.docx|application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'no', '2025-06-18 16:46:15');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chat_notifications`
--

INSERT INTO `chat_notifications` (`notif_id`, `receiver_id`, `sender_id`, `message`, `is_read`, `related_message_id`, `created_at`) VALUES
(191, 45, 50, 'V', 'no', 188, '2025-06-18 16:36:55'),
(193, 45, 47, 'New upload: INTERNSHIP PERFORMANCE RATING FORM.docx', 'no', 190, '2025-06-18 16:46:15');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `filename`
--

CREATE TABLE `filename` (
  `filename_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `count` enum('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20') DEFAULT '0',
  `category` enum('pre','post','hte','journal') DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filename`
--

INSERT INTO `filename` (`filename_id`, `filename`, `count`, `category`, `updated_at`, `user_id`) VALUES
(1, 'Internship Application Form', '1', 'pre', '2025-06-18 00:23:36', NULL),
(2, 'Letter of intent for Student Internship (application Letter)', '2', 'pre', '2025-06-18 00:23:36', NULL),
(3, 'Resume', '3', 'pre', '2025-06-18 00:23:36', NULL),
(4, 'Certificate of Registration', '4', 'pre', '2025-06-18 00:23:36', NULL),
(5, 'Photocopy of ID w/ 3 Specimen Signature', '5', 'pre', '2025-06-18 00:23:36', NULL),
(6, 'Medical Insurance', '6', 'pre', '2025-06-18 00:23:36', NULL),
(7, 'Medical Certificate', '7', 'pre', '2025-06-18 00:23:36', NULL),
(8, 'Certificate of Units Earned before Internship', '8', 'pre', '2025-06-18 00:23:36', NULL),
(9, 'Parental Consent', '9', 'pre', '2025-06-18 00:23:36', NULL),
(10, 'Photocopy of Parent ID with 3 Specimen Signature', '10', 'pre', '2025-06-18 00:23:36', NULL),
(11, 'PSA Birth Certificate', '11', 'pre', '2025-06-18 00:23:36', NULL),
(12, 'Endorsement Letter', '12', 'pre', '2025-06-18 00:23:36', NULL),
(13, 'Internship Contract', '13', 'pre', '2025-06-18 00:23:36', NULL),
(14, 'Memorandum of Agreement', '14', 'pre', '2025-06-18 00:23:36', NULL),
(15, ' OJT Orientation Attendance Sheet', '15', 'pre', '2025-06-18 00:23:36', NULL),
(16, 'Successful OJT Application', '15', 'pre', '2025-06-18 00:23:36', NULL),
(17, ' Google Form Internship Evaluation for Intern', '1', 'post', '2025-06-18 00:23:36', NULL),
(18, 'OJT Report Hardbound Guide and Official Form Header', '2', 'post', '2025-06-18 00:23:36', NULL),
(19, 'Google Form Internship Evaluation for Supervisor', '3', 'post', '2025-06-18 00:23:36', NULL),
(20, 'Certificate of Completion', '4', 'post', '2025-06-18 00:23:36', NULL),
(21, 'Internship Journal Evaluation Form', '5', 'post', '2025-06-18 00:23:36', NULL),
(22, 'Internship Performance Rating Form', '6', 'post', '2025-06-18 00:23:36', NULL),
(23, 'Internship Feedback Mechanism', '7', 'post', '2025-06-18 00:23:36', NULL),
(24, 'Internship Monitoring and Evaluation Form', '8', 'post', '2025-06-18 00:23:36', NULL),
(46, 'Internship Contract Agreement', '1', 'hte', '2025-06-18 14:00:50', NULL),
(47, 'Internship Feedback Mechanism', '3', 'hte', '2025-06-18 14:01:59', NULL),
(48, 'Internship Monitoring  and Evaluation Form', '4', 'hte', '2025-06-18 14:01:55', NULL),
(49, 'Memorandum of Agreement', '2', 'hte', '2025-06-18 14:01:39', NULL),
(50, 'Week 1', '1', 'journal', '2025-06-18 17:51:16', 45),
(51, 'Week 2', '2', 'journal', '2025-06-18 17:50:43', 45),
(52, 'Week 3', '3', 'journal', '2025-06-18 17:51:38', 45),
(53, 'Week 4', '4', 'journal', '2025-06-18 17:51:43', 45),
(54, 'Week 5', '5', 'journal', '2025-06-18 17:53:12', 45),
(55, 'Week 6', '6', 'journal', '2025-06-18 17:53:21', 45);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `file_comments`
--

INSERT INTO `file_comments` (`file_comment_id`, `filename_id`, `commenter_id`, `uploadedby_id`, `comment`, `created_at`) VALUES
(125, 46, 47, NULL, 'nagsubmit na po ako pacheck nalang po', '2025-06-18 14:19:19'),
(126, 46, 48, NULL, 'pacheck po salamat', '2025-06-18 14:19:44'),
(127, 50, 50, NULL, 'naupload ko n apo', '2025-06-18 16:44:06'),
(128, 50, 45, 50, 'okie', '2025-06-18 16:44:13'),
(129, 46, 45, 47, 'approved na po', '2025-06-18 16:47:40');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hte`
--

INSERT INTO `hte` (`hte_id`, `hte_name`, `hte_address`, `hte_status`, `trainer_id`, `coordinator_id`, `trainee_id`, `hte_created`) VALUES
(10, 'Sales Rain', 'One Corporate, Ortigas', 'approved', 47, 45, NULL, '2025-06-18 14:11:56'),
(11, 'WT Migremo Systems Inc.', 'WT Migremo Systems Inc., Mandaluyong', 'pending', 48, 46, NULL, '2025-06-18 14:12:58'),
(12, 'PLDT Corp.', 'Quezon City', 'pending', NULL, 45, NULL, '2025-06-18 15:15:22');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ojt_status_history`
--

INSERT INTO `ojt_status_history` (`id`, `student_id`, `old_status`, `new_status`, `date_assigned`, `date_changed`, `old_hte_id`, `new_hte_id`) VALUES
(19, 50, 'pending', 'deployed', '2025-06-18 23:04:07', '2025-06-18 23:13:06', NULL, 10),
(20, 49, 'pending', 'deployed', '2025-06-18 23:02:41', '2025-06-18 23:13:26', NULL, 11),
(21, 50, 'deployed', 'deployed', '2025-06-18 23:13:06', '2025-06-18 23:15:56', 10, 12),
(22, 50, 'deployed', 'pulled-out', '2025-06-18 23:15:56', '2025-06-18 23:16:25', 12, 12),
(23, 50, 'pulled-out', 'deployed', '2025-06-18 23:16:25', '2025-06-18 23:16:36', 12, 10),
(24, 49, 'deployed', 'pending', '2025-06-18 23:13:26', '2025-06-18 23:20:49', 11, 11),
(25, 49, 'pending', 'deployed', '2025-06-19 00:37:41', '2025-06-19 00:38:02', 11, 11),
(26, 50, 'deployed', 'pending', '2025-06-19 02:04:03', '2025-06-19 02:09:01', 10, 10),
(27, 50, 'pending', 'deployed', '2025-06-19 02:09:01', '2025-06-19 02:09:17', 10, 10);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`sessions_id`, `users_id`, `session_data`, `last_activity`, `creation_time`) VALUES
('h7l2lljr0fo3m9lmnbaqbq3cvc', 50, '{\"user_id\":50,\"role\":\"student\",\"institute\":\"Institute of Engineering and Applied Technology\",\"username\":\"Prince\"}', '2025-06-18 17:04:31', '2025-06-18 17:04:31'),
('ttcvm0fad36dmh2m1qohje9c0r', 45, '{\"user_id\":45,\"role\":\"coordinator\",\"institute\":\"Institute of Engineering and Applied Technology\",\"username\":\"dave\"}', '2025-06-18 16:59:09', '2025-06-18 16:59:09');

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
  `checkedby_id` int(11) DEFAULT NULL,
  `original_file_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`uploads_id`, `filename_id`, `file_name`, `filepath`, `uploadedby_id`, `upload_status`, `submitted_on`, `updated_on`, `checkedby_id`, `original_file_name`) VALUES
(54, 46, 'Derrick_Cruz_Internship Contract Agreement_znagtb.pdf', '../../upload_hte/Derrick_Cruz_Internship Contract Agreement_znagtb.pdf', 47, 'accepted', '2025-06-18 22:19:04', '2025-06-19 00:47:47', 45, NULL),
(55, 46, 'Annie_Madis_Internship Contract Agreement_36vnyt.pdf', '../../upload_hte/Annie_Madis_Internship Contract Agreement_36vnyt.pdf', 48, 'processing', '2025-06-18 22:19:35', '2025-06-18 22:19:35', NULL, NULL),
(56, 50, 'Prince_Dela Cruz_Journal Task 1_j3tylo.pdf', '../../upload_journal/Prince_Dela Cruz_Journal Task 1_j3tylo.pdf', 50, 'accepted', '2025-06-19 00:43:44', '2025-06-19 00:44:24', 45, 'BunaladeMedel_IT500_OJTHardbound.pdf'),
(57, 49, 'Derrick_Cruz_Memorandum of Agreement_2f6tyu.pdf', '../../upload_hte/Derrick_Cruz_Memorandum of Agreement_2f6tyu.pdf', 47, 'processing', '2025-06-19 00:58:58', '2025-06-19 00:58:58', NULL, NULL),
(58, 1, 'Prince_Dela Cruz_Internship Application Form_wfxqct.pdf', '../../upload_pre/Prince_Dela Cruz_Internship Application Form_wfxqct.pdf', 50, 'rejected', '2025-06-19 01:05:15', '2025-06-19 01:07:19', 45, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`users_id`, `coordinator_id`, `fname`, `mname`, `lname`, `sex`, `address`, `phone`, `institute`, `school_id`, `course`, `hte_id`, `year_section`, `designation`, `role`, `email`, `username`, `password`, `temppass`, `otp`, `activate`, `guid`, `image_profile`, `chat_stats`, `users_account`, `created_on`, `updated_on`, `attended`, `assigned`, `ojt_stats`, `is_completed`, `time_in`, `time_out`, `remarks`) VALUES
(1, NULL, 'Medel', 'C.', 'Bunalade', 'male', 'Balaong, San Miguel,  Bulacan', '', 'Institute of Engineering and Applied Technology', '', NULL, NULL, NULL, 'Dean IEAT', 'admin', 'ieatadmin@gmail.com', 'ieatadmin', '$2y$10$5r97/mooj32K5MZhY85yYuFfeBaMh.GQRRBcFRu14idxwfqAM0qbe', NULL, NULL, 0, NULL, NULL, 'offline', 'enabled', '2025-03-07 13:27:26', '2025-06-18 16:57:24', 'no', NULL, 'pending', NULL, '2025-06-19 00:57:16', '2025-06-19 00:57:24', NULL),
(2, NULL, 'Aljohn', 'P.', 'Dumlao', 'male', 'San Jose, San Miguel,  Bulacan', '', 'Institute of Management', '', NULL, NULL, NULL, 'Dean IM', 'admin', 'im@gmail.com', 'imadmin', '$2y$10$W6sIvlDFNWeW4e9.S8mGnuYXzL1HwTafrg4NXdqewSVGRR4WsQUiK', NULL, NULL, 0, NULL, NULL, 'offline', 'enabled', '2025-03-07 14:56:59', '2025-06-18 14:10:27', 'no', NULL, 'pending', NULL, '2025-06-18 22:04:17', '2025-06-18 22:10:27', NULL),
(45, NULL, 'Mr. Dave', NULL, 'Macalinao', 'male', NULL, NULL, 'Institute of Engineering and Applied Technology', NULL, 'BS in Information Technology', NULL, NULL, NULL, 'coordinator', NULL, 'dave', '$2y$10$Km0E/GA2byw9o3HMx6/nJeVzGdycY8bbiyc4GngyuZOQHwPptgZy2', '$2y$10$Km0E/GA2byw9o3HMx6/nJeVzGdycY8bbiyc4GngyuZOQHwPptgZy2', NULL, 0, NULL, 'siplogo.png', 'offline', 'enabled', '2025-06-18 08:05:49', '2025-06-18 23:19:54', 'no', NULL, 'pending', NULL, '2025-06-19 01:54:39', '2025-06-19 07:19:54', NULL),
(46, NULL, 'Nerilyn', NULL, 'Victoria', 'female', NULL, NULL, 'Institute of Management', NULL, 'BS in Hospitality Management', NULL, NULL, NULL, 'coordinator', NULL, 'neri', '$2y$10$wm3bzKnPEMlvLOryvKBLa.yhJr5veF325K9WxQSTBPms3Hc0yBTAG', '$2y$10$wm3bzKnPEMlvLOryvKBLa.yhJr5veF325K9WxQSTBPms3Hc0yBTAG', NULL, 0, NULL, 'siplogo.png', 'offline', 'enabled', '2025-06-18 08:07:07', '2025-06-18 16:38:56', 'no', NULL, 'pending', NULL, '2025-06-19 00:38:36', '2025-06-19 00:38:56', NULL),
(47, 45, 'Derrick', NULL, 'Cruz', 'male', NULL, NULL, 'Institute of Engineering and Applied Technology', NULL, 'BS in Information Technology', NULL, NULL, 'Team lead', 'trainer', NULL, 'derrick', '$2y$10$bvNkFyR69mMMzJfU.UlQ6uOe83ElkevKm7U5iWFM9WBHxaBhNA6dO', '$2y$10$bvNkFyR69mMMzJfU.UlQ6uOe83ElkevKm7U5iWFM9WBHxaBhNA6dO', NULL, 0, NULL, 'siplogo.png', 'offline', 'enabled', '2025-06-18 08:13:57', '2025-06-18 18:46:54', 'no', NULL, 'pending', NULL, '2025-06-19 02:04:10', '2025-06-19 02:46:54', NULL),
(48, 46, 'Annie', NULL, 'Madis', 'female', NULL, NULL, 'Institute of Management', NULL, 'BS in Hospitality Management', NULL, NULL, 'HR', 'trainer', NULL, 'annie', '$2y$10$2bXAlmT0c2kGaDpUHvVp3OQyXtbgnx7F1GkYP4HxE/qUe1Z/uYbvO', '$2y$10$2bXAlmT0c2kGaDpUHvVp3OQyXtbgnx7F1GkYP4HxE/qUe1Z/uYbvO', NULL, 0, NULL, 'siplogo.png', 'offline', 'enabled', '2025-06-18 08:15:38', '2025-06-18 14:21:24', 'no', NULL, 'pending', NULL, '2025-06-18 22:21:18', '2025-06-18 22:21:24', NULL),
(49, NULL, 'Aljohn', 'Pomarejos', 'Dumlao', 'male', 'San Jose, San Miguel,  Bulacan', '09302377567', 'Institute of Management', '2018101104', 'BS in Hospitality Management', 11, NULL, NULL, 'student', 'aljohndumlaobasc@gmail.com', 'aljohn', '$2y$10$Ew.c117ZRE2/GtG80h5joOZlJAEX67fmmthHfNwixW6Fgf/ozS67u', NULL, NULL, 1, 'x5E83J', NULL, 'offline', 'enabled', '2025-06-18 14:24:55', '2025-06-18 16:39:14', 'no', NULL, 'deployed', NULL, '2025-06-19 00:39:04', '2025-06-19 00:39:14', NULL),
(50, NULL, 'Prince', 'C.', 'Dela Cruz', 'male', 'Balaong, San Miguel,  Bulacan', '09977325599', 'Institute of Engineering and Applied Technology', '2018100593', 'BS in Information Technology', 10, NULL, NULL, 'student', 'pd776963@gmail.com', 'Prince', '$2y$10$XZ2LJx.o6QUn1/tRwrmgJeQHKXOc9f0XuRg49CNkOvqeDpmA6aAGG', NULL, NULL, 1, 'DeYG6k', NULL, 'offline', 'enabled', '2025-06-18 14:27:22', '2025-06-18 18:09:17', 'no', NULL, 'deployed', NULL, '2025-06-19 01:41:58', '2025-06-19 02:04:03', NULL);

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
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `school_id` (`school_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `qr_code_data` (`qr_code_data`),
  ADD KEY `idx_ip_timestamp` (`ip_address`,`timestamp`),
  ADD KEY `idx_scanned_ip_timestamp` (`scanned_by_ip_address`,`scanned_timestamp`),
  ADD KEY `idx_scanned_timestamp` (`scanned_timestamp`);

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
  ADD PRIMARY KEY (`filename_id`),
  ADD KEY `fk_user_id` (`user_id`);

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
  MODIFY `announcements_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `chat_notifications`
--
ALTER TABLE `chat_notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comments_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `filename`
--
ALTER TABLE `filename`
  MODIFY `filename_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `file_comments`
--
ALTER TABLE `file_comments`
  MODIFY `file_comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `hte`
--
ALTER TABLE `hte`
  MODIFY `hte_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notifications_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1870;

--
-- AUTO_INCREMENT for table `ojt_status_history`
--
ALTER TABLE `ojt_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `uploads_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
-- Constraints for table `filename`
--
ALTER TABLE `filename`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`users_id`) ON DELETE SET NULL;

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
