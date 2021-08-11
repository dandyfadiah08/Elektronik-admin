-- drop index, reason: not unique becase value can be replicate with users.status=pending
ALTER TABLE `users` DROP INDEX `email`;
ALTER TABLE `users` DROP INDEX `phone_no`;
ALTER TABLE `users` DROP INDEX `nik`;

-- add column for verification phone_no and email
ALTER TABLE `users` ADD `phone_no_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `photo_id`, ADD `email_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `phone_no_verified`;
ALTER TABLE `users` ADD `nik_verified` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `email_verified`, ADD `submission` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `nik_verified`;
