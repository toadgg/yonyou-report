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

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

//Route::get('/home', 'HomeController@index');

Route::any('/', 'HomeController@index');

Route::group(['prefix' => 'report'], function () {
    Route::any('xyjh', 'Report\\XYJHController@index')->name('report.xyjh');
    Route::get('xyjh/export','Report\\XYJHController@export')->name('export.xyjh');

    Route::any('xmsyf', 'Report\\XMSYFController@index')->name('report.xmsyf');
    Route::get('xmsyf/export','Report\\XMSYFController@export')->name('export.xmsyf');

    Route::any('heritages', 'Report\\HeritagesController@index')->name('report.heritages');
    Route::any('heritages/export', 'Report\\HeritagesController@export')->name('export.heritages');

});

