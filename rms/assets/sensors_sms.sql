ALTER TABLE `sensors` ADD `sms_alert` BOOLEAN NOT NULL DEFAULT FALSE ;
ALTER TABLE `sensors_alarm` ADD `sms_count_day` INT NOT NULL , ADD `lastsms` DATETIME NOT NULL ;