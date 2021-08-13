-- drop index, reason: not unique becase value can be replicate with users.status=pending
ALTER TABLE `users` DROP INDEX `email`;
ALTER TABLE `users` DROP INDEX `phone_no`;
ALTER TABLE `users` DROP INDEX `nik`;

-- add column for verification phone_no and email
ALTER TABLE `users` ADD `phone_no_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `photo_id`, ADD `email_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `phone_no_verified`;
ALTER TABLE `users` ADD `nik_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `email_verified`, ADD `submission` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `nik_verified`;

-- add table for refresh_token
CREATE TABLE `refresh_tokens` ( `id` INT(24) UNSIGNED NOT NULL AUTO_INCREMENT , `user_id` INT(24) UNSIGNED NOT NULL , `token` VARCHAR(255) NOT NULL , `created_at` DATETIME NOT NULL , `expired_at` DATETIME NOT NULL , PRIMARY KEY (`id`), KEY `refresh_tokens_user_id` (`user_id`), CONSTRAINT `refresh_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) ENGINE = InnoDB;
