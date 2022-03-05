<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageResource;
use App\Jobs\ResizeImageJob;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    //
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif,svg',
                'max:4096',
            ],
            'name' => [
                'required',
                'string',
                'unique:'.Image::class.',name'
            ]
        ]);

        $name = $request->input('name');
        $file = $request->file('file');

        DB::transaction(function () use ($name, $file){
            $imageModel = new Image();
            $imageModel->name = $name;
            $imageModel->save();
            $imageModel->setImage($file);
        });

        return response()->json([], 201);
    }

    public function resize(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'exists:'.Image::class.',name'
            ],
            'width' => [
                'required',
                'integer'
            ],
            'height' => [
                'required',
                'integer'
            ],
        ]);

        $name = $request->input('name');
        $width = $request->input('width');
        $height = $request->input('height');

        /** @var Image $imageModel */
        $imageModel = Image::name($name)->firstOrFail();
        ResizeImageJob::dispatch($imageModel, $width, $height);
    }

    public function delete(Image $image)
    {
        $image->deleteMedia($image->getImage());
        $image->delete();
    }

    public function lists(Request $request) : JsonResource
    {
        $this->validate($request, [
            'name' => [
                'nullable',
                'string'
            ],
            'data' => [
                'nullable',
                'boolean'
            ],
        ]);

        return ImageResource::collection(Image::nameLike($request->input('name'))->get());
    }
}
