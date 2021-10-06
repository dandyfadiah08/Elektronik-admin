CREATE TABLE `development_ppa`.`notification_queues` ( `id` INT(24) NOT NULL AUTO_INCREMENT ,  `token` TEXT NOT NULL ,  `token_type` ENUM('onesignal','fcm') NOT NULL ,  `data` JSON NOT NULL ,  `created_at` DATETIME NOT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;
ALTER TABLE `notification_queues` ADD `scheduled` DATETIME NOT NULL AFTER `created_at`;
