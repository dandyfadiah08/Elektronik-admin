CREATE TABLE `retry_photos` (
 `retry_photo_id` int(11) NOT NULL AUTO_INCREMENT,
 `check_id` int(11) NOT NULL,
 `reason` text NOT NULL,
 `photo_device_1` text DEFAULT NULL,
 `photo_device_2` text DEFAULT NULL,
 `photo_device_3` text DEFAULT NULL,
 `photo_device_4` text DEFAULT NULL,
 `photo_device_5` text DEFAULT NULL,
 `photo_device_6` text DEFAULT NULL,
 `status` enum('created','done') NOT NULL DEFAULT 'created',
 `updated_by` varchar(255) DEFAULT NULL,
 `updated_at` datetime DEFAULT NULL,
 `created_at` datetime NOT NULL,
 `created_by` varchar(64) NOT NULL,
 PRIMARY KEY (`retry_photo_id`),
 KEY `retry_photo_id` (`retry_photo_id`,`check_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `device_check_details` ADD `retry_photo_id` INT(11) NULL AFTER `s_pen`, ADD `damage` TEXT NULL AFTER `retry_photo_id`;
