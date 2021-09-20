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

-- 24/08/2021
ALTER TABLE `admins` ADD `token_notification` VARCHAR(255) NULL DEFAULT NULL AFTER `status`;
ALTER TABLE `device_check_details` ADD `fullset_price` INT(12) NOT NULL DEFAULT '0' AFTER `fullset`;

-- 25/08/2021
ALTER TABLE `admin_roles` ADD `r_survey` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_admin`;
ALTER TABLE `master_prices` ADD `price_fullset` INT(12) UNSIGNED NOT NULL DEFAULT '0' AFTER `price_e`;
ALTER TABLE `device_check_details` ADD `survey_quiz_1` TINYINT(1) NULL AFTER `waiting_date`, ADD `survey_quiz_2` TINYINT(1) NULL AFTER `survey_quiz_1`, ADD `survey_quiz_3` TINYINT(1) NULL AFTER `survey_quiz_2`, ADD `survey_quiz_4` TINYINT(1) NULL AFTER `survey_quiz_3`, ADD `survey_id` INT(11) NULL AFTER `survey_quiz_4`, ADD `survey_name` VARCHAR(100) NULL AFTER `survey_id`, ADD `survey_log` VARCHAR(100) NULL AFTER `survey_name`, ADD `survey_date` DATETIME NULL AFTER `survey_log`;
ALTER TABLE `device_check_details` ADD `survey_fullset` TINYINT(1) NULL AFTER `survey_quiz_4`;

-- 27/08/2021
ALTER TABLE `users` ADD `pin` VARCHAR(255) NULL DEFAULT NULL AFTER `notification_token`;
ALTER TABLE `address_provinces` ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'inactive' AFTER `name`, ADD `updated_by` VARCHAR(100) NULL AFTER `status`, ADD `updated_at` DATETIME NULL AFTER `updated_by`;
ALTER TABLE `address_cities` ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'inactive' AFTER `name`, ADD `updated_by` VARCHAR(100) NULL AFTER `status`, ADD `updated_at` DATETIME NULL AFTER `updated_by`;
ALTER TABLE `address_districts` ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `name`, ADD `updated_by` VARCHAR(100) NULL AFTER `status`, ADD `updated_at` DATETIME NULL AFTER `updated_by`;
ALTER TABLE `address_villages` ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `name`, ADD `updated_by` VARCHAR(100) NULL AFTER `status`, ADD `updated_at` DATETIME NULL AFTER `updated_by`;
UPDATE `address_cities` SET status='active' WHERE 
name like '%jakarta%'
or name like '%bogor%'
or name like '%depok%'
or name like '%tangerang%'
or name like '%bekasi%';
UPDATE `address_provinces` SET `status` = 'active' WHERE `address_provinces`.`province_id` IN ('31','32','36');

-- 28/08/2021
ALTER TABLE `admin_roles` ADD `r_proceed_payment` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_survey`;
ALTER TABLE `admin_roles` ADD `r_mark_as_failed` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_proceed_payment`;

-- 30/08/2021
CREATE TABLE `settings` ( `setting_id` INT(11) NOT NULL AUTO_INCREMENT ,  `_key` VARCHAR(32) NOT NULL ,  `val` VARCHAR(255) NOT NULL ,  `updated_at` DATETIME NULL ,  `updated_by` VARCHAR(100) NULL ,    PRIMARY KEY  (`setting_id`)) ENGINE = InnoDB;
INSERT INTO `settings` (`setting_id`, `_key`, `val`, `updated_at`, `updated_by`) VALUES (NULL, '2fa_secret', '', NULL, NULL);
INSERT INTO `settings` (`setting_id`, `_key`, `val`, `updated_at`, `updated_by`) VALUES (NULL, '2fa_status', 'n', NULL, NULL);
ALTER TABLE `device_check_details` ADD `token` TEXT NULL DEFAULT NULL AFTER `survey_date`;
-- jalankan 1.0.3 payment_methods.sql

-- 31/08/2021
ALTER TABLE `commission_rate` CHANGE `price_form` `price_from` INT(12) NOT NULL, CHANGE `price_to` `price_to` INT(12) NOT NULL, CHANGE `commision_1` `commission_1` INT(12) NOT NULL COMMENT 'untuk level 0', CHANGE `commision_2` `commission_2` INT(12) NOT NULL COMMENT 'untuk level 1', CHANGE `commision_3` `commission_3` INT(12) NOT NULL COMMENT 'untuk level 2';
ALTER TABLE `device_check_details` ADD `payment_date` DATETIME NULL DEFAULT NULL AFTER `token`;


-- 01/09/2021
ALTER TABLE `user_balance` CHANGE `status` `status` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '1 = sukses, 2 = pending, 3 = failed';
ALTER TABLE `user_payouts` CHANGE `status` `status` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '1 = sukses, 2 = pending, 3 = failed';
ALTER TABLE `device_check_details` ADD `transfer_proof` VARCHAR(255) NULL DEFAULT NULL AFTER `payment_date`, ADD `transfer_notes` VARCHAR(255) NULL DEFAULT NULL AFTER `transfer_proof`;

-- 03/09/2021
ALTER TABLE `device_check_details` ADD `general_notes` VARCHAR(255) NULL DEFAULT NULL AFTER `transfer_notes`;

-- 14/09/2021
ALTER TABLE `user_payments` ADD `active` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `account_name`;
ALTER TABLE `user_payments` CHANGE `active` `status` ENUM('active','pending','invalid') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending';
ALTER TABLE `device_check_details` ADD `lock_until_date` DATETIME NULL DEFAULT NULL AFTER `waiting_date`;
ALTER TABLE `device_check_details` ADD `account_name_check` VARCHAR(100) NULL DEFAULT NULL AFTER `account_name`, ADD `account_bank_check` ENUM('valid','pending','invalid') NULL DEFAULT NULL AFTER `account_name_check`, ADD `account_bank_error` VARCHAR(255) NULL DEFAULT NULL AFTER `account_bank_check`;

-- 16/09/2021
ALTER TABLE `device_check_details` ADD `customer_email` VARCHAR(100) NULL DEFAULT NULL AFTER `customer_phone`;

-- 20/09/2021
ALTER TABLE `user_payouts` ADD `transfer_proof` VARCHAR(255) NOT NULL AFTER `withdraw_ref`, ADD `transfer_notes` VARCHAR(255) NOT NULL AFTER `transfer_proof`;
ALTER TABLE `admin_roles` ADD `r_change_payment` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_confirm_appointment`;
