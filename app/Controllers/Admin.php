<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class Admin extends BaseController
{
	use ResponseTrait;

	

	public function index()
	{
		if(!$this->session->has('admin_id')) return redirect()->to(base_url());

		// var_dump($this->session->get());
		// var_dump($this->session->has('admin_id'));
        echo view('templates/header');//, $data);
		echo view('admin/dashboard');
		echo view('templates/footer');
	}

	public function logout()
	{
		$this->session->remove(['admin_id', 'username', 'role_id']);
		return redirect()->to(base_url());
	}

}
