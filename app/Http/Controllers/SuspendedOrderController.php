<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;
use DateTime;

class SuspendedOrderController extends Controller
{
    
    
    public function getSuspendedOrder(){
        $user = JWTAuth::parseToken()->toUser();
        
        $order = DB::table('suspended_order')
            ->select('quantity', 'item_id AS id', 'pack', 'vendor_id AS vendor', DB::raw('DATE(`date`) AS date'))
            ->where('user_id', '=', $user->id)
            ->get();
            
        foreach($order as $item){
            $date = new DateTime($item->date);
            $item->date = $date->format('l, F j, Y');
        }
        
        return response()->json($order, 200);
    }
      
    
    public function postSuspendedOrder(Request $request){
        $this->deleteSuspendedOrder();
        $user = JWTAuth::parseToken()->toUser();
        $order = $request->input('order');
        foreach($order as $input){
            $quantity = filter_var($input['quantity'], FILTER_SANITIZE_STRING);
            $pack = filter_var($input['pack'], FILTER_SANITIZE_STRING);
            $itemId = filter_var($input['id'], FILTER_SANITIZE_STRING);
            $vendorId = filter_var($input['vendor'], FILTER_SANITIZE_STRING);

            DB::table('suspended_order')->insert(['user_id' => $user->id, 'pack' => $pack, 'vendor_id' => $vendorId, 'quantity' => $quantity, 'item_id' => $itemId]);
        }
        return response()->json(true, 200);
    }
    
    
    public function deleteSuspendedOrder(){
        $user = JWTAuth::parseToken()->toUser();
        $affectedRows = DB::table('suspended_order')
                ->where('user_id', '=', $user->id)
                ->delete();
        
        return response()->json($affectedRows, 200);
    }
    
    
    
    
   
    
}