-- 2022/01/10
ALTER TABLE `users` ADD `birthdate` DATE NULL DEFAULT NULL AFTER `name`;
ALTER TABLE `users` ADD `internal_agent` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `type`;
