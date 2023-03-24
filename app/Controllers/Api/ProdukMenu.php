<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\Api\BaseController;
use \Firebase\JWT\JWT;
use App\Libraries\FirebaseCoudMessaging;
use App\Models\Users;
use App\Models\MasterPameranModule;
use App\Models\MasterKategoriModule;
use App\Models\MitraPameran;


class ProdukMenu extends BaseController
{
    use ResponseTrait;
    protected $request;
    public function __construct()
    {
        $this->db = \Config\Database::connect();

        helper('rest_api');
        helper('validation');
        helper('redis');
        helper('otp');
    }
    public function index($id_pameran = null)
    {
        helper("cookie");
        $response = initResponse();
        $key = $_SERVER['jwt.key'];
        $baseurl = base_url();
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                // $decoded = JWT::decode($token, $key, [env('jwt.hash')]);

                $MitraPameran = new MasterPameranModule();
                $Datakategori = new MasterKategoriModule();
                $id_pameran = $this->request->getVar('id_pameran');
                $produk = $MitraPameran->productharga($id_pameran);
                $fieldkategori =  $Datakategori->showkategori();
                $data = [];
                $kategori = [];
                foreach ($produk as $key => $value) {
                    $data[] = ['id_product' => $value['id_product'], 'kategori' => $value['kategori']];
                }
                $jumlah = count($data);
                $duplicated = [];
                foreach ($produk as $key => $value) {


                    $kategori[] = [$value['kategori']];
                }
                sort($kategori);
                foreach ($kategori as $k => $v) {

                    if (($kt = array_search($v, $kategori)) !== false and $k != $kt) {
                        unset($kategori[$kt]);
                        $duplicated[] = $v;
                    }
                }
                sort($kategori);
                $kategoriData = [];
                $tamp = [];
                foreach ($kategori as $dataProduk) {
                    $kategori = $dataProduk;

                    $kategoriData[] = $kategori;
                }
                foreach ($kategoriData as $key => $value) {
                    foreach ($fieldkategori as $key => $data) {
                        if ($value[0] == $data['nama_kategori']) {
                            $tamp[] = ['Kategori' => $value[0], 'gambar' => $data['deskripsi']];
                        }
                    }
                }
                return $this->response->setJSON(['success' => true, 'message' => 'Data Mitra', 'data' => $tamp]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }


        helper('log');
        writeLog(
            "api",
            "produkmenu\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
    public function kodeproduct($id_pameran = null, $kategori = null)
    {
        helper("cookie");
        $response = initResponse();
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $MitraPameran = $MitraPameran = new MasterPameranModule();
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                new MasterPameranModule();
                $id_pameran = $this->request->getVar('id_pameran');
                $kategori = $this->request->getVar('kategori');
                $produk = $MitraPameran->producthargaUser($id_pameran, $kategori);

                return $this->response->setJSON(['success' => true, 'message' => 'Data Mitra', 'data' => $produk]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }
        helper('log');
        writeLog(
            "api",
            "kodeproduk\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
    public function tambahproduk()
    {
        $response = initResponse();
        helper("cookie");
        $response = initResponse();
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                $MitraPameran = new MasterPameranModule();
                $id_pameran = $this->request->getVar('id_pameran');
                $id_product = $this->request->getVar('id_product');
                $sn = $this->request->getVar('sn');
                if ($sn == '') {
                    return $this->response->setJSON(['success' => false, 'message' => 'Serial Number Tidak Boleh Kosong'])->setStatusCode(409);
                } else {
                    $produk = $MitraPameran->dataProduct($id_pameran, $id_product);
                    $session = \Config\Services::session();
                    $session = session();
                    $session->set('id_product', $produk[0]['id_product']);
                    $session->set('kode_produk', $produk[0]['kode_produk']);
                    $session->set('id_pameran', $produk[0]['id_pameran']);
                    $session->set('kategori', $produk[0]['kategori']);
                    $session->set('merk', $produk[0]['merk']);
                    $session->set('spec', $produk[0]['spec']);
                    $session->set('size', $produk[0]['size']);
                    $session->set('tahun', $produk[0]['tahun']);
                    $session->set('subsidi', $produk[0]['subsidi']);
                    $session->set('subsidi_mitra1', $produk[0]['subsidi_mitra1']);
                    $session->set('subsidi_mitra2', $produk[0]['subsidi_mitra2']);
                    $session->set('harga', $produk[0]['harga']);
                    $session->set('date_save_product', date('Y-m-d h:i:s'));
                    $session->set('sn', $sn);
                    $userData = $_SESSION;
                    return $this->response->setJSON(['success' => true, 'message' => 'Data Produk Sudah tersimpan dalam session']);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }

        helper('log');
        writeLog(
            "api",
            "createproduk\n"
                . json_encode($this->request->getGet()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
    public function tambahregister()
    {
        helper("cookie");
        $username = get_cookie('user');
        $id_user = get_cookie('user_id');
        $id_mitra = get_cookie('id_mitra');
        $id_toko = get_cookie('id_toko');
        $nama_te = get_cookie('nama_te');
        $alamat_te = get_cookie('alamat_te');
        $user_type = get_cookie('user_type');
        $response = initResponse();

        $MitraPameran = new MasterPameranModule();
        // $id_pameran = $this->request->getVar('id_pameran');
        // $kategori = $this->request->getVar('kategori');
        helper("cookie");
        $response = initResponse();
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                $register = $MitraPameran->dataregister($id_user, $username);
                $session = \Config\Services::session();
                $session = session();
                $session->set('username', $username);
                $session->set('user_id', $id_user);
                $session->set('id_mitra', $id_mitra);
                $session->set('id_toko', $id_toko);
                $session->set('user_type', $user_type);
                $session->set('nama_te', $nama_te);
                $session->set('alamat_te', $alamat_te);
                $session->set('kode_register', $register['kode_register']);
                $session->set('date_saveregister', $register['date_save']);
                $userData = $_SESSION;
                return $this->response->setJSON(['success' => true, 'message' => 'Data Register Telah Berhasil di simpan dalam Seassion']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }

        helper('log');
        writeLog(
            "api",
            "createkoderegister\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
    public function hapusdataProduct()
    {
        $response = initResponse();
        helper("cookie");
        $response = initResponse();
        $key = $_SERVER['jwt.key'];
        $header = $this->request->getHeader("Authorization");
        if ($header == '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Token is Empty'])->setStatusCode(409);
        } else {
            $token = explode(' ', $header)[2];
            if ($token == get_cookie("token")) {
                $session = \Config\Services::session();
                $session = session();
                $userData = $_SESSION;
                session_destroy();
                // $session->remove('kode_register');
                // $session->remove('date_saveregister');
                // $session->remove('id_product');
                // $session->remove('id_pameran');
                // $session->remove('kategori');
                // $session->remove('merk');
                // $session->remove('spec');
                // $session->remove('size');
                // $session->remove('tahun');
                // $session->remove('subsidi');
                // $session->remove('subsidi_mitra1');
                // $session->remove('subsidi_mitra2');
                // $session->remove('harga');
                // $session->remove('date_save_product');
                // $session->remove('sn');

                return $this->response->setJSON(['success' => true, 'message' => 'Data Product Telah terhapus']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Token Invalid'])->setStatusCode(409);
            }
        }

        helper('log');
        writeLog(
            "api",
            "destroyAllProduct\n"
                . json_encode($this->request->getPost()) . "\n"
                . json_encode($response)
        );
        return $this->respond($response, 200);
    }
}
