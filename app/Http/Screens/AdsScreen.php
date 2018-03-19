<?php

namespace App\Http\Screens;

use Illuminate\Http\Request;
use Orchid\Platform\Screen\Screen;
use App\Layouts\AdsLayout;

class AdsScreen extends Screen
{
    /**
     * Display header name
     *
     * @var string
     */
    public $name = 'AdsScreen';

    /**
     * Display header description
     *
     * @var string
     */
    public $description = 'AdsScreen';

    /**
     * Query data
     *
     * @return array
     */
    public function query() : array
    {
        return [];
    }

    /**
     * Button commands
     *
     * @return array
     */
    public function commandBar() : array
    {
        return [];
    }

    /**
     * Views
     *
     * @return array
     * @throws \Orchid\Platform\Exceptions\TypeException
     */
    public function layout() : array
    {
        return [
            AdsLayout::class,
        ];
    }
}
