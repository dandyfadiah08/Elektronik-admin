-- add column total transaction and saving

ALTER TABLE `referrals` ADD `transaction` INT(8) NOT NULL DEFAULT '0' AFTER `status`, ADD `saving` VARCHAR(16) NOT NULL DEFAULT '0' AFTER `transaction`;

ALTER TABLE `user_balance` ADD `from_user_id` INT(24) NULL AFTER `check_id`;
