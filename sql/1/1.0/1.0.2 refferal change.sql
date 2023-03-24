-- add column total transaction and saving

ALTER TABLE `referrals` ADD `transaction` INT(8) NOT NULL DEFAULT '0' AFTER `status`, ADD `saving` VARCHAR(16) NOT NULL DEFAULT '0' AFTER `transaction`;

ALTER TABLE `user_balance` ADD `from_user_id` INT(24) NULL AFTER `check_id`;

ALTER TABLE `users` ADD `count_referral` INT(4) NOT NULL DEFAULT '0' AFTER `active_balance`;

-- 27/08/2021

CREATE TABLE `commission_rate` (
 `id` int(12) NOT NULL AUTO_INCREMENT,
 `price_form` varchar(16) NOT NULL,
 `price_to` varchar(16) NOT NULL,
 `commision_1` varchar(12) NOT NULL COMMENT 'untuk level 0',
 `commision_2` varchar(12) NOT NULL COMMENT 'untuk level 1',
 `commision_3` varchar(12) NOT NULL COMMENT 'untuk level 2',
 `created_at` datetime NOT NULL,
 `created_by` varchar(64) NOT NULL,
 `updated_at` datetime NOT NULL,
 `updated_by` varchar(64) NOT NULL,
 `deleted_at` datetime DEFAULT NULL,
 `deleted_by` varchar(64) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4


-- 30-08-2021

	CREATE TABLE `available_date_time` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` enum('date','time') NOT NULL,
 `status` enum('active','inactive') NOT NULL,
 `days` int(11) DEFAULT NULL,
 `value` varchar(11) DEFAULT NULL,
 `updated_at` int(11) DEFAULT NULL,
 `updated_by` int(11) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4


-- 31-08-2021

RENAME TABLE `user_addresses` TO `addresses`;

ALTER TABLE `addresses` DROP INDEX `user_addresses_user_id_foreign`;
ALTER TABLE `addresses` CHANGE `user_id` `check_id` INT(24) UNSIGNED NOT NULL;
ALTER TABLE `addresses` DROP FOREIGN KEY `user_addresses_user_id_foreign`;
ALTER TABLE `addresses` ADD CONSTRAINT `addresses_check_id_foreign` FOREIGN KEY (`check_id`) REFERENCES `tradein`(`check_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `device_check_details` ADD `payment_method_id` TINYINT(3) UNSIGNED NULL AFTER `token`;
ALTER TABLE `device_check_details` ADD `account_number` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `payment_method_id`;
ALTER TABLE `device_check_details` ADD `account_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `account_number`;

ALTER TABLE `appointments` DROP INDEX `appointments_user_payment_id_foreign`;
ALTER TABLE `appointments` DROP FOREIGN KEY `appointments_user_payment_id_foreign`;
ALTER TABLE `appointments` DROP `user_payment_id`;



-- 06-09-2021
ALTER TABLE `admins_roless` ADD `r_submission` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_mark_as_failed`;


-- 16-09-2021
ALTER TABLE `user_payouts` ADD `withdraw_ref` VARCHAR(255) NULL DEFAULT NULL AFTER `check_id`;


-- 23-09-2021
ALTER TABLE `admins_roless` ADD `r_change_address` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_change_payment`;


-- 24-09-2021
ALTER TABLE `admins_roless` ADD `r_change_setting` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_change_address`;
ALTER TABLE `admins_roless` ADD `r_change_available_date_time` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_change_setting`;


-- 12-10-2021
INSERT INTO `settings` ( `_key`, `val`, `updated_at`, `updated_by`) VALUES ('version_app1_ios', '1.0.0', NOW(), NULL), 
('version_app2_ios', '1.0.0', NOW(), NULL);
