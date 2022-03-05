<?php

namespace Tests\Feature\Traits;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait ImageTrait
{
    public function imageFactory(?string $name = null, ?UploadedFile $file = null) : Image
    {
        $name = $name ?? Str::random();
        $file = $file ?? UploadedFile::fake()->image('random.jpg');

        $image = new Image();
        $image->name = $name;
        $image->save();

        $image->setImage($file);

        return $image;
    }
}
