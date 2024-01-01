<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AllNotification;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

    //Admin
    public function sendGlobalNotification(Request $request){

        if(!Gate::allows('SEND_GLOBAL_NOTIFICATION')){
            return $this->sendUnauthorizedResponse();
        }

        $u_data = Auth::user();
        $role = $u_data->account_type;
        $role = $u_data->roles[0]->pivot->role_id;
        if($role == 1 || $role == 2 || $role == 3) {
            $users = User::select('id')->where('account_type', '<>', 10)->where('account_type', '<>', 11)->get();
            $user_id = Auth::id();
            foreach($users as $user) {
                $model = new AllNotification();
                $model->sender = $user_id;
                $model->receiver = $user->id;
                $model->title = $request->title;
                $model->description = $request->description;
                $model->save();
            }
            return $this->sendSuccessResponse([], 'Data Saved Successfully!');
        }  else {
            return $this->sendErrorResponse('This feature is only for Admin or Support Admin');
        }
    }
    public function getAllUsers(Request $request){
        if(!Gate::allows('GET_ALL_USER')){
            return $this->sendUnauthorizedResponse();
        }
        $u_data = Auth::user();
        $role = $u_data->account_type;
        if($role == 10 || $role == 11) {
            $users = User::select('id')->where('account_type', '<>', 10)->where('account_type', '<>', 11)->pluck('id')->toArray();
            return $this->sendSuccessResponse($users, 'Data fetched Successfully!');
        }  else {
            return $this->sendErrorResponse('This feature is only for Admin or Support Admin');
        }
    }

    // End Admin

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

    public function seenNotification(int $id){
        if(!empty($id)){
            AllNotification::where('id', $id)->update(['seen' =>1]);
            return $this->sendSuccessResponse([], 'Notification Seen Successfully!');
        } else {
            return $this->sendErrorResponse('Notification ID is required!');
        }
    }

    public function seenAllNotification() {
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
