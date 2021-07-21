<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use App\Models\AdminsModel;

class Dashboard extends BaseController
{
	use ResponseTrait;

	protected $admin_model;

	public function __construct() {
		$this->admin_model = new AdminsModel();
	}

	public function index()
	{
		if(!session()->has('admin_id')) return redirect()->to(base_url());
		$data = [
			'page' => (object)[
				'title' => 'Admin Role',
				'subtitle' => 'Admin Role (subtitle)',
			],
			'admin' => $this->admin_model->find(session()->admin_id),
		];

		return view('dashboard/index', $data);
	}

	public function logout()
	{
		session()->remove(['admin_id', 'username', 'role_id']);
		return redirect()->to(base_url());
	}

}
