ALTER TABLE `rmd_tasks` ADD `type` ENUM('service','kitchen') NOT NULL DEFAULT 'service' ;
ALTER TABLE `checklists` ADD `type` ENUM('service','kitchen') NOT NULL DEFAULT 'service';