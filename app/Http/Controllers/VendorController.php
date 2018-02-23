<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;

class VendorController extends Controller
{
    
    
    
    public function postVendor(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $name = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        if($name == "") return response()->json(['error'=>'Empty stirng!'], 404);
        
        DB::table('vendors')->insert(['name' => $name]);
        $id = DB::getPdo()->lastInsertId();
        if(!$id) return response()->json(['error'=>'Sorry somthing went wrong!'], 404);
        
        DB::table('vendor_user')->insert(['vendor_id' => $id, 'user_id' => $user->id]);
        
        return response()->json(['vendor' => $name], 200);
        
    }
    
    public function getVendor($id){
        
        if(!is_numeric($id)) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $vendor = DB::table('vendors')
        ->select('name', 'id')
        ->where('id', '=', $id)
        ->get();
        
        return response()->json(['vendor' => $vendor], 200);
    }
    
    
    public function getVendors(){
        $user = JWTAuth::parseToken()->toUser();
        $vendors = DB::table('vendor_user')
            ->select('vendors.name', 'vendors.id')
            ->join('vendors', 'vendors.id','=', 'vendor_user.vendor_id')
            ->where('vendor_user.user_id', '=', $user->id)
            ->get();
        
        
        return response()->json($vendors, 200);
    }
    
    public function putVendor(Request $request, $id){
        $user = JWTAuth::parseToken()->toUser();
        $name = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        if($name == "" || !is_numeric($id)) return response()->json(['error'=>'Empty stirng!'], 404);
        
        $count = DB::table('vendor_user')
            ->where('vendor_id', '=', $id, 'AND', 'user_id', '=', $user->id)
            ->count();
        
        if($count != 1) return response()->json(['error' => 'Vendor not found!', 500]);
        DB::table('vendors')
            ->where('id', '=', $id)
            ->update(['name'=> $name]);
        
        return response()->json(['vendor' => $name], 200);
        
    }
    
    public function deleteVendor($id){
        if(!is_numeric($id)) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $user = JWTAuth::parseToken()->toUser();
        $count = DB::table('user_items_list')
            ->where('user_id', '=', $user->id)
            ->where('vendor_id', '=', $id)
            ->count();
        if($count >0 )return response(['error' => 'Can\'t remove', 'count'=> $count], 500);
        
       
        DB::table('vendor_user')
            ->where('vendor_id', '=', $id, 'AND', 'user_id', '=', $user->id)
            ->delete();
        DB::table('recipient_vendor')
            ->where('vendor_id', '=', $id)
            ->delete();
        return response()->json(true, 200);
    }
    
}