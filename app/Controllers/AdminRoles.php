<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use App\Models\AdminsModel;
use App\Models\AdminRolesModel;

class AdminRoles extends BaseController
{
	use ResponseTrait;

	protected $model, $admin_model;

	public function __construct() {
		$this->model = new AdminRolesModel();
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
			'role' => $this->model->find(session()->admin_id),
		];

		return view('admin_roles/index', $data);
	}

}
