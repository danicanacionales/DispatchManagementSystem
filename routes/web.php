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

Route::get('/', 'LoginController@index');
Route::get('/login', 'LoginController@store');
Route::get('/register', 'PagesController@register');
Route::post('/regacc', 'LoginController@create');

Route::get('/logout', function () {
    session() -> flush();
    return view('auth.login');
});

Route::get('/ongoing', 'OngoingEventsController@index');
Route::get('/conversations/{event_id?}', function($event_id){
    return $event_id;
});

Route::get('/addevent', 'EventsController@index');


Route::get('/map', 'MapController@index');