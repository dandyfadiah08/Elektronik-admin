ALTER TABLE `admins_roless` 
ADD `r_admins_roles` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_admins`
,ADD `r_user` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_admins_roles`
,ADD `r_commission_rate` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_user`
,ADD `r_2fa` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_commission_rate`
,ADD `r_transaction` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_commission_rate`
,ADD `r_device_check` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_transaction`
,ADD `r_review` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_device_check`
,ADD `r_promo` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_review`
,ADD `r_promo_view` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_promo`
,ADD `r_price` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_promo_view`
,ADD `r_price_view` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_price`
,ADD `r_logs` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_price_view`
,ADD `r_manual_transfer` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_proceed_payment`
,ADD `r_withdraw` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_2fa`
,ADD `r_view_photo_id` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_submission`
,ADD `r_view_phone_no` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_view_photo_id`
,ADD `r_view_email` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_view_phone_no`
,ADD `r_view_payment_detail` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_view_email`
,ADD `r_view_address` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_view_payment_detail`
,ADD `r_confirm_appointment` ENUM('y','n') NOT NULL DEFAULT 'n' AFTER `r_view_address`
;

ALTER TABLE `admins_roless` DROP `r_survey`;




