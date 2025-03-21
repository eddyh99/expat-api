<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;


class Mdl_user extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
        
    public function getall_user(){
        $sql    = "SELECT * FROM pengguna WHERE is_deleted='no'";
        $query  = $this->db->query($sql);
        return $query->getResult();
    }

    public function add_users($mdata){
        $user   = $this->db->table("pengguna");
        if (!$user->insert($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Unable to add pengguna"
	        ];
            return (object) $error;
        }
    }

    public function update_user($mdata, $username){
        $user   = $this->db->table("pengguna");
        $user->where("username",$username);
        $user->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }

    public function getby_username($username){
        $sql    = "SELECT * FROM pengguna WHERE username=? and is_deleted='no'";
        $query  = $this->db->query($sql,$username);
        return $query->getRow();
    }

    public function delete_user($username){
        $user   = $this->db->table("pengguna");
        if ($username!="admin"){
            $user->where("username",$username);
            $user->set("is_deleted",'yes');
            if (!$user->update()){
                $error=[
                    "code"       => "5051",
                    "error"      => "04",
                    "message"    => "Failed to delete user"
                ];
                return (object) $error;    
            }
        }else{
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add user"
	        ];
            return (object) $error;
        }

    }
    
    public function get_allStaff(){
        $sql="SELECT c.id as cabang_id, b.id as staffid, b.nama, c.nama as cabang, c.alamat,a.is_deleted, b.is_driver  
            FROM assigncabang a INNER JOIN member b ON a.member_id=b.id 
            INNER JOIN cabang c ON a.cabang_id=c.id WHERE a.is_deleted='no' AND b.status='active'";
        $query=$this->db->query($sql);
        return $query->getResult();
    }
    
    public function cek_cabang($idstaff){
		$sql="SELECT * FROM assigncabang WHERE member_id=? AND is_deleted='no'";
		$query=$this->db->query($sql,$idstaff);
		if ($query->getNumRows()>0){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Username already Assign to other cabang"
	        ];
            return (object) $error;
		}
	}

    public function addStaff($mdata){
        $staff   = $this->db->table("assigncabang");
        if (!$staff->replace($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add staff to cabang"
	        ];
            return (object) $error;
        }
    }

    public function delete_Staff($where){
        $staff   = $this->db->table("assigncabang");
        $staff->where($where);
        $staff->set(array("is_deleted"=>'yes',"update_at"=>date("Y-m-d H:i:s")));
        if (!$staff->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete staff"
            ];
            return (object) $error;    
        }
    }
}