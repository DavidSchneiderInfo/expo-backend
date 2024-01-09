<?php

use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\Medium;
use App\Models\User;
use App\Repositories\MatchRepository;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

Route::post('auth/sign-in', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    /** @var User $user */
    $user = User::query()
        ->where('email', $credentials['email'])
        ->first();

    if($user === null || Auth::validate($credentials) !== true)
    {
        return response()->json([
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    return response()->json([
        'user' => $user,
        'token' => $user->createToken('mobile')->plainTextToken,
    ]);
});

Route::post('auth/sign-up', function (Request $request) {
    $request->validate([
        'username' => 'required|string|between:6,32',
        'email' => 'required|email',
        'password' => 'required',
        'birthday' => 'required|date',
    ]);

    try {
        $user = new User([
            'name' => $request->get('username'),
            'password' => bcrypt($request->get('password')),
            'email' => $request->get('email'),
            'birthday' => $request->get('birthday'),
        ]);
        $user->save();
    }
    catch (UniqueConstraintViolationException)
    {
        return response()->json([
            'message' => 'These credentials have already been taken.',
        ], 409);
    }

    return response()->json([
        'user' => $user,
        'token' => $user->createToken('mobile')->plainTextToken,
    ]);
});

Route::middleware('auth:sanctum')->group(function ($router) {
    $router->get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    $router->patch('/user', function (Request $request) {
        $requestData = $request->validate([
            'birthday' => 'required',
            'name' => 'required',
            'email' => 'required',
            'bio' => 'required',
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->update($requestData);

        return new UserResource($user);
    });

    $router->get('/match', function (Request $request, MatchRepository $repo) {
        return ProfileResource::collection(
            $repo->getProfiles($request->user())
        );
    });

    $router->post('/match', function (Request $request) {
        /** @var User $givingUser */
        $givingUser = $request->user();

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        /** @var User $receivingUser */
        $userId = (int)$request->get('user_id');
        $receivingUser = User::query()->findOrFail($userId);

        $givingUser->likesToUsers()->save($receivingUser);

        return response()->json([
            'match' => $givingUser->likesFromUsers()->where('user_id', $userId)->count() > 0
        ]);
    });

    $router->post('upload/media', function (Request $request) {
        $request->validate([
            'media' => 'required|array',
            'media.*' => 'required|file',
        ]);

        /** @var User $user */
        $user = $request->user();

        foreach ($request->file('media') as $media)
        {
            /** @var UploadedFile $media */
            $fileName = Str::uuid()->toString()
                . '.'
                . $media->extension();

            $fullPath = Storage::disk('profiles')->putFileAs($user->id, $media, $fileName);

            $user->media()->save(new Medium([
                'path' => $fullPath,
            ]));
        }

        return response()->json();
    });
});

