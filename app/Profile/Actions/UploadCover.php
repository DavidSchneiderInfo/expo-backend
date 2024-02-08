<?php

declare(strict_types=1);

namespace App\Profile\Actions;

use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadCover
{
    public function upload(Profile $profile, UploadedFile $medium): Profile
    {
        $fileName = 'cover.'
            . $medium->extension();

        $fullPath = Storage::disk('profiles')->putFileAs($profile->id, $medium, $fileName);
        $profile->update([
            'cover' => $fullPath
        ]);

        return $profile;
    }
}
