CREATE TABLE `merchants` ( `merchant_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,  `merchant_name` VARCHAR(100) NOT NULL ,  `merchant_code` VARCHAR(5) NOT NULL ,  `status` ENUM('active','inactive') NOT NULL ,  `created_at` DATETIME NOT NULL ,  `created_by` VARCHAR(64) NOT NULL ,  `updated_at` DATETIME NOT NULL ,  `updated_by` VARCHAR(64) NOT NULL ,  `deleted_at` DATE NULL ,  `deleted_by` VARCHAR(64) NULL ,    PRIMARY KEY  (`merchant_id`)) ENGINE = InnoDB;
ALTER TABLE `users` ADD `merchant_id` INT(11) NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `admin_roles` ADD `r_merchant` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_export_transaction`;
ALTER TABLE `device_checks` ADD `merchant_id` INT(11) NULL DEFAULT NULL AFTER `user_id`;

 -- 2021/11/03
ALTER TABLE `notification_queues` CHANGE `token_type` `token_type` ENUM('onesignal','fcm','onesignal2') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

-- 2021/11/04
INSERT INTO `settings_tnc` (`setting_tnc_id`, `_key`, `val`, `count`, `updated_at`, `updated_by`) VALUES (NULL, 'tnc_app3', 'Terms & Condition', '0', NOW(), 'system');
INSERT INTO `settings_tnc` (`setting_tnc_id`, `_key`, `val`, `count`, `updated_at`, `updated_by`) VALUES (NULL, 'app3_merchant_code_help', 'Terms & Condition', '0', NOW(), 'system');
INSERT INTO `settings` (`setting_id`, `_key`, `val`, `count`, `updated_at`, `updated_by`) VALUES (NULL, 'chat_app3', 'https://google.com', '0', NOW(), 'system');
INSERT INTO `settings` (`setting_id`, `_key`, `val`, `count`, `updated_at`, `updated_by`) VALUES (NULL, 'version_app3', '1.0.0', '0', NOW(), 'system');
INSERT INTO `settings` (`setting_id`, `_key`, `val`, `count`, `updated_at`, `updated_by`) VALUES (NULL, 'version_app3_ios', '1.0.0', '0', NOW(), 'system');
