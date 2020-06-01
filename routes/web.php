<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('servers');
});

Auth::routes();

//Server Routes
Route::get('/servers', 'ServersController@index')->middleware('auth')->name('servers');
Route::get('/servers/add', 'ServersController@addserver')->middleware('auth')->name('addserver');
Route::post('/servers/add', 'ServersController@createserver')->middleware('auth')->name('createserver');
Route::post('/servers/delete/{id}', 'ServersController@deleteserver')->middleware('auth')->name('deleteserver');
Route::post('/servers/edit/{id}', 'ServersController@editserver')->middleware('auth')->name('editserver');
Route::post('/servers/save', 'ServersController@saveserver')->middleware('auth')->name('saveserver');

//Users Routes
Route::get('/users', 'UsersController@index')->middleware('auth')->name('users');
Route::get('/users/add', 'UsersController@adduser')->middleware('auth')->name('adduser');
Route::post('/users/add', 'UsersController@createuser')->middleware('auth')->name('createuser');
Route::post('/users/edit/{id}', 'UsersController@edituser')->middleware('auth')->name('edituser');
Route::post('/users/save', 'UsersController@saveuser')->middleware('auth')->name('saveuser');
Route::post('/users/delete/{id}', 'UsersController@deluser')->middleware('auth')->name('deluser');

//SSH Client Route
Route::post('/ssh/{id}', 'SshController@connectssh')->middleware('auth')->name('connectssh');
Route::get('/sharessh', 'SshController@sharessh')->middleware('auth')->name('sharessh');