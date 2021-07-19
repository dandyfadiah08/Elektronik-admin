-- create table tb_admin
CREATE TABLE `tb_admin` (
 `admin_id` int(24) NOT NULL AUTO_INCREMENT,
 `username` varchar(32) NOT NULL,
 `password` text NOT NULL,
 `role_id` int(24) NOT NULL,
 `active` set('1','2','3','') NOT NULL DEFAULT '1',
 `created_by` varchar(32) NOT NULL,
 `created_at` datetime NOT NULL,
 `updated_by` varchar(32) NOT NULL,
 `updated_at` datetime NOT NULL,
 `deleted_by` varchar(32) DEFAULT NULL,
 `deleted_at` datetime DEFAULT NULL,
 PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- insert records admin master:master
INSERT INTO `tb_admin` (`admin_id`, `username`, `password`, `role_id`, `active`, `created_by`, `created_at`, `updated_by`, `updated_at`, `deleted_by`, `deleted_at`) VALUES ('1', 'master', '425e30e5b7ef70adeb9bda82633217381e721449e1730e4d26ff382fece174a72227ec252cb35146201a47afc5c76e3f24c13f6371ecc87c6c1c83c6e9fdc237d8eb0549403fcf15fe8d8261b8c02839bc8944277071', '1', '1', 'master', NOW(), 'master', NOW(), NULL, NULL);
