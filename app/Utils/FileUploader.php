<?php

namespace App\Utils;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * TODO later change the job and the class
 * Class FileUploader
 * @package App\Utils
 */
class FileUploader
{
    /**
     * @param $file
     * @param string $directory
     * @param string $key
     * @param string|null $oldFile
     * @return string
     */
    public function fileUpload($file, string $directory, string $key, string $oldFile = null): string
    {
        if ($oldFile) {
            $this->deleteFile($oldFile);
        }

        $path = $this->concatIdentifier($directory);

        $name = time() . '_' . $key . '.' . $file->getClientOriginalExtension();

        $file->move($path, $name);

        return "storage/$directory/$name";
    }

    /**
     * @param $qrcode
     * @param string $directory
     * @param string $key
     * @param string $extension
     * @return string
     */
    public function qrCodeUpload($qrcode, string $directory, string $key, string $extension = 'svg'): string
    {
        $name = time() . '_' . $key . '.' . $extension;
        $path = "$directory/$name";
        Storage::put($path, $qrcode);
        return "storage/$path";
    }

    /**
     * @param string $directory
     * @return string
     */
    private function concatIdentifier(string $directory): string
    {
        return storage_path("app/public/$directory/");
    }

    /**
     * @param string $file
     */
    public function deleteFile(string $file): void
    {
        if (File::exists($file)) {
            File::delete($file);
        }
    }
}
