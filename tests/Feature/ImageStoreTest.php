<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImageStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_stores_an_image()
    {
        $name = Str::random();
        $file = UploadedFile::fake()->image('random.jpg');
        $fileContent = file_get_contents($file->getPathname());
        $response = $this->json('post', '/api/image', [
            'file' => $file,
            'name' => $name,
        ]);

        $response->assertStatus(201);

        /** @var Image $imageModel */
        $imageModel = Image::name($name)->first();

        $this->assertNotNull($imageModel, 'Model not found');

        $mediaObject = $imageModel->getImage();

        $this->assertNotNull($mediaObject, 'Media object not found');

        $this->assertEquals($fileContent, file_get_contents($mediaObject->getPath()), 'File loaded differs from uploaded file');
    }

    public function test_application_fails_on_not_image_file_stored()
    {
        $name = Str::random();
        $file = UploadedFile::fake()->create('random.txt', '2048', 'text/plain');
        $response = $this->json('post', '/api/image', [
            'file' => $file,
            'name' => $name,
        ]);

        $response->assertStatus(422);
    }

    public function test_application_fails_on_add_images_with_same_name()
    {
        $name = Str::random();
        $file = UploadedFile::fake()->image('random.jpg');
        $this->json('post', '/api/image', [
            'file' => $file,
            'name' => $name,
        ]);

        $file = UploadedFile::fake()->image('random.jpg');
        $response = $this->json('post', '/api/image', [
            'file' => $file,
            'name' => $name,
        ]);

        $response->assertStatus(422);
    }

}
