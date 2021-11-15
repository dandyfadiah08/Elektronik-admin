ALTER TABLE `admin_roles` ADD `r_export_user` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_export_withdraw`;
