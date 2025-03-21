<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;
use App\Models\V1\Mdl_settings;


class Mdl_transaksi extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->settings= new Mdl_settings();
    }
        
    public function get_all($userid) {
        $path = base_url()."/images/user/";
        $nowyear=date("Y");
        
        $sql="SELECT 
                CONCAT('$path', picture) AS picture, 
                email,
                status,
                nama, 
                plafon, 
                is_driver,
                CASE
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_platinum') THEN 'Platinum'
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_gold') THEN 'Gold'
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_silver') THEN 'Silver'
        			ELSE 'Bronze'
    		    END AS membership,
                    role, 
                    IFNULL(SUM(x.saldo), 0) AS saldo, 
                    IFNULL(SUM(x.poin),0) AS poin, 
                    dob, 
                    gender, 
                    country, 
                    phone,
                CASE
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_platinum') THEN CONCAT(
                (SELECT value FROM settings WHERE content = 'step1_platinum'), ',',
                (SELECT value FROM settings WHERE content = 'step2_platinum'), ',',
                (SELECT value FROM settings WHERE content = 'step3_platinum'), ',',
                (SELECT value FROM settings WHERE content = 'step4_platinum'), ',',
                (SELECT value FROM settings WHERE content = 'step5_platinum'), ',',
                (SELECT value FROM settings WHERE content = 'step6_platinum')
            )
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_gold') THEN CONCAT(
                (SELECT value FROM settings WHERE content = 'step1_gold'), ',',
                (SELECT value FROM settings WHERE content = 'step2_gold'), ',',
                (SELECT value FROM settings WHERE content = 'step3_gold'), ',',
                (SELECT value FROM settings WHERE content = 'step4_gold'), ',',
                (SELECT value FROM settings WHERE content = 'step5_gold'), ',',
                (SELECT value FROM settings WHERE content = 'step6_gold')
            )
        			WHEN SUM(x.poin) >= (SELECT CAST(value AS UNSIGNED) FROM settings WHERE content = 'poin_silver') THEN CONCAT(
                (SELECT value FROM settings WHERE content = 'step1_silver'), ',',
                (SELECT value FROM settings WHERE content = 'step2_silver'), ',',
                (SELECT value FROM settings WHERE content = 'step3_silver'), ',',
                (SELECT value FROM settings WHERE content = 'step4_silver'), ',',
                (SELECT value FROM settings WHERE content = 'step5_silver'), ',',
                (SELECT value FROM settings WHERE content = 'step6_silver')
            )
        		ELSE CONCAT(
                (SELECT value FROM settings WHERE content = 'step1_bronze'), ',',
                (SELECT value FROM settings WHERE content = 'step2_bronze'), ',',
                (SELECT value FROM settings WHERE content = 'step3_bronze'), ',',
                (SELECT value FROM settings WHERE content = 'step4_bronze'), ',',
                (SELECT value FROM settings WHERE content = 'step5_bronze'), ',',
                (SELECT value FROM settings WHERE content = 'step6_bronze')
            )
    		    END AS step_values
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
                        id_member = ?
                        AND status = 'success' 
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
                        id_member = ?
                        AND is_paid = 'yes' 
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
                        id_member = ?
                        AND is_paid = 'yes' 
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
                        id_member = ?
                        AND is_paid = 'yes' 
                        AND is_proses = 'complete' 
                        AND YEAR(a.tanggal) = {$nowyear}
                ) x ON x.id_member = a.id 
                WHERE 
                    a.id = ?
                GROUP BY 
                    a.id, a.picture, a.nama, a.plafon, a.role, a.dob, a.gender, a.country, a.phone;";
        // $sql = "SELECT CONCAT('$path',picture) as picture, nama, plafon, membership, role, IFNULL(sum(x.saldo),0) as saldo, sum(poin) as poin, dob, gender, country, phone 
        // 		FROM member a LEFT JOIN ( 
        //         	SELECT id_member, sum(nominal) as saldo, sum(poin) as poin FROM history_topup WHERE id_member=? AND status='success' 
                    
        //             UNION ALL 
                    
        //             SELECT id_member, sum(jumlah*harga)*-1 as saldo, 0 as poin FROM transaksi a INNER JOIN transaksi_detail b ON a.id=b.id_transaksi WHERE id_member=? AND is_paid='yes' AND carabayar='expatbalance'
                    
        //             UNION ALL
                    
        //             SELECT id_member, sum(delivery_fee)*-1 as saldo, 0 as poin FROM transaksi a WHERE id_member=? AND is_paid='yes' AND carabayar='expatbalance'
                    
        //             UNION ALL
                    
        //             SELECT id_member, 0 as saldo, sum(poin) as poin FROM transaksi a INNER JOIN transaksi_detail b ON a.id=b.id_transaksi WHERE id_member=? AND is_paid='yes' AND is_proses='complete'
        //         ) x ON x.id_member=a.id WHERE id=?";
        $query = $this->db->query($sql, [$userid,$userid,$userid,$userid,$userid])->getRow();
        return $query;
    }
	
	public function update_status($invoice,$tipe){
	    if ($tipe=="topup"){
    		$trans = $this->db->table("history_topup");
    		$trans->where("invoice",$invoice);
    		$trans->set("status",'success');
    		$trans->set("update_at",date("Y-m-d H:i:s"));
    		$trans->update();
	    }else{
    		$trans = $this->db->table("transaksi");
			$trans->where("id_transaksi",$invoice);
    		$trans->set("is_paid",'yes');
    		$trans->set("update_at",date("Y-m-d H:i:s"));
    		$trans->update();
	    }
        return $this->db->error();
	}
	
	public function set_topup($mdata){
		$trans = $this->db->table("history_topup");
		if (!$trans->insert($mdata)){
			 $error=[
	            "code"       => "5051",
	            "error"      => "04",
	            "message"    => $this->db->error()
	        ];
            return (object) $error;
		}		
	}
  
  	public function transaction_add($mdata,$items){
      	$transaksi=$this->db->table("transaksi");
      	$detailtransaksi=$this->db->table("transaksi_detail");
	    
      	$this->db->transStart();
          $transaksi->insert($mdata);
          $transid=$this->db->insertID();
          $error[]=$this->db->error();

          $detail=array();
          $totalpembelian=0;
          foreach ($items as $dt){
              if ($dt->tipe=="produk"){
                $sql	 = "INSERT INTO transaksi_detail (id_transaksi, tipe, sku, nama, jumlah, harga, prd_group)
                            SELECT ?, 'produk', o.sku , o.nama, ? , o.price, ?
                            FROM produk o
                            WHERE o.id = ?";
              }elseif($dt->tipe=="optional"){
                $sql	 = "INSERT INTO transaksi_detail (id_transaksi, tipe, sku, nama, jumlah, harga, prd_group)
                            SELECT ?, 'optional', o.sku , o.optional, ? , o.price, ?
                            FROM produk_optional o
                            WHERE o.id = ?";
              }elseif($dt->tipe=="additional"){
                $sql	 = "INSERT INTO transaksi_detail (id_transaksi, tipe, sku, nama, jumlah, harga, prd_group)
                            SELECT ?, 'additional', o.sku , o.additional, ? , o.price, ?
                            FROM produk_additional o
                            WHERE o.id = ?";
              }elseif($dt->tipe=="satuan"){
                $sql	 = "INSERT INTO transaksi_detail (id_transaksi, tipe, sku, nama, jumlah, harga, prd_group)
                            SELECT ?, 'satuan', o.sku , o.satuan, ? , o.price, ?
                            FROM produk_satuan o
                            WHERE o.id = ?";
              }
            $query = $this->db->query($sql,[$transid,$dt->jumlah,$dt->group, $dt->id]);
          }

          $error[]=$this->db->error();
          $countpoin = "SELECT sum(jumlah*harga) as total FROM transaksi_detail WHERE id_transaksi=?";
          $totalpembelian = $this->db->query($countpoin,$transid)->getRow()->total;
  		  
  		  $base_poin=$this->settings->get_settings('poin_calculate')->value;
          $poin = floor($totalpembelian/$base_poin);
          
          $transaksi->where("id",$transid);
          $transaksi->set("poin",$poin);
          $transaksi->update();
          $error[]=$this->db->error();

    	$this->db->transComplete();
        if ($this->db->transStatus() === false) {
          return $error;
		}
    }
  
  	public function get_transaksi($userid){
      $sql	 = "SELECT a.*, b.nama as cabang FROM transaksi a INNER JOIN cabang b ON a.cabang=b.id WHERE id_member=? ORDER BY tanggal DESC";
      $query = $this->db->query($sql,$userid);
      return $query->getResult();
    }
  
  	public function transaksi_byid($id){
      $sql	 = "SELECT * FROM transaksi_detail WHERE id_transaksi=?";
      $query = $this->db->query($sql,$id);
      return $query->getResult();
    }
    
    //history
    public function history_topup($awal,$akhir,$id_member=NULL){
        if (!empty($id_member)){
            $sql    = "SELECT a.*, b.memberid as qrmember FROM history_topup a INNER JOIN member b ON a.id_member=b.id WHERE date(tanggal) BETWEEN ? AND ? AND id_member=? ORDER BY tanggal DESC";
            $query  = $this->db->query($sql,[$awal,$akhir,$id_member]);
        }else{
            $sql    = "SELECT a.*, b.memberid as qrmember FROM history_topup a INNER JOIN member b ON a.id_member=b.id WHERE date(tanggal) BETWEEN ? AND ?  ORDER BY tanggal DESC";
            $query  = $this->db->query($sql,[$awal,$akhir]);
        }
        return $query->getResult();
    }

    public function history_poin($awal,$akhir,$id_member){
        $sql    = "SELECT deskripsi, tanggal, poin FROM (
                    SELECT 'Topup' as deskripsi, created_at as tanggal, poin FROM history_topup WHERE date(created_at) BETWEEN ? AND ? AND status='success' AND id_member=?
                    UNION ALL
                    SELECT concat('Transaction ',id_transaksi) as deskripsi, tanggal, poin FROM transaksi WHERE date(tanggal) BETWEEN ? AND ? and is_paid='yes' AND is_proses='complete' AND id_member=?) x ORDER BY tanggal DESC
        ";
        $query  = $this->db->query($sql,[$awal,$akhir,$id_member,$awal,$akhir,$id_member]);
        return $query->getResult();
    }
    
    public function history_transaksi($awal,$akhir,$id_member){
        $sql    = "SELECT x.*,y.nama,y.total 
                    FROM transaksi x INNER JOIN 
                    (
                        SELECT id_transaksi, nama, count(1) as total 
                            FROM `transaksi_detail` 
                        WHERE tipe='produk' GROUP BY id_transaksi
                    )y ON x.id=y.id_transaksi 
                    WHERE date(tanggal) BETWEEN ? AND ? AND id_member=? ORDER BY tanggal DESC";
        $query  = $this->db->query($sql,[$awal,$akhir,$id_member]);
        return $query->getResult();
    }
    
    //deprecated
/*    public function history_byinvoice($invoice){
        $sql="select a.*, b.jumlah, b.harga, d.satuan, e.optional, 
            f.additional,g.nama as cabang,g.alamat as almtcabang, i.title,i.alamat, i.phone, h.nama 
            FROM transaksi a INNER JOIN transaksi_detail b ON a.id=b.id_transaksi 
            INNER JOIN produk_detail c ON b.id_varian=c.id 
            INNER JOIN produk_satuan d ON c.id_satuan=d.id 
            LEFT JOIN produk_optional e ON c.id_optional=e.id 
            LEFT JOIN produk_additional f ON c.id_additional=f.id 
            INNER JOIN cabang g ON a.cabang=g.id 
            INNER JOIN produk h ON c.id_produk=h.id
            LEFT JOIN pengiriman i ON a.id_pengiriman=i.id
            WHERE a.id_transaksi=?
            ";
        $query=$this->db->query($sql,$invoice);
        return $query->getResult();
    }
*/    
    public function transaksi_adminbyid($invoice){
        $path = base_url()."/images/user/";
        $prodpath = base_url()."/images/produk/";
        $sql="SELECT (SELECT CONCAT('$prodpath',picture) FROM produk WHERE sku=b.sku) as imgprod , CONCAT('$path',e.picture) as picture,
                a.*, b.*,c.nama as cabang,c.alamat as almtcabang,d.title,d.alamat, 
                d.phone,e.nama as customer, e.memberid as qrmember,f.nama as namadriver 
                FROM transaksi a INNER JOIN transaksi_detail b ON a.id=b.id_transaksi 
                INNER JOIN cabang c ON a.cabang=c.id LEFT JOIN pengiriman d ON a.id_pengiriman=d.id 
                INNER JOIN member e ON a.id_member=e.id 
                LEFT JOIN member f ON a.driver=f.id WHERE a.id_transaksi=?";
        $query=$this->db->query($sql,$invoice);
        return $query->getResult();
    }
    
    public function getTransaksi($awal,$akhir, $paid, $cabang=NULL){
        if ($awal==$akhir){
            if (empty($cabang)){
                $sql="SELECT b.id as memberid, a.id, b.memberid as qrmember, a.id_transaksi, b.nama, c.nama as cabang, a.is_proses, a.tanggal, a.carabayar, b.memberid as qrmember  
                        FROM transaksi a INNER JOIN member b ON a.id_member=b.id INNER JOIN cabang c ON a.cabang=c.id
                      WHERE date(tanggal) = ? AND is_paid=?  
                      ";
                $query=$this->db->query($sql,[$awal,$paid]);
            }else{
                $sql="SELECT b.id as memberid, a.id, b.memberid as qrmember,a.id_transaksi, b.nama, c.nama as cabang, a.is_proses, a.tanggal, a.carabayar  , b.memberid as qrmember
                        FROM transaksi a INNER JOIN member b ON a.id_member=b.id INNER JOIN cabang c ON a.cabang=c.id
                      WHERE date(tanggal) = ? AND is_paid=?  AND cabang=?
                      ";
                $query=$this->db->query($sql,[$awal,$paid,$cabang]);
            }
        }else{
            if (empty($cabang)){
                $sql="SELECT b.id as memberid, b.memberid as qrmember,a.id_transaksi, b.nama, c.nama as cabang, a.is_proses, a.tanggal, a.carabayar , b.memberid as qrmember 
                        FROM transaksi a INNER JOIN member b ON a.id_member=b.id INNER JOIN cabang c ON a.cabang=c.id
                      WHERE date(tanggal) BETWEEN ? AND ? AND is_paid=?  
                      ";
                $query=$this->db->query($sql,[$awal,$akhir,$paid]);
            }else{
                $sql="SELECT b.id as memberid, b.memberid as qrmember,a.id_transaksi, b.nama, c.nama as cabang, a.is_proses, a.tanggal, a.carabayar, b.memberid as qrmember  
                        FROM transaksi a INNER JOIN member b ON a.id_member=b.id INNER JOIN cabang c ON a.cabang=c.id
                      WHERE date(tanggal) BETWEEN ? AND ? AND is_paid=?  AND cabang=?
                      ";
                $query=$this->db->query($sql,[$awal,$akhir,$paid,$cabang]);
            }
        }
        return $query->getResult();
    }
    
    public function set_payment($invoice,$userid){
        $topup=$this->db->table("history_topup");
        $topup->where(["invoice"=>$invoice,"id_member"=>$userid]);
        $topup->set(["status"=>"success","update_at"=>date("Y-m-d H:i:s")]);
        $topup->update();
        return ($this->db->affectedRows()>0)?true:false;
    }
    
    public function cekpoin($invoice,$tipe){
        if ($tipe=="topup"){
            $sql="SELECT poin, status FROM history_topup WHERE invoice=?";
        }else{
            $sql="SELECT poin, is_paid, is_proses FROM transaksi WHERE id_transaksi=?";
        }
        $query= $this->db->query($sql,$invoice);
        return $query->getRow();
    }
    
    public function update_transaksi($invoice,$mdata){
        $transaksi=$this->db->table("transaksi");
        $transaksi->where("id_transaksi",$invoice);
        $transaksi->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }
    
    public function get_detailTransaksi($start_date,$end_date,$idmember){
        $sql    = "SELECT nominal as topup, tanggal FROM history_topup WHERE date(tanggal) BETWEEN ? AND ? AND id_member=? AND status='success'";
        $topup  = $this->db->query($sql,[$start_date,$end_date,$idmember])->getResult();
        
        $sql2   = "SELECT nama, sum(jumlah) as jumlah, mergesku FROM( 
                    SELECT GROUP_CONCAT(nama SEPARATOR ' ') as nama, jumlah,GROUP_CONCAT(sku SEPARATOR '-') as mergesku 
                    FROM transaksi a INNER JOIN transaksi_detail b ON a.id=b.id_transaksi 
                    WHERE a.id_member=? AND is_proses='complete' GROUP BY prd_group, a.id_transaksi 
                    )x GROUP BY mergesku;";
        $history= $this->db->query($sql2, $idmember)->getResult();
        return array(
                "topup"     => $topup,
                "history"   => $history
            );
    }
    
    //topup untuk pegawai
    public function resetplafon(){
        $now=date("Y-m-d H:i:s");
        $sql="INSERT INTO history_topup (id_member, invoice, tanggal, nominal, status, created_at)
                SELECT 
                    a.id AS id_member, 
                    LPAD(FLOOR(RAND() * 10000000000), 10, '0') AS invoice, 
                    '$now' AS tanggal, 
                    (a.plafon - IFNULL(SUM(x.saldo), 0)) AS nominal, 
                    'success' AS status,
                    '$now' as created_at 
                FROM 
                    member a 
                LEFT JOIN (
                    SELECT 
                        id_member, 
                        SUM(nominal) AS saldo 
                    FROM 
                        history_topup ht 
                    INNER JOIN 
                        member m ON ht.id_member = m.id
                    WHERE 
                        role = 'pegawai'
                        AND ht.status = 'success' 
                    GROUP BY ht.id_member
                
                    UNION ALL 
                
                    SELECT 
                        id_member, 
                        SUM(jumlah * harga) * -1 AS saldo 
                    FROM 
                        transaksi a 
                    INNER JOIN 
                        transaksi_detail b ON a.id = b.id_transaksi 
                    INNER JOIN 
                        member m ON a.id_member = m.id
                    WHERE 
                        role = 'pegawai'
                        AND is_paid = 'yes' 
                        AND carabayar = 'expatbalance' 
                    GROUP BY a.id_member
                
                    UNION ALL 
                
                    SELECT 
                        id_member, 
                        SUM(delivery_fee) * -1 AS saldo 
                    FROM 
                        transaksi t 
                    INNER JOIN 
                        member m ON t.id_member = m.id
                    WHERE 
                        role = 'pegawai'
                        AND is_paid = 'yes' 
                        AND carabayar = 'expatbalance' 
                    GROUP BY t.id_member
                ) x ON x.id_member = a.id 
                WHERE 
                    role = 'pegawai'
                GROUP BY a.id
                HAVING nominal > 0;";   
         $query  = $this->db->query($sql);
         return $this->db->error();
    }    
    
}