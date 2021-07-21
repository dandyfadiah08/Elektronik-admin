<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
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

		return view('admin/dashboard', $this->model->asArray()->find(session()->admin_id));
	}

	public function logout()
	{
		session()->remove(['admin_id', 'username', 'role_id']);
		return redirect()->to(base_url());
	}

}
