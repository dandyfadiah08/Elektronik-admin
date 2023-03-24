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
use App\Models\MasterPameranModule;
use App\Models\MasterKategoriModule;
use App\Models\JenisGradingModule;
use App\Models\AdminRolesModel;
use App\Models\PotonganModule;
use App\Models\MasterCouriers;
use App\Models\MasterAdminModule;
use App\Models\MasterRoleModule;
use App\Models\LogModule;
use App\Models\MasterKuisionerModule;

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
	protected $data, $admin, $role;

	/**
	 * needed model variables
	 */
	protected $Admin, $AdminRole, $Pameran, $Kategori, $JGrading, $MasterKuisioner;

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
		$this->db = \Config\Database::connect();
		session();
		helper(['rest_api', 'role']);
		$this->log = new Log();
		if (!session()->has('id_admin')) {
			header('Location: ' . base_url());
			exit;
		}
		$this->Admin = new AdminsModel();
		$this->AdminRole = new AdminRolesModel();
		$this->Potongan = new PotonganModule();
		$this->Log = new LogModule();
		$this->Pameran = new MasterPameranModule();
		$this->MasterKuisioner = new MasterKuisionerModule();
		$this->Admin = new MasterAdminModule();
		$this->Kategori = new MasterKategoriModule();
		$this->JGrading = new JenisGradingModule();
		$this->masterRole = new MasterRoleModule();
		$this->role = $this->AdminRole->find(session()->id_role);
		$this->admin = $this->Admin->find(session()->id_admin);
		$this->unreviewed_count = '';
		// if (hasAccess($this->role, ['tradein', 'statistik'])) {
		// 	$this->Counter = new Counter();
		// 	$this->unreviewed_count = $this->Counter->unreviewedCount(); // select dari db
		// 	$this->unreviewed_count = $this->unreviewed_count > 0 ? $this->unreviewed_count : '';
		// }
		// $this->transaction_count = '';
		// if (hasAccess($this->role, ['admin'])) {
		// 	$this->Counter = new Counter();
		// 	$this->transaction_count = $this->Counter->transactionCount(); // select dari db
		// 	$this->transaction_count = $this->transaction_count > 0 ? $this->transaction_count : '';
		// }
		// $this->withdraw_count = '';
		// if (hasAccess($this->role, ['pameran'])) {
		// 	$this->Counter = new Counter();
		// 	$this->withdraw_count = $this->Counter->withdrawCount(); // select dari db
		// 	$this->withdraw_count = $this->withdraw_count > 0 ? $this->withdraw_count : '';
		// }
		// $this->submission_count = '';
		// if (hasAccess($this->role, ['potongan'])) {
		// 	$this->Counter = new Counter();
		// 	$this->submission_count = $this->Counter->submissionCount(); // select dari db
		// 	$this->submission_count = $this->submission_count > 0 ? $this->submission_count : '';
		// }

		$this->data = [
			'admin' => $this->admin,
			'role' => $this->role,

		];
	}
}
