<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;


class Mdl_settings extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
        
    public function get_settings($field){
        $sql    = "SELECT * FROM settings WHERE content=?";
        $query  = $this->db->query($sql,$field);
        return $query->getRow();
    }
    
    public function getall_setting(){
        $sql    = "SELECT * FROM settings";
        $query  = $this->db->query($sql);
        return $query->getResult();
    }
    
    public function save_settings($mdata){
        $setting=$this->db->table("settings");
        
        if (!$setting->updateBatch($mdata,"content")){
            $err=(object)array(
                    "code"      => 5051,
                    "message"   => $this->db->error()
                );
            return $err;
        }
    }
}