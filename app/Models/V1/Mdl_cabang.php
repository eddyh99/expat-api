<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;
use App\Models\V1\Mdl_settings;

class Mdl_cabang extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $db; 
    protected $setting;
    
	public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->setting = new Mdl_settings();
    }
        
    public function getall_cabang() {
		$path = base_url()."/images/cabang/";
        $sql = "SELECT nama, alamat, kontak, provinsi, opening, CONCAT('$path',picture) as picture, is_deleted,id FROM cabang WHERE is_deleted='no'";
        $query = $this->db->query($sql)->getResult();
        return $query;
    }

    public function get_cabangbyid($id){
		$path = base_url()."/images/cabang/";
        $sql = "SELECT nama, alamat, kontak,provinsi, opening,latitude,longitude, CONCAT('$path',picture) as picture, is_deleted cabang,id
				FROM cabang WHERE id=?";
        $query = $this->db->query($sql,$id)->getRow();        

        $max=$this->setting->get_settings('max_area');
        $query->max = $max->value;
        return $query;
    }
    
   public function add_cabang($mdata, $produk) {
        $cabang   = $this->db->table("cabang");
        if (!$cabang->insert($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add Cabang"
	        ];
            return (object) $error;
        }

        $id = $this->db->insertID();
    
        // Generate placeholders for the IN clause
        $produk = array_map('intval', $produk);
        $placeholders = implode(',', array_fill(0, count($produk), '?'));
    
        // Construct the SQL query without placeholder in CONCAT
        $sql = "
            UPDATE produk
            SET cabang = CONCAT(cabang, ',$id')
            WHERE id IN ($placeholders)
            AND cabang NOT LIKE ?
        ";
    
        // Flatten the parameters for binding
        $params = array_merge($produk, ["%$id%"]);

        // Execute the query
        $query = $this->db->query($sql, $params);
        return true; // Success
    }


    public function update_cabang($mdata, $id){
        $cabang   = $this->db->table("cabang");
        $cabang->where("id",$id);
        $cabang->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }

    public function delete_cabang($id){
        $cabang   = $this->db->table("cabang");
        $cabang->where("id",$id);
        $cabang->set("is_deleted",'yes');
        if (!$cabang->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete Cabang"
            ];
            return (object) $error;    
        }
    }    
    
}