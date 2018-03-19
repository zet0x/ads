<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Widgets
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */
    'widgets' => [
        'HeaderMenuWidget' => App\Http\Widgets\HeaderMenuWidget::class,
        'SlideBarMenuWidget' => App\Http\Widgets\Account\SlideBarMenuWidget::class,
    ],
];
