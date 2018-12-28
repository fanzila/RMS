CREATE TABLE IF NOT EXISTS `wp_roles` (
`id` int(11) NOT NULL,
  `wp_role` enum('administrator','author','subscriber','contributor','editor') NOT NULL DEFAULT 'subscriber',
  `id_group_rms` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8_general_ci;

ALTER TABLE `wp_roles`
 ADD PRIMARY KEY (`id`);