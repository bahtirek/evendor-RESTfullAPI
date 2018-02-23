<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;

class CustomItemController extends Controller
{
   
    public function postCustomItem(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $name = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $vendorId = filter_var($request->input('vendorId'), FILTER_SANITIZE_STRING);
        
        DB::table('main_items_list')->insert(['name' => $name, 'family' => 'cus']);
        
        $id = DB::getPdo()->lastInsertId();
        
        DB::table('user_items_list')->insert(['user_id' => $user->id, 'item_id' => $id, 'vendor_id' => $vendorId, 'pack' => 'Case']);
        DB::table('custom_items_list')->insert(['user_id' => $user->id, 'item_id' => $id]);
        
        return response()->json($id, 200);
    }
    
    
}




