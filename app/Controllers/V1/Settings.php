<?php
namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\V1\Mdl_settings;

class Settings extends BaseController
{
    use ResponseTrait;
    

    public function __construct()
    {   
        $this->setting    = new Mdl_settings();
	}

    public function get_setting(){
        $response=[
            "code"      => "200",
            "error"     => NULL,
            "messages"   => $this->setting->getall_setting()
        ];
        return $this->respond($response);
	}
    
    public function updateSetting(){
        $validation = $this->validation;
        $validation->setRules([
                    'basepoin' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Base Point is required',
						]
					],
					'deliveryfee' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Delivery Fee is required',
						]
                    ],
                    'maxarea' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Max Area Delivery is required',
                        ]
                    ],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();
        $filters = array(
            'basepoin'      => FILTER_SANITIZE_NUMBER_INT, 
            'deliveryfee'   => FILTER_SANITIZE_NUMBER_INT, 
            'maxarea'       => FILTER_SANITIZE_NUMBER_INT, 
        );

	    $filtered = array();
        foreach($data as $key=>$value) {
             $filtered[$key] = filter_var($value, $filters[$key]);
        }
        
        $data=(object) $filtered;
	    
	    $mdata=array(
	            array(
	                "content"   => "poin_calculate",
	                "value"     => $data->basepoin
	            ),
	            array(
	                "content"   => "delivery_fee",
	                "value"     => $data->deliveryfee),
	            array(
	                "content"   => "max_area",
	                "value"     => $data->maxarea
	           ),
	        );
        $result     = $this->setting->save_settings($mdata);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[	            
	            "messages"    => "Settings successfully updated"
	        ];
	    return $this->respond($response);
        	
    }
    
    public function updatemembership(){
        $validation = $this->validation;
        $validation->setRules([
                    'type' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Membership Type is required',
						]
					],
					'description' => [
						'rules'  => 'required',
						'errors' => [
							'required'      => 'Description is required',
						]
                    ],
                    'step1' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Milestone 1 is required',
                        ]
                    ],
                    'step2' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Milestone 2 is required',
                        ]
                    ],
                    'step3' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Milestone 3 is required',
                        ]
                    ],
                    'step4' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Milestone 4 is required',
                        ]
                    ],
                    'step5' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Milestone 5 is required',
                        ]
                    ],
                    'step6' => [
                        'rules'  => 'required',
                        'errors' => [
                            'required'      => 'Milestone 5 is required',
                        ]
                    ],
            ]);
        
        if (!$validation->withRequest($this->request)->run()){
            return $this->fail($validation->getErrors());
        }

	    $data       = $this->request->getJSON();

        if ($data->type=="Bronze"){
            $poin="poin_bronze";
            $step1="step1_bronze";
            $step2="step2_bronze";
            $step3="step3_bronze";
            $step4="step4_bronze";
            $step5="step5_bronze";
            $step6="step6_bronze";
        }elseif ($data->type=="Silver"){
            $poin="poin_silver";
            $step1="step1_silver";
            $step2="step2_silver";
            $step3="step3_silver";
            $step4="step4_silver";
            $step5="step5_silver";
            $step6="step6_silver";
        }elseif ($data->type=="Gold"){
            $poin="poin_gold";
            $step1="step1_gold";
            $step2="step2_gold";
            $step3="step3_gold";
            $step4="step4_gold";
            $step5="step5_gold";
            $step6="step6_gold";
        }elseif ($data->type=="Platinum"){
            $poin="poin_platinum";
            $step1="step1_platinum";
            $step2="step2_platinum";
            $step3="step3_platinum";
            $step4="step4_platinum";
            $step5="step5_platinum";
            $step6="step6_platinum";
        }
        
        $mdata=array(
                array(
                        "content"   => $data->type,
                        "value"     => $data->description 
                    ),
                array(
                        "content"   => $poin,
                        "value"     => $data->minpoin
                    ),
                array(
                        "content"   => $step1,
                        "value"     => $data->step1
                    ),
                array(
                        "content"   => $step2,
                        "value"     => $data->step2
                    ),
                array(
                        "content"   => $step3,
                        "value"     => $data->step3
                    ),
                array(
                        "content"   => $step4,
                        "value"     => $data->step4
                    ),
                array(
                        "content"   => $step5,
                        "value"     => $data->step5
                    ),
                array(
                        "content"   => $step6,
                        "value"     => $data->step6
                    ),
                
            );
        $result     = $this->setting->save_settings($mdata);
        if (@$result->code==5051){
    	    return $this->fail($result->message);
	    }

	    $response=[	            
	            "messages"    => "Membership successfully updated"
	        ];
	    return $this->respond($response,200);
    }
}
