<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateImage extends Model
{
    use HasFactory;

    const USER_ID = 'user_id';
    const IMAGE = 'image';
    const IMAGE_TYPE = 'image_type';
    const IMAGE_PATH = 'image_path';
    const IMAGE_VISIBILITY = 'image_visibility';
    const IMAGE_DISK = 'disk';
    const IMAGE_AVATAR = 'per_avatar_url';
    const IMAGE_MAIN = 'per_main_image_url';

    const IMAGE_TYPE_1 = 'avatar';
    const IMAGE_TYPE_2 = 'main-image';
    const IMAGE_TYPE_3 = 'additional-image-one';
    const IMAGE_TYPE_4 = 'additional-image-two';
    const IMAGE_TYPE_5 = 'additional-image-three';
    const IMAGE_TYPE_6 = 'additional-image-four';
    const IMAGE_TYPE_7 = 'additional-image-five';
    const IMAGE_TYPE_8 = 'additional-image-six';

    protected $fillable = [
        self::USER_ID,
        self::IMAGE_TYPE,
        self::IMAGE_PATH,
        self::IMAGE_VISIBILITY,
        self::IMAGE_DISK,
    ];

    /**
     * @param int $type
     * @return string
     */
    public static function getImageType(int $type): string
    {
        switch ($type){
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
                $image_type = 'IMAGE_TYPE_'.$type;
                break;
            default:
                $image_type = 'IMAGE_TYPE_1';
        }
        return constant('self::'.$image_type);
    }
}
