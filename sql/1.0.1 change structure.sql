-- drop index, reason: not unique becase value can be replicate with users.status=pending
ALTER TABLE `users` DROP INDEX `email`;
ALTER TABLE `users` DROP INDEX `phone_no`;
ALTER TABLE `users` DROP INDEX `nik`;

-- add column for verification phone_no and email
ALTER TABLE `users` ADD `phone_no_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `photo_id`, ADD `email_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `phone_no_verified`;
ALTER TABLE `users` ADD `nik_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `email_verified`, ADD `submission` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `nik_verified`;

-- add table for refresh_token
CREATE TABLE `refresh_tokens` ( `id` INT(24) UNSIGNED NOT NULL AUTO_INCREMENT , `user_id` INT(24) UNSIGNED NOT NULL , `token` VARCHAR(255) NOT NULL , `created_at` DATETIME NOT NULL , `expired_at` DATETIME NOT NULL , PRIMARY KEY (`id`), KEY `refresh_tokens_user_id` (`user_id`), CONSTRAINT `refresh_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE = InnoDB;

-- 18/08/2021
ALTER TABLE `appointments` ADD `courier_name` VARCHAR(100) NULL AFTER `deleted_at`, ADD `courier_phone` VARCHAR(32) NULL AFTER `courier_name`, ADD `courier_expedition` INT(64) NULL AFTER `courier_phone`;
ALTER TABLE `appointments` CHANGE `choosen_time` `choosen_time` CHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `device_checks` ADD `user_id` INT(24) NULL AFTER `status`, ADD `type_user` ENUM('agent','nonagent') NULL AFTER `user_id`, ADD `status_internal` TINYINT(2) NOT NULL DEFAULT '1' AFTER `type_user`;
ALTER TABLE `device_check_details` ADD `battery` TINYINT(1) NOT NULL AFTER `harddisk`, ADD `root` TINYINT(1) NOT NULL AFTER `battery`, ADD `cpu_detail` TEXT NULL AFTER `root`, ADD `harddisk_detail` TEXT NULL AFTER `cpu_detail`, ADD `battery_detail` TEXT NULL AFTER `harddisk_detail`, ADD `root_detail` TEXT NULL AFTER `battery_detail`;
ALTER TABLE `device_checks` CHANGE `imei` `imei` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `device_checks` CHANGE `check_code` `check_code` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `key_code` `key_code` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `device_checks` ADD `fcm_token` TEXT NOT NULL AFTER `status_internal`;

-- insert to master_promos and master_prices
INSERT INTO `master_promos` (`promo_id`, `promo_name`, `start_date`, `end_date`, `codes`, `quota`, `quota_type`, `initial_quota`, `quota_value`, `used_quota`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`, `deleted_by`, `deleted_at`) VALUES 
(NULL, 'Active Promo', '2021-08-18', '2021-08-31', NULL, 'n', 'promo', '0', '0', '0', '1', 'system', NOW(), 'system', NOW(), NULL, NULL),
(NULL, 'Active Promo 2', '2021-08-18', '2021-08-31', NULL, 'n', 'promo', '0', '0', '0', '1', 'system', NOW(), 'system', NOW(), NULL, NULL),
(NULL, 'Inactive Promo', '2021-08-01', '2021-08-15', NULL, 'n', 'promo', '0', '0', '0', '1', 'system', NOW(), 'system', NOW(), NULL, NULL);

-- 23/08/2021
ALTER TABLE `users` ADD `notification_token` VARCHAR(100) NULL DEFAULT NULL AFTER `submission`;

ALTER TABLE `device_check_details` ADD `customer_name` VARCHAR(100) NULL DEFAULT NULL AFTER `photo_imei_registered`, ADD `customer_phone` VARCHAR(15) NULL DEFAULT NULL AFTER `customer_name`;
ALTER TABLE `device_check_details` ADD `finished_date` DATETIME NULL DEFAULT NULL AFTER `customer_phone`, ADD `waiting_date` DATETIME NULL DEFAULT NULL AFTER `finished_date`;
