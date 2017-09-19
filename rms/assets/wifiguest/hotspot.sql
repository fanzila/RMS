-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 01, 2014 at 10:56 AM
-- Server version: 5.5.35
-- PHP Version: 5.3.10-1ubuntu3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `hotspot`
--

-- --------------------------------------------------------

--
-- Table structure for table `creds`
--

CREATE TABLE IF NOT EXISTS `creds` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `email` varchar(30) NOT NULL,
  `clientIP` varchar(50) NOT NULL,
  `clientUserAgent` TEXT NOT NULL,
  `clientMac` varchar(50) NOT NULL,
  `optout` BOOLEAN NOT NULL DEFAULT TRUE,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `creds`
--

INSERT INTO `creds` (`id`, `email`, `optout`) VALUES
(1, 'somefake@address.com', 'true');

--
-- Table structure for table `params`
--

CREATE TABLE IF NOT EXISTS `params` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `wifi_pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
