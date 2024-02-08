<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RefreshSessionController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Match\GetMatchListController;
use App\Http\Controllers\Match\MatchController;
use App\Http\Controllers\User\GetImageController;
use App\Http\Controllers\User\UpdateProfileController;
use App\Http\Controllers\User\UploadAvatarController;
use App\Http\Controllers\User\UploadCoverController;
use App\Http\Controllers\User\UploadMediaController;
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
    $router->post('user/avatar', UploadAvatarController::class);
    $router->post('user/cover', UploadCoverController::class);
    $router->post('upload/media', UploadMediaController::class);

    $router->post('match/list', GetMatchListController::class);
    $router->post('match', MatchController::class);

    $router->get('images/{profileId}/{imageName}', GetImageController::class);
});

Route::post('auth/sign-in', LoginController::class);
Route::post('auth/sign-up', SignUpController::class);

