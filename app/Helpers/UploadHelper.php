<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadHelper
{
    /**
     * Image upload helper
     *
     * @param $file
     * @param $directory
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function imageUpload($file, $directory, $width = 300, $height = 300): string
    {
        $filename = time() . $file->getClientOriginalName();
        $image = Image::make($file)->fit($width, $height);
        Storage::put("public/images/{$directory}/{$filename}/", (string)$image->encode());
        return "images/avatars/{$filename}";
    }

    public static function imageUploadOriginalSize($file, $directory): string
    {
        $filename = time() . $file->getClientOriginalName();
        $image = Image::make($file);
        Storage::put("public/images/{$directory}/{$filename}/", (string)$image->encode());
        return "images/avatars/{$filename}";
    }
        public static function file_upload($file = NULL, $dir = NULL,$width='270',$height='250')
        {
            if ($dir == NULL || $file ==NULL) {
                return false;
            } else {
                $name = $file;

                $new_name = time().$name->getClientOriginalName();

                $new_name = pathinfo($new_name,PATHINFO_FILENAME);
                $new_name = \Illuminate\Support\Str::slug($new_name);

                $get_extension = $name->getClientOriginalExtension();


                if($get_extension == 'jpg' || $get_extension =='png' || $get_extension =='jpeg'){

                    $new_name = $new_name.".".$get_extension;

                    $name->move($dir,$new_name);


                    $image = Image::make($dir.$new_name)->resize($width,$height);
                    $image->save();
                    return $dir.$new_name;
                }
                else {
                    $name->move($dir, $new_name);

                    return $dir . $new_name;
                }
            }
        }


    /**
     * File upload helper
     *
     * @param $file
     * @param $directory
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function fileUpload($file, $directory): string
    {
        $filename = time() . $file->getClientOriginalName();
        Storage::put("public/files/{$directory}/{$filename}/",file_get_contents($file));

        return "files/{$directory}/{$filename}";
    }
}
