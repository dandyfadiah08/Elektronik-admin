<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Users;
use CodeIgniter\API\ResponseTrait;

class UserController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $db = new Users;
        $user = $db->get()->getResult();
        return $this->response->setJSON(['sucess' => true, 'mesage' => 'OK', 'data' => $user]);
    }

    public function create()
    {
        if (!$this->validate([
            'username'     => 'required|is_unique[m_users.username]',
            'password'     => 'required|min_length[6]',
            'name'           => 'required',
            'address'    => 'required',
            'phone'        => 'required'
        ])) {
            return $this->response->setJSON(['success' => false, 'data' => null, "message" => \Config\Services::validation()->getErrors()]);
        }
        $date = strtotime("tomorrow");
        $end_date = date("Y-m-d H:i:s", $date);
        $insert = [
            'username' => $this->request->getVar('username'),
            'password' => $this->request->getVar('password'),
            'password_enk' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'name' => $this->request->getVar('name'),
            'hak_akses' => $this->request->getVar('hakakses'),
            'alamat' => $this->request->getVar('alamat'),
            'email' => $this->request->getVar('email'),
            'date_save' => $end_date,
        ];

        $db = new Users;
        $save  = $db->insert($insert);

        return $this->setResponseFormat('json')->respondCreated(['sucess' => true, 'mesage' => 'OK']);
    }

    public function show($id)
    {
        $db = new Users;
        $user = $db->where('id', $id)->first();

        return $this->response->setJSON(['sucess' => true, 'mesage' => 'OK', 'data' => $user]);
    }

    public function update($id)
    {
        if (!$this->validate([
            'username' => 'permit_empty|is_unique[m_users.username,id,' . $id . ']',
            'password' => 'permit_empty|min_length[6]',
            'name' => 'permit_empty',
            'hak_akses' => 'permit_empty',
            'alamat' => 'permit_empty',
            'email' => 'permit_empty',
        ])) {
            return $this->response->setJSON(['success' => false, "message" => \Config\Services::validation()->getErrors()]);
        }

        $db = new Users;
        $exist = $db->where('id_user', $id)->first();

        if (!$exist) {
            return $this->response->setJSON(['success' => false, "message" => 'User not found']);
        }

        $update = [
            'username' => $this->request->getVar('username') ? $this->request->getVar('username') : $exist['username'],
            'password' => $this->request->getVar('password') ? $this->request->getVar('password') : $exist['password'],
            'password_enk' => $this->request->getVar('password') ? password_hash($this->request->getVar('password'), PASSWORD_DEFAULT) : $exist['password'],
            'name' => $this->request->getVar('name') ? $this->request->getVar('name') : $exist['name'],
            'alamat' => $this->request->getVar('alamat')  ? $this->request->getVar('alamat') : $exist['alamat'],
            'email' => $this->request->getVar('email') ? $this->request->getVar('email') : $exist['email']
        ];

        $db = new Users;
        $save  = $db->update($id, $update);

        return $this->response->setJSON(['success' => true, 'message' => 'OK']);
    }

    public function delete($id)
    {
        $db = new Users;
        $db->where('id', $id);
        $db->delete();

        return $this->response->setJSON(['sucess' => true, 'mesage' => 'OK']);
    }
}
