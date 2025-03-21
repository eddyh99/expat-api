<?php
namespace App\Controllers\V1\Mobile;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_produk;

class Produk extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->produk   = new Mdl_produk();
	}

    public function favproduk(){
        $response=[
            "messages"   => $this->produk->getall_fav()
        ];
        return $this->respond($response);
    }
  
  	public function get_allproduk(){
        $response=[
            "messages"   => $this->produk->getall_produk()
        ];
        return $this->respond($response);
    }

    public function getproduk_byid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->produk->get_produkbyid($id)
        ];
        return $this->respond($response);

    }
	
  	public function getproduk_bycabang(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->produk->getproduk_bycabang($id)
        ];
        return $this->respond($response);      
    }
  
  	public function getcabang_byprodukid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->produk->getcabang_byproduk($id)
        ];
        return $this->respond($response);            
    }

    public function get_varianbyid(){
      $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
      $response=[
            "messages"   => $this->produk->get_allvarian($id)
        ];
        return $this->respond($response);
    }
  
    public function get_detailbyid(){
      $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
      $response=[
            "messages"   => $this->produk->getdetail_byid($id)
        ];
        return $this->respond($response);
    }
}
