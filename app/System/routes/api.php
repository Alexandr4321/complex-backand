<?php

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

Route::group(['namespace' => 'Api'], function () {
    Route::group(['prefix' => 'v1'], function () {
        
//        // auth
//        Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
//            Route::post('register', 'AuthController@postRegister')->name('postRegister');
//            Route::post('login', 'AuthController@postLogin')->name('postLogin');
//            Route::post('sendconfirm', 'AuthController@postSendConfirm')->name('postSendConfirm');
//            Route::post('confirm', 'AuthController@postConfirm')->name('postConfirm');
//             Route::get('logout', 'AuthController@getLogout')->name('logout');
//
//            Route::group(['middleware' => 'auth:api'], function() {
//                Route::get('info', 'AuthController@getInfo')->name('getInfo');
//            });
//        });
//
//
//        // passwords
//        Route::group(['prefix' => 'passwords', 'as' => 'password.'], function () {
//            Route::get('find/{token}', 'PasswordResetController@getFind')->name('find');
//            Route::post('create', 'PasswordResetController@postCreate')->name('create');
//            Route::post('reset', 'PasswordResetController@postReset')->name('reset');
//        });
//
//
//        // users
//        Route::group(['prefix' => 'users', 'as' => 'user.'], function () {
//            Route::group(['middleware' => 'auth:api'], function() {
//                Route::get('{user}', 'UserController@get')->name('get');
//                Route::get('', 'UserController@getList')->name('getList');
//                Route::post('', 'UserController@post')->name('post');
//                Route::put('{user}', 'UserController@put')->name('put');
//                Route::delete('{user}', 'UserController@delete')->name('delete');
//
//                Route::patch('{user}/password', 'UserController@patchPassword')->name('patchPassword');
//            });
//        });
    });
});
