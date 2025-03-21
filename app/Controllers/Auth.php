<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_member;
use App\Models\V1\Mdl_user;
use App\Models\ValidateToken;
use App\Models\V1\Mdl_transaksi;


class Auth extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->member   = new Mdl_member();
        $this->user    	= new Mdl_user();
        $this->valid    = new ValidateToken();
        $this->transaksi    = new Mdl_transaksi();
	}

    public function register(){
	    $validation = $this->validation;
        $validation->setRules([
					'email' => [
						'rules'  => 'required|valid_email',
						'errors' => [
							'required'      => 'Email is required',
							'valid_email'   => 'Invalid Email format'
						]
					],
					'passwd' => [
					    'rules'  => 'required|min_length[8]',
					    'errors' =>  [
					        'required'      => 'Password is required',
					        'min_length'    => 'Min length password is 8 character'
					    ]
					],
					'is_google' => [
					    'rules'  => 'required',
					    'errors' =>  [
					        'required'      => 'is Google is required',
					    ]
					],

            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }
        
	    $data           = $this->request->getJSON();

	/*	$filters = array(
            'email'     => FILTER_VALIDATE_EMAIL, 
            'passwd'  	=> FILTER_UNSAFE_RAW, 
        );

		$filtered = array();
        foreach($data as $key=>$value) {
             $filtered[$key] = filter_var($value, $filters[$key]);
        }
        
        $data=(object) $filtered;*/

        $mdata = array(
				"memberid"	=> uniqid(),
    	        "email"     => $data->email,
    	        "passwd"    => $data->passwd,
    	        "status"    => ($data->is_google=="yes") ? "active":"new",
    	        "is_google" => $data->is_google,
    	        "request_time"  => date("Y-m-d H:i:s"),
				"created_at"=> date("Y-m-d H:i:s")
    	);
	    
	    $result = $this->member->add($mdata);
	    if (@$result->code==1062){
            $response=[
                "status"    => 400,
                "error"     => null,
                "messages"  => [
                    "error" => "Email(s) have already been used, please try another"
                    ]
            ];
    	   return $this->respond($response,400);
	    }
	    $response=[
	            "status"    => 200,
	            "error"     => null,
	            "messages"  => $result,
	        ];
	   return $this->respond($response,200);
	}
	
	public function cekToken(){
	    $token  = $this->request->getGet('token', FILTER_SANITIZE_STRING);
	    $member = $this->member->getby_token($token);
        if (@$member->code==5051){
            $response=[
                "status"    => 400,
                "error"     => 02,
                "messages"  => [
                    "error" => "Invalid Token/Expired Token"
                    ]
            ];
            return $this->respond($response,400);
        }
        
        $response=[
	            "status"    => 200,
	            "error"     => null,
	            "messages"  => $member//"continue",
	        ];
	    return $this->respond($response,200);
	}

	public function get_resettoken(){
	    $email = $this->request->getGet('email', FILTER_VALIDATE_EMAIL);
        $member = $this->member->getby_email($email);
	    if (@$member->code==5051){
	        $response=[
                "status"    => 400,
                "error"     => 02,
                "messages"  => [
                    "error" => "User Not Found"
                    ]
            ];
            return $this->respond($response,400);
	    }

        $response=[
	            "status"    => 200,
	            "error"     => null,
	            "messages"  => $this->member->resetToken($email),
	        ];
	   return $this->respond($response,200);
	}

	public function activate() {
	    $token = $this->request->getGet('token', FILTER_SANITIZE_STRING);
	    $member = $this->member->getby_token($token);

	    if (@$member->code==5051){
            $response=[
                "status"    => 400,
                "error"     => 02,
                "messages"  => [
                    "error" => "Invalid Token/Expired Token"
                    ]
            ];
            return $this->respond($response,400);
	    }
        if (@$member->status == 'active') {
            $response=[
                "status"    => 400,
                "error"     => null,
                "messages"  => [
                    "error" => "Member already active"
                    ]
            ];
            return $this->respond($response,400);
	    } else if (@$member->status == 'disabled') {
            $response=[
                "status"    => 400,
                "error"     => null,
                "messages"  => [
                    "error" => "Your account is suspended. Please contact administrator"
                    ]
            ];
            return $this->respond($response,400);
	    } 
	    
	    $result = $this->member->activate($member->email);
	    if (@$member->code==5051){
            $response=[
                "status"    => 400,
                "error"     => 02,
                "messages"  => [
                    "error" => "User Not Found"
                    ]
            ];
            return $this->respond($response,400);
	    }
	    
	    $response=[
	            "status"    => 200,
	            "error"     => null,
	            "messages"    => "Member is successfully activated"
	        ];
	    return $this->respond($response,200);
	}
    
    public function signin(){
	    $validation = $this->validation;
        $validation->setRules([
					'email' => [
						'rules'  => 'required|valid_email',
						'errors' => [
							'required'      => 'Email is required',
							'valid_email'   => 'Invalid Email format'
						]
					],
					'passwd' => [
					    'rules'  => 'required|min_length[8]',
					    'errors' =>  [
					        'required'      => 'Password is required',
					        'min_length'    => 'Min length password is 8 character'
					    ]
					],
					'is_google' => [
					    'rules'  => 'required',
					    'errors' =>  [
					        'required'      => 'is Google is required',
					    ]
					],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }
        
	    $data           = $this->request->getJSON();

        $member = $this->member->getby_email($data->email);
	    if (@$member->code==5051){
	        $response=[
                "status"    => 400,
                "error"     => 02,
                "messages"  => [
                    "error" => "User Not Found"
                    ]
            ];
            return $this->respond($response,400);
	    }
        if ($data->passwd == $member->passwd) {
            if ($member->is_google!=$data->is_google){
                $response=[
                        "status"    => 400,
                        "error"     => '03',
                        "messages"  => [
                            "error" => "Invalid username or password"
                            ]
                    ];
				return $this->respond($response,400);
            }
            if ($member->status=='new'){
                $response=[
                        "status"    => 400,
                        "error"     => '03',
                        "messages"  => [
                            "error" => "Please activate your account"
                            ]
                    ];
				return $this->respond($response,400);
            }elseif($member->status=='disabled') {
                $response=[
                        "status"    => 400,
                        "error"     => '04',
                        "messages"  => [
                            "error" => "Your account is suspended. Please contact administrator"
                            ]
                    ];
				return $this->respond($response,400);
            }elseif($member->status=='active'){
                $session_data = array(
    				'id'        => $member->id,
					'nama'		=> $member->nama,
    				'memberid'  => $member->memberid,
    				'phone'     => $member->phone,
					'role'		=> $member->role,
					'pin'       => $member->pin,
					'plafon'	=> $member->plafon,
    			);
    			
        	    $response=[
        	        "status"    => 200,
	                "error"     => null,
        	        "messages"    => $session_data
        	   ];
			    return $this->respond($response,200);
			}else{
                $response= "Invalid username or password";
				return $this->fail($response);
            }
        }else {
            $response=[
                    "status"    => 400,
                    "error"     => '04',
                    "messages"  => [
                        "error" => "Invalid username or password"
                        ]
                ];
			return $this->respond($response,400);
        }
    }
	
	public function logintoken(){
		$token = $this->request->getGet('token', FILTER_SANITIZE_STRING);
		$member = $this->valid->checkAPIkey($token);
		$response=[
					"messages"	=> $member
				];
		return $this->respond($response);
	}
	
	public function expatsignin(){
		$validation = $this->validation;
        $validation->setRules([
					'username' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'username is required',
						]
					],
					'passwd' => [
					    'rules'  => 'required',
					    'errors' =>  [
					        'required'      => 'Password is required',
					    ]
					],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }
        
	    $data           = $this->request->getJSON();

        $member = $this->user->getby_username($data->username);
        
	    if (@$member->code==5051){
	        return $this->fail(@$member);
	    }

        if ($data->passwd == @$member->passwd) {            
                $session_data = array(
					'username'	=> $data->username,
					'role'		=> $member->role,
    			);
    			
				$response=[
					"messages"	=> $session_data
				];
				return $this->respond($response);
		}else {
			$response="Wrong Password or username";
            return $this->fail($response, 400);
        }
	}

    public function resetpassword(){
	    $email = $this->request->getGet('email', FILTER_SANITIZE_STRING);
        $token=$this->member->resetToken($email);

	    if (@$token->code==5051){
	        return $this->fail(@$token);
	    }

	    $response=[
	            "messages"  => [
	                    "token"   => $token
	                ]
	        ];
	   return $this->respond($response);
	}    
	
	public function updatepassword(){
	    $validation = $this->validation;
        $validation->setRules([
                    'token' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Reset token is required',
						]
					],
					'password' => [
						'rules'  => 'required|min_length[40]',
						'errors' => [
							'required'      => 'Password is required',
							'min_length'    => 'Min length password is 40 characters'
						]
					]
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $filters = array(
            'token'     => FILTER_SANITIZE_STRING, 
            'password'  => FILTER_UNSAFE_RAW, 
        );

	    $filtered = array();
        foreach($data as $key=>$value) {
             $filtered[$key] = filter_var($value, $filters[$key]);
        }
        
        $data=(object) $filtered;
	    
	    $member     = $this->member->getby_token($data->token);
	    if (@$member->code==5051){
    	    return $this->fail($member);
	    }

        $where=array(
            "email"     => $member->email,
            "token"     => $data->token
            );
        $mdata=array(
            "passwd"    => $data->password,
            "token"     => NULL
            );
            
        $result=$this->member->change_password($mdata,$where);
        if (@$result->code==5051){
	        return $this->fail(@$result);
	    }
	    $response=[
	            "messages"    => "Password successfully changed"
	        ];
	    return $this->respond($response);
        
	}
	
    public function updatepass(){
        $validation = $this->validation;
        $validation->setRules([
                'userid' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'User ID is required',
                    ]
                ],
                'oldpass' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Old Password is required',
                    ]
                ],
                'newpass' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'New Password is required',
                    ]
                ],
        ]);

        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

        $data   = $this->request->getJSON();
        $result     = $this->member->update_pass($data->oldpass, $data->newpass,$data->userid);
        if (!$result){
    	    $response=[
    	        "status"    => 400,
                "error"     => 03,
                "messages"  => [
                    "error" => "Incorrect old password"
                    ]
    	    ];
    	    return $this->respond($response,400);
        }else{
    	    $response=[
    	        "status"    => 200,
                "error"     => null,
	            "messages"    => "Password is successfully changed"
    	    ];
    	    return $this->respond($response,200);
        }
        
    }

	public function reset_plafon(){
        $result=$this->transaksi->resetplafon();
        
	    $response=[
	            "messages"    => $result
	        ];
	    return $this->respond($response);
	    
	}
	
}
