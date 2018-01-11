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

Route::get('/home', 'HomeController@index');

Route::any('/', 'HomeController@index');

Route::group(['prefix' => 'report'], function () {
    Route::any('xyjh', 'Report\\XYJHController@index')->name('report.xyjh');
    Route::get('xyjh/export','Report\\XYJHController@export')->name('export.xyjh');

    Route::any('xmsyf', 'Report\\XMSYFController@index')->name('report.xmsyf');
    Route::get('xmsyf/statistics', 'Report\\XMSYFController@statistics')->name('report.xmsyf.statistics');
    Route::get('xmsyf/export','Report\\XMSYFController@export')->name('export.xmsyf');

    Route::any('jfkdj', 'Report\\JFKDJController@index')->name('report.jfkdj');
    Route::get('jfkdj/export','Report\\JFKDJController@export')->name('export.jfkdj');

    Route::any('customer', 'Report\\CustomerController@index')->name('report.customer');
    Route::get('customer/statistics', 'Report\\CustomerController@statistics')->name('report.customer.statistics');
    Route::get('customer/export','Report\\CustomerController@export')->name('export.customer');

    Route::any('meeting', 'Report\\MeetingController@index')->name('report.meeting');
    Route::get('meeting/{id}', 'Report\\MeetingController@show')->name('report.meeting.show');

    Route::any('inventory', 'Report\\InventoryController@index')->name('report.inventory');
    Route::get('inventory/statistics', 'Report\\InventoryController@statistics')->name('report.inventory.statistics');
    Route::get('inventory/export','Report\\InventoryController@export')->name('export.inventory');

    Route::any('heritages', 'Report\\HeritagesController@index')->name('report.heritages');
    Route::any('heritages/export', 'Report\\HeritagesController@export')->name('export.heritages');

});

