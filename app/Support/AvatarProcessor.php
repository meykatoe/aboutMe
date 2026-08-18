<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class AvatarProcessor
{
    protected const MAX_DIMENSION = 512;

    protected const JPEG_QUALITY = 85;

    /**
     * Re-encode an uploaded avatar to a fixed-size JPEG, discarding EXIF/ICC
     * metadata and any non-pixel data the original file carried.
     *
     * @throws RuntimeException if the file isn't a decodable raster image
     */
    public static function process(UploadedFile $file): string
    {
        $source = @imagecreatefromstring(file_get_contents($file->getRealPath()));

        if ($source === false) {
            throw new RuntimeException('Uploaded file is not a valid image.');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, self::MAX_DIMENSION / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($target, 255, 255, 255);
        imagefill($target, 0, 0, $white);

        imagecopyresampled(
            $target, $source,
            0, 0, 0, 0,
            $targetWidth, $targetHeight, $width, $height,
        );

        imagedestroy($source);

        ob_start();
        imagejpeg($target, null, self::JPEG_QUALITY);
        $contents = ob_get_clean();

        imagedestroy($target);

        return $contents;
    }
}
