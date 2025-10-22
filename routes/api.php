<?php

use Illuminate\Support\Facades\Route;
use App\System\Classes\AutoRoute;

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


// Auth
AutoRoute::controller(\App\Auth\Controllers\Api\AuthController::class);
AutoRoute::controller(\App\Auth\Controllers\Api\UserController::class);


// Base
AutoRoute::controller(\App\Base\Controllers\Api\FileController::class);
AutoRoute::controller(\App\Base\Controllers\Api\TranslationController::class);


// App



AutoRoute::controller(\App\App\Controllers\Api\FaqController::class);
AutoRoute::controller(\App\App\Controllers\Api\PushController::class);
AutoRoute::controller(\App\App\Controllers\Api\NotificationController::class);
AutoRoute::controller(\App\App\Controllers\Api\ApartmentController::class);
AutoRoute::controller(\App\App\Controllers\Api\AutoController::class);
AutoRoute::controller(\App\App\Controllers\Api\ComplexController::class);
AutoRoute::controller(\App\App\Controllers\Api\GuestAutoController::class);
AutoRoute::controller(\App\App\Controllers\Api\HomeController::class);
AutoRoute::controller(\App\App\Controllers\Api\NewsController::class);
AutoRoute::controller(\App\App\Controllers\Api\PolicyController::class);
AutoRoute::controller(\App\App\Controllers\Api\SupportController::class);



