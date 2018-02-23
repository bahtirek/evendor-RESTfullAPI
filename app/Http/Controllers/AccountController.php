<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Account;
use JWTAuth;

class AccountController extends Controller{
    
    public function postAccount(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $this->validate($request, [
            'company' => 'required',
            'address' => 'required',
            'city' => 'required',
            'zipcode' => 'required',
            'state' => 'required',
            'email' => 'required|email',
            'phone' => 'required'
        ]);
        $company = $request->input('company');
        $address = $request->input('address');
        $city = $request->input('city');
        $zipcode = $request->input('zipcode');
        $state = $request->input('state');
        $email = $request->input('email');
        $phone = $request->input('phone');
        
        
        $count = DB::table('accounts')
            ->select('user_id')
            ->where('user_id', '=', $user->id)
            ->count();
        if($count == 0){
            $id = DB::table('accounts')
                ->insert([  'company' => $request->input('company'),
                            'address' => $request->input('address'),
                            'city' => $request->input('city'),
                            'zipcode' => $request->input('zipcode'),
                            'state' => $request->input('state'),
                            'email' => $request->input('email'),
                            'phone' => $request->input('phone'),
                            'user_id' => $user->id]);
        
            return response()->json($id, 200);
            
        }else{
            $affectedRows = DB::table('accounts')
                ->where('user_id', '=', $user->id)
                ->update([  'company' => $request->input('company'),
                            'address' => $request->input('address'),
                            'city' => $request->input('city'),
                            'zipcode' => $request->input('zipcode'),
                            'state' => $request->input('state'),
                            'email' => $request->input('email'),
                            'phone' => $request->input('phone')]);
        
            return response()->json($affectedRows, 200);
            
        }
        
        
        
        
    }
    
    
    
    public function putAccount(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $this->validate($request, [
            'company' => 'required',
            'address' => 'required',
            'city' => 'required',
            'zipcode' => 'required',
            'state' => 'required',
            'email' => 'required|email',
            'phone' => 'required'
        ]);
        
        
            
        
       
        $affectedRows = DB::table('accounts')
                ->where('user_id', '=', $user->id)
                ->update([  'company' => $request->input('company'),
                            'address' => $request->input('address'),
                            'city' => $request->input('city'),
                            'zipcode' => $request->input('zipcode'),
                            'state' => $request->input('state'),
                            'email' => $request->input('email'),
                            'phone' => $request->input('phone')]);
        
        return response()->json($affectedRows, 200);
    
    }
    
    
    
    
    public function getAccount(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        //$this->validate($request, ['token' => 'required']);
        
        //if($user != $request->input('token')) return request()->json(['error' => 'Validation failed'], 401);
        
        $account = DB::table('accounts')
            ->select()
            ->where('user_id', '=', $user->id)
            ->get();
        
        
        return response()->json($account, 200);
    }
    
}