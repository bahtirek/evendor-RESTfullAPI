<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;

class ItemNoteController extends Controller
{
    
    
    /*public function getItemNote(){
        $user = JWTAuth::parseToken()->toUser();
        
        $groups = DB::table('items_note')
            ->select('note', 'item_id')
            ->where('user_id', '=', $user->id)
            ->get();
        
        
        return response()->json($groups, 200);
    }*/
      
    
    public function postItemNote(Request $request, $id){
        $user = JWTAuth::parseToken()->toUser();
        $note = filter_var($request->input('note'), FILTER_SANITIZE_STRING);
        if(!$id) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        DB::table('items_note')->insert(['user_id' => $user->id, 'note' => $note, 'item_id' => $id]);
        
        return response()->json($request, 200);
    }
    
    
    public function putItemNote(Request $request, $id){
        $user = JWTAuth::parseToken()->toUser();
        if(!$id) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $note = filter_var($request->input('note'), FILTER_SANITIZE_STRING);
        $affectedRows = DB::table('items_note')
                ->where('item_id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->update(['note'=> $note]);
        
        return response()->json($affectedRows, 200);
    }
    
    public function deleteItemNote(Request $request, $id){
        $user = JWTAuth::parseToken()->toUser();
        if(!$id) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $affectedRows = DB::table('items_note')
                ->where('item_id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->delete();
        
        return response()->json($affectedRows, 200);
    }
    
    
    
    
   
    
}