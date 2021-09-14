CREATE TABLE `logs_2021` ( `id` INT(24) UNSIGNED NOT NULL AUTO_INCREMENT , `category` TINYINT(3) NOT NULL , `user` VARCHAR(64) NOT NULL , `log` VARCHAR(1000) NOT NULL , `created_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

-- for testing
CREATE TABLE `logs_2020` ( `id` INT(24) UNSIGNED NOT NULL AUTO_INCREMENT , `category` TINYINT(3) NOT NULL , `user` VARCHAR(64) NOT NULL , `log` VARCHAR(1000) NOT NULL , `created_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `logs_2020` (`id`, `category`, `user`, `log`, `created_at`) VALUES (NULL, '1', 'master', 'Testing', NOW());
