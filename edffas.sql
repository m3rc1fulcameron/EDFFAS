-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2015 at 03:02 AM
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `edffas`
--

-- --------------------------------------------------------

--
-- Table structure for table `bounties`
--

CREATE TABLE IF NOT EXISTS `bounties` (
`id` int(11) unsigned NOT NULL,
  `postPlayerID` int(11) unsigned NOT NULL,
  `targetPlayerID` int(11) unsigned NOT NULL,
  `amount` int(12) unsigned DEFAULT NULL,
  `reason` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `bounties`
--

INSERT INTO `bounties` (`id`, `postPlayerID`, `targetPlayerID`, `amount`, `reason`) VALUES
(1, 1, 1, 20000001, 'This is a test bounty with an amount set at $20,000,000'),
(2, 2, 1, 20000, 'Test of the bounty system.'),
(3, 1, 3, 300250000, 'Jerk');

-- --------------------------------------------------------

--
-- Table structure for table `factions`
--

CREATE TABLE IF NOT EXISTS `factions` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `abbreviatedName` varchar(255) COLLATE utf8_bin NOT NULL,
  `allegianceID` int(11) unsigned NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Dumping data for table `factions`
--

INSERT INTO `factions` (`id`, `name`, `abbreviatedName`, `allegianceID`) VALUES
(1, 'Independent Dummy', 'IDF', 1),
(2, 'Empire Dummy', 'EDF', 2),
(3, 'Federation Dummy', 'FDF', 3),
(4, 'Alliance Dummy', 'ADF', 4),
(5, 'No abbreviatedName dummy', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE IF NOT EXISTS `players` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `rankID` int(11) unsigned DEFAULT NULL,
  `shipID` int(11) unsigned DEFAULT NULL,
  `factionID` int(11) unsigned DEFAULT NULL,
  `powerID` int(11) unsigned DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id`, `name`, `rankID`, `shipID`, `factionID`, `powerID`, `notes`) VALUES
(1, 'test', 1, 1, 1, 8, 'This is a test account.'),
(2, 'Test2', 3, 3, 2, 4, 'This is a second test account.'),
(3, 'TeSt3', 5, 8, 3, 6, 'Third Test Account'),
(4, 'tesT4', 7, 12, 4, NULL, 'some Other account');

-- --------------------------------------------------------

--
-- Table structure for table `powers`
--

CREATE TABLE IF NOT EXISTS `powers` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `allegianceID` int(11) unsigned NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

--
-- Dumping data for table `powers`
--

INSERT INTO `powers` (`id`, `name`, `allegianceID`) VALUES
(1, 'Arissa Lavigny-Duval', 2),
(2, 'Aisling Duval', 2),
(3, 'Archon Delaine', 1),
(4, 'Denton Patreus', 2),
(5, 'Edmund Mahon', 4),
(6, 'Felicia Winters', 3),
(7, 'Li Yong-Rui', 1),
(8, 'Pranav Antal', 1),
(9, 'Zachary Hudson', 3),
(10, 'Zemina Torval', 2);

-- --------------------------------------------------------

--
-- Table structure for table `ranks`
--

CREATE TABLE IF NOT EXISTS `ranks` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- Dumping data for table `ranks`
--

INSERT INTO `ranks` (`id`, `name`) VALUES
(1, 'Harmless'),
(2, 'Mostly Harmless'),
(3, 'Novice'),
(4, 'Competent'),
(5, 'Expert'),
(6, 'Master'),
(7, 'Dangerous'),
(8, 'Deadly'),
(9, 'Elite');

-- --------------------------------------------------------

--
-- Table structure for table `ships`
--

CREATE TABLE IF NOT EXISTS `ships` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=21 ;

--
-- Dumping data for table `ships`
--

INSERT INTO `ships` (`id`, `name`) VALUES
(1, 'Adder'),
(2, 'Anaconda'),
(3, 'Asp Explorer'),
(4, 'Cobra'),
(5, 'Diamondback Explorer'),
(6, 'Diamondback Scout'),
(7, 'Eagle'),
(8, 'Federal Dropship'),
(9, 'Fer-de-Lance'),
(10, 'Hauler'),
(11, 'Imperial Clipper'),
(12, 'Imperial Courier'),
(13, 'Orca'),
(14, 'Python'),
(15, 'Sidewinder'),
(16, 'Type-6 Transporter'),
(17, 'Type-7 Transporter'),
(18, 'Type-9 Heavy'),
(19, 'Viper'),
(20, 'Vulture');

-- --------------------------------------------------------

--
-- Table structure for table `superpowers`
--

CREATE TABLE IF NOT EXISTS `superpowers` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `backgroundColor` varchar(255) COLLATE utf8_bin NOT NULL,
  `textColor` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `superpowers`
--

INSERT INTO `superpowers` (`id`, `name`, `backgroundColor`, `textColor`) VALUES
(1, 'Independent', '#FFBF00', '#8A4B08'),
(2, 'Empire', '#2E64FE', '#0404B4'),
(3, 'Federation', '#04B404', '#0B3B0B'),
(4, 'Alliance', '#FF0000', '#610B0B');

-- --------------------------------------------------------

--
-- Table structure for table `wantedadvisories`
--

CREATE TABLE IF NOT EXISTS `wantedadvisories` (
`id` int(11) unsigned NOT NULL,
  `targetPlayerID` int(11) unsigned NOT NULL,
  `factionID` int(11) unsigned NOT NULL,
  `reason` text COLLATE utf8_bin
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `wantedadvisories`
--

INSERT INTO `wantedadvisories` (`id`, `targetPlayerID`, `factionID`, `reason`) VALUES
(1, 1, 1, 'This is a test. EIC wants test'),
(2, 1, 2, 'This is another test of wanted advisories.');

-- --------------------------------------------------------

--
-- Table structure for table `watches`
--

CREATE TABLE IF NOT EXISTS `watches` (
`id` int(11) unsigned NOT NULL,
  `targetPlayerID` int(11) unsigned NOT NULL,
  `postPlayerID` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bounties`
--
ALTER TABLE `bounties`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factions`
--
ALTER TABLE `factions`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `powers`
--
ALTER TABLE `powers`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ranks`
--
ALTER TABLE `ranks`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ships`
--
ALTER TABLE `ships`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `superpowers`
--
ALTER TABLE `superpowers`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wantedadvisories`
--
ALTER TABLE `wantedadvisories`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `watches`
--
ALTER TABLE `watches`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bounties`
--
ALTER TABLE `bounties`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `factions`
--
ALTER TABLE `factions`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `powers`
--
ALTER TABLE `powers`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `ranks`
--
ALTER TABLE `ranks`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ships`
--
ALTER TABLE `ships`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `superpowers`
--
ALTER TABLE `superpowers`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `wantedadvisories`
--
ALTER TABLE `wantedadvisories`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `watches`
--
ALTER TABLE `watches`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
