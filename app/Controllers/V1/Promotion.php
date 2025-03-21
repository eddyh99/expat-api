<?php
namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_cabang;
use App\Models\V1\Mdl_promosi;

class Promotion extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->promosi   = new Mdl_promosi();
	}


    public function getpromo_byid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->promosi->get_promobyid($id)
        ];
        return $this->respond($response);
    }

    
    public function addPromo(){
        $validation = $this->validation;
        $validation->setRules([
                    'deskripsi' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Description is required',
						]
					],
					'tipe' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Tipe Promotional is required',
						]
                    ],
                    'start_date' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Start date promotion is required',
                        ]
                    ],
                    'end_date' => [
                            'rules'  => 'required',
                            'errors' => [
                                'required'  => 'End date promotion is required',
                            ]
                    ],
                    'milestone' => [
                            'rules'  => 'required',
                            'errors' => [
                                'required'  => 'Milestone is required',
                            ]
                    ],
                    'minimum' => [
                            'rules'  => 'required',
                            'errors' => [
                                'required'  => 'Minimum Purchase is required',
                            ]
                    ],
                    'discount_type' => [
                            'rules'  => 'required',
                            'errors' => [
                                'required'  => 'Discount type is required',
                            ]
                    ]
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $postpath   = FCPATH."images/promosi/";
        $fileName   = "promo_" . time();

        if (!empty($data->image)){

            $ext    = explode("/",$data->image->mime);
            $source=fopen($data->image->name,"rb");
            $destination=fopen($postpath.$fileName.".{$ext[1]}","w");   
            $content=fread($source,filesize($data->image->name));
            $fileName   = $fileName.".{$ext[1]}";
    
            fwrite($destination,$content);
            fclose($source);
            fclose($destination);    
        }
	    
        $mdata=array(
            "deskripsi"     => $data->deskripsi,
            "tipe"          => $data->tipe,
            "tanggal"       => $data->start_date,
            "end_date"      => $data->end_date,
            "milestone"     => $data->milestone,
            "minimum"        => $data->minimum,
            "discount_type"  => $data->discount_type,
            "potongan"      => $data->disc_amount,
            "picture"       => (!empty($data->image))  ? $fileName : "default.png", 
            "created_at"    => date("Y-m-d H:i:s")
        );

        $result     = $this->promosi->add_promo($mdata);

        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[
	            "messages"    => "Promotion successfully added"
	        ];
	    return $this->respond($response);
        	
    }


    public function updatePromo(){
        $validation = $this->validation;
        $validation->setRules([
            'deskripsi' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Description is required',
                ]
            ],
            'tipe' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Tipe Promotional is required',
                ]
            ],
            'start_date' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Start date promotion is required',
                ]
            ],
            'end_date' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => 'End date promotion is required',
                    ]
            ],
            'milestone' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => 'Milestone is required',
                    ]
            ],
            'minimum' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => 'Minimum Purchase is required',
                    ]
            ],
            'discount_type' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'  => 'Discount type is required',
                    ]
            ]
        ]);
    
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $id         = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $promo      = $this->promosi->get_promobyid($id);
        $fileName   = '';
        if (!empty($promo->picture)){
            $url   = $promo->picture;
            $fileName = pathinfo(basename($url), PATHINFO_FILENAME);
            if ($fileName=="default"){
                $fileName   = "promo_" . time();
            }

        }
        
        if (!empty($data->image)){ 
            $postpath   = FCPATH."images/promosi/";
    
            $ext        = explode("/",$data->image->mime);
            $source     = fopen($data->image->name,"rb");
            $destination= fopen($postpath.$fileName.".{$ext[1]}","w");        
            $content    = fread($source,filesize($data->image->name));
            $fileName   = $fileName.".{$ext[1]}";

            fwrite($destination,$content);
            fclose($source);
            fclose($destination);    
        }else{
            $fileName = basename($promo->picture);
        }

        $mdata=array(
            "deskripsi"     => $data->deskripsi,
            "tipe"          => $data->tipe,
            "tanggal"       => $data->start_date,
            "end_date"      => $data->end_date,
            "picture"       => $fileName, 
            "milestone"     => $data->milestone,
            "minimum"        => $data->minimum,
            "discount_type"  => $data->discount_type,
            "potongan"      => $data->disc_amount,
            "update_at"    => date("Y-m-d H:i:s")
        );

        $result     = $this->promosi->update_promo($mdata,$id);
        if (!$result){
	        $error    = "Failed to update Promotion";
    	    return $this->fail($error);
        }

	    $response=[
	            "messages"    => "Promotion successfully updated"
	        ];
	    return $this->respond($response);
        	
    }

    public function deletePromo(){
        $id         = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $result     = $this->promosi->delete_promo($id);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "Promotion successfully deleted"
        ];
        return $this->respondDeleted($response);
    }
}
