<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index() {
        $categories = Category::all();
        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'categories' => $categories
        ]);
    }

    public function show($id) {
        $category = Category::find($id);
        if (is_object($category)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'category' => 'La categoria no existe'
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
            $validate = \Validator::make($param_array, ['name' => 'required']);
            //save
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la categoria'
                ];
            } else {
                $category = new Category();
                $category->name = $param_array['name'];
                $category->save();
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                ];
                //return result
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
        //validate data
        if (!empty($param_array)) {
            $validate = \Validator::make($param_array, ['name' => 'required']);
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la categoria'
                ];
            } else {
                //remove the fields that dont wanna update
                unset($param_array['id']);
                unset($param_array['created_at']);
                //update
                $category = Category::where('id', $id)->update($param_array);
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $param_array
                ];
            }
        }else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha enviado ninguna categoria'
            ];
        }
        return response()->json($data, $data['code']);
    }

}
