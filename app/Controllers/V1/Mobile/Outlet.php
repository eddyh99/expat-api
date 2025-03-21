<?php
namespace App\Controllers\V1\Mobile;

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

}
