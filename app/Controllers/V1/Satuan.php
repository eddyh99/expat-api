<?php
namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_satuan;

class Satuan extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->satuan   = new Mdl_satuan();
	}

  
  	public function get_allsatuan(){
        $response=[
            "messages"   => $this->satuan->getall_satuan()
        ];
        return $this->respond($response);
    }

    public function get_groupsatuan(){
        $response=[
            "messages"   => $this->satuan->getgroup_satuan()
        ];
        return $this->respond($response);
    }

    public function getsatuan_byid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->satuan->get_satuanbyid($id)
        ];
        return $this->respond($response);

    }


    public function addSatuan(){
        $validation = $this->validation;
        $validation->setRules([
                    'satuan' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Satuan is required',
						]
					],
                    'groupname' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'deskripsi is required',
                        ]
                    ],
                     'sku' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'SKU is required',
                        ]
                    ],
                    'price' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Price is required',
                        ]
                    ],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
	    
        $mdata=array(
            "satuan"    => $data->satuan,
            "groupname" => $data->groupname,
            "sku"       => $data->sku,
            "price"     => $data->price,
            "created_at"=> date("Y-m-d H:i:s")
        );

        $result     = $this->satuan->add_satuan($mdata);

        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[
	            "messages"    => "Satuan successfully added"
	        ];
	    return $this->respond($response);
        	
    }


    public function updateSatuan(){
        $validation = $this->validation;
        $validation->setRules([
            'satuan' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Satuan is required',
                ]
            ],
            'groupname' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Groupname is required',
                ]
            ],
             'sku' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'SKU is required',
                ]
            ],
            'price' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Price is required',
                ]
            ],
        ]);
    
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $id         = $this->request->getGet('id', FILTER_SANITIZE_STRING);

        $mdata=array(
            "satuan"    => $data->satuan,
            "groupname"	=> $data->groupname,
            "sku"       => $data->sku,
            "price"     => $data->price,
            "update_at"	=> date("Y-m-d H:i:s")
        );

        $result     = $this->satuan->update_satuan($mdata,$id);
        if (!$result){
	        $error    = "Failed to update Satuan";
    	    return $this->fail($error);
        }

	    $response=[
	            "messages"    => "Satuan successfully updated"
	        ];
	    return $this->respond($response);
        	
    }

    public function deleteSatuan(){
        $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $result     = $this->satuan->delete_satuan($id);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "Satuan successfully deleted"
        ];
        return $this->respondDeleted($response);
    }

}
