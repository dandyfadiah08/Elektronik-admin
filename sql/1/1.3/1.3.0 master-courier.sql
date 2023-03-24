-- 2022/05/012
CREATE TABLE `couriers` (
 `courier_id` varchar(14) NOT NULL COMMENT 'uniqid(''c'')',
 `courier_name` varchar(64) NOT NULL,
 `courier_phone` varchar(15) NOT NULL,
 `courier_expedition` varchar(64) NOT NULL,
 `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
 `created_at` datetime NOT NULL,
 `created_by` varchar(64) NOT NULL,
 `updated_at` datetime NOT NULL,
 `updated_by` varchar(64) NOT NULL,
 `deleted_at` datetime DEFAULT NULL,
 `deleted_by` varchar(64) DEFAULT NULL,
 PRIMARY KEY (`courier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `device_check_details` ADD `pickup_order_no` VARCHAR(64) NULL DEFAULT NULL AFTER `damage`;
ALTER TABLE `admins_roless` ADD `r_courier` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_export_tax`, ADD `r_courier_view` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_courier`;
ALTER TABLE `appointments` CHANGE `courier_expedition` `courier_expedition` VARCHAR(64) NULL DEFAULT NULL;
ALTER TABLE `appointments` ADD `courier_id` VARCHAR(14) NULL AFTER `deleted_at`;
