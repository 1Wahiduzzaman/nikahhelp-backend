<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AllNotification;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Auth;

class AllNotificationController extends Controller
{
    // For every cases this will fire
    public function saveNotifications(Request $request){                
        $model = new AllNotification();
        $model->sender = $request->sender;
        $model->receiver = $request->receiver;
        $model->team_id = $request->team_id;
        $model->title = $request->title;
        $model->description = $request->description;
        $model->save();
        return $this->sendSuccessResponse([], 'Data Saved Successfully!');
    }

    public function listNotifications(Request $request){  
        $user_id = Auth::id();      
        $active_team = TeamMember::where('user_id', $user_id)
        ->where('status', 1)
        ->first();
        $active_team_id = isset($active_team) ? $active_team->team_id : 0;        
        $data = AllNotification::with('sender')->with('team')
            ->where('team_id', $active_team_id)
            ->where('receiver', $user_id)
            ->orWhere(function($q){               
                $q->where(['team_id'=> null, 'receiver' => Auth::id()]);                  
            })   
            ->orderBy('created_at', 'desc')    
            ->get();
        return $this->sendSuccessResponse($data, 'Data fetched Successfully!');
    }

    public function seenNotification(){
        $user_id = Auth::id();      
        $active_team = TeamMember::where('user_id', $user_id)
        ->where('status', 1)
        ->first();
        $active_team_id = isset($active_team) ? $active_team->team_id : 0; 
        AllNotification::where('team_id', $active_team_id)
        ->where('receiver', $user_id)
        ->orWhere(function($q){               
            $q->where(['team_id'=> null, 'receiver' => Auth::id()]);                  
        })   
        ->update(['seen' =>1]);
        return $this->sendSuccessResponse([], 'Notification Seen Successfully!');
    }
}
