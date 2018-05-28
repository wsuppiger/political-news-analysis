-- phpMyAdmin SQL Dump
-- version 4.0.10.18
-- https://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: May 28, 2018 at 03:30 PM
-- Server version: 5.6.39-cll-lve
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `projectNews`
--

-- --------------------------------------------------------

--
-- Table structure for table `onlineData`
--

CREATE TABLE IF NOT EXISTS `onlineData` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `news source` text NOT NULL,
  `url` text NOT NULL,
  `story` text NOT NULL,
  `anger` double NOT NULL,
  `disgust` double NOT NULL,
  `fear` double NOT NULL,
  `joy` double NOT NULL,
  `sadness` double NOT NULL,
  `analytical` double NOT NULL,
  `confident` double NOT NULL,
  `tentative` double NOT NULL,
  `openness_big5` double NOT NULL,
  `conscientiousness_big5` double NOT NULL,
  `extraversion_big5` double NOT NULL,
  `agreeableness_big5` double NOT NULL,
  `emotional_range_big5` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
