<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Profile\Actions\UploadAvatar;
use App\Profile\Actions\UploadMedia;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class UploadAvatarController extends Controller
{
    public function __construct(
        private readonly UploadAvatar $uploadAvatar,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): ProfileResource
    {
        $this->logger->debug('hit', [
            'method' => $request->method()
        ]);
        $request->validate([
            'avatar' => 'required|file',
        ]);

        /** @var User $user */
        $user = $request->user();

        return new ProfileResource(
            $this->uploadAvatar->upload($user->profile, $request->file('avatar'))
        );
    }
}
