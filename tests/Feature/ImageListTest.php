<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Feature\Traits\ImageTrait;
use Tests\TestCase;

class ImageListTest extends TestCase
{
    use RefreshDatabase, ImageTrait;

    public function test_application_lists_images_with_no_parameters()
    {
        $images = explode(',', '0,1,2,3,4,5,6,7,8,9');

        collect($images)->map(fn() => $this->imageFactory());

        $response = $this->json('get', '/api/image');

        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertNotEmpty($data);;
    }

    public function test_application_lists_images_with_parameters()
    {
        $images = explode(',', '0,1,2,3,4,5,6,7,8,9');

        collect($images)->map(fn() => $this->imageFactory('test'.Str::random()));

        $response = $this->json('get', '/api/image');

        $response->assertOk();

        $data = json_decode($response->getContent(), true);

        $this->assertNotEmpty($data);;

        /*Filters out all the images that doesn't start with test*/
        $data = collect($data)->filter(fn(array $entry) => Str::startsWith($entry['name'], 'test'))->values()->all();

        $this->assertTrue(count($data) >= 10);
    }

}
