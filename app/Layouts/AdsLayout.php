<?php

namespace App\Layouts;

use Orchid\Platform\Layouts\Rows;
use Orchid\Platform\Platform\Fields\TD;
use Orchid\Platform\Fields\Field;

class AdsLayout extends Rows
{
    /**
     * Views

     * @return array
     * @throws \Orchid\Platform\Exceptions\TypeException
     */
    public function fields(): array
    {
        return [

            Field::tag('picture')
                ->name('profile.avatar')
                ->title('Аватар')
                ->width(200)
                ->height(200),

            Field::tag('input')
                ->name('profile.name')
                ->required()
                ->title('Псевдоним'),

            Field::tag('input')
                ->name('profile.email')
                ->required()
                ->readonly()
                ->disable()
                ->title('Электронная почта'),

            Field::tag('input')
                ->name('profile.first_name')
                ->required()
                ->title('Имя'),

            Field::tag('input')
                ->name('profile.last_name')
                ->required()
                ->title('Фамилия'),

        ];
    }
}
