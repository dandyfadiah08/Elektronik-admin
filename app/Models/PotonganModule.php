<?php

namespace App\Models;

use CodeIgniter\Model;

class PotonganModule extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'data_potong';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['id', 'kategori', 'que_1', 'que_2', 'que_3', 'que_4', 'que_5', 'que_6', 'que_7', 'que_8', 'que_9', 'que_10', 'pertanyaan_1', 'pertanyaan_2', 'pertanyaan_3', 'pertanyaan_4', 'pertanyaan_5', 'pertanyaan_6', 'pertanyaan_7', 'pertanyaan_8', 'pertanyaan_9', 'pertanyaan_10', 'deleted_at'];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];
    function formatHarga($str)
    {
        $str = number_format($str, 0, ',', '.');
        return $str;
    }
    function Updatepotong($data_potong)
    {
        $model = $this->db->table('data_potong');
        $model->set('kategori', $data_potong['kategori'])
            ->set('que_1', $data_potong['que_1'])
            ->set('que_2', $data_potong['que_2'])
            ->set('que_3', $data_potong['que_3'])
            ->set('que_4', $data_potong['que_4'])
            ->set('que_5', $data_potong['que_5'])
            ->set('que_6', $data_potong['que_6'])
            ->set('que_7', $data_potong['que_7'])
            ->set('que_8', $data_potong['que_8'])
            ->set('que_9', $data_potong['que_9'])
            ->set('que_10', $data_potong['que_10'])
            ->set('pertanyaan_1', $data_potong['Pertanyaan1'])
            ->set('pertanyaan_2', $data_potong['Pertanyaan2'])
            ->set('pertanyaan_3', $data_potong['Pertanyaan3'])
            ->set('pertanyaan_4', $data_potong['Pertanyaan4'])
            ->set('pertanyaan_5', $data_potong['Pertanyaan5'])
            ->set('pertanyaan_6', $data_potong['Pertanyaan6'])
            ->set('pertanyaan_7', $data_potong['Pertanyaan7'])
            ->set('pertanyaan_8', $data_potong['Pertanyaan8'])
            ->set('pertanyaan_9', $data_potong['Pertanyaan9'])
            ->set('pertanyaan_10', $data_potong['Pertanyaan10'])
            ->where('id', $data_potong['id'])->update();
        return $data_potong;
    }
}
