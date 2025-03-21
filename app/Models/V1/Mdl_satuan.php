<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;


class Mdl_satuan extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
        
  
   	public function getall_satuan() {
        $sql = "SELECT * 
				FROM produk_satuan WHERE is_deleted='no'";
        $query = $this->db->query($sql)->getResult();
        return $query;
    }
  	
  	public function getgroup_satuan(){
    	$sql	= "SELECT DISTINCT groupname FROM produk_satuan";
      	$query	= $this->db->query($sql)->getResult();
      	return $query;
    }

    public function get_satuanbyid($id){
        $sql = "SELECT *
				FROM produk_satuan WHERE id=?";
        $query = $this->db->query($sql,$id)->getRow();        
        return $query;
    }

    public function add_satuan($mdata){
        $satuan   = $this->db->table("produk_satuan");
        if (!$satuan->insert($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add satuan"
	        ];
            return (object) $error;
        }
    }

    public function update_satuan($mdata, $id){
        $satuan   = $this->db->table("produk_satuan");
        $satuan->where("id",$id);
        $satuan->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }

    public function delete_satuan($id){
        $satuan   = $this->db->table("produk_satuan");
        $satuan->where("id",$id);
        $satuan->set("is_deleted",'yes');
        if (!$satuan->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete Satuan"
            ];
            return (object) $error;    
        }
    }   
   
}