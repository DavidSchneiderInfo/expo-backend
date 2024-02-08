<?php

declare(strict_types=1);

namespace App\Profile\Actions;

use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadAvatar
{
    public function upload(Profile $profile, UploadedFile $medium): Profile
    {
        $fileName = 'avatar.'
            . $medium->extension();

        $fullPath = Storage::disk('profiles')->putFileAs($profile->id, $medium, $fileName);
        $profile->update([
            'avatar' => $fullPath
        ]);

        return $profile;
    }
}
