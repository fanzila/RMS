-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 27 Décembre 2017 à 16:58
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
-- Structure de la table `permissions_categories`
--

CREATE TABLE IF NOT EXISTS `permissions_categories` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `permissions_categories`
--

INSERT INTO `permissions_categories` (`id`, `name`, `key`) VALUES
(3, 'Cashier', 'cashier'),
(4, 'Cams', 'camera'),
(5, 'News', 'news'),
(6, 'Skills', 'skills'),
(7, 'Admin CRUD', 'crud'),
(8, 'Messaging', 'pm_reports'),
(9, 'Order/Products', 'order_products'),
(10, 'Users', 'users'),
(11, 'Checklists', 'checklists'),
(12, 'miscelleanous', 'misc'),
(13, 'POS message', 'posmessage');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `permissions_categories`
--
ALTER TABLE `permissions_categories`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `permissions_categories`
--
ALTER TABLE `permissions_categories`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
