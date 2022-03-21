<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ImageUpload
{


    public static function uploadSingleImage(UploadedFile $file, string $dir = ''): string
    {
      $path = Storage::disk('public')->put($dir, $file, 'public');
      return $path;
    }

    public static function imageExists(string $path): bool
    {
      if (Storage::disk('public')->exists($path)) {
        return true;
      }

      return false;
    }

    public static function deleteFile(string $path): bool
    {
      $checkStatus = Self::imageExists($path);
      if ($checkStatus) {
        $status = Storage::disk('public')->delete($path);
        return $status;
      }

      return false;
    }
}
