<?php

namespace App\Models;

use CodeIgniter\Model;

class Referrals extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'referrals';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	// protected $allowedFields        = ['parent_id','child_id','ref_level','status','created_at','updated_at'];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';

	public function getReferral($where, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		if($order) $this->orderBy($order);
        if(is_array($where)) $output = $this->where($where)->first();
        else $output = $this->find($where);
        return $output;
    }

	public function getReferralWithDetailParent($where, $select = false)
    {
        $output = null;
        $this->select($select)
			->from('referrals as referral', true)
			->join('users u','u.user_id = referral.parent_id');
        if($where) $this->where($where);
		$output = $this->get()->getResult();
        return $output;
    }

	public function getActiveReferralByChildId($child_id, $select = false, $order = false)
    {
        $output = null;
        if($select) $this->select($select);
		$this->from('referrals as referral', true)
			->join('users u','u.user_id = referral.parent_id');
		if($order) $this->orderBy($order);
        $output = $this->where([
			'referral.child_id'	=> $child_id,
			'referral.status'	=> 'active'
		])->get()->getResult();
        return $output;
    }

	public function CountAllChild($where){
		$output = null;
        
        if(is_array($where)) $output = $this->where($where)->countAllResults();
        else $output = $this->find($where)->countAllResults();
        return $output;
	}

	public function getDownlineData($parent_id , $order = false, $limit = false, $start = 0){
		$this
					->select('referral.parent_id,
								referral.child_id,
								u.name,
								referral.transaction,
								referral.saving,
								r2.transaction_ref,
								r2.saving_ref,
								referral.status',
							)
					->from('referrals as referral', true)
					->join('(SELECT rs.parent_id, r.child_id, SUM(r.transaction) AS transaction_ref, SUM(r.saving) AS saving_ref, COUNT(rs.child_id)
					FROM referrals r
					JOIN referrals rs ON rs.child_id = r.child_id AND rs.ref_level = 1
					WHERE r.parent_id = ' . $parent_id . ' AND r.ref_level = 2
					GROUP BY rs.parent_id) AS r2', 'r2.parent_id = referral.child_id','left')
					->join('users u','u.user_id = referral.child_id AND u.phone_no_verified=\'y\'')
                    ->where('referral.parent_id', $parent_id)
                    ->where('referral.ref_level', '1')
					;

					if($order) $this->orderBy($order);
					if($limit) $this->limit($limit, $start);
		return $this
                    ->get()
                    ->getResult();
	}

	public function countReferralActiveByParent($user_id){
		$output = null;
        $this->select("COUNT(parent_id) as count_referral")
		->from('referrals as r', true)
		->join('users u', 'u.user_id=r.parent_id', 'left')
		->join('users u2', 'u2.user_id=r.child_id', 'left');
        $output = $this->where([
			'r.parent_id'	=> $user_id,
			'r.ref_level'	=> 1,
			'u2.phone_no_verified'	=> 'y'
		])
		->groupBy("r.parent_id")
		->get()->getResult();
        return count($output) > 0 ? $output[0] : false;
	}

	public function countReferralActiveByParent_old20211017($user_id){
		$output = null;
        $this->select("COUNT(parent_id) as count_referral");
		$this->from('referrals as referral', true);
        $output = $this->where([
			'referral.parent_id'	=> $user_id,
			'referral.ref_level'	=> 1
		])
		->groupBy("referral.parent_id")
		->get()->getResult();
        return count($output) > 0 ? $output[0] : false;
	}

	public function countReferralByParent($user_id){
		$output = null;
        $this->select("r.parent_id, COUNT(IF(r.status = 'active', r.parent_id, NULL)) AS jum_user_active, COUNT(IF(r.status = 'pending', r.parent_id, NULL)) AS jum_user_pending")
		->from('referrals as r', true)
		->join('users u', 'u.user_id=r.parent_id', 'left')
		->join('users u2', 'u2.user_id=r.child_id', 'left');
        $output = $this->where([
			'r.parent_id'	=> $user_id,
			'r.ref_level'	=> 1,
			'u2.phone_no_verified'	=> 'y'
		])
		->groupBy("r.parent_id")
		->get()->getResult();
        return count($output) > 0 ? $output[0] : false;
	}
}
