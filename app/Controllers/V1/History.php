<?php
namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_cabang;
use App\Models\V1\Mdl_transaksi;

class History extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->cabang       = new Mdl_cabang();
        $this->transaksi    = new Mdl_transaksi();
	}

    public function gethistoryTopup(){
        $start_date      = $this->request->getGet('start_date', FILTER_SANITIZE_STRING);
		$end_date       = $this->request->getGet('end_date', FILTER_SANITIZE_STRING);
	    $response=[
	            "messages"  => $this->transaksi->history_topup($start_date,$end_date),
	        ];
	   return $this->respond($response);
    }
    
    public function getTransaksi(){
        $start_date     = $this->request->getGet('start_date', FILTER_SANITIZE_STRING);
		$end_date       = $this->request->getGet('end_date', FILTER_SANITIZE_STRING);
        $is_paid        = $this->request->getGet('is_paid', FILTER_SANITIZE_STRING);
		$cabang         = $this->request->getGet('cabang', FILTER_SANITIZE_STRING);
	    $response=[
	            "messages"  => $this->transaksi->getTransaksi($start_date,$end_date,$is_paid,$cabang),
	        ];
	    return $this->respond($response);
    }

    public function getdetail_history(){
        $idmember       = $this->request->getGet('idmember', FILTER_SANITIZE_STRING);
        $start_date     = $this->request->getGet('start_date', FILTER_SANITIZE_STRING);
		$end_date       = $this->request->getGet('end_date', FILTER_SANITIZE_STRING);
        
	    $response=[
	            "messages"  => $this->transaksi->get_detailTransaksi($start_date,$end_date,$idmember),
	        ];
	    return $this->respond($response);
    }
    
    public function setPaymentStatus(){
        $validation = $this->validation;
        $validation->setRules([
                    'invoice' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Invoice is required',
						]
					],
                    'memberid' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Member ID is required',
                        ]
                    ],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $response=[
	            "messages"  => $this->transaksi->set_payment($data->invoice,$data->memberid),
	        ];
	    return $this->respond($response);
    }
    
    public function detailTransaksi(){ 
        $invoice     = $this->request->getGet('invoice', FILTER_SANITIZE_STRING);
      	$response=[
            "messages"   => $this->transaksi->transaksi_adminbyid($invoice)
        ];
        return $this->respond($response);
    }
    
    public function process_order(){
        $validation = $this->validation;
        $validation->setRules([
                    'invoice' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Invoice is required',
						]
					],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
	    $mdata = array(
	            "driver"    => empty($data->id_driver) ? null:$data->id_driver,
	            "is_proses" => empty($data->id_driver) ? "complete" : 'delivery',
	            "update_at" => date("Y-m-d H:i:s")
	        );

        $response=[
	            "messages"  => $this->transaksi->update_transaksi($data->invoice,$mdata),
	        ];
	    return $this->respond($response);
    }
}
