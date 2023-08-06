<?php

namespace App\Http\Controllers;

use App\Traits\DeleteTrait;
use App\Traits\UploadTrait;
use Illuminate\Contracts\Filesystem\Filesystem;
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
            // return response()->json(['message' => $request->hasFile('image'), 'id' => $user_id, 'image'=> $image->getClientOriginalName()], 200);
            $imageName = $this->uploadOne($getImage, $user_id, $image->getClientOriginalName()); // params file, path, name
            $name = str_replace('.'.$image->getClientOriginalExtension(), '', $image->getClientOriginalName());
            $file[$name] = 'img/'.$user_id.'/'.$imageName;

            return json_encode($file);
        }catch (\Exception $e){
            return json_encode($e->getMessage());
        }

    }

    public function show(Filesystem $filesystem, Request $request, String $id, $path)
    {
            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(
                    $request
                ),
                'source' => resource_path('image/'.$id),
                'cache' => $filesystem->getDriver(),
                'cache_path_prefix' => '.cache/'.$id,
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
            if (File::exists(storage_path('app/.cache/'.$id.$path))) {
                // code...
                $storageDelete = Storage::deleteDirectory(storage_path('app/.cache/'.$id));
                return $removeDir && $deleted && $storageDelete ? 'removed' : 'not-rmeoved';
            }
               return $removeDir && $deleted ? 'removed' : 'not-rmeoveds';

            
        } catch (\Exception $exception) {
            return json_encode($exception->getMessage());
        }
    }

}



