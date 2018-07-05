<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use JWTAuth;
use Carbon\Carbon;
use Hash;
use App\Mail\FoodConn_Activation;
use Mail;
use Illuminate\Mail\Mailable;

class UserController extends Controller{
    
    public function signUp(Request $request){
        
        $dt = new Carbon();
        
        DB::table('users')
            ->where('activated', '=', 0)
            ->whereDate('created_at', '<', Carbon::now()->subHours(24))
            ->delete();//deleting unactivated account
        
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);
        
        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);
        
        $user->save();
        
        $hash = $this->getHash($user->email);
        
        DB::table('users_activation')->insert(['user_id' => $user->id, 'hash' => $hash]);
        
        
        Mail::to($user->email)->send(new FoodConn_Activation($hash));
        
        return response()->json(['message' => $hash], 201);
        
        
    }
    
    public function getHash($email){
        $hash = Hash::make($email);
        $hash = preg_replace('/[^A-Za-z0-9]/', '', $hash);
        $count = DB::table('users_activation')->where('hash', '=', $hash)->count();
        if($count > 0) $this->getHash();
        return $hash;
    }
    
    
    public function userActivation($hash){
        $hash = filter_var($hash, FILTER_SANITIZE_STRING);
        $user = DB::table('users_activation')
            ->where('hash', '=', $hash)
            ->select('user_id AS userId')
            ->get();
        
        
        $count = DB::table('users')
            ->where('id', '=', $user[0]->userId)
            ->update(['activated' => 1]);
        DB::table('users_activation')
            ->where('user_id', '=', $user[0]->userId)
            ->delete();
        
        return response()->json($count, 200);
    }
    
    
    
    public function signin(Request $request){
        
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        
        $userNotActivated = User::where(['email' => $request->input('email'), 'activated' => 0])->exists();
        
        if($userNotActivated) return response()->json(['error' => 'Worng email or password'], 401);
        
        $credentials = $request->only('email', 'password');
        
        try{
            if(!$token = JWTAuth::attempt($credentials)){
                return response()->json(['error' => 'Worng email or password'], 401);
            }
        }catch(JWTException $e){
            return response()->json(['error'=> 'Coud not create token'], 500);
        }
        
        return response()->json(['token' => $token], 200);
    }
    
    
    public function updateUser(Request $request){
        
        $user = JWTAuth::parseToken()->toUser();
        
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'oldpassword' => 'required'
        ]);
        
        $name = $request->input('name');
        $email = $request->input('email');
        $password = bcrypt($request->input('password'));
        $oldpassword = $request->input('oldpassword');
        
        if($user->email != $email){
            $count = DB::table('users')
                ->select()
                ->where('email', '=', $email)
                ->count();
            return response()->json(['email'=>'used'], 400);
        }
        
        try{
            if(!$token = JWTAuth::attempt(['email' => $email, 'password' => $oldpassword])){
                return response()->json(['oldpassword' => 'wrong'], 400);
            }
        }catch(JWTException $e){
            return response()->json(['oldpassword' => 'wrong'], 400);
        }
        
        $affectedRows = DB::table('users')
                ->where('id', '=', $user->id)
                ->update(['name' => $name, 'email' => $email, 'password' => $password]);
        
        return response()->json(['message' => 'User credentilas updated!'], 201);
    }
    
   
    
    public function loginEdit(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        
        $data = DB::table('users')
                ->select('name', 'email')
                ->where('id', '=', $user->id)
                ->get();
        return response()->json([$data], 200);
    }
    
    public function emailCheck(Request $request){
        
        $this->validate($request, [
            'email' => 'required|email|unique:users'
        ]);
        
        return response()->json(true, 200);
    }
}