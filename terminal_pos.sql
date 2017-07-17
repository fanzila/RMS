-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Lun 17 Juillet 2017 à 11:09
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
-- Structure de la table `terminal_pos`
--

CREATE TABLE IF NOT EXISTS `terminal_pos` (
  `id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `model` varchar(255) CHARACTER SET utf8 NOT NULL,
  `id_bu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `terminal_pos`
--

INSERT INTO `terminal_pos` (`id`, `name`, `model`, `id_bu`) VALUES
('17ECAC88-5E11-4898-B384-C794A3CB2AB9', 'fanzila’s iPad', 'iPad3,4', 1),
('400CF750-73A5-416C-BC5B-B72F385CD7A9', 'iPad Support #3', 'iPad2,5', 1),
('593E344D-E456-47FD-9108-65BBF02573F0', 'iPad Support #2', 'iPad2,1', 1),
('6A4A27EA-497A-4A90-9796-7A2AEC341ED5', 'iPad', 'iPad4,4', 1),
('82BDCBD8-1B1D-467E-899E-140DAAC2B87F', 'fanzila’s iPad', 'iPad4,1', 1),
('C2C37B6D-4BD9-481E-84FD-2EA3339DC2FF', 'Nicolas Leonard’s iPad', 'iPad4,4', 1),
('E20CC5B2-23E2-48D3-903C-61CE4DC1873F', 'iPad Support #4', 'iPad2,7', 1),
('ECDC9160-774D-4FF7-9C2C-00AA5C8A94AB', 'iPad Support #5', 'iPad2,7', 1);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `terminal_pos`
--
ALTER TABLE `terminal_pos`
 ADD UNIQUE KEY `id` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
