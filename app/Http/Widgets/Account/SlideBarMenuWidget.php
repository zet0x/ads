<?php

namespace App\Http\Widgets\Account;

use Orchid\Platform\Widget\Widget;
use Illuminate\Support\Facades\Auth;
use App\Ad;

class SlideBarMenuWidget extends Widget {

    /**
     * @return mixed
     */
    private $arr;

    public function __construct()
    {
    	$user = Auth::user();
    	$arr = array(
    		"ads" => $this-> count_ads($user->id),
    		"favorites" => $this->count_favorite(),
     		);
		$this->arr = $arr;
    }

    private function count_ads($id)
    {
    	$ads = Ad::where('user_id',$id)
    			->count();
		return $ads;
    }

    private function count_favorite()
    {
    	return 0;
    }
    
    public function handler(){
         return view('widget.account.slidebarmenu',[
         	"arResult" => $this->arr,
         	]);
     }

}
