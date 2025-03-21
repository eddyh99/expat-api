<?php
namespace App\Controllers\V1\Mobile;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_member;
use App\Models\V1\Mdl_transaksi;
use App\Models\ValidateToken;
use App\Models\V1\Mdl_settings;

class Order extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->member    	= new Mdl_member();
        $this->transaksi    = new Mdl_transaksi();
        $this->auth    		= new ValidateToken();
        $this->setting      = new Mdl_settings();
	}
	
	public function add_address(){
      	$userid	= $this->getuser()->id;
		$data   = $this->request->getJSON();

		$filters = array(
            'title'   	=> FILTER_SANITIZE_STRING, 
            'alamat'   	=> FILTER_SANITIZE_STRING, 
            'phone'  	=> FILTER_SANITIZE_STRING,
            'is_primary'=> FILTER_SANITIZE_STRING,
            'latitude'  => FILTER_SANITIZE_STRING,
            'longitude' => FILTER_SANITIZE_STRING
        );

		$filtered = array();
        foreach($data as $key=>$value) {
             $filtered[$key] = filter_var($value, $filters[$key]);
        }
        
        $data=(object) $filtered;		
		
		$mdata=array(
			"id_member"	=> $userid,
			"title"     => $data->title,
			"alamat"	=> $data->alamat,
			"phone"		=> $data->phone,
			"is_primary"=> $data->is_primary,
			"latitude"  => $data->latitude,
			"longitude" => $data->longitude,
			"created_at"=> date("Y-m-d H:i:s")
		);

      	$result = $this->member->add_address($mdata);
	    $response=[
	            "messages"  => "Success",
	        ];
	   return $this->respond($response);
	}

    public function update_address(){		
      	$userid	= $this->getuser()->id;
	    $data   = $this->request->getJSON();
		$filters = array(
            'title'   	=> FILTER_SANITIZE_STRING, 
            'alamat'   	=> FILTER_SANITIZE_STRING, 
            'phone'  	=> FILTER_SANITIZE_STRING,
            'is_primary'=> FILTER_SANITIZE_STRING,
            'latitude'  => FILTER_SANITIZE_STRING,
            'longitude' => FILTER_SANITIZE_STRING
        );

		$filtered = array();
        foreach($data as $key=>$value) {
             $filtered[$key] = filter_var($value, $filters[$key]);
        }
        
        $data	= (object) $filtered;

      	$id     = $this->request->getGet('id', FILTER_SANITIZE_STRING);
      	$mdata=array(
			"id_member"	=> $userid,
			"title"     => $data->title,
			"alamat"	=> $data->alamat,
			"phone"		=> $data->phone,
			"is_primary"=> $data->is_primary,
			"latitude"  => $data->latitude,
			"longitude" => $data->longitude,
			"update_at" => date("Y-m-d H:i:s")
		);
		
      	$result = $this->member->update_address($mdata,$id);
	    $response=[
	            "messages"  => $result,
	        ];
	   return $this->respond($response);
	}
  
  	public function last_address(){
      	$userid	= $this->getuser()->id;
      	$data   = $this->request->getJSON();
      	foreach ($this->getSettings() as $dt){
      	    if ($dt->content=="delivery_fee"){
      	        $delivery_fee = $dt->value;
      	        break;
      	    }
      	}
      	
      	$response=[
            "messages"   => [
                    "address"  => $this->member->last_address($userid),
                    "delivery_fee" => $delivery_fee
                ]
        ];
        return $this->respond($response);
    }
  
  	public function list_address(){
      	$userid	= $this->getuser()->id;
      	foreach ($this->getSettings() as $dt){
      	    if ($dt->content=="delivery_fee"){
      	        $delivery_fee = $dt->value;
      	        break;
      	    }
      	}
      	$response=[
            "messages"   => [
                    "address"  => $this->member->get_address($userid),
                    "delivery_fee" => $delivery_fee
                ]
        ];
        return $this->respond($response);
    }
  
  	public function delete_address(){
      	$data   = $this->request->getJSON();
      	$id     = $this->request->getGet('id', FILTER_SANITIZE_STRING);
      
      	$result     = $this->member->delete_address($id);      	
      	$response=[
            "messages"    => "Address successfully deleted"
        ];
        return $this->respondDeleted($response);
    }
  
 	public function add_transaksi(){
      	$userid	= $this->getuser()->id;
		$data   = $this->request->getJSON();
        
        $delivery = $this->setting->get_settings('delivery_fee');
		$mdata=array(
			"id_transaksi"	=> "INV-".time(),
			"id_pengiriman"	=> $data->id_pengiriman,
          	"alamat"        => $data->alamat,
          	"phone"         => $data->phone,
          	"id_member"		=> $userid,
          	"carabayar"     => $data->carabayar,
          	"delivery_fee"  => (!empty($data->id_pengiriman)) ? $delivery->value:0,
          	"note"          => $data->note,
          	"is_paid"       => ($data->carabayar=="expatbalance") ? 'yes':'no',
          	"tanggal"		=> date("Y-m-d H:i:s"),
			"cabang"		=> $data->id_cabang,          	
			"created_at"=> date("Y-m-d H:i:s")
		);

      	$result = $this->transaksi->transaction_add($mdata,$data->items);
	    $response=[
	            "messages"  => $mdata["id_transaksi"],
	        ];
	   return $this->respond($response);
	}
  
  	public function list_transaksi(){
      	$userid	= $this->getuser()->id;
      	$response=[
            "messages"   => $this->transaksi->get_transaksi($userid)
        ];
        return $this->respond($response);
    }
  
 	public function detail_transaksibyid(){
      	$id     = $this->request->getGet('id', FILTER_SANITIZE_STRING);
      	$response=[
            "messages"   => $this->transaksi->transaksi_byid($id)
        ];
        return $this->respond($response);
    }
}