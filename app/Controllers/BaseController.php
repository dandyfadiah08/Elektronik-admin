<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Log;
use App\Libraries\Counter;
use CodeIgniter\API\ResponseTrait;
use App\Models\AdminsModel;
use App\Models\AdminRolesModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
	use ResponseTrait;
	/**
	 * Instance of the main Request object.
	 *
	 * @var IncomingRequest|CLIRequest
	 */
	protected $request;

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];

	/**
	 * $log Hold Logs library to write every action of the user (admin)
	 */
	protected $log;

	/**
	 * needed variables
	 */
	protected $data,$admin,$role,$unreviewed_count,$transaction_count,$withdraw_count;

	/**
	 * needed model variables
	 */
	protected $Admin,$AdminRole;

	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.: $this->session = \Config\Services::session();
		// $this->session = \Config\Services::session();

		session();
		helper(['rest_api', 'role']);
		$this->log = new Log();
		if(!session()->has('admin_id')) {
			header('Location: '.base_url());
			exit;
		}
		$this->Admin = new AdminsModel();
		$this->AdminRole = new AdminRolesModel();
		$this->role = $this->AdminRole->find(session()->role_id);
		$this->admin = $this->Admin->find(session()->admin_id);
		$this->unreviewed_count = '';
		if(hasAccess($this->role, ['r_device_check', 'r_review'])) {
			$this->Counter = new Counter();
			$this->unreviewed_count = $this->Counter->unreviewedCount(); // select dari db
			$this->unreviewed_count = $this->unreviewed_count > 0 ? $this->unreviewed_count : '';
		}
		$this->transaction_count = '';
		if(hasAccess($this->role, ['r_transaction'])) {
			$this->Counter = new Counter();
			$this->transaction_count = $this->Counter->transactionCount(); // select dari db
			$this->transaction_count = $this->transaction_count > 0 ? $this->transaction_count : '';
		}
		$this->withdraw_count = '';
		if(hasAccess($this->role, ['r_withdraw'])) {
			$this->Counter = new Counter();
			$this->withdraw_count = $this->Counter->withdrawCount(); // select dari db
			$this->withdraw_count = $this->withdraw_count > 0 ? $this->withdraw_count : '';
		}
		$this->submission_count = '';
		if(hasAccess($this->role, ['r_submission'])) {
			$this->Counter = new Counter();
			$this->submission_count = $this->Counter->submissionCount(); // select dari db
			$this->submission_count = $this->submission_count > 0 ? $this->submission_count : '';
		}

		$this->data = [
			'admin' => $this->admin,
			'role' => $this->role,
			'unreviewed_count' => $this->unreviewed_count,
			'transaction_count' => $this->transaction_count,
			'withdraw_count' => $this->withdraw_count,
			'submission_count' => $this->submission_count,
		];
	}
}
