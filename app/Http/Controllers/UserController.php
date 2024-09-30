<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller {

    public function register(Request $request) {
        //get user data by post
        $json = $request->input('json', null);
        // $param = json_decode($json); //return objet
        $param_array = json_decode($json, true); //return array
        if (!empty($param_array)) {
            //clean data
            $param_array = array_map('trim', $param_array);
            //validate
            $validate = \validator($param_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);
            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado correctamente',
                    'errors' => $validate->errors()
                );
            } else {
                //encrypt passw
                //$pwd = password_hash($param_array['password'],PASSWORD_BCRYPT,['cost'=>4]);
                $pwd = hash('sha256', $param_array['password']);
                //create user
                $user = new User();
                $user->name = $param_array['name'];
                $user->surname = $param_array['surname'];
                $user->email = $param_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';
                //save user in db
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        } else {
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Los datos enviados no son correctos'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
        //recive data from POST
        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        //$params = json_decode($json);
        $params_array = json_decode($json, true);
        //validate data
        $validate = \validator($params_array, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        } else {
            $pwd = hash('sha256', $params_array['password']);
            $signup = $jwtAuth->signup($params_array['email'], $pwd);
            if (!empty($params_array['gettoken']))
                $signup = $jwtAuth->signup($params_array['email'], $pwd, true);
        }
        // $pwd = password_hash($password,PASSWORD_BCRYPT,['cost'=>4]);
        return response()->json($signup, 200);
    }

    public function update(Request $request) {
        //check if the user is identify
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        //get PUT data 
        $json = $request->input('json', null);
        $param__array = json_decode($json, true);

        if ($checkToken && !empty($param__array)) {
            //get identified user
            $user = $jwtAuth->checkToken($token, true);
            //validate data
            $validate = \Validator::make($param__array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        'email' => 'required|email|unique:users' . $user->sub //id user
            ]);

            //remove fields that dont want to use
            unset($param__array['id']);
            unset($param__array['role']);
            unset($param__array['password']);
            unset($param__array['created_at']);
            unset($param__array['remember_token']);

            //update user in db
            $user_update = User::where('id', $user->sub)->update($param__array);
            //return array with result
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $param__array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'User no identificado'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {
        //get request data
        $image = $request->file('file0');
        //validate
        $validate = \Validator::make($request->all(), [
                    'file0' => 'required|image|mimes:jpg,jpeg,png,gif,jfif,bmp,tiff'
        ]);
        //save img
        if (!$image || $validate->fails()) {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            );
        } else {
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));
            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }
        //return result     
        return response()->json($data, $data['code']);
    }

    public function getImage($filename) {
        $isset = \Storage::disk('users')->exists($filename);
        if ($isset) {
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        }else{
            $data = array(
                'code' => 404,
                'status' => 'error',
                'image' => 'La imagen no existe'
                );
             return response()->json($data, $data['code']);
        }
    }
    
    public function detail($id){
        $user = User::find($id);
        if(is_object($user)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        }else{
            $data = array(
                'code' => 404,
                'status' => 'success',
                'message' => 'El usuario no existe'
            );
        }
        return response()->json($data, $data['code']);
    }

}
