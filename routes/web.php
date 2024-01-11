<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Home;

date_default_timezone_set("Asia/Bangkok");
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
Route::get('/', Home::class)->name('home')->middleware('auth');
Route::get('login', App\Http\Livewire\Login::class)->name('login');

// All login
Route::group(['middleware' => ['auth']], function(){
    Route::get('profile',App\Http\Livewire\Profile::class)->name('profile');
    Route::get('back-to-admin',[App\Http\Controllers\IndexController::class,'backtoadmin'])->name('back-to-admin');

    Route::get('plc',App\Http\Livewire\Plc\Index::class)->name('plc.index');
});

Route::group(['middleware' => ['auth']], function(){
    Route::get('api/get-kepesertaan',[\App\Http\Controllers\Api\KepesertaanController::class,'index'])->name('api.get-kepesertaan');
});

// Administrator
Route::group(['middleware' => ['auth','access:1']], function(){
    Route::get('setting',App\Http\Livewire\Setting::class)->name('setting');
    Route::get('users/insert',App\Http\Livewire\User\Insert::class)->name('users.insert');
    Route::get('user-access', App\Http\Livewire\UserAccess\Index::class)->name('user-access.index');
    Route::get('user-access/insert', App\Http\Livewire\UserAccess\Insert::class)->name('user-access.insert');
    Route::get('users',App\Http\Livewire\User\Index::class)->name('users.index');
    Route::get('users/edit/{id}',App\Http\Livewire\User\Edit::class)->name('users.edit');
    Route::post('users/autologin/{id}',[App\Http\Livewire\User\Index::class,'autologin'])->name('users.autologin');
    Route::get('log-activity',App\Http\Livewire\LogActivity\Index::class)->name('log-activity');
});
