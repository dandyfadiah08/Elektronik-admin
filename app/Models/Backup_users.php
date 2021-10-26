<<<<<<< HEAD
<?php

namespace App\Models;

use CodeIgniter\Model;

class Backup_users extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'backup_users';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'backup_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	/*
	 Catatan: untuk rollback hasil update (kembalikan data dari backup ke master table)
	 '2021-09-01' adalah tanggal backup_at (cron resetPendingBalance dijalankan)

		UPDATE users u
		JOIN backup_users bu ON bu.user_id=u.user_id
		AND date_format(backup_at, "%Y-%m-%d") = '2021-09-01'
		SET u.pending_balance=bu.pending_balance

	*/
}
=======
<?php

namespace App\Models;

use CodeIgniter\Model;

class Backup_users extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'backup_users';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'backup_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	/*
	 Catatan: untuk rollback hasil update (kembalikan data dari backup ke master table)
	 '2021-09-01' adalah tanggal backup_at (cron resetPendingBalance dijalankan)

		UPDATE users u
		JOIN backup_users bu ON bu.user_id=u.user_id
		AND date_format(backup_at, "%Y-%m-%d") = '2021-09-01'
		SET u.pending_balance=bu.pending_balance

	*/
}
>>>>>>> 4ceb680f190ba5888faff33d0231bebcaea1154d
