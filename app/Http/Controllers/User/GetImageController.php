<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GetImageController extends Controller
{
    public function __invoke(Request $request, string $profileId, string $fileName): StreamedResponse|JsonResponse
    {
        try {
            Profile::query()->findOrFail($profileId);

            $fullPath = 'profiles'
                . DIRECTORY_SEPARATOR
                . $profileId
                . DIRECTORY_SEPARATOR
                . $fileName;

            if(!Storage::exists($fullPath)) {
                throw new ModelNotFoundException();
            }
                return Storage::download($fullPath);

        } catch (ModelNotFoundException)
        {
            return response()->json([
                'message'=>'File not found'
            ], 404);
        }
    }
}
