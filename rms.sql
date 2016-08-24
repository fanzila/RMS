-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 24, 2016 at 09:13 AM
-- Server version: 5.5.50-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `demo_rms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_balance`
--

CREATE TABLE IF NOT EXISTS `bank_balance` (
  `val` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bank_balance`
--

INSERT INTO `bank_balance` (`val`) VALUES
('1053');

-- --------------------------------------------------------

--
-- Table structure for table `bus`
--

CREATE TABLE IF NOT EXISTS `bus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `zip` int(10) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bu_info` text COLLATE utf8_unicode_ci,
  `training_link` text COLLATE utf8_unicode_ci NOT NULL,
  `pos_db_dir` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pos_archives_dir` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_pos_cash_method` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_order` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `delivery_header` text COLLATE utf8_unicode_ci NOT NULL,
  `delivery_info` text COLLATE utf8_unicode_ci NOT NULL,
  `pushover_device` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bus`
--

INSERT INTO `bus` (`id`, `name`, `country`, `zip`, `description`, `bu_info`, `training_link`, `pos_db_dir`, `pos_archives_dir`, `id_pos_cash_method`, `email_order`, `delivery_header`, `delivery_info`, `pushover_device`) VALUES
(1, 'FR75BANQ', 'FR', 75002, '', '<h1>Welcome in your personnal space.</h1><p>You find usefull informations put by managers, check your skills and manage your trainees if you have some.</p>', 'here a link for an how to work page embedded in an iFrame', '/location/of/the/cashpad.db', '/location/of/the/cashpad/archives', 'to fill', 'order-fr75banq@yourRestaurant.com', '/!\\SAMPLE/!\\\nCity Hall\nSIREN 123456789 12345\nAPE 1234A\nTVA FR12345678901\nDelivery address : 8, rue de la Banque\n75002 Paris\n/!\\SAMPLE/!\\', 'Put here a note which appear on each order sent to suppliers by FR75BANQ', 'FR75BANQ'),
(2, 'FR75LOUV', 'FR', 75001, '', '<h1>Welcome in your personnal space.</h1><p>You find usefull informations put by managers, check your skills and manage your trainees if you have some.</p>', 'here a link for an how to work page embedded in an iFrame', '/directory/of/cashpad/db/for/FR75LOUV/db.db', '', 'to fill', 'order-fr75louv@yourRestaurant.com', '/!\\SAMPLE/!\\\r\nCity Hall\r\nSIREN 123456789 12345\r\nAPE 1234A\r\nTVA FR12345678901\r\nDelivery address : 4, place du Louvre\r\n75001 Paris\r\n/!\\SAMPLE/!\\', 'Put here a note which appear on each order sent to suppliers by FR75LOUV', 'FR75LOUV');

-- --------------------------------------------------------

--
-- Table structure for table `cameras`
--

CREATE TABLE IF NOT EXISTS `cameras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_bu` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `adress` text COLLATE utf8_unicode_ci NOT NULL,
  `adress_local` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_bu` (`id_bu`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `cameras`
--

INSERT INTO `cameras` (`id`, `id_bu`, `name`, `adress`, `adress_local`) VALUES
(1, 1, 'cam1', 'http://194.103.218.16/mjpg/video.mjpg', 'http://194.103.218.16/mjpg/video.mjpg'),
(2, 1, 'cam2', 'The external http link of the third camera', 'The internal http link of the third camera'),
(3, 1, 'cam3', 'The external http link of the third camera', 'The internal http link of the third camera');

-- --------------------------------------------------------

--
-- Table structure for table `checklists`
--

CREATE TABLE IF NOT EXISTS `checklists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Dumping data for table `checklists`
--

INSERT INTO `checklists` (`id`, `name`, `active`, `order`, `id_bu`) VALUES
(2, 'PAUSE', 0, 2, 1),
(3, 'OPENING', 1, 1, 1),
(4, 'CLOSING', 1, 3, 1),
(5, 'ORDER: 3/week', 0, 4, 1),
(6, 'ORDER: weekly', 0, 5, 1),
(7, 'ORDER: monthly', 0, 6, 1),
(9, 'OPENING', 0, 1, 2),
(10, 'CLOSING', 0, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `checklist_records`
--

CREATE TABLE IF NOT EXISTS `checklist_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `id_checklist` int(11) NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_2` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=969 ;

--
-- Dumping data for table `checklist_records`
--

INSERT INTO `checklist_records` (`id`, `user`, `date`, `id_checklist`, `data`) VALUES
(959, '1', '2016-06-24 10:49:50', 3, 'a:14:{s:9:"comment-1";s:25:"First record for the demo";i:68;s:2:"on";s:10:"comment-68";s:0:"";i:7;s:2:"on";s:9:"comment-7";s:0:"";i:12;s:2:"on";s:10:"comment-12";s:0:"";i:21;s:2:"on";s:10:"comment-21";s:19:"now it''s automatic!";s:4:"user";s:1:"1";s:12:"id_checklist";s:1:"3";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"OPENING";s:16:"checklist_rec_id";s:0:"";}'),
(960, '3', '2016-06-24 10:50:57', 4, 'a:10:{s:10:"comment-27";s:20:"First closing record";i:35;s:2:"on";s:10:"comment-35";s:0:"";i:38;s:2:"on";s:10:"comment-38";s:0:"";s:4:"user";s:1:"3";s:12:"id_checklist";s:1:"4";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"CLOSING";s:16:"checklist_rec_id";s:0:"";}'),
(961, '2', '2016-07-11 17:31:40', 3, 'a:15:{i:1;s:2:"on";s:9:"comment-1";s:0:"";i:68;s:2:"on";s:10:"comment-68";s:0:"";i:7;s:2:"on";s:9:"comment-7";s:0:"";i:12;s:2:"on";s:10:"comment-12";s:0:"";i:21;s:2:"on";s:10:"comment-21";s:0:"";s:4:"user";s:1:"2";s:12:"id_checklist";s:1:"3";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"OPENING";s:16:"checklist_rec_id";s:0:"";}'),
(962, '3', '2016-07-12 09:10:43', 4, 'a:11:{i:27;s:2:"on";s:10:"comment-27";s:0:"";i:35;s:2:"on";s:10:"comment-35";s:0:"";i:38;s:2:"on";s:10:"comment-38";s:0:"";s:4:"user";s:1:"3";s:12:"id_checklist";s:1:"4";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"CLOSING";s:16:"checklist_rec_id";s:0:"";}'),
(963, '2', '2016-07-12 09:15:59', 3, 'a:15:{i:1;s:2:"on";s:9:"comment-1";s:0:"";i:68;s:2:"on";s:10:"comment-68";s:0:"";i:7;s:2:"on";s:9:"comment-7";s:0:"";i:12;s:2:"on";s:10:"comment-12";s:0:"";i:21;s:2:"on";s:10:"comment-21";s:0:"";s:4:"user";s:1:"2";s:12:"id_checklist";s:1:"3";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"OPENING";s:16:"checklist_rec_id";s:0:"";}'),
(964, '5', '2016-07-21 12:56:29', 3, 'a:14:{i:1;s:2:"on";s:9:"comment-1";s:0:"";i:68;s:2:"on";s:10:"comment-68";s:0:"";s:9:"comment-7";s:2:"sv";i:12;s:2:"on";s:10:"comment-12";s:0:"";i:21;s:2:"on";s:10:"comment-21";s:0:"";s:4:"user";s:1:"5";s:12:"id_checklist";s:1:"3";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"OPENING";s:16:"checklist_rec_id";s:0:"";}'),
(965, '3', '2016-07-21 12:58:57', 3, 'a:14:{s:9:"comment-1";s:3:"dsv";i:68;s:2:"on";s:10:"comment-68";s:0:"";i:7;s:2:"on";s:9:"comment-7";s:0:"";i:12;s:2:"on";s:10:"comment-12";s:0:"";i:21;s:2:"on";s:10:"comment-21";s:0:"";s:4:"user";s:1:"3";s:12:"id_checklist";s:1:"3";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"OPENING";s:16:"checklist_rec_id";s:0:"";}'),
(966, '2', '2016-07-21 12:59:49', 3, 'a:14:{s:9:"comment-1";s:4:"dsfv";i:68;s:2:"on";s:10:"comment-68";s:0:"";i:7;s:2:"on";s:9:"comment-7";s:0:"";i:12;s:2:"on";s:10:"comment-12";s:0:"";i:21;s:2:"on";s:10:"comment-21";s:0:"";s:4:"user";s:1:"2";s:12:"id_checklist";s:1:"3";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"OPENING";s:16:"checklist_rec_id";s:0:"";}'),
(967, '5', '2016-07-25 10:55:55', 3, 'a:14:{i:1;s:2:"on";s:9:"comment-1";s:0:"";i:68;s:2:"on";s:10:"comment-68";s:0:"";i:7;s:2:"on";s:9:"comment-7";s:0:"";i:12;s:2:"on";s:10:"comment-12";s:0:"";s:10:"comment-21";s:4:"lala";s:4:"user";s:1:"5";s:12:"id_checklist";s:1:"3";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"OPENING";s:16:"checklist_rec_id";s:0:"";}'),
(968, '3', '2016-07-25 10:56:28', 4, 'a:11:{i:27;s:2:"on";s:10:"comment-27";s:0:"";i:35;s:2:"on";s:10:"comment-35";s:6:"dfghjk";i:38;s:2:"on";s:10:"comment-38";s:0:"";s:4:"user";s:1:"3";s:12:"id_checklist";s:1:"4";s:6:"action";s:10:"save_tasks";s:14:"checklist_name";s:7:"CLOSING";s:16:"checklist_rec_id";s:0:"";}');

-- --------------------------------------------------------

--
-- Table structure for table `checklist_tasks`
--

CREATE TABLE IF NOT EXISTS `checklist_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_checklist` int(11) NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `priority` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  `day_week_num` set('0','2','3','4','5','6','1') COLLATE utf8_unicode_ci DEFAULT NULL,
  `day_month_num` set('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=69 ;

--
-- Dumping data for table `checklist_tasks`
--

INSERT INTO `checklist_tasks` (`id`, `id_checklist`, `name`, `comment`, `priority`, `active`, `order`, `day_week_num`, `day_month_num`) VALUES
(1, 3, 'Turn lights on (table `checklist_tasks` in the db)', 'Description of the task. This way, impossible to do it wrong ^^', '1', 1, 100, NULL, NULL),
(7, 3, 'Check the desserts stock', 'Description of the task. This way, impossible to do it wrong ^^', '2', 1, 800, NULL, NULL),
(12, 3, 'Check bread stock : classic + gluten free', 'Description of the task. This way, impossible to do it wrong ^^', '3', 1, 1200, NULL, NULL),
(21, 3, 'Start the music', 'Description of the task. This way, impossible to do it wrong ^^', '1', 1, 2100, NULL, NULL),
(27, 4, 'Turn off the caffee machine', 'Description of the task. This way, impossible to do it wrong ^^', '2', 1, 800, NULL, NULL),
(35, 4, 'Clean', 'Description of the task. This way, impossible to do it wrong ^^', '1', 1, 12000, NULL, NULL),
(38, 4, 'Close the cashier and record it following the process', 'Description of the task. This way, impossible to do it wrong ^^', '3', 1, 15000, NULL, NULL),
(68, 3, 'Count the cash, record it in the cashier', 'Description of the task. This way, impossible to do it wrong ^^', '3', 1, 450, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) DEFAULT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('311aab0357bee7ba438e96ed28b5ee0d', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0', 1472022753, 'a:1:{s:9:"user_data";s:0:"";}');

-- --------------------------------------------------------

--
-- Table structure for table `discount`
--

CREATE TABLE IF NOT EXISTS `discount` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nature` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `client` text NOT NULL,
  `reason` text NOT NULL,
  `id_user` int(11) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `discount_log`
--

CREATE TABLE IF NOT EXISTS `discount_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_discount` int(11) NOT NULL,
  `nature` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `client` text NOT NULL,
  `reason` text NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `event_type` enum('create','update') NOT NULL DEFAULT 'create',
  `deleted` tinyint(1) NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `level`) VALUES
(1, 'admin', 'Administrator', 3),
(2, 'staff', '', 0),
(3, 'manager1', 'The manager with the fewer rights', 1),
(4, 'manager2', 'The manager with the most rights', 2),
(5, 'extra', 'extra', 0);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `text`, `picture`, `date`, `id_user`) VALUES
(1, 'RMS News!', 'rms-news', '<p class="p1">Hello,&nbsp;</p>\n<p class="p2">&nbsp;</p>\n<p class="p1">Some infos :</p>\n<p class="p2">&nbsp;</p>\n<p class="p1">* Here you will find&nbsp;</p>\n<p class="p1">A news system that is design to be easy to read and be accessed..</p>\n<p class="p1">This is make to automaticaly send the text by mail to all the staff ;).</p>\n<p class="p2">&nbsp;</p>\n<p class="p1">Cheers,&nbsp;</p>\n<p class="p1">&mdash;&nbsp;</p>\n<p class="p1">fanzila</p>\n\n<p class="p3"><span class="s1">gitHub : <a href="https://github.com/fanzila/RMS"><span class="s2">https://github.com/fanzila/RMS</span></a></span></p>\n<p class="p3"><span class="s1">web : <a href="http://fanzila.github.io/RMS"><span class="s2">Website</span></a></span></p>', 'logoTresGrandGitHub.png', '2015-08-24 09:15:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `news_bus`
--

CREATE TABLE IF NOT EXISTS `news_bus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_news` int(11) NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_news` (`id_news`,`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `news_bus`
--

INSERT INTO `news_bus` (`id`, `id_news`, `id_bu`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `news_confirm`
--

CREATE TABLE IF NOT EXISTS `news_confirm` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_news` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('none','sent','confirmed','error') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `date_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_confirmed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id_user` int(11) NOT NULL,
  `IP` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `news_confirm`
--

INSERT INTO `news_confirm` (`id`, `id_news`, `key`, `status`, `date_sent`, `date_confirmed`, `id_user`, `IP`) VALUES
(1, 1, '1fffbf1feeca7955f78bde14655ef367', 'sent', '2016-07-12 14:13:04', '0000-00-00 00:00:00', 1, ''),
(2, 1, 'e6b066cdeffff5212bb17cef0cbb0dbc', 'sent', '2016-07-12 14:13:04', '0000-00-00 00:00:00', 2, ''),
(3, 1, '572dbb29092686e755fb03a90f06e581', 'sent', '2016-07-12 14:13:05', '0000-00-00 00:00:00', 3, ''),
(4, 1, '8e95f46d05db27c69fc1612e2eb2c9bf', 'sent', '2016-07-12 14:13:05', '0000-00-00 00:00:00', 5, ''),
(5, 1, 'ab0e4d3df6a8af04c6d43bc07504f0d2', 'sent', '2016-07-12 14:13:05', '0000-00-00 00:00:00', 6, '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idorder` int(11) NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `data` longblob NOT NULL,
  `status` enum('draft','sent') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'draft',
  `supplier_id` int(255) NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idorder_2` (`idorder`),
  KEY `idorder` (`idorder`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `idorder`, `user`, `date`, `data`, `status`, `supplier_id`, `id_bu`) VALUES
(1, 1606274587, '1', '2016-06-27 13:21:34', 0x613a333a7b693a303b613a31323a7b733a323a226964223b693a333b733a343a2271747479223b733a313a2231223b733a343a226e616d65223b733a383a226d696e7420726f64223b733a393a227061636b6167696e67223b733a313a2231223b733a383a22756e69746e616d65223b733a353a2242554e4348223b733a353a22636f646566223b733a303a22223b733a373a2269646f72646572223b733a31303a2231363036323734353837223b733a383a22737570706c696572223b733a393a22535550504c49455231223b733a353a227375706964223b733a313a2231223b733a353a227072696365223b733a333a22353030223b733a383a226174747269627574223b733a313a2230223b733a31333a22737562746f74616c7072696365223b693a3530303b7d693a313b613a31323a7b733a323a226964223b693a353b733a343a2271747479223b733a313a2232223b733a343a226e616d65223b733a363a22746f6d61746f223b733a393a227061636b6167696e67223b733a313a2231223b733a383a22756e69746e616d65223b733a343a224b494c4f223b733a353a22636f646566223b733a303a22223b733a373a2269646f72646572223b733a31303a2231363036323734353837223b733a383a22737570706c696572223b733a393a22535550504c49455231223b733a353a227375706964223b733a313a2231223b733a353a227072696365223b733a343a2231363030223b733a383a226174747269627574223b733a313a2230223b733a31333a22737562746f74616c7072696365223b693a333230303b7d693a323b613a31323a7b733a323a226964223b693a363b733a343a2271747479223b733a313a2233223b733a343a226e616d65223b733a373a2261727567756c61223b733a393a227061636b6167696e67223b733a313a2231223b733a383a22756e69746e616d65223b733a333a22424f58223b733a353a22636f646566223b733a303a22223b733a373a2269646f72646572223b733a31303a2231363036323734353837223b733a383a22737570706c696572223b733a393a22535550504c49455231223b733a353a227375706964223b733a313a2231223b733a353a227072696365223b733a343a2239393030223b733a383a226174747269627574223b733a313a2230223b733a31333a22737562746f74616c7072696365223b693a32393730303b7d7d, 'sent', 1, 1),
(2, 1606276636, '1', '2016-06-27 13:21:35', 0x613a323a7b693a303b613a31323a7b733a323a226964223b693a373b733a343a2271747479223b733a313a2235223b733a343a226e616d65223b733a31333a2277686974652063616262616765223b733a393a227061636b6167696e67223b733a313a2231223b733a383a22756e69746e616d65223b733a353a225049454345223b733a353a22636f646566223b733a303a22223b733a373a2269646f72646572223b733a31303a2231363036323736363336223b733a383a22737570706c696572223b733a393a22535550504c49455233223b733a353a227375706964223b733a313a2233223b733a353a227072696365223b733a333a22393030223b733a383a226174747269627574223b733a313a2230223b733a31333a22737562746f74616c7072696365223b693a343530303b7d693a313b613a31323a7b733a323a226964223b693a393b733a343a2271747479223b733a313a2236223b733a343a226e616d65223b733a31353a22667265736820636f7269616e646572223b733a393a227061636b6167696e67223b733a313a2231223b733a383a22756e69746e616d65223b733a353a2242554e4348223b733a353a22636f646566223b733a303a22223b733a373a2269646f72646572223b733a31303a2231363036323736363336223b733a383a22737570706c696572223b733a393a22535550504c49455233223b733a353a227375706964223b733a313a2233223b733a353a227072696365223b733a333a22353030223b733a383a226174747269627574223b733a313a2230223b733a31333a22737562746f74616c7072696365223b693a333030303b7d7d, 'sent', 3, 1),
(3, 1606278839, '1', '2016-06-27 13:21:35', 0x613a313a7b693a303b613a31323a7b733a323a226964223b693a313b733a343a2271747479223b733a313a2234223b733a343a226e616d65223b733a353a226361726f74223b733a393a227061636b6167696e67223b733a313a2231223b733a383a22756e69746e616d65223b733a343a224b494c4f223b733a353a22636f646566223b733a303a22223b733a373a2269646f72646572223b733a31303a2231363036323738383339223b733a383a22737570706c696572223b733a393a22535550504c49455232223b733a353a227375706964223b733a313a2232223b733a353a227072696365223b733a333a22363030223b733a383a226174747269627574223b733a313a2230223b733a31333a22737562746f74616c7072696365223b693a323430303b7d7d, 'sent', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders_confirm`
--

CREATE TABLE IF NOT EXISTS `orders_confirm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idorder` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('none','sent','confirmed','error') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `date_sent` datetime NOT NULL,
  `date_confirmed` datetime NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `IP` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idorder` (`idorder`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Dumping data for table `orders_confirm`
--

INSERT INTO `orders_confirm` (`id`, `idorder`, `key`, `status`, `date_sent`, `date_confirmed`, `comment`, `IP`) VALUES
(8, 1606274587, 'ca56e8a83718c2341c6df2b89073b9a7', 'sent', '2016-06-27 15:21:34', '0000-00-00 00:00:00', '', ''),
(9, 1606278839, '4fd35e4e2478a011a222e1efa01dc07d', 'sent', '2016-06-27 15:21:35', '0000-00-00 00:00:00', '', ''),
(10, 1606276636, '3c908defe855f571d7dc3d3b04a8d705', 'sent', '2016-06-27 15:21:35', '0000-00-00 00:00:00', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `orders_products`
--

CREATE TABLE IF NOT EXISTS `orders_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `products` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_products_stock`
--

CREATE TABLE IF NOT EXISTS `orders_products_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `stock` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_suppliers`
--

CREATE TABLE IF NOT EXISTS `orders_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `suppliers` longblob NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `params`
--

CREATE TABLE IF NOT EXISTS `params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `val` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=113 ;

--
-- Dumping data for table `params`
--

INSERT INTO `params` (`id`, `key`, `val`) VALUES
(59, 'sms_api_account', 'fill with a mail address'),
(60, 'sms_api_key', 'fill with a key to generate'),
(61, 'notification_email', 'fill with a mail address'),
(62, 'sms_api_num', 'fill with a phone number'),
(68, 'twilio_sid', 'fill with the sid for twilio'),
(69, 'twilio_token', 'fill with the token for twilio'),
(70, 'twilio_num_from', 'fill with a phone number'),
(71, 'twilio_num_to', 'fill with a phone number'),
(72, 'twilio_app_sid', 'fill with the app_sid for twilio'),
(75, 'pushover_address', 'fill with the pushover address'),
(76, 'pushover_token', 'fill with the pushover token'),
(77, 'pushover_user', 'fill with the pushover user'),
(78, 'server_name', 'fill with the server address'),
(79, 'google_api_client_id', 'fill with google_api_client_id'),
(80, 'google_api_service_account_name', 'fill with google_api_service_account_name'),
(81, 'google_api_file_id_cuisine', 'fill with google_api_file_id_cuisine'),
(82, 'google_api_file_gid_cuisine', 'fill with google_api_file_gid_cuisine'),
(83, 'keylogin', 'fill with the key for app connexion'),
(84, 'keylogin_user', 'keylogin_user'),
(85, 'keylogin_pass', 'hank123'),
(86, 'keylogin_wiki_user', 'keylogin_user'),
(87, 'keylogin_wiki_pass', 'hank123'),
(88, 'google_api_file_id_supplier', 'fill with google_api_file_id_supplier'),
(89, 'google_api_file_gid_supplier', 'fill with google_api_file_gid_supplier'),
(102, 'myfox_user', 'fill with your myfox login'),
(103, 'myfox_pass', 'fill with your myfox pass'),
(104, 'myfox_client_id', 'fill with your myfox_client_id'),
(105, 'myfox_client_secret', 'fill with your myfox client secret'),
(106, 'api_box_url', 'fill with you api_box_url'),
(107, 'ifttmakerkey', 'fill with your key ifttmakerkey'),
(108, 'ovh_sms_user', 'fill with your ovh_sms_username'),
(109, 'ovh_sms_pass', 'fill with your ovh_sms_pass'),
(110, 'ovh_sms_nic', 'fill with your ovh_sms_nic'),
(111, 'welcome_email', 'Hello!\r\n\r\nWelcome in RMS\r\n\r\nThis is the default text for the welcome mail of new users.'),
(112, 'default_password', 'hank123');

-- --------------------------------------------------------

--
-- Table structure for table `pos_archives`
--

CREATE TABLE IF NOT EXISTS `pos_archives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_2` (`file`,`id_bu`),
  KEY `id` (`id`),
  KEY `id_bu` (`id_bu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pos_movements`
--

CREATE TABLE IF NOT EXISTS `pos_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movement` enum('middle','open','close','safe_in','safe_out','pos_in','pos_out') COLLATE utf8_unicode_ci NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `closing_file` text COLLATE utf8_unicode_ci NOT NULL,
  `closing_id` int(11) NOT NULL,
  `pos_cash_amount` float NOT NULL,
  `safe_cash_amount` float NOT NULL,
  `safe_tr_num` int(11) NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `pos_movements`
--

INSERT INTO `pos_movements` (`id`, `movement`, `id_user`, `date`, `comment`, `closing_file`, `closing_id`, `pos_cash_amount`, `safe_cash_amount`, `safe_tr_num`, `id_bu`) VALUES
(1, 'close', 2, '2016-02-23 21:18:57', '', 'a db name', 465, 400, 0, 0, 1),
(2, 'open', 3, '2016-02-24 11:16:55', '', '', 0, 400, 0, 0, 1),
(3, 'close', 2, '2016-02-24 21:11:56', '', 'a db name', 466, 1300, 0, 0, 1),
(4, 'open', 1, '2016-02-25 11:11:12', '', '', 0, 300, 0, 0, 1),
(5, 'close', 1, '2016-02-26 21:15:52', '', 'a db name', 468, 700, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pos_payments`
--

CREATE TABLE IF NOT EXISTS `pos_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_payment` int(11) NOT NULL,
  `id_movement` int(11) NOT NULL,
  `amount_pos` float NOT NULL,
  `amount_user` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_movement` (`id_movement`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pos_payments`
--

INSERT INTO `pos_payments` (`id`, `id_payment`, `id_movement`, `amount_pos`, `amount_user`) VALUES
(1, 2, 1, 1489.44, 1504.44),
(2, 3, 1, 173.4, 140.6);

-- --------------------------------------------------------

--
-- Table structure for table `pos_payments_type`
--

CREATE TABLE IF NOT EXISTS `pos_payments_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `report` enum('yes','no','other','') COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `pos_id` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_close` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_open` text COLLATE utf8_unicode_ci NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`,`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Dumping data for table `pos_payments_type`
--

INSERT INTO `pos_payments_type` (`id`, `name`, `report`, `active`, `pos_id`, `comment_close`, `comment_open`, `id_bu`) VALUES
(1, 'Cash', 'yes', 1, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1),
(2, 'Credit Card', 'yes', 1, 'BEE1F7F4-303E-49EC-ACD0-0F5C85D68B30', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1),
(3, 'Restaurant ticket', 'yes', 1, 'C834C277-9F78-4D7F-9377-0567166A8DDC', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1),
(4, 'Check', 'yes', 1, '08D131DD-838E-49BF-9881-7B7C020E0F54', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1),
(5, 'Delivroo', 'no', 1, 'A4D291F4-BE37-4957-9318-E01426A33049', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1),
(6, 'Take_Eat_Easy', 'no', 1, 'E89159E9-947D-4496-A603-BDD85F1F64F4', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1),
(7, 'Fidelity points', 'no', 1, 'ec34f65e-647d-11e2-b201-001a92ba1fbf', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1),
(9, 'Cashout', 'yes', 1, '', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1),
(10, 'Credit Note', 'no', 1, 'EB194EFF-1603-4076-8DED-F695F52E2BC9', 'Explain how to close the caschier whit this.', 'Explain how to open the caschier whit this.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `privmsgs`
--

CREATE TABLE IF NOT EXISTS `privmsgs` (
  `privmsg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `privmsg_author` bigint(20) NOT NULL,
  `privmsg_date` varchar(20) NOT NULL,
  `privmsg_subject` varchar(1024) NOT NULL,
  `privmsg_body` varchar(60000) NOT NULL,
  `privmsg_notify` tinyint(1) DEFAULT NULL,
  `privmsg_deleted` tinyint(1) DEFAULT NULL,
  `privmsg_ddate` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`privmsg_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `privmsgs`
--

INSERT INTO `privmsgs` (`privmsg_id`, `privmsg_author`, `privmsg_date`, `privmsg_subject`, `privmsg_body`, `privmsg_notify`, `privmsg_deleted`, `privmsg_ddate`) VALUES
(1, 1, '2016-08-24 09:07:50', 'Subject n°1 for report function  customisable in the dedicated crud or directly in report_subjects table', '<p>Body n°1 for report function. customisable in the dedicated crud or directly in report_subjects table</p>', 1, NULL, NULL),
(2, 1, '2016-08-24 09:08:00', 'Subject n°2 for report function  customisable in the dedicated crud or directly in report_subjects table', '<p>Body n°2 for report function. customisable in the dedicated crud or directly in report_subjects table</p>', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `privmsgs_to`
--

CREATE TABLE IF NOT EXISTS `privmsgs_to` (
  `pmto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pmto_message` bigint(20) NOT NULL,
  `pmto_recipient` bigint(20) NOT NULL,
  `pmto_read` tinyint(1) DEFAULT NULL,
  `pmto_rdate` varchar(20) DEFAULT NULL,
  `pmto_deleted` tinyint(1) DEFAULT NULL,
  `pmto_ddate` varchar(20) DEFAULT NULL,
  `pmto_allownotify` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`pmto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `privmsgs_to`
--

INSERT INTO `privmsgs_to` (`pmto_id`, `pmto_message`, `pmto_recipient`, `pmto_read`, `pmto_rdate`, `pmto_deleted`, `pmto_ddate`, `pmto_allownotify`) VALUES
(1, 1, 2, NULL, NULL, NULL, NULL, NULL),
(2, 1, 3, NULL, NULL, NULL, NULL, NULL),
(3, 2, 5, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `id_unit` int(11) NOT NULL,
  `packaging` float NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL,
  `id_category` int(11) NOT NULL,
  `freq_inventory` enum('low','medium','high') COLLATE utf8_unicode_ci NOT NULL,
  `pos_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_reference` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index` (`name`,`id_supplier`,`id_category`,`id_unit`),
  KEY `id_supplier` (`id_supplier`),
  KEY `active` (`active`),
  KEY `deleted` (`deleted`),
  KEY `pos_id` (`pos_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `id_supplier`, `price`, `id_unit`, `packaging`, `active`, `id_category`, `freq_inventory`, `pos_id`, `supplier_reference`, `comment`, `deleted`) VALUES
(1, 'arugula', 1, 9930, 4, 2, 1, 1, 'high', '', 'ref added', '', 0),
(2, 'carot', 2, 630, 3, 1, 1, 2, 'high', '', '', '', 0),
(3, 'fresh coriander', 3, 500, 5, 1, 1, 1, 'high', '', '', '', 0),
(4, 'lemon', 2, 2200, 3, 1, 1, 1, 'high', '', '', '', 0),
(5, 'mint rod', 1, 500, 5, 1, 1, 1, 'high', '', 'added here too', '', 0),
(6, 'orange', 2, 1300, 3, 1, 1, 1, 'high', '', '', '', 0),
(7, 'red onion', 3, 1200, 3, 1, 1, 1, 'high', '', '', '', 0),
(8, 'tomato', 1, 1600, 3, 1, 1, 1, 'high', '', '', '', 0),
(9, 'white cabbage', 3, 900, 1, 1, 1, 1, 'high', '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `products_attribut`
--

CREATE TABLE IF NOT EXISTS `products_attribut` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `products_category`
--

CREATE TABLE IF NOT EXISTS `products_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `products_category`
--

INSERT INTO `products_category` (`id`, `name`, `active`, `deleted`) VALUES
(1, 'none', 1, 0),
(2, 'vegetable', 1, 0),
(3, 'grocery', 1, 0),
(4, 'seasoning', 1, 0),
(5, 'frozen', 1, 0),
(6, 'fresh', 1, 0),
(7, 'cereal', 1, 0),
(8, 'drink', 1, 0),
(9, 'wrapping', 1, 0),
(10, 'consumable', 1, 0),
(11, 'flyer', 1, 0),
(12, 'none', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `products_mapping`
--

CREATE TABLE IF NOT EXISTS `products_mapping` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pos` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `coef` float NOT NULL DEFAULT '0',
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index` (`id_pos`,`id_product`),
  UNIQUE KEY `id_pos` (`id_pos`,`id_product`,`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `products_mapping`
--

INSERT INTO `products_mapping` (`id`, `id_pos`, `id_product`, `coef`, `id_bu`) VALUES
(1, 1, 1, 1, 1),
(2, 1, 3, 1.02, 1),
(3, 1, 4, 1, 1),
(4, 1, 5, 1.02, 1),
(5, 1, 9, 1.02, 1),
(6, 2, 1, 1, 1),
(7, 2, 2, 1.02, 1),
(8, 3, 2, 1, 1),
(9, 3, 9, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products_stock`
--

CREATE TABLE IF NOT EXISTS `products_stock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `warning` int(11) NOT NULL DEFAULT '0',
  `mini` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `last_update_user` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update_pos` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update_id_user` int(11) NOT NULL,
  `qtty` float NOT NULL DEFAULT '0',
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_product` (`id_product`,`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `products_stock`
--

INSERT INTO `products_stock` (`id`, `id_product`, `warning`, `mini`, `max`, `last_update_user`, `last_update_pos`, `last_update_id_user`, `qtty`, `id_bu`) VALUES
(1, 1, 0, 0, 0, '2016-08-19 13:31:06', '0000-00-00 00:00:00', 1, 0, 0),
(2, 2, 18, 11, 50, '2016-08-19 11:30:37', '0000-00-00 00:00:00', 1, 20, 0),
(3, 3, 0, 0, 0, '2016-07-12 07:08:32', '0000-00-00 00:00:00', 1, 0, 0),
(4, 4, 25, 15, 50, '2016-06-24 11:49:24', '0000-00-00 00:00:00', 1, 20, 0),
(5, 5, 10, 5, 50, '2016-08-19 13:30:27', '0000-00-00 00:00:00', 1, 20, 0),
(6, 6, 15, 8, 50, '2016-06-24 11:51:24', '0000-00-00 00:00:00', 1, 20, 0),
(7, 7, 70, 50, 99, '2016-06-24 11:52:24', '0000-00-00 00:00:00', 1, 20, 0),
(8, 8, 20, 10, 50, '2016-06-24 11:53:24', '0000-00-00 00:00:00', 1, 20, 0),
(9, 9, 20, 10, 50, '2016-06-24 11:54:24', '0000-00-00 00:00:00', 1, 20, 0);

-- --------------------------------------------------------

--
-- Table structure for table `products_unit`
--

CREATE TABLE IF NOT EXISTS `products_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

--
-- Dumping data for table `products_unit`
--

INSERT INTO `products_unit` (`id`, `name`, `comment`) VALUES
(0, '-', ''),
(1, 'PIECE', 'Piece'),
(2, 'GRM', 'Gram'),
(3, 'KILO', 'Kilo'),
(4, 'BOX', 'Box'),
(5, 'BUNCH', 'Bunch'),
(6, 'LITER', 'Liter'),
(7, 'ML', 'Mililiter'),
(8, 'PACKAGE', 'Package'),
(18, 'ROLL', 'Roll');

-- --------------------------------------------------------

--
-- Table structure for table `report_subjects`
--

CREATE TABLE IF NOT EXISTS `report_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `text` text NOT NULL,
  `bu_id` mediumint(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `report_subjects`
--

INSERT INTO `report_subjects` (`id`, `name`, `text`, `bu_id`) VALUES
(1, 'Subject n°1 for report function  customisable in the dedicated crud or directly in report_subjects table', 'Body n°1 for report function. customisable in the dedicated crud or directly in report_subjects table', 1),
(2, 'Subject n°2 for report function  customisable in the dedicated crud or directly in report_subjects table', 'Body n°2 for report function. customisable in the dedicated crud or directly in report_subjects table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rmd_log`
--

CREATE TABLE IF NOT EXISTS `rmd_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_task` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_task` (`id_task`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

--
-- Dumping data for table `rmd_log`
--

INSERT INTO `rmd_log` (`id`, `id_user`, `id_task`, `date`) VALUES
(1, 1, 1, '2016-06-27 10:58:44'),
(2, 2, 2, '2016-06-27 10:58:44'),
(3, 1, 3, '2016-06-27 10:58:44'),
(4, 6, 1, '2016-06-28 22:17:28'),
(5, 1, 2, '2016-07-04 10:23:08'),
(6, 6, 1, '2016-07-21 13:17:08'),
(7, 1, 1, '2016-07-25 11:01:11'),
(8, 1, 12, '2016-07-25 15:36:07'),
(9, 6, 12, '2016-07-25 15:37:46'),
(10, 1, 12, '2016-07-25 15:42:01'),
(11, 6, 1, '2016-07-25 15:42:17'),
(12, 2, 3, '2016-07-25 15:42:31'),
(13, 6, 13, '2016-07-25 15:50:53'),
(14, 6, 1, '2016-07-28 10:38:08'),
(15, 5, 1, '2016-08-23 17:05:52'),
(16, 3, 2, '2016-08-24 09:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `rmd_meta`
--

CREATE TABLE IF NOT EXISTS `rmd_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_task` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `repeat_interval` varchar(255) NOT NULL,
  `repeat_year` varchar(255) NOT NULL,
  `repeat_month` varchar(255) NOT NULL,
  `repeat_day` varchar(255) NOT NULL,
  `repeat_week` varchar(255) NOT NULL,
  `repeat_weekday` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_id` (`id_task`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `rmd_meta`
--

INSERT INTO `rmd_meta` (`id`, `id_task`, `start`, `repeat_interval`, `repeat_year`, `repeat_month`, `repeat_day`, `repeat_week`, `repeat_weekday`) VALUES
(1, 1, '2016-08-23 17:05:52', '1296000', '', '', '', '', ''),
(2, 3, '2016-07-25 15:42:31', '2592000', '', '', '', '', ''),
(3, 2, '2016-08-24 09:07:00', '2592000', '', '', '', '', ''),
(12, 13, '2016-07-25 15:50:53', '2592000', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `rmd_notif`
--

CREATE TABLE IF NOT EXISTS `rmd_notif` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_task` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `interval` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_task_2` (`id_task`),
  KEY `id_task` (`id_task`),
  KEY `id_task_3` (`id_task`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rmd_notif`
--

INSERT INTO `rmd_notif` (`id`, `id_task`, `start`, `end`, `interval`, `last`) VALUES
(1, 1, '10:00:00', '17:01:00', '18000', '2016-04-07 15:05:02'),
(2, 2, '10:00:00', '18:01:00', '25200', '0000-00-00 00:00:00'),
(3, 3, '10:00:00', '18:01:00', '25200', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `rmd_tasks`
--

CREATE TABLE IF NOT EXISTS `rmd_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` text COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `priority` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_bu` (`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rmd_tasks`
--

INSERT INTO `rmd_tasks` (`id`, `task`, `comment`, `active`, `priority`, `id_bu`) VALUES
(1, 'Clean the cold room', 'Description of the task. This way, impossible to do it wrong ^^', 1, '1', 1),
(2, 'Technical control', 'Description of the task. This way, impossible to do it wrong ^^', 1, '3', 1),
(3, 'Check stock packaging', 'Description of the task. This way, impossible to do it wrong ^^', 1, '2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_cashmovements`
--

CREATE TABLE IF NOT EXISTS `sales_cashmovements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pos` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` float NOT NULL,
  `method` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `archive` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `customer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_pos` (`id_pos`,`id_bu`),
  KEY `id_bu` (`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

--
-- Dumping data for table `sales_cashmovements`
--

INSERT INTO `sales_cashmovements` (`id`, `id_pos`, `date`, `user`, `amount`, `method`, `description`, `archive`, `customer`, `type`, `id_bu`) VALUES
(1, '7596CF1A-8AA8-478B-B8D8-AFCB360180EB', '2016-06-29 08:19:25', '35B7AB99-1823-4B81-8250-193108FD2CF5', -1410000, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(2, '88299356-A0B3-4A63-A186-318F850BE576', '2016-06-29 08:19:37', 'f9442164-73eb-11e0-91be-001a92ba1fbf', -12292600, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(3, '413167BD-BD66-44BE-8255-81AEFC88AB40', '2016-06-29 08:19:39', 'f9442164-73eb-11e0-91be-001a92ba1fbf', 5000, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(4, 'EEAA0B55-B407-48B7-8915-FAE09E5F1B4B', '2016-06-29 08:19:44', '090B2380-8839-40AF-8066-00AFFD894437', -1010000, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(5, '47B50808-D62A-4683-9531-9154588D17AC', '2016-06-29 08:19:46', 'f9442164-73eb-11e0-91be-001a92ba1fbf', -900000, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(6, 'AA46FA75-D39B-4849-B755-48B5D293B1E5', '2016-06-29 08:19:52', 'f9442164-73eb-11e0-91be-001a92ba1fbf', 10000, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(7, '62D27B0C-97FF-4438-BF11-AA8F0207AE61', '2016-06-29 08:19:49', 'f9442164-73eb-11e0-91be-001a92ba1fbf', 11500, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(8, 'D62E8209-4C90-4F9E-BF61-DC3B0CB0DD72', '2016-06-29 08:19:56', '9920E7AE-E499-4CFD-A033-B4BAEA676062', -40000, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(9, '13B362AB-D18A-4B33-AC2F-0EBA05B7E034', '2016-06-29 08:20:01', '9920E7AE-E499-4CFD-A033-B4BAEA676062', -1060000, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', '', 0, 1),
(10, 'e7f19c4d-a695-433e-9159-a9dcdf0e75a6', '2016-06-29 08:20:04', 'f9442164-73eb-11e0-91be-001a92ba1fbf', 10000, '7DD4A3FB-ADC2-49D9-9EDE-01129023FE37', '', 'a db file', 'C8F566D6-3464-4079-B9D2-CBCED16FEBD5', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_customers`
--

CREATE TABLE IF NOT EXISTS `sales_customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `zipcode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `loyalty_points` int(11) NOT NULL,
  `account` float NOT NULL,
  `balance` float NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pos_id` (`pos_id`,`id_bu`),
  KEY `id_bu` (`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `sales_customers`
--

INSERT INTO `sales_customers` (`id`, `pos_id`, `lastname`, `firstname`, `zipcode`, `city`, `country`, `email`, `phone`, `loyalty_points`, `account`, `balance`, `date_created`, `date_last_seen`, `deleted`, `id_bu`) VALUES
(1, '3D94A430-0E8E-4C2B-AF05-88F72F12818C', 'Client', 'First', '', '', '', '', '', 16, 125530, -139800, '2016-06-27 09:35:05', '2016-06-10 10:35:04', 0, 0),
(2, '451CDFBE-24FE-41BC-9084-5F71EA928567', '', '', '', '', '', '', '', 0, 0, 0, '2014-11-17 19:28:19', '0000-00-00 00:00:00', 1, 0),
(3, '8EB678F8-877C-4B13-9A2F-729B95AE8717', '', '', '', '', '', '', '', 0, 0, 0, '2016-06-27 09:49:32', '0000-00-00 00:00:00', 1, 0),
(4, 'BC856894-A983-47DB-9AEB-E8878D385100', '', '', '', '', '', '', '', 0, 0, 0, '2014-11-19 13:41:18', '0000-00-00 00:00:00', 1, 0),
(5, 'C1C0FBEA-8881-4E5A-A201-8D6F0282BD32', '', '', '', '', '', '', '', 0, 0, 0, '2016-06-27 09:49:36', '0000-00-00 00:00:00', 1, 0),
(6, 'EE0602B9-69CB-46D6-9511-43635991ABB0', '', '', '', '', '', '', '', 0, 0, 0, '2016-06-27 09:49:28', '0000-00-00 00:00:00', 1, 0),
(7, 'F35A6990-5F5B-48C5-8433-334E5A69C47F', 'Client', 'Second', '', '', '', '', '', 0, 0, 0, '2016-06-27 09:37:17', '0000-00-00 00:00:00', 0, 0),
(8, 'F4D5705B-B095-4207-81FA-D5303397F90B', '', '', '', '', '', '', '', 2, 0, 0, '2016-06-27 09:49:39', '2014-12-09 11:23:46', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sales_product`
--

CREATE TABLE IF NOT EXISTS `sales_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pos` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `last_update_stock` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_pos` (`id_pos`,`id_bu`),
  KEY `last_update_stock` (`last_update_stock`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sales_product`
--

INSERT INTO `sales_product` (`id`, `id_pos`, `name`, `category`, `deleted`, `last_update_stock`, `id_bu`) VALUES
(1, '04BDC61A-9CE4-41E5-8272-89B04DFB3D97', 'Tata Monique', '1A41C9AC-2BDA-421D-A64A-876A82F2A84F', 0, '2016-03-11 13:30:30', 1),
(2, 'DAA478C9-AB5B-44C3-A5E1-6E31C39C61A3', 'Carrot cake', '19E864A9-7FE1-4982-989D-F930E2C50091', 0, '2016-03-11 13:30:31', 1),
(3, '9B0AC897-B054-4FD3-ADDA-C9CC22B5B693', 'Without sauce', '609CE134-236E-4FA2-A534-28761DAF3E84', 0, '0000-00-00 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_productaddon`
--

CREATE TABLE IF NOT EXISTS `sales_productaddon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pos` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_pos_product` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `property_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_pos` (`id_pos`,`id_bu`),
  KEY `id_pos_product` (`id_pos_product`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=35 ;

--
-- Dumping data for table `sales_productaddon`
--

INSERT INTO `sales_productaddon` (`id`, `id_pos`, `id_pos_product`, `property_name`, `category`, `deleted`, `id_bu`) VALUES
(1, 'F8BA28B1-EF91-4E79-8E27-3BF970F68339', '518F9893-AD15-450C-8249-7A72A4E31F8F', '', 'DC992209-C8BD-47EB-906F-76518540EA4B', 0, 1),
(2, '22289E8E-AE35-4054-AE18-EBC8088991C0', '88D9970F-89AF-4510-B7F4-6DF6977FC181', '', 'DC992209-C8BD-47EB-906F-76518540EA4B', 0, 1),
(3, '62BCC566-F5B9-4FBB-9CC9-7F524B269FD6', '4DE5D100-82D3-4D86-B4C5-62885BAB0C10', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(4, 'C95796C0-A6F7-45EA-9018-E042381C74DC', '18AE5ED1-994C-4E26-A0C9-FB7B5A7F25AE', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(5, '9BA14CE2-61C2-4130-AB53-EF5E4AB00971', '279C61AC-1CF8-409E-BDF6-DF886DE568A2', '', 'DC992209-C8BD-47EB-906F-76518540EA4B', 0, 1),
(6, '6D7FA148-EB2B-4C70-8309-6DCCE26B1EE1', '3FEFFB66-A327-4723-8FC4-F30094129BAE', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(7, 'F00404B7-2434-4794-BC93-EF088ADC7896', 'EB367366-82CE-4CD6-B65C-3768FDF67DD9', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(8, '3E9B2B2E-7F7E-43CD-8202-3BFAB392B0AC', '16EE9C98-6AF8-4167-BA3E-2ACBEE03DF40', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(9, '0D30077B-8A1D-4700-954E-CE74BE5FE036', '2E37997F-3D88-4C1C-8032-8970BD688023', '', 'DC992209-C8BD-47EB-906F-76518540EA4B', 0, 1),
(11, '967FED67-B86F-463B-B217-768092E5428C', '0802A1F1-FCCB-488E-BA41-DF996B296114', '', '6F9A5C8D-4D90-4D6E-B78B-21413D06061F', 0, 1),
(12, '0664D838-EFCD-42B6-A705-AEC8889C94F1', 'C4FE067D-C0C6-4770-98D4-A61F001982AD', '', '6F9A5C8D-4D90-4D6E-B78B-21413D06061F', 1, 1),
(13, '3C02D6D8-A03A-424A-BD18-4AD2E47E778B', '67BBBE30-373B-4C68-9041-D41787410B85', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(14, 'D42B3BA9-B540-4783-8475-0D689F3D9954', '4395541F-EAB7-45B0-AFAC-FA69AC4D94E1', '', 'DC992209-C8BD-47EB-906F-76518540EA4B', 1, 1),
(15, '9119C2AC-BC55-49AB-979F-9D916816F08B', '9B0AC897-B054-4FD3-ADDA-C9CC22B5B693', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(16, '303AC5B1-BA81-46C6-BA93-870D4E26B259', 'E53F74CC-5CC2-45FE-902B-E12504BA9DE8', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(17, '7B376244-3A06-417E-8459-81B8C6DCF6EA', 'FF93E830-5357-43C1-ABCA-DAFCA8956945', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(18, '1B982C5B-03CB-40A3-8E29-D7658DFC4BC7', 'E3E3229B-CCCE-4749-BB2E-1057279D7B1A', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(19, '83CBD456-36AB-4764-A64B-9B81BBE90BF2', '6BDBE0D2-B551-4D92-A900-FC43CD5E0819', '', '7A52D0F8-5007-4F98-A27D-617B0CA9B5A5', 0, 1),
(20, '2F07AE56-5333-45A9-BF4A-D4BE60DF8CCE', '', 'Lemon', '32F68E6D-3012-4D3F-9674-2FBB1206592C', 0, 1),
(31, 'BDEAE19D-3073-4F11-A290-DD19B2B89915', '', 'Tata monique', '9A6AEFB5-7FF9-45BE-BE85-006B7291B615', 0, 1),
(32, '99DB5B98-DA66-4B27-A761-1811385602BE', '', 'Tomato', '63B7C597-142F-40EB-92B9-0CFC133EE31F', 0, 1),
(34, '427E629C-C9F0-4352-AC1B-70CD6426CB78', '', 'Onions', '63B7C597-142F-40EB-92B9-0CFC133EE31F', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_receipt`
--

CREATE TABLE IF NOT EXISTS `sales_receipt` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sequential_id` int(11) NOT NULL,
  `owner` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_closed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `canceled` tinyint(1) NOT NULL,
  `amount_total` int(11) NOT NULL,
  `amount_paid` int(11) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `period_id` int(11) NOT NULL,
  `id_bu` int(11) NOT NULL,
  UNIQUE KEY `sequential_id` (`sequential_id`,`id_bu`),
  KEY `date_closed` (`date_closed`),
  KEY `canceled` (`canceled`),
  KEY `done` (`done`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sales_receipt`
--

INSERT INTO `sales_receipt` (`id`, `sequential_id`, `owner`, `date_created`, `date_closed`, `canceled`, `amount_total`, `amount_paid`, `done`, `period_id`, `id_bu`) VALUES
('FEB6B5B7-FA0F-447C-98B6-AC1D9BF0F24A', 1, 'f9442164-73eb-11e0-91be-001a92ba1fbf', '2016-06-25 13:50:19', '2014-11-01 09:26:43', 1, 10500, 0, 1, 0, 1),
('BB922B96-83F1-4D35-9C81-BC7BFF66967E', 2, 'f9442164-73eb-11e0-91be-001a92ba1fbf', '2016-06-25 13:50:19', '2014-11-01 09:27:11', 0, 0, 0, 1, 0, 1),
('0FC5956A-0818-473A-826C-4EC54B3527F8', 3, 'f9442164-73eb-11e0-91be-001a92ba1fbf', '2016-06-25 13:50:19', '2014-11-01 10:18:52', 0, 11500, 0, 1, 0, 1),
('33FD3DA5-7696-49FA-B86A-4A35806892B6', 4, 'EB10D075-FF48-48B5-97EF-698AE440E2A8', '2016-06-25 13:50:19', '2014-11-01 09:39:27', 1, 52000, 0, 1, 0, 1),
('C8AB6AFC-D1E9-472C-ACC5-8FD23BDFEAFA', 5, 'EB10D075-FF48-48B5-97EF-698AE440E2A8', '2016-06-25 13:50:19', '2014-11-01 09:42:40', 1, 13000, 0, 1, 0, 1),
('CC96AFD4-21BD-45F0-9B08-231A5BC5C4DD', 6, 'EB10D075-FF48-48B5-97EF-698AE440E2A8', '2016-06-25 13:50:19', '2014-11-01 09:45:38', 1, 7000, 0, 1, 0, 1),
('FBE8E27A-4C1E-4108-BD12-2A1957FA9CC5', 7, 'EB10D075-FF48-48B5-97EF-698AE440E2A8', '2016-06-25 13:50:19', '2014-11-01 10:23:31', 0, 13000, 0, 1, 0, 1),
('D43D900D-B6A5-42DA-B1FD-01E9B8FE8E78', 8, 'f9442164-73eb-11e0-91be-001a92ba1fbf', '2016-06-25 13:50:19', '2014-11-01 10:35:21', 0, 2000, 0, 1, 0, 1),
('8CD1E47E-7EAA-4616-9574-C56AC1F4BF4E', 9, 'f9442164-73eb-11e0-91be-001a92ba1fbf', '2016-06-25 13:50:19', '2014-11-01 10:35:09', 1, 13000, 0, 1, 0, 1),
('D0A75882-93FC-4585-B1B0-A8E831E70BE4', 10, 'f9442164-73eb-11e0-91be-001a92ba1fbf', '2016-06-25 13:50:19', '2014-11-01 10:37:26', 0, 21500, 0, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_receiptitem`
--

CREATE TABLE IF NOT EXISTS `sales_receiptitem` (
  `id` int(11) NOT NULL,
  `receipt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `product` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `id_bu` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`,`id_bu`),
  KEY `receipt` (`receipt`),
  KEY `product` (`product`),
  KEY `quantity` (`quantity`),
  KEY `id_bu` (`id_bu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sales_receiptitem`
--

INSERT INTO `sales_receiptitem` (`id`, `receipt`, `product`, `quantity`, `id_bu`) VALUES
(1, 'DCE3CAC1-3E47-4050-9F0F-3028057DF0C1', 'D922B314-24F7-42D0-92FF-DD80FC341713', 0, 1),
(2, 'DCE3CAC1-3E47-4050-9F0F-3028057DF0C1', '44C4E205-2ABE-42CF-B07C-D484DA761A72', 0, 1),
(50, 'DCE3CAC1-3E47-4050-9F0F-3028057DF0C1', '756E193D-4076-416D-B298-0ED6141B7D2D', 0, 1),
(51, 'DCE3CAC1-3E47-4050-9F0F-3028057DF0C1', '3F4ED4EA-0F43-4C10-AC6E-D16D799FE938', 0, 1),
(52, 'DCE3CAC1-3E47-4050-9F0F-3028057DF0C1', 'B7FFF815-187C-4AA6-9690-1045406ABAC7', 0, 1),
(55, 'FEB6B5B7-FA0F-447C-98B6-AC1D9BF0F24A', '04BDC61A-9CE4-41E5-8272-89B04DFB3D97', 1000, 1),
(100, '33FD3DA5-7696-49FA-B86A-4A35806892B6', '4395541F-EAB7-45B0-AFAC-FA69AC4D94E1', 1000, 1),
(101, '33FD3DA5-7696-49FA-B86A-4A35806892B6', 'B4BBC19C-1433-442C-A6C4-0B93755AF470', 1000, 1),
(102, '33FD3DA5-7696-49FA-B86A-4A35806892B6', '04BDC61A-9CE4-41E5-8272-89B04DFB3D97', 1000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_receiptitemaddon`
--

CREATE TABLE IF NOT EXISTS `sales_receiptitemaddon` (
  `id` int(11) NOT NULL,
  `receiptitem` int(11) NOT NULL,
  `productaddon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `id_bu` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`,`id_bu`),
  KEY `receiptitem` (`receiptitem`),
  KEY `productaddon` (`productaddon`),
  KEY `quantity` (`quantity`),
  KEY `id_bu` (`id_bu`),
  KEY `id_bu_2` (`id_bu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sales_receiptitemaddon`
--

INSERT INTO `sales_receiptitemaddon` (`id`, `receiptitem`, `productaddon`, `quantity`, `id_bu`) VALUES
(3, 55, '967FED67-B86F-463B-B217-768092E5428C', 1, 1),
(4, 55, '0664D838-EFCD-42B6-A705-AEC8889C94F1', 1, 1),
(25, 105, '0664D838-EFCD-42B6-A705-AEC8889C94F1', 1, 1),
(26, 105, '967FED67-B86F-463B-B217-768092E5428C', 1, 1),
(27, 105, '9119C2AC-BC55-49AB-979F-9D916816F08B', 1, 1),
(28, 105, '3E9B2B2E-7F7E-43CD-8202-3BFAB392B0AC', 1, 1),
(29, 105, '3C02D6D8-A03A-424A-BD18-4AD2E47E778B', 1, 1),
(30, 105, '6D7FA148-EB2B-4C70-8309-6DCCE26B1EE1', 1, 1),
(33, 183, '0664D838-EFCD-42B6-A705-AEC8889C94F1', 1, 1),
(40, 244, '0664D838-EFCD-42B6-A705-AEC8889C94F1', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sensors`
--

CREATE TABLE IF NOT EXISTS `sensors` (
  `id` int(11) NOT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `correction` float NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_bu` (`id_bu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sensors`
--

INSERT INTO `sensors` (`id`, `reference`, `name`, `correction`, `id_bu`) VALUES
(0, '28A81A08060000EF', 'Sensor 1', -5, 1),
(1, '28DC0809060000DB', 'Sensor 2', -10, 1),
(2, '28FADF0A06000058', 'Sensor 3', 0, 1),
(3, '2829DE0906000080', 'Outside', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sensors_alarm`
--

CREATE TABLE IF NOT EXISTS `sensors_alarm` (
  `id_sensor` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `min` int(11) NOT NULL,
  `lastalarm` datetime NOT NULL,
  PRIMARY KEY (`id_sensor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sensors_alarm`
--

INSERT INTO `sensors_alarm` (`id_sensor`, `max`, `min`, `lastalarm`) VALUES
(0, 19, -10, '2016-06-06 19:54:01'),
(1, 20, -2, '2016-06-10 17:06:02'),
(2, 19, -5, '2016-06-13 10:30:02');

-- --------------------------------------------------------

--
-- Table structure for table `sensors_temp`
--

CREATE TABLE IF NOT EXISTS `sensors_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_sensor` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `temp` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `temp` (`temp`),
  KEY `id_sensor` (`id_sensor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1717074 ;

--
-- Dumping data for table `sensors_temp`
--

INSERT INTO `sensors_temp` (`id`, `id_sensor`, `date`, `temp`) VALUES
(1717070, 0, '2016-06-04 11:56:02', 11.38),
(1717071, 1, '2016-06-04 11:56:04', 11.75),
(1717072, 2, '2016-06-04 11:56:05', 22.56),
(1717073, 3, '2016-06-04 11:56:06', 17.94);

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE IF NOT EXISTS `skills` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `name`, `deleted`) VALUES
(1, 'Service', 0);

-- --------------------------------------------------------

--
-- Table structure for table `skills_category`
--

CREATE TABLE IF NOT EXISTS `skills_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `skills_category`
--

INSERT INTO `skills_category` (`id`, `name`, `deleted`) VALUES
(1, 'Burger', 0),
(2, 'Frite', 0);

-- --------------------------------------------------------

--
-- Table structure for table `skills_item`
--

CREATE TABLE IF NOT EXISTS `skills_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_skills` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `id_cat` int(11) NOT NULL,
  `id_sub_cat` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `skills_item`
--

INSERT INTO `skills_item` (`id`, `id_skills`, `name`, `id_cat`, `id_sub_cat`, `deleted`) VALUES
(1, 1, 'fill the coocker', 2, 2, 0),
(2, 1, 'check the refill', 1, 1, 0),
(3, 1, 'toast the bread', 1, 2, 0),
(4, 1, 'don''t eat the bread', 1, 2, 1),
(5, 1, 'chat with customers', 2, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `skills_log`
--

CREATE TABLE IF NOT EXISTS `skills_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_skills_record` int(11) NOT NULL,
  `type` enum('create','edit') NOT NULL DEFAULT 'create',
  `date` datetime NOT NULL,
  `bu_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `skills_log`
--

INSERT INTO `skills_log` (`id`, `id_user`, `id_skills_record`, `type`, `date`, `bu_id`) VALUES
(1, 1, 1, 'create', '2016-08-23 17:10:17', 1),
(2, 1, 1, 'edit', '2016-08-23 17:14:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `skills_log_item`
--

CREATE TABLE IF NOT EXISTS `skills_log_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_log` int(11) NOT NULL,
  `id_record_item` int(11) NOT NULL,
  `checked` enum('YES','NO') DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `skills_log_item`
--

INSERT INTO `skills_log_item` (`id`, `id_log`, `id_record_item`, `checked`, `comment`) VALUES
(1, 2, 4, 'YES', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `skills_record`
--

CREATE TABLE IF NOT EXISTS `skills_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sponsor` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `skills_record`
--

INSERT INTO `skills_record` (`id`, `id_sponsor`, `id_user`, `date`) VALUES
(1, 1, 3, '2016-08-23 17:14:44');

-- --------------------------------------------------------

--
-- Table structure for table `skills_record_item`
--

CREATE TABLE IF NOT EXISTS `skills_record_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_skills_record` int(11) NOT NULL,
  `id_skills_item` int(11) NOT NULL,
  `checked` tinyint(1) NOT NULL,
  `date` datetime DEFAULT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `skills_record_item`
--

INSERT INTO `skills_record_item` (`id`, `id_skills_record`, `id_skills_item`, `checked`, `date`, `comment`) VALUES
(1, 1, 1, 0, '2016-08-23 17:10:17', ''),
(2, 1, 2, 0, '2016-08-23 17:10:17', ''),
(3, 1, 3, 0, '2016-08-23 17:10:17', ''),
(4, 1, 5, 1, '2016-08-23 17:14:44', '');

-- --------------------------------------------------------

--
-- Table structure for table `skills_sub_category`
--

CREATE TABLE IF NOT EXISTS `skills_sub_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `skills_sub_category`
--

INSERT INTO `skills_sub_category` (`id`, `name`, `deleted`) VALUES
(0, 'NONE', 0),
(1, 'Bread', 0),
(2, 'Cuisson', 0);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `main_product` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `carriage_paid` int(11) NOT NULL DEFAULT '0',
  `payment_type` enum('LCR','WIRE','CHEQ','CARD','CASH','DEBIT') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'CARD',
  `payment_delay` text COLLATE utf8_unicode_ci NOT NULL,
  `delivery_days` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_sale_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_sale_tel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_sale_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_order_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_order_tel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contact_order_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_method` enum('email','tel','fax','www') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'email',
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment_internal` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_order` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_delivery` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_delivery_info` text COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `deleted` (`deleted`),
  KEY `active` (`active`),
  KEY `order_method` (`order_method`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `id_category`, `name`, `main_product`, `location`, `carriage_paid`, `payment_type`, `payment_delay`, `delivery_days`, `contact_sale_name`, `contact_sale_tel`, `contact_sale_email`, `contact_order_name`, `contact_order_tel`, `contact_order_email`, `order_method`, `website`, `comment_internal`, `comment_order`, `comment_delivery`, `comment_delivery_info`, `active`, `deleted`, `id_bu`) VALUES
(1, 6, 'SUPPLIER1', 'French fries', 'Paris', 74, 'LCR', 'each 20 of the month', 'Every Friday', 'M. XXX', 'A phone number', 'A mail address', 'XXX', 'A phone number', 'A mail address', 'email', '', '', '', '', '', 1, 0, 1),
(2, 6, 'SUPPLIER2', '', 'Paris', 120, 'DEBIT', '', 'Every days exept sunday', 'Mrs. YYY', 'A phone number', 'A mail address', 'YYY', 'A phone number', 'A mail address', 'email', '', '', '', '', '', 1, 0, 1),
(3, 8, 'SUPPLIER3', 'Fresh vegetables', 'Paris', 0, 'DEBIT', '30 days', '', 'Sir ZZZ', 'A phone number', 'A mail address', 'ZZZ', 'A phone number', 'A mail address', 'email', '', '', '', '', '', 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers_category`
--

CREATE TABLE IF NOT EXISTS `suppliers_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Dumping data for table `suppliers_category`
--

INSERT INTO `suppliers_category` (`id`, `name`, `active`, `deleted`) VALUES
(1, 'food', 1, 0),
(2, 'misc', 1, 0),
(3, 'cheese', 1, 0),
(4, 'steak', 1, 0),
(5, 'bread', 1, 0),
(6, 'frozen', 1, 0),
(7, 'multi', 1, 0),
(8, 'fresh', 1, 0),
(9, 'vegetable', 1, 0),
(10, 'grocery', 1, 0),
(11, 'drink', 1, 0),
(12, 'flyer', 1, 0),
(13, 'Seasoning', 1, 0),
(14, 'Tools', 1, 0),
(15, 'Wrapping', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `turnover`
--

CREATE TABLE IF NOT EXISTS `turnover` (
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` float NOT NULL,
  `last` datetime NOT NULL,
  `num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `turnover`
--

INSERT INTO `turnover` (`date`, `amount`, `last`, `num`) VALUES
('2016-01-09 13:11:04', 100000, '2016-06-24 13:09:45', 73);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `activation_code` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `forgotten_password_code` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` varchar(255) NOT NULL,
  `current_bu_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `comment`, `current_bu_id`) VALUES
(1, '127.0.0.1', 'hank.admin', '$2y$08$vkJzY1JC.6i1xEBvyubm9.1Kjit8REdyrNfHPlzViW5f/lj7Ss8mG', '', 'hank@admin.com', NULL, NULL, NULL, '5kgHzpF8gFDvLW9JUWYoUe', 1268889823, 1472022305, 1, 'Hank', 'Admin', NULL, '+33601234567', '', 2),
(2, '127.0.0.1', 'first.manager', '$2y$08$956OT95hXSMu1QkVY8LNnuhIAxRPkzEuioAN6cSIxZZhvXUj7wN8K', NULL, 'first@manager.com', NULL, NULL, NULL, '1bSczh5igb47vY38Slg3ze', 1412713197, 1471961750, 1, 'First', 'Manager', NULL, '', '', 1),
(3, '127.0.0.1', 'second.manager', '$2y$08$956OT95hXSMu1QkVY8LNnuhIAxRPkzEuioAN6cSIxZZhvXUj7wN8K', NULL, 'second@manager.com', NULL, NULL, NULL, 'quC3Y2KSQxjiYIEko.vJBu', 1423127551, 1471802892, 1, 'second', 'manager', NULL, '', '', 2),
(4, '127.0.0.1', 'first.inactive', '$2y$08$XI.2nKDzZUxrTxhvIPmjM..i6VV4IB7A.ZVstTnijx7bU.jyA3CAW', NULL, 'first@inactive.com', NULL, NULL, NULL, 'HSDJ8ON9Wfv82H0hwTgRPe', 1423592634, 1467184529, 0, 'First', 'Inactive', NULL, '', '', 0),
(5, '127.0.0.1', 'first.staff', '$2y$08$956OT95hXSMu1QkVY8LNnuhIAxRPkzEuioAN6cSIxZZhvXUj7wN8K', NULL, 'first@staff.com', NULL, NULL, NULL, 'Wt0QnSEGBY7rEl4O.k781e', 1417951912, 1471951620, 1, 'First', 'Staff', NULL, '', '', 1),
(6, '127.0.0.1', 'first.extra', '$2y$08$MYUkPAEtatRJIrsz0UcG3.W4Wk/W/a9j73DHJlhRdnCJJvcIU4yvq', NULL, 'first@extra.com', NULL, NULL, NULL, 'CydinDwXXNsfvFwC0GAiY.', 1418153691, 1471703519, 1, 'First', 'Extra test', NULL, '+33601234566', '', 1),
(7, '127.0.0.1', 'keylogin_user', '$2y$08$956OT95hXSMu1QkVY8LNnuhIAxRPkzEuioAN6cSIxZZhvXUj7wN8K', NULL, 'keylogin@user.com', NULL, NULL, NULL, NULL, 1418153691, NULL, 1, 'keylogin_user', '', NULL, '', '', 1),
(8, '127.0.0.1', 'test-prenom.test-nom', '$2y$08$HPPb26y.YQBqZK..aXA2n.Gbmitz9ujkrEiLk7/MInN.Z0PGdBqq6', NULL, 't@t.com', NULL, NULL, NULL, 'Gic9P2jvVSLzYUfRcpz4xu', 1469693550, 1470818486, 1, 'Test-prenom', 'test-nom', NULL, '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_bus`
--

CREATE TABLE IF NOT EXISTS `users_bus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `bu_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`bu_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`bu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `users_bus`
--

INSERT INTO `users_bus` (`id`, `user_id`, `bu_id`) VALUES
(45, 1, 1),
(46, 1, 2),
(47, 2, 1),
(14, 3, 1),
(15, 3, 2),
(24, 4, 1),
(17, 5, 1),
(32, 6, 2),
(29, 8, 1),
(30, 8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(25, 1, 1),
(26, 2, 3),
(9, 3, 4),
(15, 4, 2),
(11, 5, 2),
(18, 6, 5),
(7, 7, 2),
(16, 8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users_pos`
--

CREATE TABLE IF NOT EXISTS `users_pos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_pos` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_bu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user` (`id_user`,`id_pos`,`id_bu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users_pos`
--

INSERT INTO `users_pos` (`id`, `id_user`, `id_pos`, `id_bu`) VALUES
(1, 1, '3CE921B8-F96D-4C9A-9C6E-3A98A8422FDE', 1),
(2, 5, '02D653E3-DED5-4DAB-B0B4-B9E753476405', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders_confirm`
--
ALTER TABLE `orders_confirm`
  ADD CONSTRAINT `orders_confirm_ibfk_1` FOREIGN KEY (`idorder`) REFERENCES `orders` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `pos_payments`
--
ALTER TABLE `pos_payments`
  ADD CONSTRAINT `pos_payments_ibfk_1` FOREIGN KEY (`id_movement`) REFERENCES `pos_movements` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `rmd_notif`
--
ALTER TABLE `rmd_notif`
  ADD CONSTRAINT `rmd_notif_ibfk_1` FOREIGN KEY (`id_task`) REFERENCES `rmd_tasks` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
