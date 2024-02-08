<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Profile\Actions\UploadAvatar;
use App\Profile\Actions\UploadCover;
use App\Profile\Actions\UploadMedia;
use Illuminate\Http\Request;

class UploadCoverController extends Controller
{
    public function __construct(
        private readonly UploadCover $uploadCover
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): ProfileResource
    {
        $request->validate([
            'cover' => 'required|file',
        ]);

        /** @var User $user */
        $user = $request->user();

        return new ProfileResource(
            $this->uploadCover->upload($user->profile, $request->file('cover'))
        );
    }
}
