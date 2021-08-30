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