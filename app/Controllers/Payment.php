<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_transaksi;
use App\Models\V1\Mdl_member;
use App\Models\ValidateToken;
use App\Models\V1\Mdl_settings;

class Payment extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->transaksi    = new Mdl_transaksi();
        $this->member    	= new Mdl_member();
        $this->auth    		= new ValidateToken();
        $this->settings     = new Mdl_settings();
	}
	
	public function create_topup(){
		$data           = $this->request->getJSON();

		$filters = array(
            'token'     => FILTER_SANITIZE_STRING, 
            'invoice'   => FILTER_SANITIZE_STRING, 
            'amount'  	=> FILTER_VALIDATE_INT,
        );

		$filtered = array();
        foreach($data as $key=>$value) {
             $filtered[$key] = filter_var($value, $filters[$key]);
        }
        
        $data=(object) $filtered;		
		
		$member = $this->auth->checkAPIkey($data->token);
		if (empty($member)){
			$this->fail("User not found, please relogin your account");
		}
		
		$base_poin=$this->settings->get_settings('poin_calculate')->value;
		
		$mdata=array(
			"id_member"	=> $member->id,
			"invoice"	=> $data->invoice,
			"tanggal"	=> date("Y-m-d H:i:s"),
			"nominal"	=> $data->amount,
			"poin"      => floor($data->amount/$base_poin),
			"created_at"=> date("Y-m-d H:i:s")
		);
	    $result = $this->transaksi->set_topup($mdata);
	    $response=[
	            "messages"  => "Success",
	        ];
	   return $this->respond($response);
	}

    public function transaction_status(){		
        
	    $data           = $this->request->getJSON();
	    $tipe="topup";
	    if (strtolower(substr($data->invoice,0,3))=="inv"){
	        $tipe="transaksi";
	    }
	    
	    $result = $this->transaksi->update_status($data->invoice,$tipe);
	    $response=[
	            "messages"  => $result,
	        ];
	   return $this->respond($response);
	}
	
	public function checkpoin(){
	    $invoice    = $this->request->getGet('invoice', FILTER_SANITIZE_STRING);
	    $tipe       = $this->request->getGet('tipe', FILTER_SANITIZE_STRING);

	    $response=[
	            "messages"  => $this->transaksi->cekpoin($invoice,$tipe),
	        ];
	   return $this->respond($response);
	}
}