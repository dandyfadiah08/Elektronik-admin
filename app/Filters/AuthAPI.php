<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;

class AuthAPI implements FilterInterface
{
	public $decoded_token;

	/**
	 * Do whatever processing this filter needs to do.
	 * By default it should not return anything during
	 * normal execution. However, when an abnormal state
	 * is found, it should return an instance of
	 * CodeIgniter\HTTP\Response. If it does, script
	 * execution will end and that Response will be
	 * sent back to the client, allowing for error pages,
	 * redirects, etc.
	 *
	 * @param RequestInterface $request
	 * @param array|null       $arguments
	 *
	 * @return mixed
	 */
	public function before(RequestInterface $request, $arguments = null)
	{
		$status_code = 403;
		$unaothorized = "Unauthorized access.";
		try {
			$header = $request->getServer(env('jwt.bearer_name'));
			if ($header) {
				$token_arr = explode(' ', $header);
				$token = count($token_arr) == 2 ? $token_arr[1] : false;
				$decoded = JWT::decode($token, env('jwt.key'), [env('jwt.hash')]);
				if ($decoded) {
					$this->decoded_token = $decoded;
					return; // literally, do nothing
				}
				$unaothorized = "Invalid token. ";
			} else {
				$unaothorized = "No token available. ";
			}
		} catch (\Exception $e) {
			$unaothorized = $e->getMessage();
			if ($unaothorized == 'Expired token') $status_code = 401;
		}
		helper('rest_api');
		$this->response = service('response');
		return $this->response->setStatusCode($status_code)->setJSON(initResponse($unaothorized));
	}

	/**
	 * Allows After filters to inspect and modify the response
	 * object as needed. This method does not allow any way
	 * to stop execution of other after filters, short of
	 * throwing an Exception or Error.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param array|null        $arguments
	 *
	 * @return mixed
	 */
	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		//
	}
}
