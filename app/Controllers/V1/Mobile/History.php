<?php
namespace App\Controllers\V1\Mobile;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_member;
use App\Models\V1\Mdl_transaksi;


class History extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->member    	= new Mdl_member();
        $this->transaksi    = new Mdl_transaksi();
	    $this->userid       =$this->getuser()->id;
	}
	
	public function history_topup(){
		$start_date      = $this->request->getGet('start_date', FILTER_SANITIZE_STRING);
		$end_date       = $this->request->getGet('end_date', FILTER_SANITIZE_STRING);
	    $response=[
	            "messages"  => $this->transaksi->history_topup($start_date,$end_date,$this->userid),
	        ];
	   return $this->respond($response);
	}

	public function history_poin(){
		$start_date      = $this->request->getGet('start_date', FILTER_SANITIZE_STRING);
		$end_date       = $this->request->getGet('end_date', FILTER_SANITIZE_STRING);

	    $response=[
	            "messages"  => $this->transaksi->history_poin($start_date,$end_date,$this->userid),
	        ];
	   return $this->respond($response);
	}

    public function history_transaksi(){
		$start_date      = $this->request->getGet('start_date', FILTER_SANITIZE_STRING);
		$end_date       = $this->request->getGet('end_date', FILTER_SANITIZE_STRING);

	    $response=[
	            "messages"  => $this->transaksi->history_transaksi($start_date,$end_date,$this->userid),
	        ];
	   return $this->respond($response);
	}
	
	public function history_byinvoice(){
		$invoice      = $this->request->getGet('invoice', FILTER_SANITIZE_STRING);
        
	    $response=[
	            "messages"  => $this->transaksi->transaksi_adminbyid($invoice),
	        ];
	   return $this->respond($response);
	}

}