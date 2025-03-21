<?php
namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;


class Mdl_produk extends Model
{
    protected $server_tz = "Asia/Singapore";

	public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
        
    public function getall_fav() {
		$path = base_url()."/images/produk/";
        $sql = "SELECT id, concat('$path',picture) as picture, deskripsi, nama, price, kategori 
				FROM produk a WHERE a.is_deleted='no' AND a.favorite='yes'";
        $query = $this->db->query($sql)->getResult();
        return $query;
    }
  
   public function getall_produk() {
		$path = base_url()."/images/produk/";
        $sql = "SELECT id, concat('$path',picture) as picture, deskripsi, nama, favorite, kategori, sku, price 
				FROM produk WHERE is_deleted='no'";
        $query = $this->db->query($sql)->getResult();
        return $query;
    }
    
    public function getsub_kategori(){
        $sql="SELECT DISTINCT subkategori FROM produk WHERE subkategori IS NOT NULL";
        $query = $this->db->query($sql)->getResult();
        return $query;
    }
  

    public function get_produkbyid($id){
        $path   = base_url()."/images/produk/";
        $sql    = "SELECT
                    p.nama,
                    concat('$path',p.picture) as picture, 
                    p.deskripsi, p.favorite, p.kategori,p.price, p.sku,
                    p.satuan,p.additional, p.cabang, p.optional, p.subkategori
                FROM 
                    produk p
                WHERE p.id=? AND p.is_deleted='no'";
        $query = $this->db->query($sql,$id)->getRow();        
        return $query;
    }
  
  	public function getproduk_bycabang($id){
        $path = base_url()."/images/produk/";
        $sql = "SELECT p.id,p.nama, concat('$path',p.picture) as picture, p.deskripsi, 
                p.favorite, p.kategori, p.satuan,p.additional, p.sku, p.subkategori,
                p.cabang, p.optional,p.price FROM produk p JOIN cabang c ON FIND_IN_SET(c.id, p.cabang) 
                WHERE c.id =? AND p.is_deleted='no'";
        $query = $this->db->query($sql,$id)->getResult();        
        return $query;      
    }
	
  	public function getcabang_byproduk($id){
      	$path = base_url()."/images/cabang/";
        $sql = "SELECT DISTINCT id_cabang, nama, alamat, CONCAT('$path',picture) as picture 
        		FROM produk_detail a INNER JOIN cabang b ON a.id_cabang=b.id WHERE id_produk=? AND b.is_deleted='no'";
        $query = $this->db->query($sql,$id)->getResult();        
        return $query; 
    }
  
    public function add_produk($mdata){ 
        $produk   = $this->db->table("produk");
        if (!$produk->insert($mdata)){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to add Produk"
            ];
            return (object) $error;    
        }
    }

    public function update_produk($mdata, $id){
        $produk   = $this->db->table("produk");
        $produk->where("id",$id);
        $produk->update($mdata);
        return ($this->db->affectedRows()>0)?true:false;
    }

    public function delete_produk($id){
        $produk   = $this->db->table("produk");
        $produk->where("id",$id);
        $produk->set("is_deleted",'yes');
        if (!$produk->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete Produk"
            ];
            return (object) $error;    
        }
    }   

  	public function add_varian($mdata){ 
        $varian   = $this->db->table("produk_detail");
      	foreach ($mdata as $dt){
          $sql=$varian->set($dt)->getCompiledInsert()." ON duplicate key UPDATE is_deleted='no'";
          $this->db->query($sql);
        }
      	return (object) $this->db->error();
    }
  	  	
  	public function get_allvarian($id){
      $sql="SELECT 
                p.nama AS name,
                ps.satuan AS satuan,
                ps.price AS ukuran_harga,
                ps.id AS ukuran_id,
                po.optional AS optional,
                po.price AS optional_harga,
                po.id AS optional_id,
                pa.additional AS additional,
                pa.price AS additional_harga,
                pa.id AS additional_id,
                c.nama AS cabang,
                c.id AS cabang_id
            FROM 
                produk p
            JOIN 
                produk_satuan ps ON FIND_IN_SET(ps.id, p.satuan)
            JOIN 
                produk_optional po ON FIND_IN_SET(po.id, p.optional)
            JOIN 
                produk_additional pa ON FIND_IN_SET(pa.id, p.additional)
            JOIN 
                cabang c ON FIND_IN_SET(c.id, p.cabang)
            WHERE p.id=? AND p.is_deleted='no'
            ORDER BY 
                p.nama, ps.satuan, po.optional, pa.additional, c.nama;

            ";
      $query=$this->db->query($sql,$id);
      return $query->getResult();
    }
  
  	public function getdetail_byid($id){
       $sql="SELECT 
            	p.nama AS name,
                p.deskripsi as deskripsi,
                p.sku as base_sku,
                p.price as base_price,
            	ps.id AS satuan_id,
            	ps.sku AS satuan_sku,
            	ps.satuan AS satuan,
            	ps.price AS ukuran_harga,
            	po.optional AS optional,
            	po.price AS optional_harga,
            	po.id AS optional_id,
            	po.sku AS optional_sku,
            	pa.additional AS additional,
            	pa.price AS additional_harga,
            	pa.id AS additional_id,
            	pa.sku as additional_sku,
            	c.nama AS cabang,
            	c.id AS cabang_id
            FROM 
            	produk p
            JOIN 
            	produk_satuan ps ON FIND_IN_SET(ps.id, p.satuan)
            JOIN 
            	produk_optional po ON FIND_IN_SET(po.id, p.optional)
            JOIN 
            	produk_additional pa ON FIND_IN_SET(pa.id, p.additional)
            JOIN 
            	cabang c ON FIND_IN_SET(c.id, p.cabang)
            WHERE p.id={$id} AND p.is_deleted='no'
            ORDER BY 
            p.nama, ps.satuan, po.optional, pa.additional, c.nama;
            ";
      $query=$this->db->query($sql,$id);
      return $query->getResult();
    }
  	
  	public function update_varian($mdata,$id){
      $varian   = $this->db->table("produk_detail");
      $varian->where("id",$id);
      $varian->update($mdata);
      return ($this->db->affectedRows()>0)?true:false;
    }
  
  	public function update_varianbyproduk($mdata,$id){
      $varian   = $this->db->table("produk_detail");
      $varian->where("id_produk",$id);
      $varian->update($mdata);
      return ($this->db->affectedRows()>0)?true:false;      
    }
  
  	public function delete_varian($id){
        $varian   = $this->db->table("produk_detail");
        $varian->where("id",$id);
        $varian->set("is_deleted",'yes');
        if (!$varian->update()){
            $error=[
                "code"       => "5051",
                "error"      => "04",
                "message"    => "Failed to delete Varian"
            ];
            return (object) $error;    
        }
    } 
  
  	
   
}