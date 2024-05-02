<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait fileUploadTrait
{
    public function fileUplaod(Request $request, $diskName, $folderName, $inputName)
    {
        //it returns object of uploaded file object
        $file = $request->file($inputName);
        $path = $file->store($folderName, ['disk' => $diskName]);
        return $path;
    }

    public function filesUplaod(Request $request, $diskName, $folderName, $inputName)
    {
        //it returns object of uploaded file object
        $files = $request->file($inputName);
        foreach ($files as $file) {
            $path[] = $file->store($folderName, ['disk' => $diskName]);
        }
        return $path;
    }

    public function fileUpdate(Request $request, string $diskName, string $folderName, string $inputName, string $oldFileName = null)
    {

        $file = $request->file($inputName);
        $path = $file->store($folderName, ['disk' => $diskName]);
        //delete the old file from storage if exist
        if (($oldFileName != null) && (Storage::disk($diskName)->exists($oldFileName))) {
            Storage::disk($diskName)->delete($oldFileName);
        }
        return $path;
    }

    public function deleteFile($diskName,  $fileName)
    {
        if ($fileName == null) {
            return;
        }
        //delete the old file from storage
        if ($fileName != null && Storage::disk($diskName)->exists($fileName)) {
            Storage::disk($diskName)->delete($fileName);
        }
    }
}
