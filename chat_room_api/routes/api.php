<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('test', function () {
    echo 'test';
});

Route::prefix('v1.0')->middleware(['captcha.check', 'auth:api'])->group(function () {
    Route::get('getUserInfo', 'User\UserController@getUserInfo');

});

//注册
Route::get('/register', function () {
    return 'login';
})->name('register');

Route::any('login', function () {
    return redirect('/');
    echo '未授权请求';
})->name('login');
