<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/// VENDOR ROUTE
Route::post('/vendor',['uses' => 'VendorController@postVendor', 'middleware' => 'auth.jwt']);

Route::get('/vendor/{id}', ['uses' => 'VendorController@getVendor', 'middleware' => 'auth.jwt']);

Route::get('/vendors', ['uses' => 'VendorController@getVendors', 'middleware' => 'auth.jwt']);

Route::put('/vendor/{id}', ['uses' => 'VendorController@putVendor', 'middleware' => 'auth.jwt']);

Route::delete('/vendor/{id}', ['uses' => 'VendorController@deleteVendor', 'middleware' => 'auth.jwt']);


/// USER ROUTE

Route::post('/user', ['uses' => 'UserController@signUp']);

Route::post('/user/signin', ['uses' => 'UserController@signin']);

Route::get('/user/emailcheck', ['uses' => 'UserController@emailcheck']);

Route::put('/user', ['uses' => 'UserController@updateUser', 'middleware' => 'auth.jwt']);

Route::post('/passvalidation', ['uses' => 'UserController@oldPasswordValidate', 'middleware' => 'auth.jwt']);

Route::get('/loginedit', ['uses' => 'UserController@loginEdit', 'middleware' => 'auth.jwt']);



/// RECIPIENT ROUTE

Route::post('/recipient', ['uses' => 'RecipientController@postRecipient', 'middleware' => 'auth.jwt']);

Route::get('/recipients', ['uses' => 'RecipientController@getRecipients', 'middleware' => 'auth.jwt']);

Route::put('/recipient/{id}', ['uses' => 'RecipientController@getRecipient', 'middleware' => 'auth.jwt']);

Route::put('/recipient/{id}', ['uses' => 'RecipientController@putRecipient', 'middleware' => 'auth.jwt']);

Route::delete('/recipient/{id}', ['uses' => 'RecipientController@deleteRecipient', 'middleware' => 'auth.jwt']);

/// Main Items List

Route::get('/items/all', ['uses' => 'MainItemsController@getAllItems', 'middleware' => 'auth.jwt']);

Route::get('/items/{id}', ['uses' => 'MainItemsController@getItems', 'middleware' => 'auth.jwt']);
    
/// USER ITEMS LIST

Route::get('useritems', ['uses' => 'UserItemsController@getItems', 'middleware' => 'auth.jwt']);

Route::post('useritem', ['uses' => 'UserItemsController@postItem', 'middleware' => 'auth.jwt']);

Route::put('useritem/{id}', ['uses' => 'UserItemsController@putItem', 'middleware' => 'auth.jwt']);

Route::delete('useritem/{id}', ['uses' => 'UserItemsController@deleteItem', 'middleware' => 'auth.jwt']);






/// Group LIST

Route::get('groups', ['uses' => 'GroupController@getGroups', 'middleware' => 'auth.jwt']);

Route::post('group', ['uses' => 'GroupController@postGroup', 'middleware' => 'auth.jwt']);

Route::put('group/{id}', ['uses' => 'GroupController@putGroup', 'middleware' => 'auth.jwt']);

Route::delete('group/{id}', ['uses' => 'GroupController@deleteGroup', 'middleware' => 'auth.jwt']);


/// CUSTOM ITEM 

Route::post('customitem', ['uses' => 'CustomItemController@postCustomItem', 'middleware' => 'auth.jwt']);


/// ORDER /// ORDER LIST

Route::get('orders', ['uses' => 'OrderController@getOrders', 'middleware' => 'auth.jwt']);

Route::get('lastorders', ['uses' => 'OrderController@getLastOrders', 'middleware' => 'auth.jwt']);

Route::get('order/{id}', ['uses' => 'OrderController@getOrder', 'middleware' => 'auth.jwt']);

Route::post('order', ['uses' => 'OrderController@postOrder', 'middleware' => 'auth.jwt']);

Route::get('orderlist', ['uses' => 'OrderController@getOrderList', 'middleware' => 'auth.jwt']); 

Route::get('orderlistforupdate', ['uses' => 'OrderController@getUpdateList', 'middleware' => 'auth.jwt']);

Route::put('orderupdate', ['uses' => 'OrderController@putOrder', 'middleware' => 'auth.jwt']); 


/// SUSPEND ORDER 

Route::get('suspendedorder', ['uses' => 'SuspendedOrderController@getSuspendedOrder', 'middleware' => 'auth.jwt']);

Route::post('suspendedorder', ['uses' => 'SuspendedOrderController@postSuspendedOrder', 'middleware' => 'auth.jwt']);

Route::put('suspendedorder', ['uses' => 'SuspendedOrderController@putSuspendedOrder', 'middleware' => 'auth.jwt']);

Route::delete('suspendedorder', ['uses' => 'SuspendedOrderController@deleteSuspendedOrder', 'middleware' => 'auth.jwt']);



/// ITEM NOTE


Route::post('itemsnote/{id}', ['uses' => 'ItemNoteController@postItemNote', 'middleware' => 'auth.jwt']);

Route::put('itemsnote/{id}', ['uses' => 'ItemNoteController@putItemNote', 'middleware' => 'auth.jwt']);

Route::delete('itemsnote/{id}', ['uses' => 'ItemNoteController@deleteItemNote', 'middleware' => 'auth.jwt']);


/// ACCOUNT 

Route::post('accounts', ['uses' => 'AccountController@postAccount', 'middleware' => 'auth.jwt', 'middleware' => 'auth.jwt']);

Route::put('accounts', ['uses' => 'AccountController@putAccount', 'middleware' => 'auth.jwt', 'middleware' => 'auth.jwt']);

Route::get('accounts', ['uses' => 'AccountController@getAccount', 'middleware' => 'auth.jwt', 'middleware' => 'auth.jwt']);

/// EMAILS

Route::get('accountactivation/{id}', ['uses' => 'UserController@userActivation']);



    
    