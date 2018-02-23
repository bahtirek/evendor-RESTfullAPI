<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;

class UserItemsController extends Controller
{
    
    
    public function getItems(){
        $user = JWTAuth::parseToken()->toUser();
        
        $vendors = DB::table('vendor_user')
            ->select('vendors.name', 'vendors.id')
            ->join('vendors', 'vendors.id','=', 'vendor_user.vendor_id')
            ->where('vendor_user.user_id', '=', $user->id)
            ->get();
        
        foreach($vendors as $vendor){
            $items = DB::table('user_items_list')
                ->where('user_items_list.vendor_id', '=', $vendor->id)
                ->where('user_items_list.user_id', '=', $user->id)
                ->join('main_items_list', 'id', '=', 'user_items_list.item_id')
                ->leftJoin("items_note",function($join) use($user){
                    $join->on('items_note.item_id', '=', 'user_items_list.item_id');
                    $join->where('items_note.user_id', '=', $user->id);
                })
                
                ->select('user_items_list.item_id AS id', 'main_items_list.name', 'main_items_list.family', 'user_items_list.pack', 'user_items_list.group_id AS groupId', 'items_note.note')
               ->get();
            $vendor->items = $items;
        }
        
        
        return response()->json($vendors, 200);
    }
      
    
    public function postItem(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $itemId = filter_var($request->input('itemId'), FILTER_SANITIZE_STRING);
        $vendorId = filter_var($request->input('vendorId'), FILTER_SANITIZE_STRING);
        $affRows = DB::table('user_items_list')->insert(['user_id' => $user->id, 'item_id' => $itemId, 'vendor_id' => $vendorId, 'pack' => 'Case']);
        return response()->json($affRows, 200);
    }
    
    
    public function putItem(Request $request, $id){
        $user = JWTAuth::parseToken()->toUser();
        if(!$id) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $value = filter_var($request->input('value'), FILTER_SANITIZE_STRING);
        $update = filter_var($request->input('update'), FILTER_SANITIZE_STRING);
        
        if($update == 'pack'){
            $affectedRows = DB::table('user_items_list')
                ->where('item_id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->update(['pack'=> $value]);
        }else if($update == 'group'){
            $affectedRows = DB::table('user_items_list')
                ->where('item_id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->update(['group_id'=> $value]);
            
        }else if($update == 'vendor'){
            $affectedRows = DB::table('user_items_list')
                ->where('item_id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->update(['vendor_id'=> $value]);
            
        }
        
        return response()->json($affectedRows, 200);
        
    }
    
    public function deleteItem($id){
        if(!is_numeric($id)) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $user = JWTAuth::parseToken()->toUser();
    
        
        $affectedRows = DB::table('user_items_list')
            ->where('item_id', '=', $id)
            ->where('user_id', '=', $user->id)
            ->delete();
        return response()->json($affectedRows, 200);
    }
   
    
}