-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 21, 2022 at 06:42 PM
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
  `goalIndex` int(255) DEFAULT NULL,
  `users_id` int(4) DEFAULT NULL,
  `groups_groupcode` varchar(10) DEFAULT NULL,
  `goalsession` varchar(15) NOT NULL
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
  `groups_groupcode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `message`, `imagepath`, `dateofmessage`, `timeofmessage`, `users_id`, `groups_groupcode`) VALUES
(110, 'test', NULL, '2022-11-16', '18:52:47', 5626, '01A'),
(111, 'test', NULL, '2022-11-16', '18:56:00', 5626, '01A'),
(112, 'test', NULL, '2022-11-16', '18:58:00', 5633, '01A'),
(113, 'test', NULL, '2022-11-16', '19:51:00', 5626, '01A'),
(114, 'test', NULL, '2022-11-17', '16:03:00', 5626, '01A'),
(161, NULL, 'media/chat_images/01A/IfdHCJLrLL.png', '2022-11-21', '18:38:00', 5626, '01A');

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
(1, '63.110000', '21.630000'),
(2, '63.115000', '21.635000'),
(3, '63.117500', '21.634240');

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
(5634, 3, 'GG', '#00FF00', '01A');

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
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1923;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106382;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5640;

--
-- AUTO_INCREMENT for table `waypoints`
--
ALTER TABLE `waypoints`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99547;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
