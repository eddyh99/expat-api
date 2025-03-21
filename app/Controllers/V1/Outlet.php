<?php
namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_cabang;

class Outlet extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->cabang    = new Mdl_cabang();
	}

    public function get_allcabang(){
        $response=[
            "messages"   => $this->cabang->getall_cabang()
        ];
        return $this->respond($response);
    }

    public function getcabang_byid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->cabang->get_cabangbyid($id)
        ];
        return $this->respond($response);

    }


    public function addCabang(){
        $validation = $this->validation;
        $validation->setRules([
                    'nama' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Nama is required',
						]
					],
					'alamat' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Alamat is required',
						]
                    ],
					'lat' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Latitude is required',
						]
                    ],
					'long' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Longitude is required',
						]
                    ],
                    'opening' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Opening is required',
                        ]
                    ],
                    'kontak' => [
                            'rules'  => 'required',
                            'errors' => [
                                'required'      => 'Kontak is required',
                            ]
                    ],
                    'provinsi' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Provinsi is required',
                        ]
                    ],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $fileName   = "expats_" . time();

        if (!empty($data->image)){
            $postpath   = FCPATH."images/cabang/";

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
            "nama"      => $data->nama,
            "alamat"    => $data->alamat,
            "opening"   => $data->opening,
            "kontak"    => $data->kontak,
            "provinsi"  => $data->provinsi,
            "latitude"  => $data->lat,
            "longitude" => $data->long,
            "picture"   => (!empty($data->image))  ? $fileName : "default.png", 
            "created_at"=> date("Y-m-d H:i:s")
        );

        $result     = $this->cabang->add_cabang($mdata,$data->produk);

        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }
        
	    $response=[
	            "messages"    => "Cabang successfully added"
	        ];
	    return $this->respond($response);
        	
    }


    public function updateCabang(){
        $validation = $this->validation;
        $validation->setRules([
            'nama' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Nama is required',
                ]
            ],
            'alamat' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Alamat is required',
                ]
            ],
			'lat' => [
				'rules'  => 'required',
				'errors' => [
					'required'      => 'Latitude is required',
				]
            ],
			'long' => [
				'rules'  => 'required',
				'errors' => [
					'required'      => 'Longitude is required',
				]
            ],
            'opening' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Opening is required',
                ]
            ],
            'kontak' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Kontak is required',
                    ]
            ],
            'provinsi' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Provinsi is required',
                ]
            ],
        ]);
    
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $id         = $this->request->getGet('id', FILTER_SANITIZE_STRING);

        $cabang     = $this->cabang->get_cabangbyid($id);
        if (!empty($cabang->picture)){
            $url        = $cabang->picture;
            $fileName   = pathinfo(basename($url), PATHINFO_FILENAME);
            if ($fileName=="default"){
                $fileName   = "expats_" . time();
            }            
        }

        if (!empty($data->image)){
            $postpath   = FCPATH."images/cabang/";
    
            $ext        = explode("/",$data->image->mime);
            $source     = fopen($data->image->name,"rb");
            $destination= fopen($postpath.$fileName.".{$ext[1]}","w");        
            $content    = fread($source,filesize($data->image->name));
            $fileName   = $fileName.".{$ext[1]}";

            fwrite($destination,$content);
            fclose($source);
            fclose($destination);    
        }else{
            $fileName = basename($cabang->picture);
        }

        $mdata=array(
            "nama"      => $data->nama,
            "alamat"    => $data->alamat,
            "opening"   => $data->opening,
            "kontak"    => $data->kontak,
            "latitude"  => $data->lat,
            "longitude" => $data->long,
            "provinsi"  => $data->provinsi,
            "picture"   => $fileName, 
            "update_at"=> date("Y-m-d H:i:s")
        );

        $result     = $this->cabang->update_cabang($mdata,$id);
        if (!$result){
	        $error    = $result;//"Failed to update Cabang";
    	    return $this->fail($error);
        }

	    $response=[
	            "messages"    => "Cabang successfully updated"
	        ];
	    return $this->respond($response);
        	
    }

    public function deleteCabang(){
        $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $result     = $this->cabang->delete_cabang($id);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "Cabang successfully deleted"
        ];
        return $this->respondDeleted($response);
    }
}
