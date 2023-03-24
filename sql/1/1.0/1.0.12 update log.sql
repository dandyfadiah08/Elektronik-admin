ALTER TABLE `logs_2021` ADD `admins_id` INT(11) NULL DEFAULT NULL AFTER `category`, ADD `user_id` INT(24) NULL DEFAULT NULL AFTER `admins_id`, ADD `check_id` INT(24) NULL DEFAULT NULL AFTER `user_id`, ADD INDEX (`admins_id`), ADD INDEX (`user_id`), ADD INDEX (`check_id`);
ALTER TABLE `logs_2021` CHANGE `user` `user` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
