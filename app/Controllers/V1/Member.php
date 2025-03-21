<?php
namespace App\Controllers\V1;

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

    public function get_allmember(){
        $role = $this->request->getGet('role', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->member->getall_member($role)
        ];
        return $this->respond($response);
    }

    public function addMember(){
        $validation = $this->validation;
        $validation->setRules([
                    'email' => [
						'rules'  => 'required|valid_email',
						'errors' => [
							'required'      => 'Username is required',
							'valid_email'   => 'Invalid Email',
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
                    'gender' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Gender is required',
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
        
        $mdata=array(
            "memberid"  => uniqid(),
            "email"     => $data->email,
            "passwd"    => $data->passwd,
            "nama"      => $data->nama,
            "gender"    => $data->gender,
            "plafon"    => $data->plafon,
            "status"    => 'active',
            "role"      => $data->role,
            "is_driver" => empty($data->is_driver) ? 'no': $data->is_driver,
            "created_at"=> date("Y-m-d H:i:s")
        );

        $result     = $this->member->add_member($mdata);

        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[
	            "messages"    => "Member successfully added"
	        ];
	    return $this->respond($response);
        	
    }

    public function get_byid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);

        $response=[
            "messages"    => $this->transaksi->get_all($id)
        ];
        return $this->respond($response);
    }

    public function updateMember(){
        $validation = $this->validation;
        $validation->setRules([
                'nama' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Nama is required',
                    ]
                ],
                'gender' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Gender is required',
                    ]
                ],
                'membership' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Membership is required',
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

        $data   = $this->request->getJSON();
        $id     = $this->request->getGet('id', FILTER_SANITIZE_STRING);

        if (empty($data->passwd)){
            $mdata=array(
                "nama"      => $data->nama,
                "gender"    => $data->gender,
                "role"      => $data->role,
                "plafon"    => $data->plafon,
            	"is_driver" => empty($data->is_driver) ? 'no': $data->is_driver,
                "update_at"=> date("Y-m-d H:i:s")
            );
        }else{
            $mdata=array(
                "passwd"    => $data->passwd,
                "nama"      => $data->nama,
                "gender"    => $data->gender,
                "role"      => $data->role,
                "plafon"    => $data->plafon,
            	"is_driver" => empty($data->is_driver) ? 'no': $data->is_driver,
                "update_at"=> date("Y-m-d H:i:s")
            );

        }

        $result     = $this->member->update_member($mdata,$id);
        if (@!$result){
    	    return $this->fail("Failed update member");
	    }

	    $response=[
	            "messages"    => "Member successfully updated"
	        ];
	    return $this->respond($response);
        	
    }

    public function manualActivation(){
        $id     = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $resmember = $this->member->getby_id($id);
        
        if (@$resmember->status == 'active') {
            $response= "Member already active";
            return $this->fail($response);
	    } else if (@$resmember->status == 'disabled') {
            $response="Your account is suspended. Please contact administrator";
            return $this->fail($response);
	    } 
	    
	    $result = $this->member->activate($resmember->email);
	    if (@$member->code==5051){
	        return $this->fail(@$result->message);
	    }
	    
	    $response=[
	            "messages"    => "Member is successfully activated"
	        ];
	    return $this->respond($response);

    }
    
    public function deleteMember(){
        $id   = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $result     = $this->member->delete_member($id);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
            "messages"    => "Member successfully deleted"
        ];
        return $this->respondDeleted($response);
    }  
    
    public function topup(){
        $validation = $this->validation;
        $validation->setRules([
                'id_member' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'ID member is required',
                    ]
                ],
                'invoice' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Invoice is required',
                    ]
                ],
                'nominal' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required'      => 'Nominal is required',
                    ]
                ],
        ]);

        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

        $data   = $this->request->getJSON();
        $mdata=array(
            "id_member" => $data->id_member,
            "invoice"   => $data->invoice,
            "nominal"   => $data->nominal,
            "tanggal"   => date("Y-m-d H:i:s"),
            "created_at"=> date("Y-m-d H:i:s")
        );

        $result     = $this->member->topup_dana($mdata);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

        $response=[
                "messages"    => "Topup successfully created"
            ];
        return $this->respond($response);
    }
}
