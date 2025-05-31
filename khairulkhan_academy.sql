-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2025 at 01:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `khairulkhan_academy`
--

-- --------------------------------------------------------

--
-- Table structure for table `content_progress`
--

CREATE TABLE `content_progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `status` enum('draft','ready','started','completed') DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `title`, `description`, `image_url`, `teacher_id`, `duration`, `price`, `is_featured`, `created_at`, `updated_at`, `is_deleted`, `status`) VALUES
(1, 'SIMPLE QURAN READING', 'Master Quran recitation with proper Tajweed and pronunciation, guided by certified tutors. Suitable for all ages and levels with one-on-one classes, flexible timings, and a structured curriculum.', 'uploads/course_images/course_6830cbdea0518.jpg', 5, '3 months', 200.00, 1, '2025-05-10 14:12:24', '2025-05-23 19:26:22', 0, 'draft'),
(2, 'QURAN WITH TAJWEED', 'Master Quran recitation with proper Tajweed and pronunciation, guided by certified tutors. Suitable for all ages and levels with one-on-one classes, flexible timings, and a structured curriculum.', 'uploads/course_images/course_6830cafbe5642.jpg', 5, '3 months', 10000.00, 1, '2025-05-19 07:53:11', '2025-05-26 07:05:53', 0, 'draft'),
(3, 'QURAN MEMORIZATION', 'Our Quran Memorization Course (Hifz Program) is designed for students of all ages who wish to memorize the Holy Quran with proper Tajweed. This course is tailored to suit each student’s pace and ability, ensuring a strong connection with the Book of Allah.', 'uploads/course_images/course_68395d8b5f098.jpg', 1, '3 months', 5000.00, 1, '2025-05-26 06:31:42', '2025-05-30 07:26:03', 0, 'draft'),
(4, 'ALL DUA\'S', 'Learn essential Duas from the Quran and Sunnah.\r\nImprove pronunciation, understand meanings, and apply them daily.\r\nPerfect for kids and adults of all levels.', 'uploads/course_images/course_683aa83e03fbd.jpg', NULL, '1 month', 1000.00, 1, '2025-05-31 06:57:02', '2025-05-31 06:57:02', 0, 'draft'),
(5, 'AHADEES', 'Learn selected Ahadees from the Prophet Muhammad (PBUH).\r\nUnderstand meanings, morals, and practical lessons.\r\nSimple and easy for all age groups to follow.', 'uploads/course_images/course_683aa8baa5ab6.jpg', NULL, '1 month', 20000.00, 1, '2025-05-31 06:59:06', '2025-05-31 06:59:06', 0, 'draft');

-- --------------------------------------------------------

--
-- Table structure for table `course_content`
--

CREATE TABLE `course_content` (
  `content_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `document_url` varchar(255) DEFAULT NULL,
  `sequence_number` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_content`
--

INSERT INTO `course_content` (`content_id`, `course_id`, `title`, `description`, `video_url`, `document_url`, `sequence_number`, `created_at`, `is_deleted`) VALUES
(1, 1, 'content-testin', 'content-testing', 'https://www.youtube.com/watch?v=9sekgEXGm-E&list=RD9sekgEXGm-E&start_radio=1', 'uploads/course_documents/doc_681f65bb74e28.docx', 1, '2025-05-10 14:42:03', 0);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `completion_status` enum('not_started','in_progress','completed') DEFAULT 'not_started',
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `course_id`, `enrollment_date`, `completion_status`, `payment_status`, `transaction_id`) VALUES
(1, 1, 1, '2025-05-10 07:00:00', 'not_started', 'paid', NULL),
(4, 1, 1, '2025-05-16 17:34:12', 'not_started', 'paid', '228288'),
(5, 1, 1, '2025-05-17 14:27:34', 'not_started', 'paid', '22');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('success','failed','pending') DEFAULT 'pending',
  `transaction_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `enrollment_id`, `amount`, `payment_date`, `payment_method`, `status`, `transaction_details`) VALUES
(3, 1, 200.00, '2025-05-10 17:46:18', 'manual', 'success', 'Marked as paid manually by admin'),
(4, 5, 200.00, '2025-05-20 08:44:08', 'manual', 'success', 'Marked as paid manually by admin');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `parent_name` varchar(100) DEFAULT NULL,
  `parent_contact` varchar(20) DEFAULT NULL,
  `previous_education` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `full_name`, `address`, `contact_number`, `parent_name`, `parent_contact`, `previous_education`, `profile_image`) VALUES
(1, 3, 'Aisha', 'C-178 SHamsi', '198921919', 'PANDA', '028219', '', '00fa38cbded2062d47ebb682b62ee29b.jpg'),
(3, 7, 'Arbaz khan', NULL, '32789237897823', 'xjsjs', '002929282828', NULL, NULL),
(4, 9, 'Faizan1 Gohar', NULL, '857834858943', 'Child', '7348573896538', NULL, NULL),
(5, 3, 'irshad', NULL, '03178353086', 'hjdshf', 'sdhfjdah', NULL, NULL),
(6, 8, 'Arbaz khan', '', '19822929211', 'shsksn', '19288392991', '', 'profile_8_1748325067.jpg'),
(7, 9, 'Full', NULL, '3329758', 'Child', '002929282828', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `user_id`, `full_name`, `specialization`, `bio`, `qualifications`, `contact_number`, `profile_image`) VALUES
(1, 6, 'Alima', 'Quran tajweed', 'uieiuhsiisyiysiysiysiyzkzzkzk', 'Intermediary', '2346723648', 'teacher_68393ed4eeb89.jpg'),
(2, 7, 'Saad Khan', 'Quran Memorization', 'I am Saad .', 'Bachelors', '432832748932', 'teacher_68393ee4d51e0.jpg'),
(3, 12, 'fiza khan', 'Tajweed course', '', '7 year experience', '32789237897823', 'teacher_68394d3e414fe.jpg'),
(4, 13, 'molana saleem ullah', 'QURAN MEMORIZATION', '', '10 year experience', '32789237897823', 'teacher_68394191d918e.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `status`, `created_at`, `last_login`, `reset_token`, `token_expiry`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$130mvh71nMaV4enO8wUAn.m.EERwKRE/fITpF1W0VUmcPjiT8TBOO', 'admin', 1, '2025-05-25 10:54:36', NULL, NULL, NULL),
(2, 'Faizan', 'faizan@gmail.com', '$2y$10$8nIBKTkvANCnX.KaHy5sR.IY1LPifrgwLOMlyemGUen1I6e9JHI0C', 'teacher', 1, '2025-05-25 10:55:57', NULL, NULL, NULL),
(3, 'arbaz', 'arbaz@gmail.com', '$2y$10$RMIA.fSszU/V0h9slM1A3uOawbneWFRpZl3GjgiUmhOfO6KvR2qpa', 'student', 1, '2025-05-25 10:59:12', NULL, NULL, NULL),
(4, 'Asad', 'asad@gmail.com', '$2y$10$130mvh71nMaV4enO8wUAn.m.EERwKRE/fITpF1W0VUmcPjiT8TBOO', 'teacher', 1, '2025-05-25 11:05:11', NULL, NULL, NULL),
(5, 'Sahil', 'sahil@gmail.com', '$2y$10$ChaJVnaVp21BjtHwhLnBweY.HaZMvSpkNIlaHueAiZpXESp.jGZue', 'teacher', 1, '2025-05-26 06:36:03', NULL, NULL, NULL),
(6, 'Alima', 'Alima@gmail.com', '$2y$10$l3bX2X4ZVzSxHPWYBS3wPOoepAanbns90mfRIW0uS6wo3.7NRC67W', 'teacher', 1, '2025-05-26 06:42:04', NULL, NULL, NULL),
(7, 'Saad', 'saad@gmail.com', '$2y$10$sj2Yp6l8rVoQ3DlwuffoN.snSkCjiXYbAxq1mmSgG0WqCTnQsaE5O', 'teacher', 1, '2025-05-26 06:53:37', NULL, NULL, NULL),
(8, 'sarfaraz', 'arbaxkhax8@gmail.com', '$2y$10$gNRwLaCa0Wxi9FbY7taBLu1bOZEwc2rsydd2.0a3glQ8zEW2wdR3y', 'student', 1, '2025-05-26 16:54:55', NULL, NULL, NULL),
(9, 'shams', 'shams@gmail.com', '$2y$10$5VTFOYu.h/mjuT74EDxnFucc8zl2ygQcA2KPmRzaFVYp3feftSs66', 'student', 1, '2025-05-26 16:57:23', NULL, NULL, NULL),
(12, 'fiza', 'fiza@gmail.com', '$2y$10$bt3gvZ1.Or8LWY/duDFrzOuxDeR011QwFyq.3ecmjmcqiKIySOIVy', 'teacher', 1, '2025-05-30 05:18:32', NULL, NULL, NULL),
(13, 'saleem ullah', 'saleemullah@gmail.com', '$2y$10$fp4rdd3JWuHHiEsEbogEBOHFbOfcS0XoaiaqdhD4u48EQupG7Ifqy', 'teacher', 1, '2025-05-30 05:24:50', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `content_progress`
--
ALTER TABLE `content_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `course_content`
--
ALTER TABLE `course_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `content_progress`
--
ALTER TABLE `content_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_content`
--
ALTER TABLE `course_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `content_progress`
--
ALTER TABLE `content_progress`
  ADD CONSTRAINT `content_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_progress_ibfk_2` FOREIGN KEY (`content_id`) REFERENCES `course_content` (`content_id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE SET NULL;

--
-- Constraints for table `course_content`
--
ALTER TABLE `course_content`
  ADD CONSTRAINT `course_content_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
