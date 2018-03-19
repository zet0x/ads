<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Ad;

class AccountController extends Controller
{
	
	public function index(Request $request)
	{
		if(!empty($request->name) and Auth::user()->name != $request->name)
		{
			$user = Auth::user();
			$user->name = $request->name;
			$user->save();
		}
		return view('pages.account.edit',[
    		
    		]);
	}
	public function ads()
	{
		$user = Auth::user();

		$ads = Ad::where('user_id',$user->id)
				->get();

		return view('pages.account.ads',[
			"arResult" => $ads,
			]);
	}
	public function add()
	{

		return view('pages.account.add',[

			]);
	}    
	public function favorite()
	{
		return view('pages.account.favorite',[

			]);
	}
}
