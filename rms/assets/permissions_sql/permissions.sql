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
-- Structure de la table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
`id` int(11) NOT NULL,
  `perm_key` varchar(30) NOT NULL,
  `perm_name` varchar(100) NOT NULL,
  `id_category` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `permissions`
--

INSERT INTO `permissions` (`id`, `perm_key`, `perm_name`, `id_category`) VALUES
(1, 'access_admin', 'Access ACL Admin Panel', 0),
(6, 'cameras', 'Access Camera Controller', 4),
(7, 'checklists', 'Access Checklists Controller', 11),
(8, 'edit_self', 'Ability to edit their own account', 10),
(9, 'edit_user', 'Ability to edit other staff accounts', 10),
(10, 'view_staff', 'View Staff Page', 10),
(11, 'extras', 'Ability to access extras', 10),
(12, 'activate_user', 'Ability to activate the account of a user', 10),
(13, 'deactivate_user', 'Ability to deactivate the account of a user', 10),
(14, 'delete_user', 'Ability to delete user', 10),
(15, 'create_user', 'Ability to create user', 10),
(16, 'edit_WP_user', 'Edit the WP account of a user', 10),
(17, 'edit_first_shift_user', 'Edit the first shift in a user account', 10),
(18, 'create_group', 'Ability to create a new group', 10),
(19, 'edit_self_group', 'Abiilty to edit their own groups', 10),
(20, 'edit_WP_self', 'Ability to edit their own WP account', 10),
(21, 'skills_manage_crud', 'Ability to access Skills CRUD Management', 6),
(22, 'skills_admin', 'Ability to access Skills Admin', 6),
(23, 'delete_WP_user', 'Ability to delete a WP User', 10),
(24, 'view_groups_user', 'View Groups for other users in Staff Page', 10),
(25, 'view_status_user', 'View Status for other users in Staff Page', 10),
(26, 'view_bus_user', 'View BUS for other users in staff page', 10),
(27, 'edit_user_group', 'Edit other user groups', 10),
(28, 'view_all_cameras', 'Ability to view all cameras', 4),
(29, 'access_cruds', 'Abitlity to access crud controller', 7),
(30, 'create_news', 'ability to create news', 5),
(31, 'view_pm_menu', 'Displays pm reports menu', 8),
(32, 'choose_cam_bu', 'Choose to display cams from bus', 4),
(33, 'view_stock_log', 'View the stock log', 9),
(34, 'product_admin', 'Access the product admin panel', 9),
(35, 'view_safe', 'Display the safe option in webcashier', 3),
(36, 'view_report', 'Display the report option in webcashier', 3),
(37, 'quittance', 'Ability to accept set OK on erroneous reports in webcashier', 3),
(40, 'send_message', 'Ability to send a report within the reports controller', 8),
(41, 'webcashier', 'Ability to access the webcashier controller', 3),
(42, 'discounts', 'Ability to access the discounts controller', 0),
(43, 'validate_persistent_discount', 'Ability to mark a persistent discount as used', 0),
(44, 'orders', 'Ability to access the Order Controller', 9),
(45, 'reminders', 'Ability to access the reminders controller', 0),
(46, 'posmessage', 'Abiility to access the cashier message controller', 8),
(47, 'wp_access', 'Ability to access the WordPress twin site', 10),
(48, 'tools_panel', 'Display the tools section in the sidebar menu', 12),
(49, 'additional_tools_panel', 'Access all tools on the sidepanel', 12),
(50, 'admin_panel_sidebar', 'Display the admin section in the sidebar menu', 12),
(51, 'additional_admin_panel_sidebar', 'Display all admin links in the sidebar', 12),
(52, 'edit_admin_user_group', 'Ability to add and remove any group from another user', 10),
(53, 'send_report_managers', 'Ability to send a report to every Manager of a BU', 8),
(54, 'additional_menu_pm', 'View every link in the menu from the PM controller', 8),
(55, 'create_skill', 'Ability to create a new skill', 6);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `permKey` (`perm_key`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=56;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
