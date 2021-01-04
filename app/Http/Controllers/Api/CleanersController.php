<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class CleanersController extends Controller
{
    public function index(){
       $allCleaners = User::where('role_id',1)->get()->toArray();
        return response($allCleaners, 200);
    }

    public function show(){

    }

    public function update(Request $request){
        try {
            $params = $request->all();
            $id = $params['id'];

            $this->validate($request, [
                'id' => 'required'
            ]);

            $cleaner =User::find($id);
            if (is_null($cleaner)) {
                return response(['error' => 'No cleaner found'], 400);
            }

            $info = [];
            foreach ($params as $key => $value) {
                $info[$key] = $value;
            }
            $cleaner->update($info);

            return response( $cleaner, 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }

    }

    public function remove(Request $request){
        try {
            $params = $request->all();
            $id = $params['id'];

            $cleaner = User::find($id);
            if (is_null($cleaner)) {
                return response(['error' => 'No cleaner found for this id'],
                    404);
            }

            $cleaner->delete();
            return response()->json( 'success', 200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }
}
