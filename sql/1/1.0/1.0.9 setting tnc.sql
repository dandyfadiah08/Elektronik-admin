CREATE TABLE `settings_tnc` (
 `setting_tnc_id` int(11) NOT NULL AUTO_INCREMENT,
 `_key` varchar(32) NOT NULL,
 `val` MEDIUMTEXT NOT NULL,
 `count` int(11) NOT NULL DEFAULT 0,
 `updated_at` datetime DEFAULT NULL,
 `updated_by` varchar(100) DEFAULT NULL,
 PRIMARY KEY (`setting_tnc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4

INSERT INTO `settings_tnc` (`setting_tnc_id`, `_key`, `val`, `updated_at`, `updated_by`) VALUES 
('1', 'tnc_app1', '1.0.0', '2021-09-14 05:28:21.000000', NULL), 
('2', 'tnc_app2', '1.0.0', '2021-09-14 05:28:21.000000', NULL);

DELETE FROM `settings` WHERE `settings`.`setting_id` = 5;
DELETE FROM `settings` WHERE `settings`.`setting_id` = 6;


INSERT INTO `settings_tnc` (`setting_tnc_id`, `_key`, `val`, `updated_at`, `updated_by`) VALUES 
('3', 'short_tnc_app2', '1.0.0', '2021-09-14 05:28:21.000000', NULL);

INSERT INTO `settings_tnc` (`setting_tnc_id`, `_key`, `val`, `updated_at`, `updated_by`) VALUES 
('4', 'short_bonus_tnc_app2', '1.0.0', '2021-09-14 05:28:21.000000', NULL);

UPDATE `settings_tnc` SET `_key` = 'bonus_tnc_app2' WHERE `settings_tnc`.`setting_tnc_id` = 3;

