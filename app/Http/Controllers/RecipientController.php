<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;

class RecipientController extends Controller
{
    public function postRecipient(Request $request){
        
        $user = JWTAuth::parseToken()->toUser();
        
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email'
        ]);
        
        $name = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $email = filter_var($request->input('email'), FILTER_SANITIZE_STRING);
        $phone = filter_var($request->input('phone'), FILTER_SANITIZE_STRING);
        if(!is_array($request->input('vendors'))) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $vendors = $request->input('vendors');
        
        DB::table('recipients')->insert(['name' => $name, 'email'=>$email, 'phone'=>$phone, 'user_id'=>$user->id]);
        
        $id = DB::getPdo()->lastInsertId();
        if(!$id) return response()->json(['error'=>'Sorry somthing went wrong!'], 404);
        
        foreach ($vendors as $vendor){
            $vendorId = $vendor['id'];
            if(!is_numeric($vendorId)) return response()->json(['error'=>'Sorry something went wrong!'], 404);
            DB::table('recipient_vendor')->insert(['recipient_id'=> $id, 'vendor_id'=>$vendorId]);
        }
        
        return response()->json(true, 200);
    }
    
    
    public function putRecipient(Request $request, $id){
        
        $user = JWTAuth::parseToken()->toUser();
        if(!is_numeric($id)) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email'
        ]);
        
        $name = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $email = filter_var($request->input('email'), FILTER_SANITIZE_STRING);
        $phone = filter_var($request->input('phone'), FILTER_SANITIZE_STRING);
        if(!is_array($request->input('vendors'))) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $vendors = $request->input('vendors');
        
        DB::table('recipients')
            ->where('id', '=', $id)
            ->update(['name'=> $name, 'email'=>$email, 'phone'=>$phone]);
        
        DB::table('recipient_vendor')
            ->where('recipient_id', '=', $id)
            ->delete();
        
        foreach ($vendors as $vendor){
            $vendorId = $vendor['id'];
            if(!is_numeric($vendorId)) return response()->json(['error'=>'Sorry something went wrong!'], 404);
            DB::table('recipient_vendor')->insert(['recipient_id'=> $id, 'vendor_id'=>$vendorId]);
        }
        
        return response()->json(true, 200);
    }
    
    
    
    public function getRecipient($id){
        if(!is_numeric($id)) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $recipient = DB::table('recipients')
            ->where('id', '=', $id)   
            ->select('name', 'id', 'phone', 'email')
            ->get();
        
        return response()->json($recipient, 200);
    }
    
    
    public function getRecipients(){
        $user = JWTAuth::parseToken()->toUser();
        $recipients = DB::table('recipients')
            ->select('name', 'email', 'phone', 'id')
            ->where('user_id', '=', $user->id)
            ->get();
        
        foreach($recipients as $recipient){
            $vendor = DB::table('recipient_vendor')
                ->select('vendors.name', 'vendors.id')
                ->join('vendors', 'vendors.id','=', 'recipient_vendor.vendor_id')
                ->where('recipient_id', '=', $recipient->id)
                ->get();
            $recipient->vendors = $vendor;
        }
        
        return response()->json($recipients, 200);
    }
    
    
    
    public function deleteRecipient($id){
        if(!is_numeric($id)) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $user = JWTAuth::parseToken()->toUser();
        
        $affectedRows = DB::table('recipients')
            ->where('id', '=', $id, 'AND', 'user_id', '=', $user->id)
            ->delete();
        
        if($affectedRows != 1) return response()->json(['error' => 'Somthing went wrong!'], 404);
        
        DB::table('recipient_vendor')
            ->where('recipient_id', '=', $id)
            ->delete();
        return response()->json($affectedRows, 200);
    }
}