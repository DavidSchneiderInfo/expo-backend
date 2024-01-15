<?php

declare(strict_types=1);

namespace App\Profile\Actions;

use App\Models\Medium;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadMedia
{
    public function upload(User $user, UploadedFile $medium): Medium
    {
        $fileName = Str::uuid()->toString()
            . '.'
            . $medium->extension();

        $fullPath = Storage::disk('profiles')->putFileAs($user->id, $medium, $fileName);
        $medium = new Medium([
            'path' => $fullPath,
        ]);
        $user->profile->media()->save($medium);
        $medium->refresh();
        return $medium;
    }
}
