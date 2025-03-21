<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;


class Mdl_additional extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
        
  
   	public function getall_additional() {
        $sql = "SELECT * 
				FROM produk_additional WHERE is_deleted='no'";
        $query = $this->db->query($sql)->getResult();
        return $query;
    }
  	
  	public function getgroup_additional(){
    	$sql	= "SELECT DISTINCT additional_group FROM produk_additional";
      	$query	= $this->db->query($sql)->getResult();
      	return $query;
    }

    public function get_additionalbyid($id){
        $sql = "SELECT *
				FROM produk_additional WHERE id=?";
        $query = $this->db->query($sql,$id)->getRow();        
        return $query;
    }

    public function add_additional($mdata){
        $additional   = $this->db->table("produk_additional");
        if (!$additional->insert($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add additional"
	        ];
            return (object) $error;
        }
    }

    public function update_additional($mdata, $id){
        $additional   = $this->db->table("produk_additional");
        $additional->where("id",$id);
        $additional->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }

    public function delete_additional($id){
        $additional   = $this->db->table("produk_additional");
        $additional->where("id",$id);
        $additional->set("is_deleted",'yes');
        if (!$additional->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete additional"
            ];
            return (object) $error;    
        }
    }   
   
}