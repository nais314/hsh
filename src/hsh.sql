-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 14, 2017 at 01:05 PM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hsh`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `id` varchar(32) NOT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`id`, `value`) VALUES
('division', '[{\"key\":\"1\",\"value\":\"314\",\"css_class\":\"css_class\",\"css_style\":\"css_style\"},{\"key\":\"2\",\"value\":\"alma\",\"css_class\":\"css_class\",\"css_style\":\"css_style\"},{\"key\":\"3\",\"value\":\"alma updated\",\"css_class\":\"css_class\",\"css_style\":\"css_style\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `division`
--

CREATE TABLE `division` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent` int(11) UNSIGNED DEFAULT NULL,
  `css_class` varchar(32) DEFAULT NULL,
  `css_style` varchar(255) DEFAULT NULL,
  `has_child` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `hostname` varchar(255) DEFAULT NULL,
  `scenario` varchar(12) DEFAULT NULL,
  `default_action` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `division`
--

INSERT INTO `division` (`id`, `parent`, `css_class`, `css_style`, `has_child`, `name`, `hostname`, `scenario`, `default_action`) VALUES
(1, NULL, NULL, NULL, 1, '314', 'localhost hsh', NULL, 'site/index'),
(2, 1, NULL, NULL, 0, 'alma', 'somehost', NULL, NULL),
(3, NULL, NULL, NULL, 0, 'alma updated', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `rootdiv` int(11) UNSIGNED NOT NULL,
  `division_id` int(11) UNSIGNED DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','super','member','newbie','guest') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `rootdiv`, `division_id`, `password`, `role`) VALUES
(1, 'admin', 1, 1, '$2y$10$27XJRDHIQv/SDUut7f4iCuvfvlTjN3nSAg9lQo2QwrwCJ1Ken9UnW', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `division`
--
ALTER TABLE `division`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `address` (`hostname`),
  ADD KEY `indx_div_parent` (`parent`),
  ADD KEY `indx_div_address` (`hostname`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rootdiv` (`rootdiv`),
  ADD KEY `division_id` (`division_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `division`
--
ALTER TABLE `division`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `constr_user_div` FOREIGN KEY (`division_id`) REFERENCES `division` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `constr_user_rootdiv` FOREIGN KEY (`rootdiv`) REFERENCES `division` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
