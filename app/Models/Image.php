<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as ImageResizer;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method static Builder|Image name(string $name)
 * @method static Builder|Image nameLike(?string $delta)
 */
class Image extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const MEDIA_COLLECTION_IMAGE = 'image';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_IMAGE)
            ->acceptsFile(function (File $file){
                $mimeType = $file->mimeType;
                return Str::startsWith($mimeType, 'image');
            })
            ->singleFile();
    }

    /**
     * @param string|UploadedFile $file
     * @return void
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function setImage(string|UploadedFile $file) : void
    {
        $this->addMedia($file)->preservingOriginal()->toMediaCollection(self::MEDIA_COLLECTION_IMAGE);
    }

    /**
     * @return Media|null
     */
    public function getImage() : Media|null
    {
        return $this->getFirstMedia(self::MEDIA_COLLECTION_IMAGE);
    }

    /**
     * @param int $width
     * @param int $height
     * @return void
     */
    public function resizeImage(int $width, int $height) : void
    {
        $img = ImageResizer::make($this->getImage()->getPath());

        $img->resize($width, $height, function ($constraint){
            $constraint->aspectRatio();
        })->save($this->getImage()->getPath());
    }

    public function scopeName($query, string $name)
    {
        return $query->where('name', $name);
    }

    public function scopeNameLike($query, ?string $delta)
    {
        if(!$delta){
            return $query;
        }

        return $query->where('name', 'like', "%{$delta}%");
    }
}
