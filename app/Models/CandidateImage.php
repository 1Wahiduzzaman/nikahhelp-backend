<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public static function getPermissionStatus(int $userId):bool
    {
        $status = false;

        $candidate = CandidateInformation::where('user_id',$userId)->first();

        if(!$candidate){
            return  $status;
        }

        /* any body can see */
        if($candidate->anybody_can_see){
            return $status = true;
        }


        $auth = CandidateInformation::where('user_id',Auth::id())->first();

        if(!$auth){
            return  $status;
        }

        /* Only Team Can see */
        if(in_array($auth->user_id,$candidate->active_team->team_members->pluck('user_id')->toArray())){
            return $status = true;
        }

        /* if auth id and candidate id is same it will return true */
        if($auth->user_id == $candidate->user_id){
            return $status = true;
        }


        /* Only Connected Team Can see */
        if($candidate->team_connection_can_see){
            if (!$auth->active_team) {
                return $status;
            }
            $candidateTeam = $candidate->active_team;
            $connectFrom = $candidateTeam->sentRequest->pluck('team_id')->toArray();
            $connectTo = $candidateTeam->receivedRequest->pluck('team_id')->toArray();
            $connectedTeamList = array_unique (array_merge($connectFrom,$connectTo)) ;
            return $status = in_array($auth->active_team->team_id,$connectedTeamList);
        }

        return $status;
    }

    public static function getCandidateMainImage(int $userId)
    {
        $status = self::getPermissionStatus($userId);
        $candidate = CandidateInformation::where('user_id',$userId)->first();
        // $mainImage = $candidate->per_avatar_url ? env('IMAGE_SERVER') . '/' . $candidate->per_avatar_url : '';
        $mainImage = isset($candidate->per_avatar_url) ? $candidate->per_avatar_url : '';
        if($status){
            // $mainImage = $candidate->per_main_image_url ? env('IMAGE_SERVER') . '/' . $candidate->per_main_image_url : '';
            $mainImage = isset($candidate->per_main_image_url) ? $candidate->per_main_image_url : '';
        }
        return $mainImage;
    }

}
