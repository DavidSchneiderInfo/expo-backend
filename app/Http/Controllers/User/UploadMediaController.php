<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Profile\Actions\UploadMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadMediaController extends Controller
{
    public function __construct(
        private readonly UploadMedia $uploadMedia
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'media' => 'required|array',
            'media.*' => 'required|file',
        ]);

        /** @var User $user */
        $user = $request->user();

        foreach ($request->file('media') as $media)
        {
            $this->uploadMedia->upload($user, $media);
        }

        return response()->json();
    }
}
