<?php
namespace App\Controllers\Api\Crm;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Exception;
use App\Libraries\firebase\JWT;

class BaseApiController extends  ResourceController{
    use ResponseTrait;
    public function __construct(){
        $requireValidation = true;
        //Verificar si el metodo es loginValidate para darle acceso sin restricción
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $urlArray = explode( '/', $_SERVER['REQUEST_URI'] );
            $nameMethod = $urlArray[ count($urlArray) - 1 ];
            if( in_array($nameMethod, ['login', 'profile']) ){
                $requireValidation = false;
            }
        }
        // Validation JWT
        if( $requireValidation ){
            $key = $this->getSecretKey();
            $authHeader = $this->request->header("Authorization");
            $authHeader = $authHeader->getValue();
            $token = $authHeader;
            try {
                $decoded = JWT::decode($token, $key, array("HS256"));
                if (!$decoded) {
                    $response = [
                        'status' => 401,
                        'error' => true,
                        'messages' => 'Access denied',
                        'data' => []
                    ];
                    return $this->respondCreated($response);
                }
            } catch (Exception $ex) {
                $response = [
                    'status' => 401,
                    'error' => true,
                    'messages' => 'Access denied',
                    'data' => []
                ];
                return $this->respondCreated($response);
            }
        }
    }

    public function getSecretKey(){
        return getenv('JWT_SECRET_KEY');
    }
    public function genericResponse($data, $msj, $code){
        if ($code == 200) {
            return $this->respond(array(
                "data" => $data,
                "code" => $code
            )); //, 404, "No hay nada"
        } else {
            return $this->respond(array(
                "msj" => $msj,
                "code" => $code
            ));
        }
    }
}