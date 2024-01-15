<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RefreshSessionController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Match\GetMatchListController;
use App\Http\Controllers\Match\MatchController;
use App\Http\Controllers\User\UpdateProfileController;
use App\Http\Controllers\User\UploadMediaController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function (Router $router) {

    $router->post('auth/refresh', RefreshSessionController::class);
    $router->patch('user', UpdateProfileController::class);
    $router->post('upload/media', UploadMediaController::class);
    $router->get('match', GetMatchListController::class);
    $router->post('match', MatchController::class);

});

Route::post('auth/sign-in', LoginController::class);
Route::post('auth/sign-up', SignUpController::class);

