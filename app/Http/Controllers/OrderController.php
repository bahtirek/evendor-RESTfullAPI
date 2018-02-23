<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendors;
use JWTAuth;
use Purifier;
use DateTime;
use stdClass;
use Mail;
use App\Mail\FoodConn_Order;

class OrderController extends Controller
{
    
    
    public function getOrderList(){
        $user = JWTAuth::parseToken()->toUser();
        $list = DB::table('user_items_list')
                ->where('user_items_list.user_id', '=', $user->id)
                ->join('main_items_list', 'main_items_list.id', '=', 'user_items_list.item_id')
                ->join('vendors', 'vendors.id', '=', 'user_items_list.vendor_id')
                ->leftJoin('groups', 'groups.id', '=', 'user_items_list.group_id')
                ->leftJoin("items_note",function($join) use($user){
                    $join->on('items_note.item_id', '=', 'user_items_list.item_id');
                    $join->where('items_note.user_id', '=', $user->id);
                })
                ->select('user_items_list.item_id AS id', 'main_items_list.name', 'main_items_list.family', 'user_items_list.pack', 'user_items_list.group_id AS groupId', 'items_note.note', 'groups.name as groupName', 'vendors.name as vendorName', 'vendors.id as vendorId')
               ->get();
        foreach($list as $item){
            $item->quantity = 0;
            if($item->groupName == null) $item->groupName = 'Ungrouped';
        }
            
        return response()->json($list, 200);
    }
    
    
    public function getUpdateList(){
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
            
            foreach($items as $item){
                $item->vendorName = $vendor->name;
                $item->vendorId = $vendor->id;
                $item->quantity = 0;
            }
            
            $vendor->items = $items;
        }
        
        return response()->json($vendors, 200);
        
    }
      
    
    public function postOrder(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        DB::table('orders')->insert(['user_id' => $user->id]);
        $id = DB::getPdo()->lastInsertId();
        
        $newOrder = $request->input('order');
        foreach($newOrder as $input){
            $quantity = filter_var($input['quantity'], FILTER_SANITIZE_STRING);
            $pack = filter_var($input['pack'], FILTER_SANITIZE_STRING);
            $itemId = filter_var($input['id'], FILTER_SANITIZE_STRING);
            $vendorId = filter_var($input['vendor'], FILTER_SANITIZE_STRING);
            
            DB::table('all_orders')->insert(['order_id'=> $id, 'pack' => $pack, 'vendor_id' => $vendorId, 'quantity' => $quantity, 'item_id' => $itemId]);
        }
        
        $notes = $request->input('note');
        
        foreach($notes as $input){
            $note = filter_var($input['note'], FILTER_SANITIZE_STRING);
            $vendorId = filter_var($input['vendorId'], FILTER_SANITIZE_STRING);
            if($note){
               DB::table('note_for_vendor')->insert(['order_id'=> $id, 'vendor_id' => $vendorId, 'note' => $note]); 
            }
            
        }
        
        $order = $this->order($id, $user);
        $this->sendOrderEmail($order, $user, $id, false);
        return response()->json($order, 200);
    }
    

    
    public function getOrder($id){
        $user = JWTAuth::parseToken()->toUser();
        if(!$id) return response()->json(['error'=>'Sorry something went wrong!'], 404);
        $vendors = $this->order($id, $user);
        return response()->json($vendors, 200);
    }
    
    
    

    
    public function getLastOrders(){
        $user = JWTAuth::parseToken()->toUser();
        $dateList = DB::table('orders')
            ->where('user_id', '=', $user->id)
            ->select('id', DB::raw('DATE(`date`) AS date'))
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get();
        foreach($dateList as $list){
            $date = new DateTime($list->date);
            $list->date = $date->format('l, F j, Y');
        }
        return response()->json($dateList, 200);
    }
    
    public function getOrders(Request $request){
        $user = JWTAuth::parseToken()->toUser();
        $date = filter_var($request['date'], FILTER_SANITIZE_STRING);
        $dateList = DB::table('orders')
            ->where('user_id', '=', $user->id)
            ->where('date', 'like', "$date%")
            ->select('id', DB::raw('DATE(`date`) AS date'))
            ->orderBy('id', 'DESC')
            ->get();
        foreach($dateList as $list){
            $date = new DateTime($list->date);
            $list->date = $date->format('l, F j, Y');
        }
        return response()->json($dateList, 200);
    }
    
   
    







    public function putOrder(Request $request){
        $user = JWTAuth::parseToken()->toUser();
       
        $newOrder = $request->input('order');
        $orderId = filter_var($request->input('orderId'), FILTER_SANITIZE_STRING);
        
        foreach($newOrder as $input){
            $quantity = filter_var($input['quantity'], FILTER_SANITIZE_STRING);
            $pack = filter_var($input['pack'], FILTER_SANITIZE_STRING);
            $itemId = filter_var($input['id'], FILTER_SANITIZE_STRING);
            $vendorId = filter_var($input['vendor'], FILTER_SANITIZE_STRING);
            $input['quantity'] = $quantity;
            $input['pack'] = $pack;
            $input['id'] = $itemId;               
            $input['vendor'] = $vendorId;
            $input['note'] = '';
                
                
           $affectedRows = DB::table('updated_orders')->insert(['order_id'=> $orderId, 'pack' => $pack, 'vendor_id' => $vendorId, 'quantity' => $quantity, 'item_id' => $itemId]);
        }
        
        $notes = $request->input('note');
        
        $newNotes = [];
        
        foreach($notes as $input){
            $newNote = (object)['note' => '', 'vendorId' => ''];
            $note = filter_var($input['note'], FILTER_SANITIZE_STRING);
            $vendorId = filter_var($input['vendorId'], FILTER_SANITIZE_STRING);
            if($note){
                DB::table('note_for_vendor')->insert(['order_id'=> $orderId, 'vendor_id' => $vendorId, 'note' => $note]); 
                $newNote->note = $note;
                $newNote->vendorId = $vendorId;
                $newNotes[] = $newNote;
            }
        }
        
        $vendors = DB::table('vendor_user')
            ->select('vendors.name', 'vendors.id')
            ->join('vendors', 'vendors.id','=', 'vendor_user.vendor_id')
            ->where('vendor_user.user_id', '=', $user->id)
            ->get();
        
        foreach($vendors as $vendor){
            $items = [];
            $vendor->note = [];
            foreach($newNotes as $note){
                if($note->vendorId == $vendor->id){
                    $vendor->note[] = $note;
                }
            }
            foreach($newOrder as $item){
                if($vendor->id == $item['vendor']){
                    $items[] = (object)$item; 
                }
            }
            
            $vendor->items = $items;
        }
        
        
        $this->sendOrderEmail($vendors, $user, $orderId, true);
        
        return response()->json($vendors, 200);
    }
    
    


    
    
    public function order($id, $user){
        
        $vendors = DB::table('all_orders')
            ->where('order_id', '=', $id)
            ->select('all_orders.vendor_id AS id', 'vendors.name')
            ->join('vendors', 'vendors.id', '=', 'all_orders.vendor_id')
            ->groupBy('vendor_id')
            ->get();
        
        $updatedVendors = DB::table('updated_orders')
            ->where('order_id', '=', $id)
            ->select('updated_orders.vendor_id AS id', 'vendors.name')
            ->join('vendors', 'vendors.id', '=', 'updated_orders.vendor_id')
            ->groupBy('vendor_id')
            ->get();
        
        
        foreach($updatedVendors as $updatedVendor){
            $match = 0;
            foreach($vendors as $vendor){
                if($vendor->id == $updatedVendor->id){
                    $match++;
                }
            }
            if($match == 0){
                $vendors[] = $updatedVendor;
            }
        }
        
        
        
        foreach($vendors as $vendor){
            $order = DB::table('all_orders')
                ->where('all_orders.order_id', '=', $id)
                ->where('all_orders.vendor_id', '=', $vendor->id)
                ->join('main_items_list', 'main_items_list.id', '=', 'all_orders.item_id')
                ->leftJoin("items_note",function($join) use($user){
                    $join->on('items_note.item_id', '=', 'all_orders.item_id');
                    $join->where('items_note.user_id', '=', $user->id);
                })
                ->select('main_items_list.name', 'main_items_list.id AS id', 'all_orders.pack', 'items_note.note', 'all_orders.quantity')
               ->get();
            
            
            
            $vendor->items = $order;
            
            $updates = DB::table('updated_orders')
                ->where('updated_orders.order_id', '=', $id)
                ->where('updated_orders.vendor_id', '=', $vendor->id)
                ->join('main_items_list', 'main_items_list.id', '=', 'updated_orders.item_id')
                ->select('main_items_list.name', 'item_id AS id', 'pack', 'quantity', 'vendor_id AS vendorId', 'date' )
                ->get();
            
            $vendor->updates = $updates;
            
            $note = DB::table('note_for_vendor')
                ->where('order_id', '=', $id)
                ->where('vendor_id', '=', $vendor->id)
                ->select('note', 'date')
                ->get();
            $vendor->note = $note;
            
        } 
        return $vendors;
    }
    
    
    public function sendOrderEmail($order, $user, $orderId, $update){
        $account = DB::table('accounts')
            ->where('user_id', '=', $user->id)
            ->select('id', 'email', 'company', 'address', 'city', 'state', 'zipcode', 'phone')
            ->get();
        
        foreach($order as $vendor){
            $recipients = DB::table('recipient_vendor')
                ->where('vendor_id', '=', $vendor->id)
                ->join('recipients', 'recipients.id', '=', 'recipient_vendor.recipient_id')
                ->select('recipients.email', 'recipients.name')
                ->get();
           
            if(count($vendor->items) > 0){
                Mail::to($recipients)->send(new FoodConn_Order($vendor->items, $vendor->note, $vendor->name, $account[0], $orderId, $update));
            }
            
        }
        
    }
    
}

    /*public function order($id, $user){
        $vendors = DB::table('all_orders')
            ->where('order_id', '=', $id)
            ->select('all_orders.vendor_id AS id', 'vendors.name')
            ->join('vendors', 'vendors.id', '=', 'all_orders.vendor_id')
            ->groupBy('vendor_id')
            ->get();
        
        
        
        
        foreach($vendors as $vendor){
            $order = DB::table('all_orders')
                ->where('all_orders.order_id', '=', $id)
                ->where('all_orders.vendor_id', '=', $vendor->id)
                ->join('main_items_list', 'main_items_list.id', '=', 'all_orders.item_id')
                ->leftJoin("items_note",function($join) use($user){
                    $join->on('items_note.item_id', '=', 'all_orders.item_id');
                    $join->where('items_note.user_id', '=', $user->id);
                })
                ->select('main_items_list.name', 'main_items_list.id AS id', 'all_orders.pack', 'items_note.note', 'all_orders.quantity', 'all_orders.vendor_id AS vendorId')
               ->get();
            
            
            
            $vendor->items = $order;
            
            
            $note = DB::table('note_for_vendor')
                ->where('order_id', '=', $id)
                ->where('vendor_id', '=', $vendor->id)
                ->select('note', 'date')
                ->get();
            $vendor->note = $note;
            
        }
        
        
        
        
        return $vendors;
    }
    
    
        
    /*public function putOrder(Request $request){
        $user = JWTAuth::parseToken()->toUser();
       
        $newOrder = $request->input('order');
        $orderId = filter_var($request->input('orderId'), FILTER_SANITIZE_STRING);
        
        foreach($newOrder as $input){
            $quantity = filter_var($input['quantity'], FILTER_SANITIZE_STRING);
            $pack = filter_var($input['pack'], FILTER_SANITIZE_STRING);
            $itemId = filter_var($input['id'], FILTER_SANITIZE_STRING);
            $vendorId = filter_var($input['vendor'], FILTER_SANITIZE_STRING);
            
            $affectedRows = 0;
            
            $affectedRows = DB::table('all_orders')
               ->where('all_orders.order_id', '=', $orderId)
               ->where('all_orders.item_id', '=', $itemId)
               ->update(['pack' => $pack, 'vendor_id' => $vendorId, 'quantity' => $quantity]);
            
            if($affectedRows == 0){
                DB::table('all_orders')->insert(['order_id'=> $id, 'pack' => $pack, 'vendor_id' => $vendorId, 'quantity' => $quantity, 'item_id' => $itemId]);
            }
        }
        
        $notes = $request->input('note');
        
        foreach($notes as $input){
            if($input['note']){
                $note = filter_var($input['note'], FILTER_SANITIZE_STRING);
                $vendorId = filter_var($input['vendorId'], FILTER_SANITIZE_STRING);
                if($note){
                   DB::table('note_for_vendor')->insert(['order_id'=> $orderId, 'vendor_id' => $vendorId, 'note' => $note]); 
                }
            }   
        }
        
        
        return response()->json($notes, 200);
    }
    */


