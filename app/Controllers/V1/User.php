<?php
namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_user;

class User extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->user    = new Mdl_user();
	}

    public function get_alluser(){
        $response=[
            "code"      => "200",
            "error"     => NULL,
            "message"   => $this->user->getall_user()
        ];
        return $this->respond($response);
	}
    
    public function addUser(){
        $validation = $this->validation;
        $validation->setRules([
                    'username' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Username is required',
						]
					],
					'passwd' => [
						'rules'  => 'required|min_length[40]',
						'errors' => [
							'required'      => 'Password is required',
							'min_length'    => 'Password is encrypted'
						]
                    ],
                    'nama' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Nama is required',
                        ]
                    ],
                    'role' => [
                            'rules'  => 'required',
                            'errors' => [
                                'required'      => 'Role is required',
                            ]
                    ]
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $filters = array(
            'username'  => FILTER_SANITIZE_STRING, 
            'passwd'    => FILTER_UNSAFE_RAW, 
            'nama'      => FILTER_SANITIZE_STRING, 
            'role'      => FILTER_SANITIZE_STRING, 
        );

	    $filtered = array();
        foreach($data as $key=>$value) {
             $filtered[$key] = filter_var($value, $filters[$key]);
        }
        
        $data=(object) $filtered;
	    
        $mdata=array(
            "username"  => $data->username,
            "nama"      => $data->nama,
            "passwd"    => $data->passwd,
            "role"      => $data->role,
            "created_at"=> date("Y-m-d H:i:s")
        );

        $result     = $this->user->add_users($mdata);

        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[	            
	            "messages"    => "User successfully added"
	        ];
	    return $this->respond($response);
        	
    }

    public function get_byusername(){
        $username      = $this->request->getGet('username', FILTER_SANITIZE_STRING);

        $response=[
            "messages"    => $this->user->getby_username($username)
        ];
        return $this->respond($response);
    }

    public function updateUser(){
        $validation = $this->validation;
        $validation->setRules([
            'username' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Username is required',
                ]
            ],
            'nama' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Nama is required',
                ]
            ],
    
            'role' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Role is required',
                    ]
            ]
        ]);
    
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $filters = array(
            'username'  => FILTER_SANITIZE_STRING, 
            'nama'      => FILTER_SANITIZE_STRING, 
            'role'      => FILTER_SANITIZE_STRING, 
            'is_driver' => FILTER_SANITIZE_STRING, 
        );

	    $filtered = array();
        foreach($data as $key=>$value) {
            if ($key!="passwd"){
                $filtered[$key] = filter_var($value, $filters[$key]);
            }else{
                $filtered[$key] = $value;
            }
        }
        
        $data=(object) $filtered;
	    
        if (empty($data->passwd)){
            $mdata=array(
                "nama"      => $data->nama,
                "role"      => $data->role,
                "update_at" => date("Y-m-d H:i:s")
            );    
        }else{
            $mdata=array(
                "nama"      => $data->nama,
                "passwd"    => $data->passwd,
                "role"      => $data->role,
                "update_at" => date("Y-m-d H:i:s")
            );
        }

        $result     = $this->user->update_user($mdata,$data->username);
        if (!$result){
	        $error    = "Failed to update user";
    	    return $this->fail($error);
        }

	    $response=[
	            "messages"    => "User successfully updated"
	        ];
	    return $this->respond($response);
        	
    }

    public function deleteUser(){
        $username   = $this->request->getGet('username', FILTER_SANITIZE_STRING);
        $result     = $this->user->delete_user($username);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "User successfully deleted"
        ];
        return $this->respondDeleted($response);
    }
    
    public function getall_staff(){
        $response=[
            "messages"   => $this->user->get_allStaff()
        ];
        return $this->respond($response);
    }
    
    public function addStaff(){
        $validation = $this->validation;
        $validation->setRules([
            'id_staff' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Username is required',
                ]
            ],
            'cabangid' => [
                'rules'  => 'required',
                'errors' => [
                    'required'      => 'Cabang ID is required',
                ]
            ],
        ]);
    
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $filters = array(
            'id_staff'  => FILTER_SANITIZE_STRING, 
            'cabangid'  => FILTER_SANITIZE_STRING, 
        );

	    $filtered = array();
        foreach($data as $key=>$value) {
			$filtered[$key] = filter_var($value, $filters[$key]);
        }
        
        $data=(object) $filtered;
		$cek = $this->user->cek_cabang($data->id_staff);
		if (@$cek->code==5051){
    	    return $this->fail($cek->message);
	    }
		
        $mdata=array(
            "member_id"     => $data->id_staff,
            "cabang_id"     => $data->cabangid,
            "is_deleted"    => 'no',
            "created_at"    => date("Y-m-d H:i:s")
        );

        $result     = $this->user->addStaff($mdata);
        if (@$result->code==5051){	       
    	    return $this->fail($result->message);
        }

	    $response=[
	            "messages"    => "Staff is sucessfully added"
	        ];
	    return $this->respond($response);        

    }

    public function deleteStaff(){
        $id_staff   = $this->request->getGet('id_staff', FILTER_SANITIZE_STRING);
        $cabangid   = $this->request->getGet('cabangid', FILTER_SANITIZE_STRING);
        $where  = array(
            "member_id"  => $id_staff,
            "cabang_id"  => $cabangid
        );
        $result     = $this->user->delete_Staff($where);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "Staff successfully deleted"
        ];
        return $this->respondDeleted($response);
    }
}
