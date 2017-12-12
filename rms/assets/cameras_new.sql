ALTER TABLE `cameras` CHANGE `adress` `address` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `cameras` DROP `adress_local`;
ALTER TABLE `cameras` ADD `login` VARCHAR(255) NOT NULL ;
ALTER TABLE `cameras` ADD `password` VARCHAR(255) NOT NULL ;