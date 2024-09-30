<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => [
                'index',
                'show',
                'getImage',
                'getPostsByCategory',
                'getPostsByUser']]);
    }

    public function index() {
        $post = Post::all()->load('category');
        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'posts' => $post
        ]);
    }

    public function show($id) {
        $post = Post::find($id)->load('category')
                                ->load('user');
        if (is_object($post)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'post' => 'El post no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        //get data POST
        $json = $request->input('json', null);
        $param_array = json_decode($json, true);
        //validate data
        if (!empty($param_array)) {
            $user = $this->getIdentity($request);
            $validate = \Validator::make($param_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required',
                        'image' => 'required',
            ]);
            //save
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el post'
                ];
            } else {
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $param_array['category_id'];
                $post->title = $param_array['title'];
                $post->content = $param_array['content'];
                $post->image = $param_array['image'];
                $post->save();
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $param_array
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ninguna categoria'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
        //get data PUT
        $json = $request->input('json', null);
        $param_array = json_decode($json, true);
        $data = [
            'code' => 400,
            'status' => 'error',
            'message' => 'No se ha guardado la categoria'
        ];
        //validate data
        if (!empty($param_array)) {
            $validate = \Validator::make($param_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required',
            ]);
            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            } else {
                //remove the fields that dont wanna update
                unset($param_array['id']);
                unset($param_array['user_id']);
                unset($param_array['created_at']);
                unset($param_array['user']);
                //get login user
                $user = $this->getIdentity($request);
                //get the object to update
                $post = Post::where('id', $id)
                        ->where('user_id', $user->sub)// the current user can update only his owns posts
                        ->first();
                if (!empty($post) && is_object($post)) {
                    //update
                    $post->update($param_array);
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'posts' => $post,
                        'changes' => $param_array
                    ];
                }
            }
        }
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request) {
        //get login user
        $user = $this->getIdentity($request);
        //get the object
        $post = Post::where('id', $id)
                ->where('user_id', $user->sub)// the current user can delete only his owns posts
                ->first();
        if (!empty($post)) {
            //delete
            $post->delete();
            //return result
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post,
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'El post no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    private function getIdentity($request) {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);
        return $user;
    }

    public function upload(Request $request) {
        //get the image
        $image = $request->file('file0');
        //validate
        $validate = \Validator::make($request->all(), [
                    'file0' => 'required|image|mimes:jpg,png,gif,jpeg,jfif,bmp,tiff|max:20480'
        ],$messages = [
            'mimes' => 'Please insert image only',
            'max'   => 'Image should be less than 20 MB'
        ]);
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message'=> $messages
                //'message' => 'Error al subir la imagen'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();
            //save
            \Storage::disk('images')->put($image_name, \File::get($image));
            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name,
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function getImage($filename) {
        //check if the image exists
        $isset = \Storage::disk('images')->exists($filename);
        if ($isset) {
            //get the image
            $file = \Storage::disk('images')->get($filename);
            return new Response($file, 200);
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function getPostsByCategory($id) {
        $posts = Post::where('category_id', $id)->get();
        return response()->json([
                    'status' => 'success',
                    'posts' => $posts
                        ], 200);
    }

    public function getPostsByUser($id) {
        $post = Post::where('user_id', $id)->get();
        return response()->json([
                    'status' => 'success',
                    'posts' => $post
                        ], 200);
    }

}
