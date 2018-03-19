<?php

namespace App\Http\Widgets;

use Orchid\Platform\Widget\Widget;
use Orchid\Platform\Core\Models\Menu;
use Illuminate\Support\Facades\App;

class HeaderMenuWidget extends Widget {

    /**
     * @return mixed
     */
     private $menu;
     
     public function __construct()
     {
     	$arMenu = array();
     	$per = 0;
     	$menu = Menu::where('type', 'header')
	    ->get();
	    foreach ($menu as $key => $value) {
	    	if($value->parent == 0)
	    	{
	    		$per = $key;
	    		$arr = [
	    			'parent' =>  $value,
	    			'child' => array(),
	    		];
	    		array_push($arMenu, $arr);
	    	}
	    	else if($value->parent == 2)
	    	{
	    		array_push($arMenu[$per]['child'], $value);
	    	}
	    }

	    $this->menu = $arMenu;
     } 	
     
     public function handler()
     {
         return view('widget.headermenu',[
         	'arResult' => $this->menu,
         	]);
     }

}
