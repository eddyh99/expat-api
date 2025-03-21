<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;


class Mdl_optional extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
        
  
   	public function getall_optional() {
        $sql = "SELECT * 
				FROM produk_optional WHERE is_deleted='no'";
        $query = $this->db->query($sql)->getResult();
        return $query;
    }
  	
  	public function getgroup_optional(){
    	$sql	= "SELECT DISTINCT optiongroup FROM produk_optional";
      	$query	= $this->db->query($sql)->getResult();
      	return $query;
    }

    public function get_optionalbyid($id){
        $sql = "SELECT *
				FROM produk_optional WHERE id=?";
        $query = $this->db->query($sql,$id)->getRow();        
        return $query;
    }

    public function add_optional($mdata){
        $optional   = $this->db->table("produk_optional");
        if (!$optional->insert($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add optional"
	        ];
            return (object) $error;
        }
    }

    public function update_optional($mdata, $id){
        $optional   = $this->db->table("produk_optional");
        $optional->where("id",$id);
        $optional->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }

    public function delete_optional($id){
        $optional   = $this->db->table("produk_optional");
        $optional->where("id",$id);
        $optional->set("is_deleted",'yes');
        if (!$optional->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete optional"
            ];
            return (object) $error;    
        }
    }   
   
}