-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 02, 2023 at 12:51 PM
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
-- Database: `positionappDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` int(4) NOT NULL,
  `start_positions_id` int(4) DEFAULT NULL,
  `goal_positions_id` int(4) DEFAULT NULL,
  `goalordernumber` int(255) DEFAULT NULL,
  `users_id` int(4) DEFAULT NULL,
  `fallbackinitials` varchar(2) DEFAULT NULL,
  `groups_groupcode` varchar(10) DEFAULT NULL,
  `goalsession` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(4) NOT NULL,
  `groupcode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `groupcode`) VALUES
(53, '01A');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(4) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `imagepath` varchar(255) DEFAULT NULL,
  `dateofmessage` date DEFAULT NULL,
  `timeofmessage` time DEFAULT NULL,
  `users_id` int(4) DEFAULT NULL,
  `fallbackinitials` varchar(2) DEFAULT NULL,
  `fallbackcolor` varchar(7) DEFAULT NULL,
  `groups_groupcode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `message`, `imagepath`, `dateofmessage`, `timeofmessage`, `users_id`, `fallbackinitials`, `fallbackcolor`, `groups_groupcode`) VALUES
(179, 'test', NULL, '2022-11-22', '15:34:00', 5626, 'MK', '#5BC0EB', '01A'),
(180, 'test', NULL, '2022-11-22', '15:43:00', 5646, 'AA', '#FF0000', '01A'),
(181, 'test', NULL, '2022-11-22', '15:44:00', 5646, 'AA', '#FF0000', '01A'),
(186, 'test', NULL, '2022-11-30', '17:12:00', 6604, 'AA', '#000000', '01A'),
(191, 'test', NULL, '2022-11-30', '18:39:00', 6602, 'MK', '#5BC0EB', '01A'),
(192, 'test', NULL, '2022-11-30', '18:40:00', 6602, 'MK', '#5BC0EB', '01A'),
(200, 'test', NULL, '2022-12-05', '17:24:00', 6616, 'AA', '#FF0000', '01A'),
(201, 'test', NULL, '2022-12-07', '18:26:00', 6617, 'AA', '#FF0000', '01A'),
(202, 'test', NULL, '2022-12-07', '18:32:00', 6602, 'MK', '#5BC0EB', '01A'),
(203, 'test', NULL, '2022-12-07', '18:32:00', 6618, 'AA', '#FF0000', '01A'),
(205, 'test', NULL, '2022-12-07', '18:54:00', 6602, 'MK', '#5BC0EB', '01A'),
(206, 'test', NULL, '2022-12-07', '18:55:00', 6602, 'MK', '#5BC0EB', '01A'),
(207, 'test', NULL, '2022-12-07', '19:51:00', 6602, 'MK', '#5BC0EB', '01A'),
(209, 'test', NULL, '2022-12-13', '15:08:00', 6602, 'MK', 'skyblue', '01A'),
(210, 'test', NULL, '2022-12-16', '17:33:00', 6602, 'MK', 'skyblue', '01A'),
(211, 'test', NULL, '2022-12-16', '17:39:00', 6622, 'AA', 'blue', '01A');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(4) NOT NULL,
  `lat` decimal(65,6) DEFAULT NULL,
  `lng` decimal(65,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `lat`, `lng`) VALUES
(2, '63.115000', '21.635000'),
(3, '63.117500', '21.634240'),
(4, '63.124000', '21.634000'),
(5, '63.088000', '21.623000');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(4) NOT NULL,
  `positions_id` int(4) DEFAULT NULL,
  `initials` varchar(2) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `groups_groupcode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `positions_id`, `initials`, `color`, `groups_groupcode`) VALUES
(5633, 2, 'FF', '#FFBBAA', '01A'),
(5634, 3, 'GG', '#00FF00', '01A'),
(6619, 4, 'HH', '#ACF000', '01A'),
(6620, 5, 'JJ', '#49212A', '01A');

-- --------------------------------------------------------

--
-- Table structure for table `waypoints`
--

CREATE TABLE `waypoints` (
  `id` int(4) NOT NULL,
  `goals_id` int(4) DEFAULT NULL,
  `positions_id` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waypoints`
--
ALTER TABLE `waypoints`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2389;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162944;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6797;

--
-- AUTO_INCREMENT for table `waypoints`
--
ALTER TABLE `waypoints`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146147;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
