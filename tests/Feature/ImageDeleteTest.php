<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Feature\Traits\ImageTrait;
use Tests\TestCase;

class ImageDeleteTest extends TestCase
{
    use RefreshDatabase, ImageTrait;

    public function test_application_delete_images()
    {
        $imageModel = $this->imageFactory();
        $filePath = $imageModel->getImage();

        $response = $this->json('delete', '/api/image/'.$imageModel->id);

        $response->assertOk();

        $imageModel = Image::find($imageModel->id);

        $this->assertNull($imageModel, 'The model stil exists after deletion');

        $this->assertFalse(file_exists($filePath), 'The file still exists after deletion');
    }

    public function test_application_fails_on_delete_not_existent_image()
    {
        $id = Image::query()->max('id') + 1;

        $response = $this->json('delete', '/api/image/'.$id);

        $response->assertStatus(404);
    }
}
