<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Account;
use JWTAuth;
use Excel;

class ExcelController extends Controller
{
	public function import()
	{
		return view('import');
	}
	
	public function importExcel()
	{
		if(Input::hasFile('import_file')){
			$path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
			})->get();
			if(!empty($data) && $data->count()){
				/*foreach ($data as $key => $value) {
					print_r($value);
				}*/
                //print_r($data);
                print_r($data->getHeading());
				
			}
            /*if(!empty($data) && $data->count()){
				foreach ($data as $key => $value) {
					$insert[] = ['items' => $value->items, 'description' => $value->description];
				}
				print_r($insert);
			}*/
		}
		//return back();
	}
}