<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;


class Mdl_member extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    public function getby_email($email) {
        $sql = "SELECT * FROM member WHERE email=?";
        $query = $this->db->query($sql, $email)->getRow();

        if (!$query) {
	        $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Invalid user/wrong password"
	        ];
            return (object) $error;
        }
        
        return $query;
    }
    
    public function resetToken($email){
        $tblmember=$this->db->table("member");
        $data=array(
                "token"         => mt_rand(1000, 9999),
                "request_time"  => date("Y-m-d H:i:s")
            );
        $tblmember->where("email",$email);
        if ($tblmember->update($data)){
            return $data["token"];
        }

    }
    
    public function getby_token($token) {
	    $now    = date("Y-m-d H:i:s");

        $sql = "SELECT * FROM member WHERE token=? AND DATE_ADD(request_time, INTERVAL 10 MINUTE) > ?";
        $query = $this->db->query($sql, [$token,$now])->getRow();

		if (!$query) {
	        $error=[
	            "code"       => "5051",
	            "error"      => "02",
	            "message"    => "Invalid Token/expired token"
	        ];
            return (object) $error;
        }
        
        return $query;
    }
    
    public function add($data=array()) {
        $tblmember=$this->db->table("member");
        if ($data["is_google"]=="no"){
            $data["token"]=mt_rand(1000, 9999);
        }
        if (!$tblmember->insert($data)){
            $error= $this->db->error();
            return (object)$error;
        }else{
            return @$data["token"];
        }
    }
        
    public function activate($email) {
        $tblmember=$this->db->table("member");
        $mdata = array(
            "token" => NULL,
            "status" => "active",
            );
        $tblmember->where("email", $email);
        $tblmember->where("status", "new");
        $tblmember->update($mdata);
        if ($this->db->affectedRows()==0){
	        $error=[
	            "code"       => "5051",
	            "error"      => "03",
	            "message"    => "Activation failed, Invalid token"
	        ];
            return (object) $error;
        }
    }
    
    /*public function resetToken($email){
        $sql="SELECT id FROM member WHERE email=?";
        if (!$this->db->query($sql,$email)->getRow()){
            $error=[
	            "code"       => "5051",
	            "error"      => "07",
	            "message"    => "Member not found"
	        ];
            return (object) $error;
        }
        $id=$this->db->query($sql,$email)->getRow()->id;

        $member=$this->db->table("member");
        $token=$this->generate_token($id);
        
        $member->where('email', $email);
        $member->set("token",$token);
        $member->update();
        return $token;
        
    }*/
    
    public function change_password($mdata, $where) {
        $member=$this->db->table("member");
        $member->where($where);
        $member->update($mdata);
        if ($this->db->affectedRows()==0){
	        $error=[
	            "code"       => "5051",
	            "error"      => "08",
	            "message"    => "Failed to change password, please try again later"
	        ];
            return (object) $error;
        }
    }
    
    public function updatedata($mdata,$where){
        $member=$this->db->table("member");
        $member->where("id",$where);
        if (!$member->update($mdata)){
	        $error=[
	            "code"       => "5051",
	            "error"      => "08",
	            "message"    => $this->db->error()
	        ];
            return (object) $error;
        }
    }    

    public function getall_member($role){
        $path = base_url()."/images/user/";
        $nowyear=date("Y");
        $sql="SELECT 
                id as memberid,
                memberid as qrmember,
                CONCAT('$path', picture) AS picture, 
                email,
                status,
                nama, 
                plafon, 
                CASE
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_platinum') THEN 'Platinum'
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_gold') THEN 'Gold'
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_silver') THEN 'Silver'
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_bronze') THEN 'Bronze'
        			ELSE 'No Membership'
    		    END AS membership,
                    role, 
                    IF(role = 'pegawai', 
                        plafon + IFNULL(SUM(x.saldo), 0), 
                        IFNULL(SUM(x.saldo), 0)
                    ) AS saldo, 
                    SUM(x.poin) AS poin, 
                    dob, 
                    gender, 
                    country, 
                    phone,
                    is_driver
		    FROM 
                    member a 
                LEFT JOIN (
                    SELECT 
                        id_member, 
                        SUM(nominal) AS saldo, 
                        SUM(poin) AS poin 
                    FROM 
                        history_topup 
                    WHERE                        
                        status = 'success' 
                        AND YEAR(tanggal) = {$nowyear}
                
                    UNION ALL 
                
                    SELECT 
                        id_member, 
                        SUM(jumlah * harga) * -1 AS saldo, 
                        0 AS poin 
                    FROM 
                        transaksi a 
                    INNER JOIN 
                        transaksi_detail b ON a.id = b.id_transaksi 
                    WHERE                        
                        is_paid = 'yes' 
                        AND carabayar = 'expatbalance' 
                        AND YEAR(a.tanggal) = {$nowyear}
                
                    UNION ALL 
                
                    SELECT 
                        id_member, 
                        SUM(delivery_fee) * -1 AS saldo, 
                        0 AS poin 
                    FROM 
                        transaksi 
                    WHERE 
                        is_paid = 'yes' 
                        AND carabayar = 'expatbalance' 
                        AND YEAR(tanggal) = {$nowyear}
                
                    UNION ALL 
                
                    SELECT 
                        id_member, 
                        0 AS saldo, 
                        SUM(poin) AS poin 
                    FROM 
                        transaksi a 
                    INNER JOIN 
                        transaksi_detail b ON a.id = b.id_transaksi 
                    WHERE 
                        is_paid = 'yes' 
                        AND is_proses = 'complete' 
                        AND YEAR(a.tanggal) = {$nowyear}
                ) x ON x.id_member = a.id 
		WHERE role=?
                GROUP BY 
                    a.id, a.picture, a.nama, a.plafon, a.role, a.dob, a.gender, a.country, a.phone;";
        $query=$this->db->query($sql,$role);
        return $query->getResult();
    }

    public function add_member($mdata){
        $member   = $this->db->table("member");
        if (!$member->insert($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add member"
	        ];
            return (object) $error;
        }
    }

    public function update_member($mdata, $id){
        $user   = $this->db->table("member");
        $user->where("id",$id);
        $user->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }

    public function getby_id($id){
        $path = base_url()."/images/user/";
        $sql    = "SELECT id, memberid as qrmember,  CONCAT('$path', picture) AS picture,nama, phone, dob, gender FROM member WHERE id=?";
        $query = $this->db->query($sql, $id)->getRow();

        if (!$query) {
	        $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Invalid user"
	        ];
            return (object) $error;
        }
        
        return $query;
    }    

    public function delete_member($id){
        $member   = $this->db->table("member");
        $member->where("id",$id);
        $member->set("status",'disabled');
        if (!$member->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete user"
            ];
            return (object) $error;    
        }
    }

    public function topup_dana($mdata){
        $topup   = $this->db->table("history_topup");
        if (!$topup->insert($mdata)){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to Add Topup"
            ];
            return (object) $error;    
        }
    }
  
  	public function add_address($mdata){
      	$kirim   = $this->db->table("pengiriman");
        if (!$kirim->insert($mdata)){
            $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => "Failed to add pengiriman"
	        ];
            return (object) $error;
        }
    }
  
  	public function last_address($idmember){
      $sql	 = "SELECT * FROM pengiriman WHERE id_member=? AND is_deleted='no' ORDER BY id DESC";
      $query = $this->db->query($sql,$idmember);
      return $query->getRow();
    }
  
  	public function get_address($idmember){
      $sql 		= "SELECT * FROM pengiriman WHERE id_member=? AND is_deleted='no'";
      $query	= $this->db->query($sql,$idmember);
      return $query->getResult();
    }
  
   public function update_address($mdata, $id){
        $user   = $this->db->table("pengiriman");
        $user->where("id",$id);
        $user->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }
  
    public function delete_address($id){
        $member   = $this->db->table("pengiriman");
        $member->where("id",$id);
        $member->set("is_deleted",'yes');
        if (!$member->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete address"
            ];
            return (object) $error;    
        }
    }
    
    public function update_pin($pin, $userid){
        $member = $this->db->table("member");
        $member->where("id",$userid);
        $member->set("pin",$pin);
        $member->update();
        return ($this->db->affectedRows()>0)?true:false;
    }
    
    public function check_pin($pin, $userid){
        $sql="SELECT * FROM member WHERE id=? AND pin=?";
        $query=$this->db->query($sql,[$userid,$pin]);
        if ($query->getNumRows()==0){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "invalid pin"
            ];
            return (object) $error;    
        }
    }
  
    public function update_pass($oldpass, $newpass, $userid){
        $member = $this->db->table("member");
        $member->where(array("passwd"=>$oldpass, "id"=>$userid));
        $member->set("passwd",$newpass);
        $member->update();
        return ($this->db->affectedRows()>0)?true:false;
    }
}