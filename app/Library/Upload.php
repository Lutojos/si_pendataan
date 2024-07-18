<?php

/**
 *
 */

namespace App\Library;

use Illuminate\Support\Facades\Storage;

/**
 * Upload.
 */
class Upload
{
    public $disk;

    /**
     *
     */
    public function __construct()
    {
        $this->disk = Storage::disk(env('FILESYSTEM_DISK', 'public'));
    }

    /**
     * upload.
     *
     * @param mixed $file
     * @param mixed $path
     * @return string
     */
    public function upload($file, $path)
    {
        $extension = $file->getClientOriginalExtension();
        $fileName  = time() . rand() . '.' . $extension;
        $filePath  = $path . '/' . $fileName;
        $this->disk->put($filePath, fopen($file, 'r+'));

        return $fileName;
    }

    /**
     * delete.
     *
     * @param mixed $path
     * @return bool
     */
    public function delete($path)
    {
        //check if file exists
        if ($this->disk->exists($path)) {
            $this->disk->delete($path);
        }

        return true;
    }

    /**
     * uploadBase64.
     *
     * @param mixed $base64
     * @param mixed $path
     * @return string
     */
    public function uploadBase64($base64, $path)
    {
        //base ="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQ"
        $explodeBase64 = explode(";base64,", $base64);
        $typeAuxiliary = explode("image/", $explodeBase64[0]);
        $imageType     = $typeAuxiliary[1];
        $imageDecoded  = base64_decode($explodeBase64[1]);
        $fileName      = time() . rand() . '.' . $imageType;
        $filePath      = $path . '/' . $fileName;
        $this->disk->put($filePath, $imageDecoded);

        return $fileName;
    }

    /**
     * checkFileExists.
     *
     * @param mixed $path
     * @return bool
     */
    public function checkFileExists($path)
    {
        return $this->disk->exists($path);
    }
}
