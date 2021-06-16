<?php
/**
 * @OA\Info(
 *   title="API ZoomCEM - Public",
 *   version="2.0",
 *     description="API description",
 *     termsOfService="http://localhost:8080/terms.html",
 *   @OA\Contact(
 *     name="Support Zoom CEM",
 * 	   url="https://tenant.zoomcrm.com/support",
 *     email="soporte@zoomcem.com"
 *   )
 * )
 * @OA\Server(url="http://localhost:8080", description="Developer Server")
 */
namespace App\Controllers\Api\Crm;

use App\Models\UserModel;
use Exception;
use App\Libraries\firebase\JWT;

class WSPublic extends BaseApiController {
    public function login(){
        $rules = [
            "email" => "required|valid_email|min_length[6]",
            "api_key" => "required",
            "password" => "required",
        ];
        $messages = [
            "email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format"
            ],
            "password" => [
                "required" => "password is required"
            ],
        ];
        if (!$this->validate($rules, $messages)) {
            $response = [
                'status' => 500,
                'error' => true,
                'message' => $this->validator->getErrors(),
                'data' => []
            ];
            return $this->respondCreated($response);
        } else {
            $userModel = new UserModel();
            $userdata = $userModel->where([
                "email" => $this->request->getVar("email"),
                "api_key" => $this->request->getVar("api_key"),
            ])->first();
            if(!empty($userdata)) {
                if(password_verify($this->request->getVar("password"), $userdata['password'])) {
                    $key = $this->getSecretKey();
                    $iat = time(); // current timestamp value
                    $nbf = $iat + 10;
                    $exp = $iat + getenv('JWT_TIME_TO_LIVE');
                    $payload = array(
                        "iss" => "The_claim",
                        "aud" => "The_Aud",
                        "iat" => $iat, // issued at
                        "nbf" => $nbf, //not before in seconds
                        "exp" => $exp, // expire time in seconds
                        "data" => $userdata,
                    );
                    $token = JWT::encode($payload, $key);
                    $response = [
                        'status' => 200,
                        'error' => false,
                        'messages' => 'User logged In successfully',
                        'data' => [
                            'token' => $token
                        ]
                    ];
                    return $this->respondCreated($response);
                } else {
                    $response = [
                        'status' => 500,
                        'error' => true,
                        'messages' => 'Incorrect details',
                        'data' => []
                    ];
                    return $this->respondCreated($response);
                }
            } else {
                $response = [
                    'status' => 500,
                    'error' => true,
                    'messages' => 'User not found',
                    'data' => []
                ];
                return $this->respondCreated($response);
            }
        }
    }

    public function profile(){
        $key = $this->getSecretKey();
        $authHeader = $this->request->header("Authorization");
        $authHeader = $authHeader->getValue();
        $token = $authHeader;
        try {
            $decoded = JWT::decode($token, $key, array("HS256"));
            if ($decoded) {
                $response = [
                    'status' => 200,
                    'error' => false,
                    'messages' => 'Api User details',
                    'data' => [
                        'profile' => $decoded
                    ]
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