<?php
namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_additional;

class additional extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->additional   = new Mdl_additional();
	}

  
  	public function get_alladditional(){
        $response=[
            "messages"   => $this->additional->getall_additional()
        ];
        return $this->respond($response);
    }

    public function get_groupadditional(){
        $response=[
            "messages"   => $this->additional->getgroup_additional()
        ];
        return $this->respond($response);
    }

    public function getadditional_byid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->additional->get_additionalbyid($id)
        ];
        return $this->respond($response);

    }


    public function addadditional(){
        $validation = $this->validation;
        $validation->setRules([
                    'additional' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'additional is required',
						]
					],
                    'additional_group' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Additional Group is required',
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
            "additional"        => $data->additional,
            "additional_group"  => $data->additional_group,
            "sku"               => $data->sku,
            "price"             => $data->price,
            "created_at"        => date("Y-m-d H:i:s")
        );

        $result     = $this->additional->add_additional($mdata);

        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[
	            "messages"    => "additional successfully added"
	        ];
	    return $this->respond($response);
        	
    }


    public function updateadditional(){
        $validation = $this->validation;
        $validation->setRules([
            'additional' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'additional is required',
                ]
            ],
            'additional_group' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Additional Group is required',
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
            "additional"    	=> $data->additional,
            "additional_group"	=> $data->additional_group,
            "sku"               => $data->sku,
            "price"             => $data->price,
            "update_at"		=> date("Y-m-d H:i:s")
        );

        $result     = $this->additional->update_additional($mdata,$id);
        if (!$result){
	        $error    = "Failed to update additional";
    	    return $this->fail($error);
        }

	    $response=[
	            "messages"    => "additional successfully updated"
	        ];
	    return $this->respond($response);
        	
    }

    public function deleteadditional(){
        $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $result     = $this->additional->delete_additional($id);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "additional successfully deleted"
        ];
        return $this->respondDeleted($response);
    }

}
