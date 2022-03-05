<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as ImageAnalizer;
use Tests\Feature\Traits\ImageTrait;
use Tests\TestCase;

class ImageResizeTest extends TestCase
{
    use RefreshDatabase, ImageTrait;

    /**
     * I cannot test the actual resize because the operation is queued
     *
     * @return void
     */
    public function test_application_resize_image_is_ok()
    {
        $imageModel = $this->imageFactory();

        $response = $this->json('put', '/api/image/resize', [
            'name' => $imageModel->name,
            'width' => 640,
            'height' => 480
        ]);

        $response->assertOk();
    }

    /**
     * I test the actual resize method in model that is called in the queue
     *
     * @return void
     */
    public function test_model_resize_image()
    {
        $imageModel = $this->imageFactory();

        $imageModel->resizeImage(150, 150);

        $imageAnalizer = ImageAnalizer::make($imageModel->getImage()->getPath());

        $this->assertTrue($imageAnalizer->getWidth() == 150, 'The width is not exact');
        $this->assertTrue($imageAnalizer->getHeight() == 150, 'The width is not exact');
    }

    public function test_application_resize_fails_if_image_name_is_wrong()
    {
        $imageModel = $this->imageFactory();

        $response = $this->json('put', '/api/image/resize', [
            'name' => Str::random(32),
            'width' => 640,
            'height' => 480
        ]);

        $response->assertStatus(422);
    }
}
