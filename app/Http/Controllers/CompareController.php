<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Account;
use JWTAuth;
use Hash;
use Carbon\Carbon;
use Mail;
use App\Mail\Price_Compare;

class CompareController extends Controller{
    
    public function compare(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $hash = $this->getHash(new Carbon());
        
        $account = DB::table('accounts')
            ->where('user_id', '=', $user->id)
            ->select('id', 'email', 'company', 'address', 'city', 'state', 'zipcode', 'phone')
            ->get();
        
        DB::table('price_compare')->insert(['user_id' => $user->id, 'hash' => $hash]);
        
        $id = DB::getPdo()->lastInsertId();
        
        $compare = $request->input('compare');
        foreach($compare as $input){
            $itemId = filter_var($input['id'], FILTER_SANITIZE_STRING);
            
            DB::table('price_compare_items')->insert(['price_compare_id'=> $id, 'item_id' => $itemId]);
        }
        
        $recipients = DB::table('recipients')
                ->where('user_id', '=', $user->id)
                ->select('recipients.email', 'recipients.name', 'recipients.vendor_sales AS vs')
                ->get();
        
        
        foreach($recipients as $recipient){
            if($recipient->vs == 1){
               Mail::to($recipient)->send(new Price_Compare($hash, $recipient->email, $compare, $account[0])); 
            }else{
                Mail::to($recipient)->send(new Price_Compare($hash, 'listonly', $compare, $account[0])); 
            }
            
        }
        
        
        
        
       return response()->json(['hash' => $compare], 200);
    }
    
    
    public function getPrice($data){
        $data = json_decode($data);
        
        $items = DB::table('price_compare')
            ->where('hash', '=', $data->hash)
            ->join('price_compare_items', 'price_compare_id', '=', 'price_compare.id')
            ->join('main_items_list', 'main_items_list.id', '=', 'price_compare_items.item_id')
            ->select('main_items_list.id', 'main_items_list.name')
            ->get();
        
        
        return response()->json($items, 200);
        
    }
    
    public function sendPrice(Request $request){
        $id = json_decode($request->id);
        $itemlist = $request->itemlist;
        $note = $request->note;
        
        $recipients = DB::table('price_compare')
            ->where('hash', '=', $id->hash)
            ->join('recipients', 'recipients.user_id', '=', 'price_compare.user_id')
            ->select('recipients.email', 'recipients.name', 'recipients.vendor_sales AS vs', 'recipients.user_id AS userId')
            ->get();
        
        $userId = $recipients[0]->userId;
        
        $account = DB::table('accounts')
            ->where('user_id', '=', $userId)
            ->select('id', 'email', 'company', 'address', 'city', 'state', 'zipcode', 'phone')
            ->get();
        
        $emails = [];
        $salesName = '';
        
        foreach($recipients as $recipient){
            if($recipient->vs == 0){
                $emails[] = $recipient->email;
            }
            if($recipient->email == $id->email){
                $salesName = $recipient->name;
            }
        }
        
        
        
        return response()->json(['$emails'=> $emails, 'userid'=>$salesName, 'note'=>$note], 200);
    }
    
    public function getHash($date){
        $hash = Hash::make($date);
        $hash = preg_replace('/[^A-Za-z0-9]/', '', $hash);
        $count = DB::table('price_compare')->where('hash', '=', $hash)->count();
        if($count > 0) $this->getHash();
        return $hash;
    }
}