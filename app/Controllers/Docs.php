<?php

namespace App\Controllers;

class Docs extends BaseController
{
	public function index()
	{
		try {
			$path = "../docs/dist/app.bundle.js";
			// $file = new \CodeIgniter\Files\File($path, true);
			$javascript = '';
			$file = fopen($path, "r");
			// while ($c = file ) {
			// 	# code...
			// }
			// $file = file_get_contents($path);
			$javascript = fread($file, filesize($path));
			// var_dump($javascript);
			echo '
			<!DOCTYPE html>
			<html lang="en">
			
			<head>
				<meta charset="UTF-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>API Documentation</title>
			</head>
			
			<body>
				<div id="swagger"></div>
			<script type="text/javascript">'.$javascript.'</script></body>
			
			</html>
			';
		} catch (\Exception $e) {
			echo $e->getMessage();
			log_message('debug', $e->getFile()."|".$e->getLine()." : ".$e->getMessage());
		}
		// return view('admin/index', $this->data);
	}
}
