<?php

namespace TMPHP\RestApiGenerators\Support;

use App\REST\Image;//todo refactor required!
use Illuminate\Support\Facades\Storage;

class ImageUploader
{

    /**
     * Upload image from file
     *
     * @param string $filePath
     * @return \App\Image $image
     */
    public static function file($filePath)
    {
        $fileContent = file_get_contents($filePath);
        $relativePath = 'public/uploads/pictures/' . rand() . '/' . time() . '_pic';

        //Saving image
        Storage::put($relativePath, $fileContent, 'public');

        //Creating new record with image info
        $image = Image::create([
            'image_src' => $relativePath
        ]);

        return $image;
    }

}