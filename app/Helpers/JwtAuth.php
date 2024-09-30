<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {

    public $key;

    public function __construct() {
        $this->key = 'clave_secreta-154896131';
    }

    public function signup($email, $password, $getToken = null) {
//users credential exists
        $user = User::where([
                    'email' => $email,
                    'password' => $password
                ])->first();
//check if it is the right (object)
        $singup = false;
        if (is_object($user)) {
            $singup = true;
        }
//generate token for the user
        if ($singup == true) {
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'description'=> $user->description,
                'image'=> $user->image,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60) //days*hours*minutes*second
            );
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
//return decoded data or the tokken by param
            if (is_null($getToken)) {
                $data = $jwt;
            } else {
                $data = $decoded;
            }
        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto'
            );
        }
//return decoded data or the token given a param
        return $data;
    }

    public function boot() {
        
    }

    public function register() {
        require_once app_path() . '/Helpers/JwtAuth.php';
    }
    
    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;
        try {
            $jwt = str_replace('"', '',$jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }
        if($getIdentity){
            return $decoded;
        }
        
        return $auth;
    }

}
