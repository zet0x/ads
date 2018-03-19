<?
namespace App\Http\Composer;

use Orchid\Platform\Kernel\Dashboard;
use App\Ad;
use App\Helpers\Ads;

class MenuComposer
{
    /**
     * MenuComposer constructor.
     *
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }
    /**
     *
     */
    public function compose()
    {
        $new_ads = Ads::new();

        $this->dashboard->menu->add('Main', [
            'slug'   => 'Ads',
            'icon'   => 'icon-briefcase',
            'route'  => '#',
            'label'  => trans('dashboards/menu.moderation_ads'),
            'childs' => true,
            'main'   => true,
            'active' => 'dashboard.ads.*',
            'sort'   => 6000,
        ]);

        foreach ($new_ads as $key => $new_ad)
        {
            if($key == 0)
            {
                $this->dashboard->menu->add('Ads', [
                    'slug'   => $new_ad->id,
                    'icon'   => 'icon-plus',
                    'route'  => route('dashboard.screens.ads.edit'),
                    'groupname'  => trans('dashboards/menu.ads'),
                    'label'  => $new_ad->title,
                    'childs' => false,
                    'main'   => false,
                    'sort'   => $new_ad->id,
                ]);   
            }
            else
            {
                $this->dashboard->menu->add('Ads', [
                    'slug'   => $new_ad->id,
                    'icon'   => 'icon-plus',
                    'route'  => route('dashboard.screens.ads.edit'),
                    'label'  => $new_ad->title,
                    'childs' => false,
                    'main'   => false,
                    'sort'   => $new_ad->id,
                ]);  
            }
        }

        
    }
}