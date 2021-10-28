<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $teamID=$this->team_id;
        $teamInfo=array();
        if(!empty($teamID) && !empty($this->team['name'])){
            $teamInfo['name']=$this->team['name'];
            $teamInfo['description']=$this->team['description'];
            $teamInfo['status']=$this->team['status'];
            $teamInfo['logo']= url('storage/'.$this->team['logo']);;
        }
        $userID=$this->user_id;
        $userInfo=array();
        if(!empty($userID) && !empty($this->user['full_name'])){
            $userInfo['name']=$this->user['full_name'];
            if($this->user->user_type==1){
                $userInfo['logo']= url('storage/'. $this->candidateUser['per_main_image_url']);
            } elseif($this->user->user_type==2){
                $userInfo['logo']= url('storage/'. $this->repUser['per_main_image_url']);
            }else{
                $userInfo['logo']= null;
            }
        }
        if(!empty($this->userInfo['per_main_image_url'])):
        $image = url('storage/' . $this->userInfo['per_main_image_url']);
        else:
            $image=null;
        endif;
        return [
            'id' => $this->id ?? null,
            'data' => $this->data ?? null,
            'type' => $this->type ?? null,
            'user_id' => $this->user_id ?? null,
            'team_id' => $this->team_id ?? null,
            'team_info' => $teamInfo ?? null,
            'user_info' => $userInfo ?? null,
            'create_date' =>Carbon::parse($this->created_at)->format('d M Y').' at '.Carbon::parse($this->created_at)->format('h:i a')
        ];

    }

}
