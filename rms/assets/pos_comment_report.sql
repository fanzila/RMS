-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Ven 20 Octobre 2017 à 15:26
-- Version du serveur :  5.6.36
-- Version de PHP :  5.6.30-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `rms`
--

-- --------------------------------------------------------

--
-- Structure de la table `pos_comment_report`
--

CREATE TABLE IF NOT EXISTS `pos_comment_report` (
`id` int(11) NOT NULL,
  `content` text NOT NULL,
  `date` datetime NOT NULL,
  `username` varchar(255) NOT NULL,
  `mov_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=0;
--
-- Index pour les tables exportées
--

--
-- Index pour la table `pos_comment_report`
--
ALTER TABLE `pos_comment_report`
 ADD PRIMARY KEY (`id`);