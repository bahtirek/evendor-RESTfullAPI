<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;

class MainItemsController extends Controller
{
    public function getAllItems(){
        $user = JWTAuth::parseToken()->toUser();
        $items = DB::table('main_items_list')
            ->select('name', 'family', 'id')
            ->get();
        
        return response()->json($items, 200);
    }
    
    public function getItems($id){
        $user = JWTAuth::parseToken()->toUser();
        $userId = $user->id;
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        if($id == 'cus'){
            $items = DB::table('custom_items_list')
                ->where('custom_items_list.user_id', '=', $user->id)
                ->join('main_items_list', 'main_items_list.id', '=', 'custom_items_list.item_id')
                ->leftJoin('user_items_list', 'user_items_list.item_id', '=', 'main_items_list.id', 'AND', 'user_items_list.user_id', '=', $user->id)
                ->leftJoin('vendors', 'vendors.id', '=', 'user_items_list.vendor_id')
                ->select('main_items_list.name', 'main_items_list.id', 'vendors.name AS vendorName', 'user_items_list.vendor_id AS vendorId')
                ->get();
        }else{
            /*$items = DB::table('main_items_list')
                ->where('family', '=', $id)
                ->leftJoin('user_items_list', 'user_items_list.item_id', '=', 'main_items_list.id', 'AND', 'user_items_list.user_id', '=', $user->id)
                ->leftJoin('vendors', 'vendors.id', '=', 'user_items_list.vendor_id')
                ->select('main_items_list.name', 'main_items_list.id', 'vendors.name AS vendorName', 'user_items_list.vendor_id AS vendorId')
                ->get(); */
            $items = DB::table('main_items_list')
                ->where('family', '=', $id)
                
                ->leftJoin("user_items_list",function($join) use($user){
                    $join->on('user_items_list.item_id', '=', 'main_items_list.id');
                    
                    $join->where('user_items_list.user_id', '=', $user->id);
                })
                ->leftJoin('vendors', 'vendors.id', '=', 'user_items_list.vendor_id')
                ->select('main_items_list.name', 'main_items_list.id', 'vendors.name AS vendorName', 'user_items_list.vendor_id AS vendorId')
                ->get();  
        }
        
        return response()->json($items, 200);
    }
    
}