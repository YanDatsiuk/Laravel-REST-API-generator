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

        //save image
        Storage::put($relativePath, $fileContent, 'public');

        //change path for access it via URL
        $relativePath = str_replace_first('public', 'storage', $relativePath);

        //create new record with image info
        $image = Image::create([
            'image_src' => $relativePath
        ]);

        return $image;
    }

}