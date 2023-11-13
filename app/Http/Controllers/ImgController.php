<?php

namespace App\Http\Controllers;

use App\Traits\DeleteTrait;
use App\Traits\UploadTrait;
// use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
class ImgController extends Controller
{
    use UploadTrait, DeleteTrait;


    public function storeImage(Request $request, String $id)
    {       
        try {
            $file = [];
            $image = $request->file('image');
            $user_id = $id;
            $getImage =  $image;

            // validate file extention
            $accepted_extentions = ['png', 'jpg', 'jpeg'];
            if(!in_array(strtolower($image->getClientOriginalExtension()), $accepted_extentions)){
                return response()->json(['message' => 'File type not accepted'], 400);
            }

            // delete directory if exist from resource path
            if (File::exists(resource_path('image/'.$user_id))) {
                File::deleteDirectory(resource_path('image/'.$user_id));
            }

            // delete directory if exist from storage path
            if (File::exists(storage_path('app/public/.cache/'.$user_id))) {
                File::deleteDirectory(storage_path('app/public/.cache/'.$user_id));
            }

            $imageName = $this->uploadOne($getImage, $user_id, $image->getClientOriginalName()); // params file, path, name
            $name = str_replace('.'.$image->getClientOriginalExtension(), '', $image->getClientOriginalName());
            $file[$name] = 'img/'.$user_id.'/'.$imageName;

            return json_encode($file);
        }catch (\Exception $e){
            return json_encode($e->getMessage());
        }

    }

    public function show(Request $request, String $id, $path)
    {
            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(
                    $request
                ),
                'source' => resource_path('image/'.$id),
                // 'cache' => $filesystem->getDriver(),
                'cache' => storage_path('app/public/.cache/'.$id.'/'),
                'cache_path_prefix' => '',
                'base_url' => 'image',
            ]);
            try {
                //code...
                return $server->getImageResponse($path, request()->all());
            } catch (\Throwable $th) {
                throw $th;
                return response()->json(['message' => 'Service does not exist'], 404);
            }

    }

    // no need to use this function because we are already handling it in the storeImage function
    public function deleteImg(Request $request, string $id, string $path)
    {
        try {
            $removeDir = false;
            $deleted = false;
            if (File::exists(resource_path('image/'. $id . $path))){
                // code...
                $deleted = false;
                // return var_dump(scandir(resource_path('image/1')));
                // return var_dump(scandir(resource_path('image/'. $id . $path)));
                $arr = [];
                foreach (scandir(resource_path('image/'. $id . $path)) as $key => $file) {
                    // code...
                    array_push($arr , 'image/'. $id . $path . '/' . $key . $file);

                    if (is_dir($file)) {
                        continue;
                    }
                   $deleted = unlink(resource_path('image/'.$id . $path . '/' . $file));
                }
                
                $removeDir = rmdir(resource_path('image/'.$id . $path));

            }

            $storageDelete = false;
            if (File::exists(storage_path('app/public/.cache/'.$id.$path))) {
                // code...
                $storageDelete = File::deleteDirectory(storage_path('app/public/.cache/'.$id.$path));
                return $removeDir && $deleted && $storageDelete ? 'removed' : 'not-removed';
            }
               return $removeDir && $deleted ? 'removed' : 'not-removed';

            
        } catch (\Exception $exception) {
            return json_encode($exception->getMessage());
        }
    }

}



