<?php

namespace App\Http\Controllers;

use App\Traits\DeleteTrait;
use App\Traits\UploadTrait;
// use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImgController extends Controller
{
    use DeleteTrait, UploadTrait;
    
    // ------------ store image in the storage ------------
    // public function storeImage(Request $request, string $id)
    // {
    //     try {
    //         $file = [];
    //         $image = $request->file('image');
    //         $user_id = $id;
    //         $getImage = $image;

    //         // validate file extention
    //         $accepted_extentions = ['png', 'jpg', 'jpeg'];

    //         // delete the old image from the cache
    //         $path = resource_path('image/'.$user_id); // Replace with your actual path
    //         if (is_dir($path)) {
    //             $files = scandir($path);
    //             // Skip "." and ".." entries
    //             $firstFile = array_values($files)[2] ?? null;

    //             if ($firstFile) {
    //                 // Generate the cache key
    //                 $full_path = $path.'/'.$firstFile;
    //                 $cache_key = 'image_'.md5($full_path);

    //                 // Check and delete the old image from the cache
    //                 if (Cache::has($cache_key)) {
    //                     Cache::forget($cache_key);
    //                 }
    //             }
    //         }

    //         if (! in_array(strtolower($image->getClientOriginalExtension()), $accepted_extentions)) {
    //             return response()->json(['message' => 'File type not accepted'], 400);
    //         }

    //         // delete directory if exist from resource path
    //         if (File::exists(resource_path('image/'.$user_id))) {
    //             File::deleteDirectory(resource_path('image/'.$user_id));
    //         }

    //         // delete directory if exist from storage path
    //         if (File::exists(storage_path('app/public/.cache/'.$user_id))) {
    //             File::deleteDirectory(storage_path('app/public/.cache/'.$user_id));
    //         }

    //         $imageName = $this->uploadOne($getImage, $user_id, $image->getClientOriginalName()); // params file, path, name
    //         $name = str_replace('.'.$image->getClientOriginalExtension(), '', $image->getClientOriginalName());
    //         $file[$name] = 'img/'.$user_id.'/'.$imageName;

    //         return json_encode($file);
    //     } catch (\Exception $e) {
    //         return json_encode($e->getMessage());
    //     }

    // }


    // ------------ store image in cloudflare r2 storage ------------
    public function storeImage(Request $request, string $id)
    {
        try {
            $file = [];
            $image = $request->file('image');

            // validate file extention
            $accepted_extentions = ['png', 'jpg', 'jpeg'];
            if (! in_array(strtolower($image->getClientOriginalExtension()), $accepted_extentions)) {
                return response()->json(['message' => 'File type not accepted'], 400);
            }


            // delete the old image from storage
            $path = 'image/'.$id; 
            // if(Storage::disk('r2')->exists($path.'/'.basename($image->getClientOriginalName()))){
            //     Storage::disk('r2')->delete($path.'/'.basename($image->getClientOriginalName()));
            // }

            // delete the whole directory
            if (Storage::disk('r2')->exists($path)) {
                Storage::disk('r2')->deleteDirectory($path);
            }
            
            // upload the new image to storage
            Storage::disk('r2')->put($path.'/'.basename($image->getClientOriginalName()), file_get_contents($image));



            $imageName = $image->getClientOriginalName(); // params file, path, name
            $name = str_replace('.'.$image->getClientOriginalExtension(), '', $image->getClientOriginalName());
            $file[$name] = 'img/'.$id.'/'.$imageName;

            return json_encode($file);
        } catch (\Exception $e) {
            error_log($e->getMessage()." hhhh 3");
            return json_encode($e->getMessage());
        }

    }

    // ---------- retrieve image from the storage ------------
    // public function show(Request $request, string $id, $path)
    // {
    //     $full_path = resource_path('image/'.$id.'/'.$path);
    //     $cache_key = 'image_'.md5($full_path);

    //     if (! File::exists($full_path)) {
    //         return response()->json(['message' => 'Service does not exist'], 404);
    //     }

    //     // Check if the image is already cached
    //     if (Cache::has($cache_key)) {
    //         $cachedImage = Cache::get($cache_key);
    //         $type = File::mimeType($full_path);

    //         return response($cachedImage, 200)->header('Content-Type', $type);
    //     }

    //     // If not cached, read the file from the disk, cache it, and return the response
    //     $file = File::get($full_path);
    //     $type = File::mimeType($full_path);
    //     Cache::put($cache_key, $file, now()->addMinutes(60)); // Cache for 60 minutes

    //     return response($file, 200)->header('Content-Type', $type);
    // }


    // ---------- retrieve image from the cloudflare r2 storage ------------
    public function show(Request $request, string $id, $path)
    {
        $full_path = 'image/'.$id.'/'.$path;

        if (! Storage::disk('r2')->exists($full_path)) {
            return response()->json(['message' => 'Image does not exist'], 404);
        }

        $file = Storage::disk('r2')->get($full_path);
        $type = Storage::disk('r2')->mimeType($full_path);

        return response($file, 200)->header('Content-Type', $type);
    }

    

    // no need to use this function because we are already handling it in the storeImage function
    // public function deleteImg(Request $request, string $id, string $path)
    // {
    //     try {
    //         $removeDir = false;
    //         $deleted = false;
    //         if (File::exists(resource_path('image/'.$id.$path))) {
    //             // code...
    //             $deleted = false;
    //             // return var_dump(scandir(resource_path('image/1')));
    //             // return var_dump(scandir(resource_path('image/'. $id . $path)));
    //             $arr = [];
    //             foreach (scandir(resource_path('image/'.$id.$path)) as $key => $file) {
    //                 // code...
    //                 array_push($arr, 'image/'.$id.$path.'/'.$key.$file);

    //                 if (is_dir($file)) {
    //                     continue;
    //                 }
    //                 $deleted = unlink(resource_path('image/'.$id.$path.'/'.$file));
    //             }

    //             $removeDir = rmdir(resource_path('image/'.$id.$path));

    //         }

    //         $storageDelete = false;
    //         if (File::exists(storage_path('app/public/.cache/'.$id.$path))) {
    //             // code...
    //             $storageDelete = File::deleteDirectory(storage_path('app/public/.cache/'.$id.$path));

    //             return $removeDir && $deleted && $storageDelete ? 'removed' : 'not-removed';
    //         }

    //         return $removeDir && $deleted ? 'removed' : 'not-removed';

    //     } catch (\Exception $exception) {
    //         return json_encode($exception->getMessage());
    //     }
    // }


    public function deleteImg(Request $request, string $id, string $path)
    {
        try {
            $removeDir = false;
            $deleted = false;
            if (File::exists(resource_path('image/'.$id.$path))) {
                // code...
                $deleted = false;
                // return var_dump(scandir(resource_path('image/1')));
                // return var_dump(scandir(resource_path('image/'. $id . $path)));
                $arr = [];
                foreach (scandir(resource_path('image/'.$id.$path)) as $key => $file) {
                    // code...
                    array_push($arr, 'image/'.$id.$path.'/'.$key.$file);

                    if (is_dir($file)) {
                        continue;
                    }
                    $deleted = unlink(resource_path('image/'.$id.$path.'/'.$file));
                }

                $removeDir = rmdir(resource_path('image/'.$id.$path));

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
