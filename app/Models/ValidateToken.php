<?php
namespace App\Models;

use CodeIgniter\Model;
use Exception;

class ValidateToken extends Model
{

    protected $allowedFields = ['token'];
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    function checkAPIkey($token)
    {
        $sql="SELECT * FROM member WHERE sha1(CONCAT(email,passwd))=?";
        $data=$this->db->query($sql,array($token))->getRow();
        if (!$data) {
            $sql2="SELECT * FROM pengguna WHERE sha1(CONCAT(username,passwd))=?";
            $data2=$this->db->query($sql2,$token)->getRow();
            if (!$data2){
                throw new Exception("invalid API Key, please check your API Key");
            }
        }
        return $data;
    }
    
    public function settings(){
        $sql="SELECT * FROM settings";
        $query=$this->db->query($sql)->getResult();
        return $query;
    }
}