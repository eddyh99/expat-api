<?php
namespace App\Controllers\V1\Mobile;

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

    public function get_allpromo(){
        $response=[
            "messages"   => $this->promosi->getall_promo()
        ];
        return $this->respond($response);
    }

    public function get_allinstore(){
        $response=[
            "messages"   => $this->promosi->get_instore()
        ];
        return $this->respond($response);
    }

    public function get_allonline(){
        $response=[
            "messages"   => $this->promosi->get_online()
        ];
        return $this->respond($response);
    }

    public function getpromo_byid(){
        $id      = $this->request->getGet('id', FILTER_SANITIZE_STRING);
        $response=[
            "messages"   => $this->promosi->get_promobyid($id)
        ];
        return $this->respond($response);
    }
}
