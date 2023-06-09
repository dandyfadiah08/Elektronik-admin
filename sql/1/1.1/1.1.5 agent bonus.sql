-- 2022-02-11
ALTER TABLE `user_balance` CHANGE `type` `type` ENUM('bonus','transaction','withdraw','agentbonus') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'bonus';
ALTER TABLE `admins_roless` ADD `r_bonus_view` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_merchant`, ADD `r_send_bonus` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_bonus_view`, ADD `r_export_bonus` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_send_bonus`;
ALTER TABLE `user_balance` ADD `created_by` VARCHAR(100) NULL AFTER `created_at`;
ALTER TABLE `user_balance` ADD `updated_by` VARCHAR(100) NULL AFTER `updated_at`;

-- 2022-02-16
ALTER TABLE `admins_roless` ADD `r_balance` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_export_bonus`;
ALTER TABLE `admins_roless` ADD `r_tax` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_balance`;
ALTER TABLE `admins_roless` ADD `r_export_tax` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_tax`;
