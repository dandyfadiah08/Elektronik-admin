<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\AdminsModel;

class Admins extends BaseController
{
	use ResponseTrait;

	protected $model;

	public function __construct() {
		$this->model = new AdminsModel();
	}

	public function index()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());

		$data = [
			'page' => (object)[
				'key' => '2-admin',
				'title' => 'Admin Role',
				'subtitle' => 'Admin Role (subtitle)',
			],
			'admin' => $this->model->find(session()->admin_id),
		];
		return view('dashboard/index', $data);
	}

	public function logout()
	{
		session()->remove(['admin_id', 'username', 'role_id']);
		return redirect()->to(base_url());
	}

}
