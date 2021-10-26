CREATE TABLE `development_ppa`.`merchants` ( `merchant_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,  `merchant_name` VARCHAR(100) NOT NULL ,  `merchant_code` VARCHAR(5) NOT NULL ,  `status` ENUM('active','inactive') NOT NULL ,  `created_at` DATETIME NOT NULL ,  `created_by` VARCHAR(64) NOT NULL ,  `updated_at` DATETIME NOT NULL ,  `updated_by` VARCHAR(64) NOT NULL ,  `deleted_at` DATE NULL ,  `deleted_by` VARCHAR(64) NULL ,    PRIMARY KEY  (`merchant_id`)) ENGINE = InnoDB;
ALTER TABLE `users` ADD `merchant_id` INT(11) NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `admin_roles` ADD `r_merchant` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_export_transaction`;
ALTER TABLE `device_checks` ADD `merchant_id` INT(11) NULL DEFAULT NULL AFTER `user_id`;
