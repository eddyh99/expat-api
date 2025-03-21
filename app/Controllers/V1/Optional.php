<?php
namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_optional;

class Optional extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->optional   = new Mdl_optional();
	}

  
  	public function get_alloptional(){
        $response=[
            "messages"   => $this->optional->getall_optional()
        ];
        return $this->respond($response);
    }

    public function get_groupoptional(){
        $response=[
            "messages"   => $this->optional->getgroup_optional()
        ];
        return $this->respond($response);
    }

    public function getoptional_byid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->optional->get_optionalbyid($id)
        ];
        return $this->respond($response);

    }


    public function addoptional(){
        $validation = $this->validation;
        $validation->setRules([
                    'optional' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'optional is required',
						]
					],
                    'optiongroup' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Optiongroup is required',
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
            "optional"    => $data->optional,
            "optiongroup" => $data->optiongroup,
            "sku"               => $data->sku,
            "price"             => $data->price,
            "created_at"=> date("Y-m-d H:i:s")
        );

        $result     = $this->optional->add_optional($mdata);

        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[
	            "messages"    => "optional successfully added"
	        ];
	    return $this->respond($response);
        	
    }


    public function updateoptional(){
        $validation = $this->validation;
        $validation->setRules([
            'optional' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'optional is required',
                ]
            ],
            'optiongroup' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Optiongroup is required',
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
            "optional"    	=> $data->optional,
            "optiongroup"	=> $data->optiongroup,
            "sku"           => $data->sku,
            "price"         => $data->price,
            "update_at"		=> date("Y-m-d H:i:s")
        );

        $result     = $this->optional->update_optional($mdata,$id);
        if (!$result){
	        $error    = "Failed to update optional";
    	    return $this->fail($error);
        }

	    $response=[
	            "messages"    => "optional successfully updated"
	        ];
	    return $this->respond($response);
        	
    }

    public function deleteoptional(){
        $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $result     = $this->optional->delete_optional($id);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "optional successfully deleted"
        ];
        return $this->respondDeleted($response);
    }

}
