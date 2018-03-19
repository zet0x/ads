<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::screen('/dashboard/ads/edit', '\App\Http\Screens\AdsScreen','dashboard.screens.ads.edit');

//Route for guest
Route::group(['namespace'=>'Guest'],function(){
	Route::get('/', 'IndexController@index')->name('home');
});

//Route for Login
Route::group(['namespace'=>'Login','middleware'=>'auth'],function(){
	Route::any('/account','AccountController@index');
	Route::any('/account/add','AccountController@add')->name('account.add_ads');
	Route::any('/account/edit','AccountController@index')->name('account.edit');
	Route::any('/account/ads','AccountController@ads')->name('account.ads');
	Route::any('/account/favorite','AccountController@favorite')->name('account.favorite');
});


Auth::routes();
