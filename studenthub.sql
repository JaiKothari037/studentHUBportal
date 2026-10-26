-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 26, 2026 at 08:55 PM
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
-- Database: `studenthub`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT '127.0.0.1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `username`, `action`, `ip_address`, `created_at`) VALUES
(1, 9, 'admin_user', 'Accessed Admin Dashboard', '127.0.0.1', '2026-10-26 19:41:57'),
(2, 9, 'admin_user', 'Accessed Admin Dashboard', '127.0.0.1', '2026-10-26 19:44:04');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(100) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'General',
  `status` enum('open','closed') DEFAULT 'open',
  `poster` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `event_date`, `venue`, `title`, `description`, `category`, `status`, `poster`) VALUES
(1, 'Hackathon', '2026-10-10', 'Lab \r\n301', 'Hackathon', NULL, 'General', 'open', NULL),
(2, 'Web Workshop', '2026-10-15', 'Lab \r\n302', 'Web Workshop', NULL, 'General', 'open', NULL),
(3, 'AI Seminar', '2026-10-20', 'Auditorium', 'AI Seminar', NULL, 'General', 'open', NULL),
(5, NULL, '2026-10-27', NULL, 'diwali', 'diwali', 'Technical', 'open', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `course`, `status`) VALUES
(1, 'Aarav Patel', 'aarav.p@gmail.com', 'CSE', 'active'),
(2, 'Ananya Sharma', 'ananya.s@gmail.com', 'IT', 'active'),
(3, 'Rohan Mehta', 'rohan.m@gmail.com', 'ECE', 'active'),
(4, 'Priya Shah', 'priya.s@gmail.com', 'CSE', 'active'),
(5, 'Dev Joshi', 'dev.j@gmail.com', 'Civil', 'active'),
(6, 'Kavya Nair', 'kavya.n@gmail.com', 'Mechanical', 'active'),
(7, 'Siddharth Verma', 'siddharth.v@gmail.com', 'CSE', 'active'),
(8, 'Diya Desai', 'diya.d@gmail.com', 'IT', 'active'),
(9, 'Yash Malhotra', 'yash.m@gmail.com', 'ECE', 'active'),
(10, 'Sneha Reddy', 'sneha.r@gmail.com', 'CSE', 'active'),
(11, 'Arjun Kumar', 'arjun.k@gmail.com', 'IT', 'active'),
(12, 'Riya Gupta', 'riya.g@gmail.com', 'Civil', 'active'),
(13, 'Aman Trivedi', 'aman.t@gmail.com', 'CSE', 'active'),
(14, 'Ishita Roy', 'ishita.r@gmail.com', 'ECE', 'active'),
(15, 'Varun Saxena', 'varun.s@gmail.com', 'Mechanical', 'active'),
(16, 'Tanvi Bhatt', 'tanvi.b@gmail.com', 'IT', 'active'),
(17, 'Karan Chopra', 'karan.c@gmail.com', 'CSE', 'active'),
(18, 'Neha Agarwal', 'neha.a@gmail.com', 'ECE', 'active'),
(19, 'Aditya Singhania', 'aditya.s@gmail.com', 'Civil', 'active'),
(20, 'Pooja Chaudhary', 'pooja.c@gmail.com', 'IT', 'inactive'),
(21, 'Jai Kothari', 'jaikothari1427@gmail.com', 'IT', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','student') NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'JaiKothari', 'jaikothari1427@gmail.com', '$2y$10$MTTyWj46rJEK/Ld1YtCSgenhtOWkX3aX92oCgSgUVtnPCrBsp03Me', '2026-10-26 13:42:20', 'student'),
(4, '25cs037', 'teststudent@gmail.com', '$2y$10$i8hEEk3zDEWSGBaBBWt6quvi/n3FuDYvK8HxiSHgRXiXnoEuqOXNK', '2026-10-26 17:02:56', 'student'),
(6, 'student_user', 'student@gmail.com', '$2y$10$89.g21/708.S1a8/G1n9O.Pq9j4zW3V2x3W7W1w1q1q1q1q1q1q1q', '2026-10-26 17:09:24', 'student'),
(8, 'new_admin', 'newadmin@gmail.com', '$2y$10$89.g21/708.S1a8/G1n9O.Pq9j4zW3V2x3W7W1w1q1q1q1q1q1q1q', '2026-10-26 17:18:29', 'admin'),
(9, 'admin_user', 'admin@gmail.com', '$2y$10$ovtTQpLRvaqvey9hW1AzTu5U.RLX/wfNQf73ckvuZtI.9zJcwCilC', '2026-10-26 17:19:55', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
