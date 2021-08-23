<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceCheckDetails extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'device_check_details';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = ['check_id','fcm_token','simcard','cpu','harddisk','battery','root','button_back','button_volume','button_power','camera_back','camera_front','screen','cpu_detail','harddisk_detail','battery_detail','root_detail','created_at','updated_at','imei_registered','fullset','photo_id','photo_device_1','photo_device_2','photo_device_3','photo_device_4','photo_device_5','photo_device_6','photo_fullset','photo_imei_registered'];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
}
