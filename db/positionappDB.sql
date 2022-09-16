-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 16, 2022 at 01:41 PM
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
  `startlat` decimal(65,6) DEFAULT NULL,
  `startlng` decimal(65,6) DEFAULT NULL,
  `goallng` decimal(65,6) DEFAULT NULL,
  `goallat` decimal(65,6) DEFAULT NULL,
  `waypoints` varchar(1000) DEFAULT NULL,
  `goalID` int(255) DEFAULT NULL,
  `groups_groupcode` varchar(10) DEFAULT NULL
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
(27, 'qOv');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(4) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `initials` varchar(2) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `groups_groupcode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `message`, `initials`, `color`, `groups_groupcode`) VALUES
(40, 'test', 'MK', '#5BC0EB', 'qOv'),
(41, 'test', 'MK', '#5BC0EB', 'qOv'),
(42, 'another test', 'MK', '#5BC0EB', 'qOv'),
(46, 'test &#39;', 'MK', '#5BC0EB', 'qOv'),
(47, 'test . &#39; , &#34; # $', 'MK', '#5BC0EB', 'qOv'),
(48, 'test', 'MK', '#FF0000', 'qOv'),
(49, 'test', 'MK', '#5BC0EB', 'qOv');

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
(298, '63.102510', '21.618230');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(4) NOT NULL,
  `lat` decimal(65,6) DEFAULT NULL,
  `lng` decimal(65,6) DEFAULT NULL,
  `positions_id` int(4) DEFAULT NULL,
  `uniqueID` varchar(10) DEFAULT NULL,
  `initials` varchar(2) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `groups_groupcode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `lat`, `lng`, `positions_id`, `uniqueID`, `initials`, `color`, `groups_groupcode`) VALUES
(2499, '63.167000', '21.835000', NULL, 'testtest11', 'TT', '#FFAABB', 'qOv'),
(2500, '63.165000', '21.830000', NULL, 'testtest22', 'EE', '#AABBFF', 'qOv'),
(3104, '63.170900', '21.813000', NULL, 'testtest44', 'RR', '#dddddd', 'qOv'),
(3301, '63.102510', '21.618230', 298, 'HXYLCGi8Iu', 'MK', '#5BC0EB', 'qOv');

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=420;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3302;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
