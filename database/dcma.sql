-- Dynamic Class Management Application Database

-- Create the database
CREATE DATABASE IF NOT EXISTS dcma;
USE dcma;

-- Create users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('student','lecturer') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert sample data for testing
-- Passwords are 'password123' hashed with PASSWORD_BCRYPT
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('john_lecturer', '$2y$12$kCCsqetKRkLSMLqPkGNs.u.lxRSab/PFx9pzYjCi55QuyHen637dW', 'John Doe', 'john.doe@example.com', 'lecturer'),
('jane_lecturer', '$2y$12$kCCsqetKRkLSMLqPkGNs.u.lxRSab/PFx9pzYjCi55QuyHen637dW', 'Jane Smith', 'jane.smith@example.com', 'lecturer'),
('alice_student', '$2y$12$kCCsqetKRkLSMLqPkGNs.u.lxRSab/PFx9pzYjCi55QuyHen637dW', 'Alice Johnson', 'alice.j@example.com', 'student'),
('bob_student', '$2y$12$kCCsqetKRkLSMLqPkGNs.u.lxRSab/PFx9pzYjCi55QuyHen637dW', 'Bob Williams', 'bob.w@example.com', 'student'),
('charlie_student', '$2y$12$kCCsqetKRkLSMLqPkGNs.u.lxRSab/PFx9pzYjCi55QuyHen637dW', 'Charlie Brown', 'charlie.b@example.com', 'student');

-- Create classes table
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `lecturer_id` int(11) NOT NULL,
  `schedule` varchar(100) DEFAULT NULL,
  `room` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_code` (`class_code`),
  KEY `lecturer_id` (`lecturer_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert sample classes
INSERT INTO `classes` (`class_code`, `name`, `description`, `lecturer_id`, `schedule`, `room`, `capacity`) VALUES
('CS101', 'Introduction to Computer Science', 'Fundamentals of programming and computer science.', 1, 'Mon/Wed 10:00-11:30', 'A101', 50),
('MA202', 'Calculus II', 'Advanced topics in differential and integral calculus.', 1, 'Tue/Thu 13:00-14:30', 'B203', 40),
('PHY301', 'Modern Physics', 'Exploring the world of quantum mechanics and relativity.', 2, 'Fri 09:00-12:00', 'C305', 30);

-- Create enrollments table
CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `grade` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_class` (`student_id`,`class_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert sample enrollments
INSERT INTO `enrollments` (`student_id`, `class_id`, `grade`) VALUES
(3, 1, 'A'),
(3, 2, 'B'),
(4, 1, 'B'),
(5, 3, 'A');

-- Create attendance table
CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `enrollment_id` (`enrollment_id`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert sample attendance
INSERT INTO `attendance` (`enrollment_id`, `attendance_date`, `status`) VALUES
(1, '2025-03-10', 'present'),
(2, '2025-03-11', 'present'),
(3, '2025-03-10', 'absent'),
(4, '2025-03-13', 'late');
