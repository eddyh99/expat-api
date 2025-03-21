<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;


class Mdl_promosi extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
        
    public function getall_promo() {
        $now=date("Y-m-d");
		$path = base_url()."/images/promosi/";
        $sql = "SELECT id, concat('$path',picture) as picture, deskripsi, tipe, tanggal, end_date, is_deleted  FROM promosi WHERE ? BETWEEN tanggal AND end_date AND is_deleted='no' LIMIT 0,10";
        $query = $this->db->query($sql,$now)->getResult();
        return $query;
    }

    public function get_instore(){
        $now=date("Y-m-d");
		$path = base_url()."/images/promosi/";
        $sql = "SELECT id, concat('$path',picture) as picture, deskripsi, tipe, tanggal, end_date, is_deleted FROM promosi WHERE tipe='instore' AND ? BETWEEN tanggal AND end_date  AND is_deleted='no'";
        $query = $this->db->query($sql,$now)->getResult();
        return $query;
    }

    public function get_online(){
        $now=date("Y-m-d");
        $path = base_url()."/images/promosi/";
        $sql = "SELECT id, concat('$path',picture) as picture, deskripsi, tipe, tanggal, end_date, is_deleted  FROM promosi WHERE tipe='online' AND ? BETWEEN tanggal AND end_date  AND is_deleted='no'";
        $query = $this->db->query($sql,$now)->getResult();
        return $query;
    }

    public function get_promobyid($id){
		$path = base_url()."/images/promosi/";
        $sql = "SELECT id, concat('$path',picture) as picture, deskripsi, tipe, tanggal, end_date, milestone, minimum, discount_type, potongan, is_deleted FROM promosi WHERE id=?";
        $query = $this->db->query($sql,$id)->getRow();
        return $query;
    }

    public function add_promo($mdata){
        $promo   = $this->db->table("promosi");
        if (!$promo->insert($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add promotion"
	        ];
            return (object) $error;
        }
    }

    public function update_promo($mdata, $id){
        $promo   = $this->db->table("promosi");
        $promo->where("id",$id);
        $promo->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }

    public function delete_promo($id){
        $promo   = $this->db->table("promosi");
        $promo->where("id",$id);
        $promo->set("is_deleted",'yes');
        if (!$promo->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete promotion"
            ];
            return (object) $error;    
        }
    }    
}