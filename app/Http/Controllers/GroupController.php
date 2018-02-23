<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;

class GroupController extends Controller
{
    
    
    public function getGroups(){
        $user = JWTAuth::parseToken()->toUser();
        
        $groups = DB::table('groups')
            ->select('name', 'id')
            ->where('user_id', '=', $user->id)
            ->get();
        
        
        return response()->json($groups, 200);
    }
      
    
    public function postGroup(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $name = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        
        DB::table('groups')->insert(['user_id' => $user->id, 'name' => $name]);
        
        $id = DB::getPdo()->lastInsertId();
        return response()->json($id, 200);
    }
    
    
    public function putGroup(Request $request, $id){
        $user = JWTAuth::parseToken()->toUser();
        if(!$id) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $name = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $affectedRows = DB::table('groups')
                ->where('id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->update(['name'=> $name]);
        
        return response()->json($affectedRows, 200);
    }
    
    public function deleteGroup(Request $request, $id){
        $user = JWTAuth::parseToken()->toUser();
        if(!$id) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $affectedRows = DB::table('groups')
                ->where('id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->delete();
        DB::table('user_items_list')
                ->where('group_id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->update(['group_id'=> 0]);
        
        return response()->json($affectedRows, 200);
    }
    
    
    
    
   
    
}