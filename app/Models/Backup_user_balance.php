<?php

namespace App\Models;

use CodeIgniter\Model;

class Backup_user_balance extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'backup_user_balance';
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

		UPDATE user_balance ub
		JOIN backup_user_balance bub ON bub.user_balance_id=ub.user_balance_id
		AND date_format(backup_at, "%Y-%m-%d") = '2021-09-01'
		SET ub.status=bub.status

	*/
}
