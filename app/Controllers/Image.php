<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Image extends BaseController
{
	use ResponseTrait;
	public function thumbnail($file = false)
	{
		// validasi file dan create nama thumbnail
		$file = $this->request->getGet('file') ?? false;
		$photo_url = './uploads/';
		$default_photo = './assets/images/photo-unavailable.png';
		$photo = !$file ? $default_photo : $photo_url . $file;
		$photo = !file_exists($photo) ? $default_photo : $photo_url . $file;
		$x = explode('.', $photo);
		$x[count($x) - 2] .= "_thumb";
		$photo_thumb = implode('.', $x);

		// create thumbnail if not exist
		if (!file_exists($photo_thumb)) {
			try {
				$image = \Config\Services::image()
				->withFile($photo)
				->fit(1000, 1000, 'center')
				->save($photo_thumb, 10);
			} catch (CodeIgniter\Images\ImageException $e) {
				// echo $e->getMessage();
			}
		}

		// show image thumbnail
		$filename = basename($photo_thumb);
		$file_extension = strtolower(substr(strrchr($filename, "."), 1));

		switch ($file_extension) {
			case "gif":
				$ctype = "image/gif";
				break;
			case "png":
				$ctype = "image/png";
				break;
			case "jpeg":
			case "jpg":
				$ctype = "image/jpeg";
				break;
			case "svg":
				$ctype = "image/svg+xml";
				break;
			default:
		}

		if (file_exists($photo_thumb)) {
			header('Content-type: ' . $ctype);
			readfile($photo_thumb);
		}
		exit();
	}
}
