<?php

namespace App\Helpers;

use App\Ad;

class Ads
{
	public static function new()
	{
		$ads = Ad::where('status_id',0)
				->get();
		return $ads;
	}
	public static function published()
	{
		$ads = Ad::where('status_id',1)
				->get();
		return $ads;
	}
	public static function ban()
	{
		$ads = Ad::where('status_id',2)
				->get();
		return $ads; 
	}
	public static function expired()
	{
		$ads = Ad::where('status_id',3)
				->get();
		return $ads;
	}
}