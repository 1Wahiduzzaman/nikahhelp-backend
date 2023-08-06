<?php

namespace App\Traits;



use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    /**
     * Upload image
     * @param
     * @return \Illuminate\Http\Response
     */
    public function uploadTwo($file, $image_path)
    {
        $salt_image  = time().rand(1111, 9999);

        $image_name = $salt_image.'.'.$file->getClientOriginalExtension();

        $file->move($image_path, $image_name);

        return $image_name;
    }

    public function uploadOne(UploadedFile $file, $user_id=null,$image_name)
    {

        try {


            $this->makeFolder($user_id);

            if (File::exists(resource_path('image/'.$user_id.'/'))) {
                $uploadfile = resource_path('image/'.$user_id.'/') . basename($image_name);
                move_uploaded_file($file, $uploadfile);
            }


            return $image_name;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }

    /**
     * Upload image
     * @param
     * @return \Illuminate\Http\Response
     */
    public function uploadMultiImage($file, $image_path)
    {
        $salt_image  = time().rand(1111, 9999);

        $image_name = $salt_image.'.'.$file->getClientOriginalExtension();

        $file->move($image_path, $image_name);

        return $image_name;
    }

   
    /**
     * @param bool $folderPath
     * @param $user_id
     * @return void
     */
    public function makeFolder($user_id): bool
    {
        if (File::exists(resource_path('image/'.$user_id.'/'))) {
            return false;
        }
        
        return File::makeDirectory(resource_path('/').'/image/'.$user_id, 0777, true, true);
    }
}
