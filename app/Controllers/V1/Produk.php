<?php
namespace App\Controllers\V1;

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
    
    public function get_subkategori(){
        $response=[
            "messages"   => $this->produk->getsub_kategori()
        ];
        return $this->respond($response);
    }
	
    public function addProduk(){
        $validation = $this->validation;
        $validation->setRules([
                    'nama' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Nama is required',
						]
					],
                    'deskripsi' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'deskripsi is required',
                        ]
                    ],
                    'kategori' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'kategori is required',
                        ]
                    ],
                    'subkategori' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Sub kategori is required',
                        ]
                    ],
                    'is_favorite' => [
                            'rules'  => 'required',
                            'errors' => [
                                'required'      => 'Favorite is required',
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
                    ]
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $fileName   = "produk_" . time();

        if (!empty($data->image)){
            $postpath   = FCPATH."images/produk/";

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
            "deskripsi" => $data->deskripsi,
          	"kategori"	=> $data->kategori,
          	"subkategori"=> $data->subkategori,
            "favorite"  => $data->is_favorite,
            "sku"       => $data->sku,
            "price"     => $data->price,
            "satuan"    => $data->satuan,
            "additional"=> $data->additional,
            "optional"  => $data->optional,
            "cabang"    => $data->cabang,
            "picture"   => (!empty($data->image))  ? $fileName : "default.png", 
            "created_at"=> date("Y-m-d H:i:s")
        );
      

        $result     = $this->produk->add_produk($mdata);

        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[
	            "messages"    => "Produk successfully added"
	        ];
	    return $this->respond($response);
        	
    }


    public function updateProduk(){
        $validation = $this->validation;
        $validation->setRules([
            'nama' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Nama is required',
                ]
            ],
            'deskripsi' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Produk is required',
                ]
            ],
          	'kategori' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'kategori is required',
                        ]
                    ],
          	'subkategori' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'sub kategori is required',
                        ]
                    ],
            'is_favorite' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Favorite is required',
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
            ]
        ]);
    
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $id         = $this->request->getGet('id', FILTER_SANITIZE_STRING);

      	$produk     = $this->produk->get_produkbyid($id);
        if (!empty($produk->picture)){
            $url   = $produk->picture;
            $fileName = pathinfo(basename($url), PATHINFO_FILENAME);
            if ($fileName=="default"){
                $fileName   = "produk_" . time();
            }
        }
        

        if (!empty($data->image)){
            $postpath   = FCPATH."images/produk/";

            $ext        = explode("/",$data->image->mime);
            $source     = fopen($data->image->name,"rb");
            $destination= fopen($postpath.$fileName.".{$ext[1]}","w");        
            $content    = fread($source,filesize($data->image->name));
            $fileName   = $fileName.".{$ext[1]}";
            
            fwrite($destination,$content);
            fclose($source);
            fclose($destination);    
        }else{
            $fileName = basename($produk->picture);
        }

        $mdata=array(
            "nama"      => $data->nama,
            "deskripsi"	=> $data->deskripsi,
          	"kategori"	=> $data->kategori,
          	"subkategori"=> $data->subkategori,
            "favorite"   => $data->is_favorite,
            "picture"   =>  $fileName, 
            "sku"       => $data->sku,
            "price"     => $data->price,
            "satuan"    => $data->satuan,
            "additional"=> $data->additional,
            "optional"  => $data->optional,
            "cabang"    => $data->cabang,
            "update_at"=> date("Y-m-d H:i:s")
        );

        $result     = $this->produk->update_produk($mdata,$id);
        if (!$result){
	        $error    = "Failed to update Produk";
    	    return $this->fail($error);
        }

	    $response=[
	            "messages"    => "Produk successfully updated"
	        ];
	    return $this->respond($response);
        	
    }

    public function deleteProduk(){
        $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $result     = $this->produk->delete_produk($id);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "Produk successfully deleted"
        ];
        return $this->respondDeleted($response);
    }
  
  	public function addvarian(){      
	    $data       = $this->request->getJSON();
      	$id         = $this->request->getGet('id', FILTER_SANITIZE_STRING);

      	$mdata	= array();
      	foreach ($data->additional as $add){
          $temp["id_produk"]=$id;
          $temp["id_additional"]=$add;
          $temp["harga"]=$data->harga;
          $temp["created_at"]=date("Y-m-d H:i:s");
          foreach ($data->satuan as $sat){
	        $temp["id_satuan"]=$sat;
            foreach ($data->optional as $op){
              $temp["id_optional"]=$op;
              foreach ($data->cabang as $cab){
                $temp["id_cabang"]=$cab;
                array_push($mdata,$temp);
              }
            }
          }          
        }
      
        $result     = $this->produk->add_varian($mdata);

      	if (@$result->code!=0){
    	    return $this->fail($result->message);
	    }

	    $response=[
	            "messages"    => "Varian successfully added"
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
  	
  	public function updateVarian(){
      $id   	= $this->request->getGet('id', FILTER_SANITIZE_STRING);
      $harga   	= $this->request->getGet('harga', FILTER_SANITIZE_STRING);
      $mdata=array(
            "harga"     => $harga,
            "update_at"=> date("Y-m-d H:i:s")
        );

      $result     = $this->produk->update_varian($mdata,$id);
      if (!$result){
        $error    = "Failed to update Varian";
        return $this->fail($error);
      }

      $response=[
        "messages"    => "Varian successfully updated"
      ];
      return $this->respond($response);
    }
  
  	public function updateVarian_byproduk(){
      $id   	= $this->request->getGet('id', FILTER_SANITIZE_STRING);
      $harga   	= $this->request->getGet('harga', FILTER_SANITIZE_STRING);
      $mdata=array(
            "harga"     => $harga,
            "update_at"=> date("Y-m-d H:i:s")
        );

      $result     = $this->produk->update_varianbyproduk($mdata,$id);
      if (!$result){
        $error    = "Failed to update Varian";
        return $this->fail($error);
      }

      $response=[
        "messages"    => "Varian successfully updated"
      ];
      return $this->respond($response);      
    }
  
   	public function deleteVarian(){
        $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $result     = $this->produk->delete_varian($id);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "Varian successfully deleted"
        ];
        return $this->respondDeleted($response);
    }

}
