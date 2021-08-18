-- add column total transaction and saving

ALTER TABLE `referrals` ADD `transaction` INT(8) NOT NULL DEFAULT '0' AFTER `status`, ADD `saving` VARCHAR(16) NOT NULL DEFAULT '0' AFTER `transaction`;