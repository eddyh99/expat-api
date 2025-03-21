<?php
namespace App\Controllers\V1\Mobile;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_transaksi;
use App\Models\V1\Mdl_member;
use App\Models\ValidateToken;

class Member extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->transaksi    = new Mdl_transaksi();
        $this->member       = new Mdl_member();
        $this->auth       	= new ValidateToken();
	}
	
	//mobile function
    public function get_userdetail(){		
        $userid=$this->getuser()->id;
		
        $response=[
            "messages"   => $this->transaksi->get_all($userid)
        ];
        return $this->respond($response);
    }
    
    public function update_pin(){
        $userid=$this->getuser()->id;
        $validation = $this->validation;
        $validation->setRules([
                    'pin' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'PIN is required',
						]
					],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data   = $this->request->getJSON();
	    $pin    = htmlspecialchars($data->pin);
	    $this->member->update_pin($pin, $userid);
	    $response=[
                "status"    => 200,
                "error"     => null,
                "messages"  => "Your PIN has been successfully created"
            ];
		return $this->respond($response,200);
    }

    public function check_pin(){
        $userid=$this->getuser()->id;
        $validation = $this->validation;
        $validation->setRules([
                    'pin' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'PIN is required',
						]
					],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data   = $this->request->getJSON();
	    $pin    = htmlspecialchars($data->pin);
	    $result=$this->member->check_pin($pin, $userid);
	    
	    if (@$result->code==5051){
            $response=[
                "status"    => 400,
                "error"     => 03,
                "messages"  => [
                    "error" => "Invalid PIN"
                    ]
            ];
            return $this->respond($response,400);
	    }

	    $response=[
                "status"    => 200,
                "error"     => null,
                "messages"  => "success"
            ];
		return $this->respond($response,200);
    }
    
    public function updateMember(){
        $userid=$this->getuser()->id;
        
        $validation = $this->validation;
        $validation->setRules([
                'nama' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Nama is required',
                    ]
                ],
        ]);

        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

        $data   = $this->request->getJSON();
        
        $member = $this->member->getby_id($userid);
        if (!empty($member->picture)){
            $fileName   = pathinfo($member->picture, PATHINFO_FILENAME);
        }else{
            $fileName   = "user_" . time();
        }

        if (!empty($data->media)){
            $path   = FCPATH."/images/user/";
    
            $fileName   = $fileName.".jpg";
            $ifp        = fopen( $path.$fileName, 'wb+' ); 
            $gbr        = $data->media;
            fwrite( $ifp, base64_decode($gbr));
            fclose( $ifp ); 
        }else{
            $fileName = $member->picture;
        }
        
        if (empty($data->passwd) && empty($data->pin)){
            $mdata=array(
                "nama"      => $data->nama,
                "gender"    => $data->gender,
                "phone"     => $data->phone,
                "country"   => $data->country,
                "dob"       => $data->dob,
                "picture"   => $fileName,
                "update_at"=> date("Y-m-d H:i:s")
            );
        }elseif (!empty($data->passwd)){
            $mdata=array(
                "nama"      => $data->nama,
                "gender"    => $data->gender,
                "phone"     => $data->phone,
                "country"   => $data->country,
                "dob"       => $data->dob,
                "passwd"    => $data->passwd,
                "picture"   => $fileName,
                "update_at" => date("Y-m-d H:i:s")
            );
        }elseif (!empty($data->pin)){
            $mdata=array(
                "nama"      => $data->nama,
                "gender"    => $data->gender,
                "phone"     => $data->phone,
                "country"   => $data->country,
                "dob"       => $data->dob,
                "pin"       => $data->pin,
                "picture"   => $fileName,
                "update_at"=> date("Y-m-d H:i:s")
            );
        }else{
            $mdata=array(
                "nama"      => $data->nama,
                "gender"    => $data->gender,
                "phone"     => $data->phone,
                "country"   => $data->country,
                "dob"       => $data->dob,
                "pin"       => $data->pin,
                "passwd"    => $data->passwd,
                "picture"   => $fileName,
                "update_at" => date("Y-m-d H:i:s")
            );

        }

        $result     = $this->member->update_member($mdata,$userid);
        if (!$result){
    	    $response=[
    	        "status"    => 400,
                "error"     => 03,
                "messages"  => [
                    "error" => "Failed to updated"
                    ]
    	    ];
    	    return $this->respond($response,400);
        }else{
    	    $response=[
    	        "status"    => 200,
                "error"     => null,
	            "messages"    => "Member successfully updated"
    	    ];
    	    return $this->respond($response,200);
        }
        	
    }
    
}
